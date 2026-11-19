<?php
/**
 * Title: Agency journal heading
 * Slug: pixelgrid/agency-blog-heading
 * Categories: pixelgrid-agency
 * Inserter: no
 * Description: The Journal heading, a short introduction and category chips for the posts page.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Journal', 'Agency blog page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'How we work, what we are learning and the small decisions behind finished projects.', 'Agency blog page introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:categories {"className":"is-style-chips"} /--></div>
<!-- /wp:group -->
