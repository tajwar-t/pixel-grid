<?php
/**
 * Setup screen tests.
 */
require_once get_parent_theme_file_path( 'inc/starter-screen.php' );

ob_start();
pixelgrid_starter_screen();
$html = ob_get_clean();
pg_assert( false !== strpos( $html, 'name="pixelgrid_variant" value="it"' ), 'screen has the it radio' );
pg_assert( false !== strpos( $html, 'name="pixelgrid_design"' ), 'screen has the design checkbox' );
preg_match( '/<input[^>]*name="pixelgrid_design"[^>]*>/', $html, $m );
pg_assert( ! empty( $m ) && false !== strpos( $m[0], 'checked' ), 'design checkbox is ticked before any design is applied' );
pg_assert( false === strpos( $html, 'Starting point in use' ), 'no variant in use on a fresh site' );

add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['agency'] = array(
			'slug'        => 'agency',
			'label'       => 'Agency',
			'description' => 'Fake agency',
		);
		return $variants;
	}
);
pg_assert( is_array( pixelgrid_starter_create( 'it', array( 'pages' => true ) ) ), 'it created' );
ob_start();
pixelgrid_starter_screen();
$html = ob_get_clean();
preg_match( '/<input[^>]*value="agency"[^>]*>/', $html, $m );
pg_assert( ! empty( $m ) && false !== strpos( $m[0], 'disabled' ), 'agency radio is disabled while it is active' );
pg_assert( false !== strpos( $html, 'Remove the current starter content to switch' ), 'switch hint shown' );
pg_assert( false !== strpos( $html, 'Starting point in use: IT services' ), 'starter content section names the variant in use' );
pg_assert( false === strpos( $html, 'Its design is applied' ), 'design not reported as applied when only pages were added' );

pg_assert( is_array( pixelgrid_starter_create( 'it', array( 'design' => true ) ) ), 'it design applied' );
ob_start();
pixelgrid_starter_screen();
$html = ob_get_clean();
pg_assert( false !== strpos( $html, 'Starting point in use: IT services. Its design is applied.' ), 'starter content section notes the applied design' );
preg_match( '/<input[^>]*name="pixelgrid_design"[^>]*>/', $html, $m );
pg_assert( ! empty( $m ) && false === strpos( $m[0], 'checked' ) && false === strpos( $m[0], 'disabled' ), 'design checkbox is unticked but available once the design is applied' );
pg_assert( false !== strpos( $html, 'design again (already applied)' ), 'design checkbox says the design is already applied' );
pixelgrid_starter_remove();

// Labels for parts and templates left in place on removal.
$core_home = get_default_block_template_types()['home']['title'];
pg_assert_same( 'Header', pixelgrid_starter_kept_label( 'wp_template_part:header' ), 'kept label for the header part' );
pg_assert_same( 'Footer', pixelgrid_starter_kept_label( 'wp_template_part:footer' ), 'kept label for the footer part' );
pg_assert_same( $core_home, pixelgrid_starter_kept_label( 'wp_template:home' ), 'kept label for the home template is the core title' );

// The removal redirect carries kept entries as type__slug so they survive sanitize_key.
$args = pixelgrid_starter_removed_args(
	array(
		'removed'         => 3,
		'kept'            => array( 'wp_template_part:header', 'wp_template:home' ),
		'styles_written'  => true,
		'styles_restored' => false,
		'revisions'       => true,
	)
);
pg_assert_same( 'wp_template_part__header,wp_template__home', $args['pixelgrid_kept'] ?? null, 'removal redirect encodes kept entries as type__slug' );
pg_assert_same( 'kept', $args['pixelgrid_styles'] ?? null, 'removal redirect reports kept styles' );
pg_assert_same( '1', $args['pixelgrid_revisions'] ?? null, 'removal redirect reports revisions' );
$args = pixelgrid_starter_removed_args(
	array(
		'removed'         => 0,
		'kept'            => array(),
		'styles_written'  => true,
		'styles_restored' => false,
		'revisions'       => false,
	)
);
pg_assert( ! isset( $args['pixelgrid_revisions'] ) && ! isset( $args['pixelgrid_kept'] ), 'removal redirect has no revisions or kept flag when there are none' );

/**
 * Renders the setup screen with the given query args.
 *
 * @param array $get Query args.
 * @return string
 */
function pg_test_screen_with( $get ) {
	$saved = $_GET;
	$_GET  = $get;
	ob_start();
	pixelgrid_starter_screen();
	$out  = ob_get_clean();
	$_GET = $saved;
	return $out;
}

$html = pg_test_screen_with(
	array(
		'pixelgrid_removed' => '3',
		'pixelgrid_kept'    => 'wp_template_part__header,wp_template__home',
	)
);
pg_assert( false !== strpos( $html, 'changed after setup: Header, ' . esc_html( $core_home ) . '.' ), 'removal notice names Header and the core home template title' );
$html = pg_test_screen_with(
	array(
		'pixelgrid_removed' => '3',
		'pixelgrid_styles'  => 'kept',
	)
);
pg_assert( false !== strpos( $html, 'Your style changes were kept' ) && false === strpos( $html, 'Revisions' ), 'kept styles notice without the Revisions hint when there are no revisions' );
$html = pg_test_screen_with(
	array(
		'pixelgrid_removed'   => '3',
		'pixelgrid_styles'    => 'kept',
		'pixelgrid_revisions' => '1',
	)
);
pg_assert( false !== strpos( $html, 'Styles, Revisions' ), 'kept styles notice shows the Revisions hint when there are revisions' );
$html = pg_test_screen_with( array( 'pixelgrid_error' => 'pixelgrid_style_missing' ) );
pg_assert( false !== strpos( $html, 'notice-error' ), 'a missing style file shows an error notice' );

$names = wp_list_pluck( wp_get_global_settings( array( 'color', 'palette', 'theme' ) ), 'name' );
pg_assert( in_array( 'Bold section: hover', $names, true ), 'palette uses neutral names' );
pg_assert( ! in_array( 'Blue section: hover', $names, true ), 'old palette name gone' );

$styles = WP_Block_Styles_Registry::get_instance()->get_registered_styles_for_block( 'core/group' );
pg_assert_same( 'Bold section', isset( $styles['tone-blue'] ) ? $styles['tone-blue']['label'] : null, 'tone-blue label is Bold section' );
pg_done();
