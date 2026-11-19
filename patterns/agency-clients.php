<?php
/**
 * Title: Agency clients
 * Slug: pixelgrid/agency-clients
 * Categories: pixelgrid-agency, testimonials
 * Keywords: clients, logos, wordmarks, quote
 * Viewport Width: 1400
 * Description: A short heading, a row of client names set as wordmarks and a client quote.
 *
 * @package pixelgrid
 */

$pixelgrid_clients = array(
	__( 'Northline', 'pixelgrid' ),
	__( 'Tidewater', 'pixelgrid' ),
	__( 'Copperleaf', 'pixelgrid' ),
	__( 'Meridian', 'pixelgrid' ),
	__( 'Foxglove', 'pixelgrid' ),
	__( 'Quarry Lane', 'pixelgrid' ),
	__( 'Larkspur', 'pixelgrid' ),
	__( 'Ashby & Vale', 'pixelgrid' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Who we work with', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-wordmarks"} -->
<ul class="wp-block-list is-style-wordmarks">
<?php foreach ( $pixelgrid_clients as $pixelgrid_client ) : ?>
<!-- wp:list-item -->
<li><?php echo esc_html( $pixelgrid_client ); ?></li>
<!-- /wp:list-item -->
<?php endforeach; ?>
</ul>
<!-- /wp:list -->

<!-- wp:pullquote -->
<figure class="wp-block-pullquote"><blockquote><p><?php echo esc_html_x( '“They asked better questions in the first meeting than we had asked ourselves in a year.”', 'Sample client quote', 'pixelgrid' ); ?></p><cite><?php echo esc_html_x( 'Hana Olsen, founder, Copperleaf Tea', 'Sample client name and role', 'pixelgrid' ); ?></cite></blockquote></figure>
<!-- /wp:pullquote --></section>
<!-- /wp:group -->
