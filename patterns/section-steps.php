<?php
/**
 * Title: Four steps
 * Slug: pixelgrid/section-steps
 * Categories: pixelgrid-sections, text
 * Keywords: process, steps, how it works
 * Viewport Width: 1400
 * Description: A numbered four-step process on a dark section.
 *
 * @package pixelgrid
 */

$pixelgrid_steps = array(
	array( __( 'Health check', 'pixelgrid' ), __( 'One free hour with an engineer. You get a short list of what to fix first.', 'pixelgrid' ) ),
	array( __( 'Plan and price', 'pixelgrid' ), __( 'A one-page plan and a fixed monthly price. No surprises later.', 'pixelgrid' ) ),
	array( __( 'Handover', 'pixelgrid' ), __( 'We record your setup, meet your team and take over the passwords safely.', 'pixelgrid' ) ),
	array( __( 'Day to day', 'pixelgrid' ), __( 'Your team messages us. We fix, report monthly and review the plan each quarter.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-night pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-night pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'How it starts', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-steps"} -->
<div class="wp-block-columns pixelgrid-steps">
<?php foreach ( $pixelgrid_steps as $pixelgrid_step ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $pixelgrid_step[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_step[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
