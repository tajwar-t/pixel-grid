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
		'styles_written'  => true,
		'revisions'       => true,
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
		'styles_written'  => false,
		'revisions'       => false,
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
pg_assert_same( array( 'wp_template_part:header' ), $result['kept'], 'undo keeps the header edited after setup' );
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

// Applying again keeps edits made after setup to styles and parts.
pixelgrid_design_apply( $variant );
$header = pg_test_custom( 'header', 'wp_template_part' );
$home   = pg_test_custom( 'home', 'wp_template' );
pg_test_set_content( $header->wp_id, 'Y' );
$edited = pg_test_styles();
$edited['settings']['color']['palette'][0]['color'] = '#ABCDEF';
pg_test_set_styles( $edited );
$state_before = get_option( 'pixelgrid_starter_design' );
$again        = pixelgrid_design_apply( $variant );
pg_assert_same( array( 'styles', 'wp_template_part:header' ), $again['skipped'] ?? null, 'second apply reports the edited styles and header as skipped' );
pg_assert_same( 'Y', get_post( $header->wp_id )->post_content, 'second apply keeps the header edit' );
pg_assert_same( $edited, pg_test_styles(), 'second apply keeps the style edit' );
$home_now = pg_test_custom( 'home', 'wp_template' );
pg_assert( null !== $home_now && (int) $home->wp_id === (int) $home_now->wp_id, 'second apply still holds the unedited home template' );
$state_after = get_option( 'pixelgrid_starter_design' );
pg_assert_same( $state_before['styles_written'], $state_after['styles_written'], 'second apply keeps the recorded styles_written' );
pg_assert_same( $state_before['entities'][0], $state_after['entities'][0], 'second apply keeps the header record' );
$result = pixelgrid_design_undo();
pg_assert_same( array( 'wp_template_part:header' ), $result['kept'], 'undo after a second apply keeps the edited header' );
pg_assert( false === $result['styles_restored'] && true === $result['styles_written'], 'undo after a second apply leaves the edited styles' );
pg_assert_same( 'Y', get_post( $header->wp_id )->post_content, 'header is still Y after undo' );
pg_assert_same( $edited, pg_test_styles(), 'style edit is still there after undo' );
pg_assert( null === pg_test_custom( 'home', 'wp_template' ), 'unedited home template is removed after a second apply' );
wp_delete_post( $header->wp_id, true );
pg_test_set_styles( $original );

// A variant without a style variation (IT) leaves user styles alone.
pg_test_set_styles( $custom_styles );
pixelgrid_design_apply( pixelgrid_variant( 'it' ) );
pg_assert_same( $custom_styles, pg_test_styles(), 'IT design leaves user styles unchanged' );
$state = get_option( 'pixelgrid_starter_design' );
pg_assert( '' === $state['styles_before'] && '' === $state['styles_written'], 'IT design records no styles backup' );
$result = pixelgrid_design_undo();
pg_assert( false === $result['styles_written'] && false === $result['styles_restored'], 'undo after IT design reports styles not written and not restored' );
pg_assert_same( $custom_styles, pg_test_styles(), 'user styles unchanged after undo of IT design' );
pixelgrid_starter_create( 'it', array( 'design' => true ) );
pg_assert_same( $custom_styles, pg_test_styles(), 'starter create with IT design leaves user styles unchanged' );
$removed = pixelgrid_starter_remove();
pg_assert( array_key_exists( 'styles_written', $removed ) && false === $removed['styles_written'], 'starter remove after IT design reports styles not written, so no kept-styles message' );
pg_assert_same( $custom_styles, pg_test_styles(), 'user styles unchanged after starter remove of IT design' );
pg_test_set_styles( $original );

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
pg_assert_same( array( 'wp_template_part:header' ), $removed['kept'], 'starter remove passes kept through' );
pg_assert_same( 'publish', get_post_status( $header->wp_id ), 'edited header is not trashed by starter remove' );
pg_assert_same( 'Y', get_post( $header->wp_id )->post_content, 'edited header keeps its content after starter remove' );
wp_delete_post( $header->wp_id, true );

/**
 * Live (not trashed) header part posts.
 *
 * @return int[]
 */
function pg_test_header_posts() {
	return get_posts(
		array(
			'post_type'      => 'wp_template_part',
			'name'           => 'header',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'fields'         => 'ids',
			'posts_per_page' => -1,
		)
	);
}

/**
 * The recorded design entity for a type and slug, or null.
 *
 * @param string $type Type.
 * @param string $slug Slug.
 * @return array|null
 */
function pg_test_entity( $type, $slug ) {
	$state = get_option( 'pixelgrid_starter_design', false );
	foreach ( is_array( $state ) ? $state['entities'] : array() as $entity ) {
		if ( $type === $entity['type'] && $slug === $entity['slug'] ) {
			return $entity;
		}
	}
	return null;
}

// Re-apply after the user deleted a backed-up customisation: no write, record kept, reported.
$own = pg_test_make_header( 'X' );
pixelgrid_design_apply( $variant );
wp_delete_post( $own, true );
$again = pixelgrid_design_apply( $variant );
pg_assert_same( array(), pg_test_header_posts(), 're-apply does not recreate a deleted backed-up header' );
pg_assert( in_array( 'wp_template_part:header', (array) ( $again['skipped'] ?? array() ), true ), 're-apply reports the deleted backed-up header as skipped' );
$record = pg_test_entity( 'wp_template_part', 'header' );
pg_assert( null !== $record && 'X' === $record['before'] && false === $record['created'] && $own === (int) $record['post_id'], 're-apply keeps the backed-up header record and its backup X' );
$result = pixelgrid_design_undo();
pg_assert( in_array( 'wp_template_part:header', $result['kept'], true ), 'undo reports the deleted backed-up header as kept' );
pg_assert_same( array(), pg_test_header_posts(), 'undo does not bring back a header the user deleted' );

// Re-apply after the user trashed a header setup created: nothing new is created.
pixelgrid_design_apply( $variant );
$header = pg_test_custom( 'header', 'wp_template_part' );
wp_trash_post( $header->wp_id );
$again = pixelgrid_design_apply( $variant );
pg_assert_same( array(), pg_test_header_posts(), 're-apply does not recreate a trashed created header' );
pg_assert( in_array( 'wp_template_part:header', (array) ( $again['skipped'] ?? array() ), true ), 're-apply reports the trashed created header as skipped' );
$record = pg_test_entity( 'wp_template_part', 'header' );
pg_assert( null !== $record && true === $record['created'] && (int) $header->wp_id === (int) $record['post_id'], 're-apply keeps the created header record' );
pixelgrid_design_undo();
pg_assert_same( array(), pg_test_header_posts(), 'undo leaves no header after a trashed created header' );
wp_delete_post( $header->wp_id, true );

// A created template takes core's default title.
pixelgrid_design_apply( $variant );
$home = pg_test_custom( 'home', 'wp_template' );
$core = get_default_block_template_types();
pg_assert_same( $core['home']['title'], $home ? get_post( $home->wp_id )->post_title : null, 'created home template uses the core default title' );
pixelgrid_design_undo();

// A missing or broken style file stops apply before anything is written.
add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['testmissing'] = array(
			'slug'      => 'testmissing',
			'label'     => 'Test missing style',
			'style'     => 'styles/does-not-exist.json',
			'parts'     => array( 'header' => 'pixelgrid/footer' ),
			'templates' => array( 'home' => 'pixelgrid/blog-heading' ),
		);
		$variants['testbroken']  = array(
			'slug'  => 'testbroken',
			'label' => 'Test broken style',
			'style' => 'tests/fixtures/broken-style.json',
			'parts' => array( 'header' => 'pixelgrid/footer' ),
		);
		// Everything IT adds, with a missing style file.
		$variants['testmissingfull'] = array_merge(
			$variants['it'],
			array(
				'slug'  => 'testmissingfull',
				'style' => 'styles/does-not-exist.json',
			)
		);
		return $variants;
	}
);
$styles_before = pg_test_styles();
foreach ( array( 'testmissing', 'testbroken' ) as $bad ) {
	$error = pixelgrid_design_apply( pixelgrid_variant( $bad ) );
	pg_assert( is_wp_error( $error ) && 'pixelgrid_style_missing' === $error->get_error_code(), "$bad: apply returns pixelgrid_style_missing" );
	pg_assert_same( $styles_before, pg_test_styles(), "$bad: user styles unchanged" );
	pg_assert( null === pg_test_custom( 'header', 'wp_template_part' ) && null === pg_test_custom( 'home', 'wp_template' ), "$bad: no part or template written" );
	pg_assert( false === get_option( 'pixelgrid_starter_design', false ), "$bad: no design option stored" );
}
// With a design already applied, a failing apply leaves everything exactly as it was.
pixelgrid_design_apply( $variant );
$state_before = get_option( 'pixelgrid_starter_design' );
$header       = pg_test_custom( 'header', 'wp_template_part' );
$styles_now   = pg_test_styles();
pg_assert( is_wp_error( pixelgrid_design_apply( pixelgrid_variant( 'testmissing' ) ) ), 'missing style over an applied design returns an error' );
pg_assert_same( $state_before, get_option( 'pixelgrid_starter_design' ), 'missing style leaves the design option unchanged' );
pg_assert_same( $header->content, pg_test_custom( 'header', 'wp_template_part' )->content, 'missing style leaves the header unchanged' );
pg_assert_same( $styles_now, pg_test_styles(), 'missing style leaves the applied styles unchanged' );
pixelgrid_design_undo();

// Starter create passes the error through and creates nothing.
$made = pixelgrid_starter_create(
	'testmissingfull',
	array(
		'design'  => true,
		'pages'   => true,
		'posts'   => true,
		'menu'    => true,
		'reading' => true,
	)
);
pg_assert( is_wp_error( $made ) && 'pixelgrid_style_missing' === $made->get_error_code(), 'starter create passes pixelgrid_style_missing through' );
$created = pixelgrid_starter_created();
pg_assert( ! $created['page'] && ! $created['post'] && ! $created['wp_navigation'] && ! $created['attachment'], 'starter create with a missing style creates no content' );
pg_assert_same( '', pixelgrid_starter_active_variant(), 'starter create with a missing style records no variant' );
pg_assert( false === get_option( 'pixelgrid_starter_reading', false ), 'starter create with a missing style leaves reading settings alone' );

// Undo reports whether the styles post has revisions to recover from.
$styles_id = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true )['ID'];
foreach ( wp_get_post_revisions( $styles_id, array( 'fields' => 'ids' ) ) as $revision ) {
	wp_delete_post_revision( $revision );
}
pixelgrid_design_apply( pixelgrid_variant( 'it' ) );
pg_assert( false === ( pixelgrid_design_undo()['revisions'] ?? null ), 'undo reports no revisions when the styles post has none' );
pixelgrid_design_apply( $variant );
pg_assert( true === ( pixelgrid_design_undo()['revisions'] ?? null ), 'undo reports revisions once the styles post has some' );
pg_test_set_styles( $original );

// A user without unfiltered_html: kses rewrites what setup sends, and undo must still match it.
register_block_pattern(
	'pixelgrid-test/kses',
	array(
		'title'   => 'Kses test',
		'content' => '<!-- wp:html --><div onclick="alert(1)" class="pg-kses">Z</div><!-- /wp:html -->',
	)
);
add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['testkses'] = array(
			'slug'  => 'testkses',
			'label' => 'Test kses',
			'style' => 'tests/fixtures/test-style.json',
			'parts' => array( 'header' => 'pixelgrid-test/kses' ),
		);
		return $variants;
	}
);
add_filter(
	'map_meta_cap',
	function ( $caps, $cap ) {
		return 'unfiltered_html' === $cap ? array( 'do_not_allow' ) : $caps;
	},
	10,
	2
);
kses_init();
pg_assert( ! current_user_can( 'unfiltered_html' ), 'test user lacks unfiltered_html' );
pixelgrid_design_apply( pixelgrid_variant( 'testkses' ) );
$header = pg_test_custom( 'header', 'wp_template_part' );
pg_assert( null !== $header && false === strpos( get_post( $header->wp_id )->post_content, 'onclick' ), 'kses stripped the onclick from the stored header' );
$result = pixelgrid_design_undo();
pg_assert_same(
	array(
		'styles_restored' => true,
		'kept'            => array(),
		'styles_written'  => true,
		'revisions'       => true,
	),
	$result,
	'undo without unfiltered_html restores styles and keeps nothing'
);
pg_assert( null === pg_test_custom( 'header', 'wp_template_part' ), 'undo without unfiltered_html deletes the created header' );
pg_assert_same( $original, pg_test_styles(), 'styles equal the original after undo without unfiltered_html' );

// Without unfiltered_html, applying again with nothing edited still counts as unchanged.
$kses_variant = pixelgrid_variant( 'testkses' );
pixelgrid_design_apply( $kses_variant );
$again = pixelgrid_design_apply( $kses_variant );
pg_assert_same( array(), $again['skipped'] ?? null, 'kses: second apply with nothing edited skips nothing' );
$result = pixelgrid_design_undo();
pg_assert( true === $result['styles_restored'] && array() === $result['kept'], 'kses: undo after an unedited second apply restores everything' );
pg_assert( null === pg_test_custom( 'header', 'wp_template_part' ), 'kses: undo after an unedited second apply deletes the header' );
pg_assert_same( $original, pg_test_styles(), 'kses: styles equal the original after an unedited second apply and undo' );

// Without unfiltered_html, applying again keeps edits made after setup.
pixelgrid_design_apply( $kses_variant );
$header = pg_test_custom( 'header', 'wp_template_part' );
pg_test_set_content( $header->wp_id, 'Y' );
$edited = pg_test_styles();
$edited['styles']['color']['background'] = '#abcdef'; // kses drops palette settings for this user, so edit a style it keeps.
pg_test_set_styles( $edited );
$edited_stored = pg_test_styles();
pg_assert_same( '#abcdef', $edited_stored['styles']['color']['background'] ?? null, 'kses: the style edit is stored' );
$again = pixelgrid_design_apply( $kses_variant );
pg_assert_same( array( 'styles', 'wp_template_part:header' ), $again['skipped'] ?? null, 'kses: second apply reports the edited styles and header as skipped' );
pg_assert_same( 'Y', get_post( $header->wp_id )->post_content, 'kses: second apply keeps the header edit' );
pg_assert_same( $edited_stored, pg_test_styles(), 'kses: second apply keeps the style edit' );
$result = pixelgrid_design_undo();
pg_assert_same( array( 'wp_template_part:header' ), $result['kept'], 'kses: undo keeps the header edited before the second apply' );
pg_assert( false === $result['styles_restored'] && true === $result['styles_written'], 'kses: undo leaves the edited styles' );
pg_assert_same( 'Y', get_post( $header->wp_id )->post_content, 'kses: header is still Y after undo' );
pg_assert_same( $edited_stored, pg_test_styles(), 'kses: style edit is still there after undo' );
wp_delete_post( $header->wp_id, true );

// With no current user (WP-CLI without --user, cron) core creates the styles post without its
// wp_theme term, because tax_input needs assign_terms. Apply must still leave a post the
// resolver finds, so the Agency colours reach the front end.
foreach ( get_posts(
	array(
		'post_type'   => 'wp_global_styles',
		'post_status' => 'any',
		'numberposts' => -1,
		'fields'      => 'ids',
	)
) as $styles_id ) {
	wp_delete_post( $styles_id, true );
}
delete_option( 'pixelgrid_starter_design' );
WP_Theme_JSON_Resolver::clean_cached_data();
$pg_user = get_current_user_id();
wp_set_current_user( 0 );
// WP-CLI without --user runs with kses off; switching user here turned it on, and kses keeps
// no settings for a user without unfiltered_html (see the kses tests above). Match WP-CLI.
kses_remove_filters();
pg_assert( ! current_user_can( 'assign_terms' ), 'no user: nobody is logged in' );
$agency_variant = pixelgrid_variant( 'agency' );
$agency_variant['parts']     = array();
$agency_variant['templates'] = array();
pixelgrid_design_apply( $agency_variant );
$styles_posts = get_posts(
	array(
		'post_type'   => 'wp_global_styles',
		'post_status' => 'any',
		'numberposts' => -1,
	)
);
pg_assert_same( 1, count( $styles_posts ), 'no user: apply makes one styles post' );
pg_assert( isset( $styles_posts[0] ) && has_term( $theme, 'wp_theme', $styles_posts[0] ), 'no user: the styles post has the wp_theme term' );
WP_Theme_JSON_Resolver::clean_cached_data();
$agency_blue_hover = '';
foreach ( pixelgrid_design_style_data( $agency_variant )['settings']['color']['palette'] as $entry ) {
	if ( 'blue-hover' === $entry['slug'] ) {
		$agency_blue_hover = $entry['color'];
	}
}
// A missing path returns all settings, so read the palette origins and pick custom here.
$user_palette = wp_get_global_settings( array( 'color', 'palette' ) );
$user_blue    = '';
foreach ( (array) ( $user_palette['custom'] ?? array() ) as $entry ) {
	if ( 'blue-hover' === ( $entry['slug'] ?? '' ) ) {
		$user_blue = $entry['color'];
	}
}
pg_assert( '' !== $agency_blue_hover && $agency_blue_hover === $user_blue, 'no user: the resolver returns the Agency blue-hover colour' );
pg_assert( false !== stripos( wp_get_global_stylesheet( array( 'variables' ) ), '--wp--preset--color--blue-hover: ' . $agency_blue_hover ), 'no user: the global stylesheet carries the Agency blue-hover colour' );
$result = pixelgrid_design_undo();
pg_assert( true === $result['styles_restored'], 'no user: undo restores the styles' );
WP_Theme_JSON_Resolver::clean_cached_data();
$user_palette = wp_get_global_settings( array( 'color', 'palette' ) );
pg_assert( empty( $user_palette['custom'] ), 'no user: undo leaves no custom palette' );
wp_set_current_user( $pg_user ); // Runs kses_init() again for this user.

pg_done();
