<?php
/**
 * Title: Quote, clients and latest notes
 * Slug: pixelgrid/section-proof
 * Categories: pixelgrid-sections, testimonials, posts
 * Keywords: testimonial, quote, clients, logos, blog
 * Viewport Width: 1400
 * Description: A large client quote, a row of client names and the three latest posts as full-width rows.
 *
 * @package pixelgrid
 */

$pixelgrid_clients = array(
	__( 'Harbour', 'pixelgrid' ),
	__( 'Kestrel', 'pixelgrid' ),
	__( 'Oakline', 'pixelgrid' ),
	__( 'Brightside', 'pixelgrid' ),
	__( 'Tallow', 'pixelgrid' ),
	__( 'Northgate', 'pixelgrid' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:pullquote -->
<figure class="wp-block-pullquote"><blockquote><p><?php echo esc_html_x( '“I used to keep a list of things that were broken. I threw it away in March. Nothing has gone on it since.”', 'Sample client quote', 'pixelgrid' ); ?></p><cite><?php echo esc_html_x( 'Priya Raman, operations director', 'Sample client name and role', 'pixelgrid' ); ?></cite></blockquote></figure>
<!-- /wp:pullquote -->

<!-- wp:list {"className":"is-style-wordmarks"} -->
<ul class="wp-block-list is-style-wordmarks">
<?php foreach ( $pixelgrid_clients as $pixelgrid_client ) : ?>
<!-- wp:list-item -->
<li><?php echo esc_html( $pixelgrid_client ); ?></li>
<!-- /wp:list-item -->
<?php endforeach; ?>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html_x( 'From our notes', 'Section heading', 'pixelgrid' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:query {"queryId":11,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"pixelgrid-rows"} -->
<div class="wp-block-query pixelgrid-rows"><!-- wp:post-template -->
<!-- wp:group {"className":"pixelgrid-row","layout":{"type":"default"}} -->
<div class="wp-block-group pixelgrid-row"><!-- wp:post-terms {"term":"category"} /-->

<!-- wp:post-title {"level":4,"isLink":true} /-->

<!-- wp:post-date /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
