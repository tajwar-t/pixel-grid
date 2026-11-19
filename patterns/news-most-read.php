<?php
/**
 * Title: News most read
 * Slug: pixelgrid/news-most-read
 * Categories: pixelgrid-news, posts
 * Keywords: news, most read, popular, numbered, list
 * Viewport Width: 1400
 * Description: The Most read label and a numbered list of five posts. The theme does not count views: setup points the list at a Most read category, so the list shows the five latest posts you put in it.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:heading {"className":"pixelgrid-news-label pixelgrid-news-rule"} -->
<h2 class="wp-block-heading pixelgrid-news-label pixelgrid-news-rule"><?php echo esc_html_x( 'Most read', 'News section label', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":49,"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"pixelgrid-news-most-read pixelgrid-cat-most-read"} -->
<div class="wp-block-query pixelgrid-news-most-read pixelgrid-cat-most-read"><!-- wp:post-template -->
<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-date /-->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:pattern {"slug":"pixelgrid/no-results"} /-->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
