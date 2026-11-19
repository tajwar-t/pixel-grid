<?php
/**
 * Title: Agency hero with changing pixel picture
 * Slug: pixelgrid/agency-hero
 * Categories: pixelgrid-agency, banner
 * Keywords: hero, intro, mosaic, studio
 * Viewport Width: 1400
 * Description: Big headline and introduction for a design studio beside a picture drawn through a pixel icon that changes every few seconds.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Brands, sites and packs people read at a glance', 'Agency hero heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Fieldwork Studio is a small design team. We make brands, websites and packaging for organisations that would rather be clear than clever.', 'Agency hero introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'contact', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'Start a project', 'Agency hero button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'work', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'See our work', 'Agency hero button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-cycle"} -->
<figure class="wp-block-image size-full is-style-mosaic-cycle"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/agency-07.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel drawing that changes between a screen, a cloud, a padlock, a speech bubble and a heart', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
