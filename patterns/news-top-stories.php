<?php
/**
 * Title: News top stories
 * Slug: pixelgrid/news-top-stories
 * Categories: pixelgrid-news, posts
 * Keywords: news, top stories, grid, latest
 * Viewport Width: 1400
 * Description: The next three posts after the lead story, in a three-column grid with image, section, headline, standfirst and byline.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:paragraph {"className":"pixelgrid-news-label"} -->
<p class="pixelgrid-news-label"><?php echo esc_html_x( 'Top stories', 'News section label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:query {"queryId":42,"query":{"perPage":3,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"pixelgrid-cards"} -->
<div class="wp-block-query pixelgrid-cards"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->

<!-- wp:post-terms {"term":"category","className":"pixelgrid-news-label"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":24} /-->

<!-- wp:group {"className":"pixelgrid-meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group pixelgrid-meta"><!-- wp:post-author-name /-->

<!-- wp:post-date /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
