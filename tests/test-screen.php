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

$names = wp_list_pluck( wp_get_global_settings( array( 'color', 'palette', 'theme' ) ), 'name' );
pg_assert( in_array( 'Bold section: hover', $names, true ), 'palette uses neutral names' );
pg_assert( ! in_array( 'Blue section: hover', $names, true ), 'old palette name gone' );

$styles = WP_Block_Styles_Registry::get_instance()->get_registered_styles_for_block( 'core/group' );
pg_assert_same( 'Bold section', isset( $styles['tone-blue'] ) ? $styles['tone-blue']['label'] : null, 'tone-blue label is Bold section' );
pg_done();
