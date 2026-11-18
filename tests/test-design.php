<?php
/**
 * Design apply and undo tests: style variation, template part and template customisations,
 * backups of earlier customisations and keeping edits made after setup.
 */
add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['testdesign'] = array(
			'slug'      => 'testdesign',
			'label'     => 'Test design',
			'style'     => 'tests/fixtures/test-style.json',
			'parts'     => array( 'header' => 'pixelgrid/footer' ),
			'templates' => array( 'home' => 'pixelgrid/blog-heading' ),
		);
		return $variants;
	}
);

$variant = pixelgrid_variant( 'testdesign' );
$theme   = get_stylesheet();

/**
 * Current user global styles content, decoded.
 *
 * @return array
 */
function pg_test_styles() {
	$post = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true );
	return json_decode( get_post( $post['ID'] )->post_content, true );
}

/**
 * Writes user global styles.
 *
 * @param array $data Decoded styles.
 */
function pg_test_set_styles( $data ) {
	$post = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true );
	wp_update_post(
		wp_slash(
			array(
				'ID'           => $post['ID'],
				'post_content' => wp_json_encode( $data ),
			)
		)
	);
	WP_Theme_JSON_Resolver::clean_cached_data();
}

/**
 * The database customisation of a template or part, or null.
 *
 * @param string $slug Slug.
 * @param string $type wp_template_part or wp_template.
 * @return WP_Block_Template|null
 */
function pg_test_custom( $slug, $type ) {
	$tpl = get_block_template( get_stylesheet() . '//' . $slug, $type );
	return ( $tpl && 'custom' === $tpl->source && $tpl->wp_id ) ? $tpl : null;
}

/**
 * Hand-made header customisation, like one saved in the Site Editor.
 *
 * @param string $content Content.
 * @return int Post ID.
 */
function pg_test_make_header( $content ) {
	$id = wp_insert_post(
		array(
			'post_type'    => 'wp_template_part',
			'post_name'    => 'header',
			'post_title'   => 'Header',
			'post_status'  => 'publish',
			'post_content' => $content,
		)
	);
	wp_set_object_terms( $id, get_stylesheet(), 'wp_theme' );
	wp_set_object_terms( $id, 'header', 'wp_template_part_area' );
	return $id;
}

/**
 * Sets a post's content.
 *
 * @param int    $id      Post ID.
 * @param string $content Content.
 */
function pg_test_set_content( $id, $content ) {
	wp_update_post(
		wp_slash(
			array(
				'ID'           => $id,
				'post_content' => $content,
			)
		)
	);
}

$fixture_palette = array(
	array(
		'slug'  => 'blue-hover',
		'color' => '#00C2A8',
		'name'  => 'Blue section: hover',
	),
);
$header_block    = '<!-- wp:template-part {"slug":"header","theme":"' . $theme . '"} /-->';

// Clean site.
$original = pg_test_styles();
pg_assert( null === pg_test_custom( 'header', 'wp_template_part' ), 'clean site has no custom header' );

pixelgrid_design_apply( $variant );
$state = get_option( 'pixelgrid_starter_design' );
pg_assert( is_array( $state ), 'apply stores the pixelgrid_starter_design option' );
global $wpdb;
pg_assert_same( 'off', $wpdb->get_var( $wpdb->prepare( "SELECT autoload FROM {$wpdb->options} WHERE option_name = %s", 'pixelgrid_starter_design' ) ), 'design option is not autoloaded' );

$header = pg_test_custom( 'header', 'wp_template_part' );
pg_assert( null !== $header, 'apply creates a custom header' );
pg_assert( false !== strpos( do_blocks( $header_block ), 'pixelgrid-footer' ), 'header renders the footer pattern' );
pg_assert_same( 'header', $header ? $header->area : '', 'custom header is in the header area' );
pg_assert_same( 'testdesign:design-wp_template_part-header', get_post_meta( $header->wp_id, '_pixelgrid_starter', true ), 'created header carries the design marker' );
$home = pg_test_custom( 'home', 'wp_template' );
pg_assert( null !== $home && false !== strpos( $home->content, 'wp:categories' ) && false === strpos( $home->content, 'wp:pattern' ), 'apply creates a custom home template with the expanded pattern' );
$styles = pg_test_styles();
pg_assert_same( $fixture_palette, $styles['settings']['color']['palette'] ?? null, 'user styles contain the fixture palette' );
pg_assert( ! isset( $styles['title'] ), 'user styles drop the variation title' );
pg_assert( true === ( $styles['isGlobalStylesUserThemeJSON'] ?? null ) && 3 === ( $styles['version'] ?? null ), 'user styles are marked as user theme.json version 3' );
pg_assert( false !== strpos( wp_json_encode( WP_Theme_JSON_Resolver::get_merged_data()->get_raw_data() ), '#00C2A8' ), 'merged global styles show the fixture colour at once' );

// Applying twice keeps the first backups.
pixelgrid_design_apply( $variant );
$state2 = get_option( 'pixelgrid_starter_design' );
pg_assert_same( $state['styles_before'], $state2['styles_before'], 'second apply keeps the first styles backup' );
pg_assert_same( 2, count( $state2['entities'] ), 'second apply keeps two entities' );
pg_assert_same( $header->wp_id, $state2['entities'][0]['post_id'], 'second apply reuses the created header' );
pg_assert( true === $state2['entities'][0]['created'], 'second apply still records the header as created' );

$result = pixelgrid_design_undo();
pg_assert_same(
	array(
		'styles_restored' => true,
		'kept'            => array(),
	),
	$result,
	'undo on a clean site restores styles and keeps nothing'
);
pg_assert( null === pg_test_custom( 'header', 'wp_template_part' ), 'undo deletes the created header' );
pg_assert( null === get_post( $header->wp_id ), 'created header post is gone' );
pg_assert( null === pg_test_custom( 'home', 'wp_template' ), 'undo deletes the created home template' );
pg_assert_same( $original, pg_test_styles(), 'styles equal the original after undo' );
pg_assert( false === get_option( 'pixelgrid_starter_design', false ), 'undo deletes the design option' );
pg_assert_same(
	array(
		'styles_restored' => false,
		'kept'            => array(),
	),
	pixelgrid_design_undo(),
	'undo with nothing applied does nothing'
);

// User styles before setup.
$custom_styles = array(
	'version'                     => 3,
	'isGlobalStylesUserThemeJSON' => true,
	'settings'                    => array(
		'color' => array(
			'palette' => array(
				array(
					'slug'  => 'blue-hover',
					'color' => '#123456',
					'name'  => 'Mine',
				),
			),
		),
	),
);
pg_test_set_styles( $custom_styles );
pixelgrid_design_apply( $variant );
pg_assert_same( $fixture_palette, pg_test_styles()['settings']['color']['palette'], 'apply replaces custom styles with the fixture' );
$result = pixelgrid_design_undo();
pg_assert( true === $result['styles_restored'], 'undo reports styles restored over custom styles' );
pg_assert_same( $custom_styles, pg_test_styles(), 'styles equal the custom palette exactly after undo' );
pg_test_set_styles( $original );

// Header customised before setup.
$own = pg_test_make_header( 'X' );
pixelgrid_design_apply( $variant );
pg_assert_same( '', get_post_meta( $own, '_pixelgrid_starter', true ), 'existing customisation gets no starter marker' );
$header = pg_test_custom( 'header', 'wp_template_part' );
pg_assert_same( $own, (int) $header->wp_id, 'apply updates the existing customisation' );
pg_assert( false !== strpos( $header->content, 'pixelgrid-footer' ), 'existing customisation now holds the pattern' );
$result = pixelgrid_design_undo();
pg_assert_same( array(), $result['kept'], 'undo keeps nothing when the header was not edited after setup' );
pg_assert_same( 'X', get_post( $own )->post_content, 'header content is X again' );
pg_assert_same( 'X', pg_test_custom( 'header', 'wp_template_part' )->content, 'custom header renders X again' );
wp_delete_post( $own, true );

// Header edited after setup.
pixelgrid_design_apply( $variant );
$header = pg_test_custom( 'header', 'wp_template_part' );
pg_test_set_content( $header->wp_id, 'Y' );
$result = pixelgrid_design_undo();
pg_assert_same( array( 'header' ), $result['kept'], 'undo keeps the header edited after setup' );
pg_assert_same( 'Y', get_post( $header->wp_id )->post_content, 'header content stays Y' );
pg_assert( null === pg_test_custom( 'home', 'wp_template' ), 'unedited home template is still removed' );
wp_delete_post( $header->wp_id, true );

// Styles edited after setup.
pixelgrid_design_apply( $variant );
$edited = pg_test_styles();
$edited['settings']['color']['palette'][0]['color'] = '#ABCDEF';
pg_test_set_styles( $edited );
$result = pixelgrid_design_undo();
pg_assert( false === $result['styles_restored'], 'undo reports styles not restored after a style edit' );
pg_assert_same( $edited, pg_test_styles(), 'the style edit remains' );
pg_test_set_styles( $original );

// Same styles in a different key order and whitespace still count as unchanged.
pixelgrid_design_apply( $variant );
$same = array_reverse( pg_test_styles(), true );
$post = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true );
pg_test_set_content( $post['ID'], wp_json_encode( $same, JSON_PRETTY_PRINT ) );
WP_Theme_JSON_Resolver::clean_cached_data();
$result = pixelgrid_design_undo();
pg_assert( true === $result['styles_restored'], 'reordered but equal styles are still restored' );
pg_assert_same( $original, pg_test_styles(), 'styles equal the original after reordered undo' );

// Starter create and remove go through apply and undo.
$made = pixelgrid_starter_create( 'testdesign', array( 'design' => true ) );
pg_assert( null !== pg_test_custom( 'header', 'wp_template_part' ), 'starter create with design applies the header' );
pg_assert_same( 'testdesign', pixelgrid_starter_active_variant(), 'design alone makes the variant active' );
$removed = pixelgrid_starter_remove();
pg_assert( true === $removed['styles_restored'], 'starter remove passes styles_restored through' );
pg_assert( null === pg_test_custom( 'header', 'wp_template_part' ), 'starter remove undoes the header' );
pg_assert_same( '', pixelgrid_starter_active_variant(), 'no active variant after remove' );

// Starter remove never trashes a marked part through its generic loop: an edited header survives.
pixelgrid_starter_create( 'testdesign', array( 'design' => true ) );
$header = pg_test_custom( 'header', 'wp_template_part' );
pg_test_set_content( $header->wp_id, 'Y' );
$removed = pixelgrid_starter_remove();
pg_assert_same( array( 'header' ), $removed['kept'], 'starter remove passes kept through' );
pg_assert_same( 'publish', get_post_status( $header->wp_id ), 'edited header is not trashed by starter remove' );
pg_assert_same( 'Y', get_post( $header->wp_id )->post_content, 'edited header keeps its content after starter remove' );

pg_done();
