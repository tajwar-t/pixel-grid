<?php
/**
 * Title: News lead story
 * Slug: pixelgrid/news-lead
 * Categories: pixelgrid-news, posts
 * Keywords: news, lead, headline, featured, latest
 * Viewport Width: 1400
 * Description: The latest post as the lead story: a large image beside its section, headline, standfirst and byline.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:query {"queryId":41,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"pixelgrid-news-lead"} -->
<div class="wp-block-query pixelgrid-news-lead"><!-- wp:post-template -->
<!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","className":"pixelgrid-hero-image"} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:post-terms {"term":"category","className":"pixelgrid-news-label"} /-->

<!-- wp:post-title {"level":2,"isLink":true,"fontSize":"xx-large"} /-->

<!-- wp:post-excerpt {"excerptLength":40,"className":"is-style-lead"} /-->

<!-- wp:group {"className":"pixelgrid-meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group pixelgrid-meta"><!-- wp:post-author-name /-->

<!-- wp:post-date /--></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:pattern {"slug":"pixelgrid/no-results"} /-->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
