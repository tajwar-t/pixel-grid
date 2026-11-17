<?php
/**
 * Title: Contact details
 * Slug: pixelgrid/section-contact
 * Categories: pixelgrid-sections, contact
 * Keywords: contact, email, phone, address
 * Viewport Width: 1400
 * Description: Page heading, contact details and a button, beside a photo drawn through a pixel envelope.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-sun pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-sun pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Tell us what is broken', 'Contact page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Email or call and an engineer replies within one working hour. A person answers the phone, day and night.', 'Contact introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'hello@example.com', 'Placeholder email address', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( '+1 800 555 0142', 'Placeholder phone number', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( '71 Example Road, Your City', 'Placeholder address', 'pixelgrid' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:hello@example.com"><?php echo esc_html_x( 'Send us an email', 'Contact button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-mail"} -->
<figure class="wp-block-image size-full is-style-mosaic-mail"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/plate-8.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel envelope', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
