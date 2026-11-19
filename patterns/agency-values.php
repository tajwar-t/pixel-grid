<?php
/**
 * Title: Agency values
 * Slug: pixelgrid/agency-values
 * Categories: pixelgrid-agency, about
 * Keywords: values, principles, studio
 * Viewport Width: 1400
 * Description: Three things the studio believes, in columns on a bold section.
 *
 * @package pixelgrid
 */

$pixelgrid_items = array(
	array( __( 'Clear beats clever', 'pixelgrid' ), __( 'If people have to work out what you mean, the design has not done its job yet.', 'pixelgrid' ) ),
	array( __( 'Go and look', 'pixelgrid' ), __( 'We visit the shop, the pier or the waiting room before we open a sketchbook.', 'pixelgrid' ) ),
	array( __( 'Leave it usable', 'pixelgrid' ), __( 'Every project ends with files and guidelines your own team can work from.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-blue pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-blue pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'What we believe', 'Section heading', 'pixelgrid' ); ?></h2>
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
