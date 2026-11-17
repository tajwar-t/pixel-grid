<?php
/**
 * Title: Footer
 * Slug: pixelgrid/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Dark footer with site details, menu, contact and latest notes.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"align":"full","className":"pixelgrid-footer","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-footer"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:site-title {"level":0} /-->

<!-- wp:site-tagline /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Menu', 'Footer heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Talk to us', 'Footer heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'hello@example.com', 'Footer placeholder email address', 'pixelgrid' ); ?><br><?php echo esc_html_x( '+1 800 555 0142', 'Footer placeholder phone number', 'pixelgrid' ); ?><br><?php echo esc_html_x( 'Helpdesk open 24 hours, every day', 'Footer opening hours', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Latest notes', 'Footer heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:latest-posts {"postsToShow":3} /--></div>
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
