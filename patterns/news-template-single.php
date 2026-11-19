<?php
/**
 * Title: News article template
 * Slug: pixelgrid/news-template-single
 * Categories: pixelgrid-news
 * Inserter: no
 * Description: Full single post template for News: section label, headline, standfirst, byline, picture, article, tags, a row of related articles and comments.
 *
 * @package pixelgrid
 */

?>
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","className":"pixelgrid-main pixelgrid-stack","layout":{"type":"constrained"}} -->
<main class="wp-block-group pixelgrid-main pixelgrid-stack"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:post-terms {"term":"category","className":"pixelgrid-news-label"} /-->

<!-- wp:post-title {"level":1,"fontSize":"xx-large"} /-->

<!-- wp:post-excerpt {"className":"is-style-lead"} /-->

<!-- wp:group {"className":"pixelgrid-meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group pixelgrid-meta"><!-- wp:post-author-name {"isLink":true} /-->

<!-- wp:post-date /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:post-featured-image {"aspectRatio":"22/9","className":"pixelgrid-hero-image"} /-->

<!-- wp:post-content {"className":"pixelgrid-prose","layout":{"type":"default"}} /-->

<!-- wp:post-terms {"term":"post_tag","className":"is-style-chips"} /-->

<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"className":"pixelgrid-news-label pixelgrid-news-rule"} -->
<h2 class="wp-block-heading pixelgrid-news-label pixelgrid-news-rule"><?php echo esc_html_x( 'Related', 'News related articles label', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":52,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"pixelgrid-cards pixelgrid-related"} -->
<div class="wp-block-query pixelgrid-cards pixelgrid-related"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-date /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"pixelgrid/comments"} /--></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
