<?php
/**
 * Title: Agency latest journal posts
 * Slug: pixelgrid/agency-journal-rows
 * Categories: pixelgrid-agency, posts
 * Keywords: blog, journal, posts, latest
 * Viewport Width: 1400
 * Description: A heading and the three latest posts as full-width rows.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'From the journal', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":21,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"pixelgrid-rows"} -->
<div class="wp-block-query pixelgrid-rows"><!-- wp:post-template -->
<!-- wp:group {"className":"pixelgrid-row","layout":{"type":"default"}} -->
<div class="wp-block-group pixelgrid-row"><!-- wp:post-terms {"term":"category"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-date /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
