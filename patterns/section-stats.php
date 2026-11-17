<?php
/**
 * Title: Numbers
 * Slug: pixelgrid/section-stats
 * Categories: pixelgrid-sections, about
 * Keywords: stats, numbers, facts
 * Viewport Width: 1400
 * Description: Four big numbers with short labels, then two columns of text.
 *
 * @package pixelgrid
 */

$pixelgrid_stats = array(
	array( __( '140', 'pixelgrid' ), __( 'teams we look after', 'pixelgrid' ) ),
	array( __( '2 min', 'pixelgrid' ), __( 'longest wait for a human reply', 'pixelgrid' ) ),
	array( __( '96%', 'pixelgrid' ), __( 'of tickets fixed the same day', 'pixelgrid' ) ),
	array( __( '7 years', 'pixelgrid' ), __( 'average client stays with us', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-sun pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-sun pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Eleven years in numbers', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-stats"} -->
<div class="wp-block-columns pixelgrid-stats">
<?php foreach ( $pixelgrid_stats as $pixelgrid_stat ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-stat"} -->
<p class="is-style-stat"><?php echo esc_html( $pixelgrid_stat[0] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_stat[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'We started as two engineers who were tired of IT firms that hid behind ticket numbers. The first rule we wrote down was that a person answers.', 'About text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php echo esc_html_x( 'That rule still decides how we grow. We open an office only when a time zone needs covering, so nobody works nights. Every client has a named engineer and a written record of their setup that any of us can pick up.', 'About text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
