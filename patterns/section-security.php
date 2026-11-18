<?php
/**
 * Title: Security section
 * Slug: pixelgrid/section-security
 * Categories: pixelgrid-sections, featured
 * Keywords: feature, service, mosaic
 * Viewport Width: 1400
 * Description: A full-height night section with text beside a photo drawn through a pixel lock.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-night pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-night pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split">
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-lock"} -->
<figure class="wp-block-image size-full is-style-mosaic-lock"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/plate-4.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel padlock', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Lock the doors', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Most break-ins start with one stolen password. We close that door first, then keep going.', 'Section introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'Sign-in protection on every account', 'Section list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Updates applied within 72 hours', 'Section list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Backups tested by restoring them, every month', 'Section list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'contact', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'Check your security', 'Section button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
