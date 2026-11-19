<?php
/**
 * Title: Agency contact details
 * Slug: pixelgrid/agency-contact
 * Categories: pixelgrid-agency, contact
 * Keywords: contact, email, phone, address, project
 * Viewport Width: 1400
 * Description: Page heading, studio contact details and a button, beside a picture drawn through a pixel envelope.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-sun pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-sun pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Start a project', 'Contact page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Tell us what you are making, when you need it and roughly what you can spend. We reply within two working days.', 'Contact introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'hello@example.com', 'Placeholder email address', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( '+1 800 555 0142', 'Placeholder phone number', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( '12 Example Yard, Your City', 'Placeholder address', 'pixelgrid' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:hello@example.com"><?php echo esc_html_x( 'Email the studio', 'Contact button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-mail"} -->
<figure class="wp-block-image size-full is-style-mosaic-mail"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/agency-10.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel envelope', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
