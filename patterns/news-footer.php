<?php
/**
 * Title: News footer
 * Slug: pixelgrid/news-footer
 * Categories: footer, pixelgrid-news
 * Block Types: core/template-part/footer
 * Description: Five section columns, each linking to its category, then About, Tips and Contact links and the copyright line.
 *
 * @package pixelgrid
 */

$pixelgrid_sections = array(
	array( 'world', _x( 'World', 'News category name', 'pixelgrid' ), _x( 'Reports from beyond the city limits.', 'News footer section text', 'pixelgrid' ) ),
	array( 'business', _x( 'Business', 'News category name', 'pixelgrid' ), _x( 'Local firms, jobs and the cost of living.', 'News footer section text', 'pixelgrid' ) ),
	array( 'technology', _x( 'Technology', 'News category name', 'pixelgrid' ), _x( 'The tools and networks the city runs on.', 'News footer section text', 'pixelgrid' ) ),
	array( 'culture', _x( 'Culture', 'News category name', 'pixelgrid' ), _x( 'Stages, screens, books and neighbourhood life.', 'News footer section text', 'pixelgrid' ) ),
	array( 'sport', _x( 'Sport', 'News category name', 'pixelgrid' ), _x( 'Results and stories from club to league.', 'News footer section text', 'pixelgrid' ) ),
);
$pixelgrid_pages    = array(
	array( 'about', _x( 'About the paper', 'News footer link', 'pixelgrid' ) ),
	array( 'tips', _x( 'Send a tip', 'News footer link', 'pixelgrid' ) ),
	array( 'contact', _x( 'Contact', 'News footer link', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"align":"full","className":"pixelgrid-footer pixelgrid-news-footer","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-footer pixelgrid-news-footer"><!-- wp:site-title {"level":0} /-->

<!-- wp:columns -->
<div class="wp-block-columns">
<?php foreach ( $pixelgrid_sections as $pixelgrid_section ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"className":"pixelgrid-news-label"} -->
<h2 class="wp-block-heading pixelgrid-news-label"><a href="<?php echo esc_url( add_query_arg( 'category_name', $pixelgrid_section[0], home_url( '/' ) ) ); ?>"><?php echo esc_html( $pixelgrid_section[1] ); ?></a></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_section[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->

<!-- wp:group {"className":"pixelgrid-footer-bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group pixelgrid-footer-bottom"><!-- wp:paragraph -->
<p>
<?php
$pixelgrid_links = array();
foreach ( $pixelgrid_pages as $pixelgrid_page ) {
	$pixelgrid_links[] = '<a href="' . esc_url( add_query_arg( 'pagename', $pixelgrid_page[0], home_url( '/' ) ) ) . '">' . esc_html( $pixelgrid_page[1] ) . '</a>';
}
echo wp_kses_post( implode( ' · ', $pixelgrid_links ) );
?>
</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>
<?php
printf(
	/* translators: %s: current year. */
	esc_html__( '© %s, all rights reserved.', 'pixelgrid' ),
	esc_html( gmdate( 'Y' ) )
);
?>
</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
