<?php
/**
 * Title: Agency ways to work together
 * Slug: pixelgrid/agency-engagements
 * Categories: pixelgrid-agency, text
 * Keywords: services, pricing, engagement, cards
 * Viewport Width: 1400
 * Description: Three ways to hire the studio, each with a short description and a typical length.
 *
 * @package pixelgrid
 */

$pixelgrid_items = array(
	array( __( 'One project', 'pixelgrid' ), __( 'A fixed scope and a fixed price: a new identity, a website or a packaging range.', 'pixelgrid' ), __( 'Usually 8 to 16 weeks', 'pixelgrid' ) ),
	array( __( 'Ongoing design', 'pixelgrid' ), __( 'A set number of studio days each month for campaigns, new pages and print.', 'pixelgrid' ), __( 'Month to month', 'pixelgrid' ) ),
	array( __( 'Workshop', 'pixelgrid' ), __( 'One day with your team to agree the problem before anything is designed.', 'pixelgrid' ), __( 'One day, with a written summary', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Ways to work together', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-cards"} -->
<div class="wp-block-columns pixelgrid-cards">
<?php foreach ( $pixelgrid_items as $pixelgrid_item ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $pixelgrid_item[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_item[1] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html( $pixelgrid_item[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
