<?php
/**
 * Title: Agency call to action
 * Slug: pixelgrid/agency-cta
 * Categories: pixelgrid-agency, call-to-action
 * Keywords: cta, contact, project
 * Viewport Width: 1400
 * Description: A bright section with a very large headline, a short offer and one button.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-sun pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-sun pixelgrid-stack"><!-- wp:heading {"fontSize":"huge"} -->
<h2 class="wp-block-heading has-huge-font-size"><?php echo esc_html_x( 'Tell us what you are making', 'Call to action heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Send a few lines about the project and your timing. We reply within two working days with honest thoughts and a rough price, even if we are not the right studio for it.', 'Call to action text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'contact', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'Start a project', 'Call to action button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
