<?php
/**
 * Title: Header
 * Slug: pixelgrid/header
 * Categories: header
 * Block Types: core/template-part/header
 * Description: Site title, main menu and a quote button.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"align":"full","className":"pixelgrid-header","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-header"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:site-title {"level":0} /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"pixelgrid-header-button"} -->
<div class="wp-block-button pixelgrid-header-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php echo esc_html_x( 'Get a quote', 'Header button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
