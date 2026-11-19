<?php
/**
 * Title: Agency header
 * Slug: pixelgrid/agency-header
 * Categories: header, pixelgrid-agency
 * Block Types: core/template-part/header
 * Description: Large site title as a wordmark, a short menu and a Start a project button.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"align":"full","className":"pixelgrid-header","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-header"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:site-title {"level":0,"fontSize":"x-large"} /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group"><!-- wp:navigation {"overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"pixelgrid-header-button"} -->
<div class="wp-block-button pixelgrid-header-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'contact', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'Start a project', 'Header button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
