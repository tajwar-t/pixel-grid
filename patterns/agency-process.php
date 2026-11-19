<?php
/**
 * Title: Agency process
 * Slug: pixelgrid/agency-process
 * Categories: pixelgrid-agency, text
 * Keywords: process, steps, how we work
 * Viewport Width: 1400
 * Description: The studio's four-step process, numbered, on a dark section.
 *
 * @package pixelgrid
 */

$pixelgrid_steps = array(
	array( __( 'Listen', 'pixelgrid' ), __( 'A half-day workshop with your team. We leave with the problem written in one sentence.', 'pixelgrid' ) ),
	array( __( 'Look around', 'pixelgrid' ), __( 'We watch how people meet your brand today: in shops, on phones, at the front desk.', 'pixelgrid' ) ),
	array( __( 'Make and test', 'pixelgrid' ), __( 'Rough versions first, shown to real people early. You see work every week.', 'pixelgrid' ) ),
	array( __( 'Hand over', 'pixelgrid' ), __( 'Files, guidelines and training, so your team can carry on without us.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-night pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-night pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'How a project runs', 'Section heading', 'pixelgrid' ); ?></h2>
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
