<?php
/**
 * Title: News section row
 * Slug: pixelgrid/news-category-row
 * Categories: pixelgrid-news, posts
 * Keywords: news, category, section, row, latest
 * Viewport Width: 1400
 * Description: A section label and the four latest posts in a row. Pick a category in the query settings to make it a section row.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_news_row' ) ) {
	require get_parent_theme_file_path( 'inc/news-row.php' );
}

pixelgrid_news_row( '', _x( 'Latest', 'News section label', 'pixelgrid' ) );
