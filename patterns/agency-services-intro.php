<?php
/**
 * Title: Agency services intro
 * Slug: pixelgrid/agency-services-intro
 * Categories: pixelgrid-agency, banner
 * Keywords: services, intro
 * Viewport Width: 1400
 * Description: Services page heading and introduction beside a picture drawn through a pixel note.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html_x( 'Ways we can help', 'Services page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Some clients need a whole new brand. Others need one good website or a box that sells. We scope every project to the problem, not to a package.', 'Services page introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'contact', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'Ask about a project', 'Services page button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-note"} -->
<figure class="wp-block-image size-full is-style-mosaic-note"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/agency-08.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel note', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
