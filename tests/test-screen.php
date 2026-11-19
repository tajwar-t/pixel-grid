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

// IT's wording is unchanged from 1.3.0.
$it_strings = array(
	'Pages: Home, Services, About, Pricing, Contact and Notes',
	'Six sample notes (posts) with categories, tags and images',
	'Use the new Home page as the homepage and Notes as the posts page',
	esc_html( "This changes the site's header, footer and some templates, and its styles when the starting point has its own (IT services keeps your current styles). Your current ones are saved and put back when you remove the starter content. Applying again only changes what you have not edited since setup, and edits you make after setup are kept on removal." ),
);
foreach ( $it_strings as $text ) {
	pg_assert( false !== strpos( $html, $text ), 'it screen shows: ' . substr( $text, 0, 40 ) );
}
pg_assert( false === strpos( $html, '<script' ), 'no script in the page body' );

// Agency is listed with its own wording.
$pg_agency = pixelgrid_variant( 'agency' );
preg_match( '/<input[^>]*value="agency"[^>]*>\s*<strong>([^<]*)<\/strong>/', $html, $m );
pg_assert_same( 'Agency', $m[1] ?? null, 'screen has the agency radio labelled Agency' );
pg_assert( is_array( $pg_agency ) && ! empty( $pg_agency['content_labels']['pages'] ) && false !== strpos( $html, esc_html( $pg_agency['content_labels']['pages'] ) ), 'screen shows the agency pages label' );

// News is listed with its own wording.
$pg_news = pixelgrid_variant( 'news' );
preg_match( '/<input[^>]*value="news"[^>]*>\s*<strong>([^<]*)<\/strong>/', $html, $m );
pg_assert_same( 'News', $m[1] ?? null, 'screen has the news radio labelled News' );
pg_assert( is_array( $pg_news ) && ! empty( $pg_news['content_labels']['pages'] ) && false !== strpos( $html, esc_html( $pg_news['content_labels']['pages'] ) ), 'screen shows the news pages label' );

// Wording shared by every variant is generic, not IT's blog name.
$html = pg_test_screen_with( array( 'pixelgrid_created' => '12-6-1' ) );
pg_assert( false !== strpos( $html, 'Starter content added: 12 pages, 6 posts and 1 menu.' ), 'created notice counts posts, not notes' );
pixelgrid_starter_create( 'it', array( 'posts' => true ) );
ob_start();
pixelgrid_starter_screen();
$html = ob_get_clean();
pg_assert( false !== strpos( $html, '<h3>Posts</h3>' ) && false === strpos( $html, '<h3>Notes</h3>' ), 'created list heads posts as Posts' );
pg_assert( false !== strpos( $html, 'Moves the starter pages, posts and menu to the trash' ), 'remove paragraph says posts' );
pixelgrid_starter_remove();

// The label switcher is enqueued on this page only.
if ( function_exists( 'pixelgrid_starter_enqueue' ) ) {
	pixelgrid_starter_enqueue( 'index.php' );
}
pg_assert( ! wp_script_is( 'pixelgrid-setup', 'enqueued' ), 'label switcher is not enqueued on other screens' );
if ( function_exists( 'pixelgrid_starter_enqueue' ) ) {
	pixelgrid_starter_enqueue( 'appearance_page_pixelgrid-setup' );
}
pg_assert( false !== has_action( 'admin_enqueue_scripts', 'pixelgrid_starter_enqueue' ), 'label switcher hooks admin_enqueue_scripts' );
pg_assert( wp_script_is( 'pixelgrid-setup', 'enqueued' ), 'label switcher is enqueued on the setup page' );
$inline = wp_scripts()->get_data( 'pixelgrid-setup', 'after' );
pg_assert( ! empty( $inline ) && false !== strpos( implode( ' ', (array) $inline ), 'pixelgrid_variant' ), 'label switcher has its inline script' );

// A variant's own content labels show, alongside IT's.
$pg_fake_filter = function ( $variants ) {
	$variants['fakelabels'] = array(
		'slug'           => 'fakelabels',
		'label'          => 'Fake labels',
		'content_labels' => array( 'pages' => 'Fake pages label for the test' ),
		'design_note'    => 'Fake design note for the test',
	);
	return $variants;
};
add_filter( 'pixelgrid_variants', $pg_fake_filter );
ob_start();
pixelgrid_starter_screen();
$html = ob_get_clean();
pg_assert( false !== strpos( $html, 'Fake pages label for the test' ), 'a variant content_labels pages text appears' );
pg_assert( false !== strpos( $html, 'Fake design note for the test' ), 'a variant design_note appears' );
pg_assert( false !== strpos( $html, 'data-pixelgrid-for="fakelabels"' ) && false !== strpos( $html, 'data-pixelgrid-for="it"' ), 'one label set per variant' );
pg_assert( false !== strpos( $html, 'Pages: Home, Services, About, Pricing, Contact and Notes' ), 'it labels still shown next to another variant' );
remove_filter( 'pixelgrid_variants', $pg_fake_filter );

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
