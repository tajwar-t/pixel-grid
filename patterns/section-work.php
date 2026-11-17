<?php
/**
 * Title: Recent work
 * Slug: pixelgrid/section-work
 * Categories: pixelgrid-sections, portfolio
 * Keywords: case studies, work, cards
 * Viewport Width: 1400
 * Description: Three case study cards with black and white pictures.
 *
 * @package pixelgrid
 */

$pixelgrid_cards = array(
	array( 5, __( 'Healthcare', 'pixelgrid' ), __( 'Nine clinics, one login', 'pixelgrid' ), __( 'Password resets fell by 80% after nine clinics moved to a single sign-in.', 'pixelgrid' ) ),
	array( 6, __( 'Logistics', 'pixelgrid' ), __( 'Off the server in the cupboard', 'pixelgrid' ), __( 'A 12-year-old server retired in one weekend, with no missed shipment.', 'pixelgrid' ) ),
	array( 7, __( 'Architecture', 'pixelgrid' ), __( 'Back at work in 40 minutes', 'pixelgrid' ), __( 'Ransomware hit on a Tuesday. Every drawing was restored before lunch.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Recent work', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-cards"} -->
<div class="wp-block-columns pixelgrid-cards">
<?php foreach ( $pixelgrid_cards as $pixelgrid_card ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"aspectRatio":"3/2","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/plate-' . $pixelgrid_card[0] . '.webp' ) ); ?>" alt="" style="aspect-ratio:3/2;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html( $pixelgrid_card[1] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $pixelgrid_card[2] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_card[3] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
