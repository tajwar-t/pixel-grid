<?php
/**
 * Title: News Technology row
 * Slug: pixelgrid/news-row-technology
 * Categories: pixelgrid-news
 * Inserter: no
 * Description: The Technology label linking to its category, then its four latest posts. Setup points the row at the Technology category.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_news_row' ) ) {
	require get_parent_theme_file_path( 'inc/news-row.php' );
}

pixelgrid_news_row( 'technology', _x( 'Technology', 'News category name', 'pixelgrid' ) );
