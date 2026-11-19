<?php
/**
 * Title: News Sport row
 * Slug: pixelgrid/news-row-sport
 * Categories: pixelgrid-news
 * Inserter: no
 * Description: The Sport label linking to its category, then its four latest posts. Setup points the row at the Sport category.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_news_row' ) ) {
	require get_parent_theme_file_path( 'inc/news-row.php' );
}

pixelgrid_news_row( 'sport', _x( 'Sport', 'News category name', 'pixelgrid' ) );
