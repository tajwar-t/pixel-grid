<?php
/**
 * Title: Helpdesk section
 * Slug: pixelgrid/section-helpdesk
 * Categories: pixelgrid-sections, featured
 * Keywords: feature, service, mosaic
 * Viewport Width: 1400
 * Description: A full-height blue section with text beside a photo drawn through a pixel chat.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-blue pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-blue pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split">
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-chat"} -->
<figure class="wp-block-image size-full is-style-mosaic-chat"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/plate-2.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel speech bubble', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Hand us the helpdesk', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Your team messages us when something breaks. A person who already knows your setup answers and fixes it remotely.', 'Section introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'A human reply in under two minutes, any hour', 'Section list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'New starters set up in one day', 'Section list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'A monthly report in plain English', 'Section list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php echo esc_html_x( 'Talk to the helpdesk', 'Section button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
