<?php
/**
 * Title: Page not found
 * Slug: pixelgrid/hidden-404
 * Inserter: no
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-night pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-night pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Page not found', 'Heading for a page that cannot be found', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'This address does not lead anywhere. The page may have moved, or the link may be mistyped.', 'Message on the page not found screen', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:pattern {"slug":"pixelgrid/search-form"} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html_x( 'Go to the home page', 'Button on the page not found screen', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-lost"} -->
<figure class="wp-block-image size-full is-style-mosaic-lost"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/plate-4.webp' ) ); ?>" alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
