<?php
/**
 * Title: Agency posts page template
 * Slug: pixelgrid/agency-template-home
 * Categories: pixelgrid-agency
 * Inserter: no
 * Description: Full posts page template: header, the Journal heading, a grid of post cards with pagination, and footer.
 *
 * @package pixelgrid
 */

?>
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","className":"pixelgrid-main pixelgrid-stack","layout":{"type":"constrained"}} -->
<main class="wp-block-group pixelgrid-main pixelgrid-stack"><!-- wp:pattern {"slug":"pixelgrid/agency-blog-heading"} /-->

<!-- wp:query {"queryId":1,"query":{"perPage":9,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"className":"pixelgrid-cards"} -->
<div class="wp-block-query pixelgrid-cards"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->

<!-- wp:post-terms {"term":"category"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":20} /-->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"none","layout":{"type":"flex","justifyContent":"left"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:pattern {"slug":"pixelgrid/no-results"} /-->
<!-- /wp:query-no-results --></div>
<!-- /wp:query -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
