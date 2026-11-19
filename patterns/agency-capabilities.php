<?php
/**
 * Title: Agency capabilities
 * Slug: pixelgrid/agency-capabilities
 * Categories: pixelgrid-agency, featured
 * Keywords: services, capabilities, mosaic
 * Viewport Width: 1400
 * Description: A full-height bold section listing what the studio makes, beside a picture drawn through a pixel screen.
 *
 * @package pixelgrid
 */

$pixelgrid_capabilities = array(
	__( 'Brand identity: names, logos, type and the rules that keep them together', 'pixelgrid' ),
	__( 'Websites and apps: from first sketch to a site your team can edit', 'pixelgrid' ),
	__( 'Packaging and print: boxes, labels, books and reports', 'pixelgrid' ),
	__( 'Campaigns and signs: posters, wayfinding and launch material', 'pixelgrid' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-blue pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-blue pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-screen"} -->
<figure class="wp-block-image size-full is-style-mosaic-screen"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/agency-11.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel screen', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'What we make', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Four kinds of work, done by the same small team, so the brand, the website and the box on the shelf all feel like one thing.', 'Section introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel">
<?php foreach ( $pixelgrid_capabilities as $pixelgrid_capability ) : ?>
<!-- wp:list-item -->
<li><?php echo esc_html( $pixelgrid_capability ); ?></li>
<!-- /wp:list-item -->
<?php endforeach; ?>
</ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'services', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'How we work with you', 'Section button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
