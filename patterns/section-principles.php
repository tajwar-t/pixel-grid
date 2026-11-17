<?php
/**
 * Title: Three principles
 * Slug: pixelgrid/section-principles
 * Categories: pixelgrid-sections, about
 * Keywords: values, principles, columns
 * Viewport Width: 1400
 * Description: Three short principles in columns on a blue section.
 *
 * @package pixelgrid
 */

$pixelgrid_items = array(
	array( __( 'A person answers', 'pixelgrid' ), __( 'No phone menus and no chatbots. You reach an engineer who can fix the problem.', 'pixelgrid' ) ),
	array( __( 'We write it down', 'pixelgrid' ), __( 'Every fix goes in your record, so the same fault is solved faster the second time, or not at all.', 'pixelgrid' ) ),
	array( __( 'Plain words', 'pixelgrid' ), __( 'Reports, quotes and advice in language your whole team can read.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-blue pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-blue pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'How we work', 'Section heading', 'pixelgrid' ); ?></h2>
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
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
