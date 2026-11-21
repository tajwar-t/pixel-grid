<?php
/**
 * Title: News Business row
 * Slug: pixelgrid/news-row-business
 * Categories: pixelgrid-news
 * Inserter: no
 * Description: The Business label linking to its category, then its three latest posts. Setup points the row at the Business category.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_news_row' ) ) {
	require get_parent_theme_file_path( 'inc/news-row.php' );
}

pixelgrid_news_row( 'business', _x( 'Business', 'News category name', 'pixelgrid' ) );
