<?php
/**
 * Title: News posts page template
 * Slug: pixelgrid/news-template-home
 * Categories: pixelgrid-news
 * Inserter: no
 * Description: Full posts page template for News: header, the Latest label, a list of the newest articles with pagination, and footer.
 *
 * @package pixelgrid
 */

?>
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","className":"pixelgrid-main pixelgrid-stack","layout":{"type":"constrained"}} -->
<main class="wp-block-group pixelgrid-main pixelgrid-stack"><!-- wp:heading {"level":1,"className":"pixelgrid-news-label"} -->
<h1 class="wp-block-heading pixelgrid-news-label"><?php echo esc_html_x( 'Latest', 'News posts page label', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:query {"queryId":51,"query":{"perPage":12,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"className":"pixelgrid-rows"} -->
<div class="wp-block-query pixelgrid-rows"><!-- wp:post-template -->
<!-- wp:group {"className":"pixelgrid-row","layout":{"type":"default"}} -->
<div class="wp-block-group pixelgrid-row"><!-- wp:post-terms {"term":"category","className":"pixelgrid-news-label"} /-->

<!-- wp:post-title {"level":2,"isLink":true} /-->

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
