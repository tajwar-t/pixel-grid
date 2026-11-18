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

$names = wp_list_pluck( wp_get_global_settings( array( 'color', 'palette', 'theme' ) ), 'name' );
pg_assert( in_array( 'Bold section: hover', $names, true ), 'palette uses neutral names' );
pg_assert( ! in_array( 'Blue section: hover', $names, true ), 'old palette name gone' );

$styles = WP_Block_Styles_Registry::get_instance()->get_registered_styles_for_block( 'core/group' );
pg_assert_same( 'Bold section', isset( $styles['tone-blue'] ) ? $styles['tone-blue']['label'] : null, 'tone-blue label is Bold section' );
pg_done();
