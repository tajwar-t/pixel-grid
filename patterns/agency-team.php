<?php
/**
 * Title: Agency team
 * Slug: pixelgrid/agency-team
 * Categories: pixelgrid-agency, team
 * Keywords: people, staff, team, studio
 * Viewport Width: 1400
 * Description: Four people with names, roles and one line each.
 *
 * @package pixelgrid
 */

$pixelgrid_people = array(
	array( __( 'Nadia Kerr', 'pixelgrid' ), __( 'Founder, creative director', 'pixelgrid' ), __( 'Runs the first workshop on every project.', 'pixelgrid' ) ),
	array( __( 'Oskar Lind', 'pixelgrid' ), __( 'Design lead, identity', 'pixelgrid' ), __( 'Draws the logos and writes the guidelines.', 'pixelgrid' ) ),
	array( __( 'Priya Shah', 'pixelgrid' ), __( 'Design lead, digital', 'pixelgrid' ), __( 'Designs and builds websites and apps.', 'pixelgrid' ) ),
	array( __( 'Sam Okafor', 'pixelgrid' ), __( 'Studio manager', 'pixelgrid' ), __( 'Keeps projects on time and answers your emails.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'The people in the room', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-cards pixelgrid-four"} -->
<div class="wp-block-columns pixelgrid-cards pixelgrid-four">
<?php foreach ( $pixelgrid_people as $pixelgrid_person ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $pixelgrid_person[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html( $pixelgrid_person[1] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_person[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
