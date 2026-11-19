<?php
/**
 * Title: News masthead
 * Slug: pixelgrid/news-header
 * Categories: header, pixelgrid-news
 * Block Types: core/template-part/header
 * Description: Masthead with today's date and the edition label, a large centred site title, then the section menu and a search box.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"align":"full","className":"pixelgrid-header pixelgrid-news-masthead","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-header pixelgrid-news-masthead"><!-- wp:group {"className":"pixelgrid-news-dateline","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group pixelgrid-news-dateline"><!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pixelgrid/today"}}},"className":"pixelgrid-news-label"} -->
<p class="pixelgrid-news-label"></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"pixelgrid-news-label"} -->
<p class="pixelgrid-news-label"><?php echo esc_html_x( 'Edition', 'News masthead label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:site-title {"level":0,"textAlign":"center","className":"pixelgrid-news-title","fontSize":"huge"} /-->

<!-- wp:group {"className":"pixelgrid-news-bar","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group pixelgrid-news-bar"><!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"left"}} /-->

<!-- wp:search {"label":"<?php echo esc_attr_x( 'Search the paper', 'search form label', 'pixelgrid' ); ?>","showLabel":false,"placeholder":"<?php echo esc_attr_x( 'Search the paper', 'search form placeholder', 'pixelgrid' ); ?>","buttonText":"<?php echo esc_attr_x( 'Search', 'search button text', 'pixelgrid' ); ?>"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
