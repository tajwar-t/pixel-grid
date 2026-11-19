<?php
/**
 * Title: Agency footer
 * Slug: pixelgrid/agency-footer
 * Categories: footer, pixelgrid-agency
 * Block Types: core/template-part/footer
 * Description: A big closing line with a button, then contact details, the menu and the copyright line.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"align":"full","className":"pixelgrid-footer","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-footer"><!-- wp:heading {"fontSize":"huge"} -->
<h2 class="wp-block-heading has-huge-font-size"><?php echo esc_html_x( 'Have a project in mind?', 'Footer closing line', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'contact', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'Start a project', 'Footer button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:site-title {"level":0} /-->

<!-- wp:site-tagline /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Say hello', 'Footer heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'hello@example.com', 'Footer placeholder email address', 'pixelgrid' ); ?><br><?php echo esc_html_x( '+1 800 555 0142', 'Footer placeholder phone number', 'pixelgrid' ); ?><br><?php echo esc_html_x( '12 Example Yard, Your City', 'Footer placeholder address', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Menu', 'Footer heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"className":"pixelgrid-footer-bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group pixelgrid-footer-bottom"><!-- wp:paragraph -->
<p>
<?php
printf(
	/* translators: %s: current year. */
	esc_html__( '© %s, all rights reserved.', 'pixelgrid' ),
	esc_html( gmdate( 'Y' ) )
);
?>
</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>
<?php
printf(
	/* translators: %s: WordPress link. */
	esc_html__( 'Built with %s', 'pixelgrid' ),
	'<a href="' . esc_url( __( 'https://wordpress.org', 'pixelgrid' ) ) . '" rel="nofollow">WordPress</a>'
);
?>
</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
