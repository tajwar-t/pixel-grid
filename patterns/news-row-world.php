<?php
/**
 * Title: News World row
 * Slug: pixelgrid/news-row-world
 * Categories: pixelgrid-news
 * Inserter: no
 * Description: The World label linking to its category, then its four latest posts. Setup points the row at the World category.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_news_row' ) ) {
	require get_parent_theme_file_path( 'inc/news-row.php' );
}

pixelgrid_news_row( 'world', _x( 'World', 'News category name', 'pixelgrid' ) );
