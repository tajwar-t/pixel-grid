<?php
/**
 * Title: Call to action
 * Slug: pixelgrid/section-cta
 * Categories: pixelgrid-sections, call-to-action
 * Keywords: cta, offer, contact
 * Viewport Width: 1400
 * Description: A red section with a very large headline, a short offer and one button.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-red pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-red pixelgrid-stack"><!-- wp:heading {"fontSize":"huge"} -->
<h2 class="wp-block-heading has-huge-font-size"><?php echo esc_html_x( 'Start with a free health check', 'Call to action heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'One engineer spends an hour with your setup and sends back a short list: what is fine, what is risky, and what to fix first. Keep the list whether you hire us or not.', 'Call to action text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php echo esc_html_x( 'Book a free health check', 'Call to action button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
