<?php
/**
 * Title: News section front template
 * Slug: pixelgrid/news-template-category
 * Categories: pixelgrid-news
 * Inserter: no
 * Description: Full category template for News: the section name, its newest article shown large as the lead, the next ones in a grid, then pagination.
 *
 * @package pixelgrid
 */

?>
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","className":"pixelgrid-main pixelgrid-stack","layout":{"type":"constrained"}} -->
<main class="wp-block-group pixelgrid-main pixelgrid-stack"><!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:query-title {"type":"archive","showPrefix":false,"className":"pixelgrid-news-label pixelgrid-news-rule"} /-->

<!-- wp:term-description {"className":"is-style-lead"} /--></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":53,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"className":"pixelgrid-cards pixelgrid-news-front"} -->
<div class="wp-block-query pixelgrid-cards pixelgrid-news-front"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->

<!-- wp:post-title {"level":2,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":24} /-->

<!-- wp:group {"className":"pixelgrid-meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group pixelgrid-meta"><!-- wp:post-author-name /-->

<!-- wp:post-date /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"none","layout":{"type":"flex","justifyContent":"left"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:pattern {"slug":"pixelgrid/no-results"} /-->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
