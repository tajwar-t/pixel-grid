<?php
/**
 * Shared block markup for the News category rows.
 *
 * The patterns news-category-row and news-row-world to news-row-sport each pass a category
 * slug and label to pixelgrid_news_row(), so the row is written once.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pixelgrid_news_row' ) ) :
	/**
	 * Prints a row of the four latest posts under a condensed section label.
	 *
	 * With a category slug, the label links to the category archive and the query carries the
	 * pixelgrid-cat-{slug} marker class, which setup turns into a filter on the real category.
	 * The pattern itself holds no term ID, so in the inserter the row shows the latest posts.
	 * With an empty slug, the row is the generic version: no link and no marker.
	 *
	 * @param string $slug  Category slug, for example world, or an empty string.
	 * @param string $label Section label, already translated. Escaped here.
	 */
	function pixelgrid_news_row( string $slug, string $label ): void {
		$query_ids = array(
			'world'      => 44,
			'business'   => 45,
			'technology' => 46,
			'culture'    => 47,
			'sport'      => 48,
		);
		$query_id  = isset( $query_ids[ $slug ] ) ? $query_ids[ $slug ] : 43;
		$classes   = 'pixelgrid-cards pixelgrid-news-row' . ( '' !== $slug ? ' pixelgrid-cat-' . sanitize_html_class( $slug ) : '' );
		$query     = array(
			'queryId'   => $query_id,
			'query'     => array(
				'perPage'  => 4,
				'pages'    => 0,
				'offset'   => 0,
				'postType' => 'post',
				'order'    => 'desc',
				'orderBy'  => 'date',
				'inherit'  => false,
			),
			'className' => $classes,
		);
		$heading   = esc_html( $label );
		if ( '' !== $slug ) {
			$heading = '<a href="' . esc_url( add_query_arg( 'category_name', $slug, home_url( '/' ) ) ) . '">' . $heading . '</a>';
		}
		?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:heading {"className":"pixelgrid-news-label pixelgrid-news-rule"} -->
<h2 class="wp-block-heading pixelgrid-news-label pixelgrid-news-rule"><?php echo wp_kses_post( $heading ); ?></h2>
<!-- /wp:heading -->

<!-- wp:query <?php echo wp_json_encode( $query ); ?> -->
<div class="wp-block-query <?php echo esc_attr( $classes ); ?>"><!-- wp:post-template {"layout":{"type":"grid","columnCount":4}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-date /-->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:pattern {"slug":"pixelgrid/no-results"} /-->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
		<?php
	}
endif;
