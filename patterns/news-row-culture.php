<?php
/**
 * Title: News Culture row
 * Slug: pixelgrid/news-row-culture
 * Categories: pixelgrid-news
 * Inserter: no
 * Description: The Culture label linking to its category, then its four latest posts. Setup points the row at the Culture category.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_news_row' ) ) {
	require get_parent_theme_file_path( 'inc/news-row.php' );
}

pixelgrid_news_row( 'culture', _x( 'Culture', 'News category name', 'pixelgrid' ) );
