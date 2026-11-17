<?php
/**
 * Title: Team
 * Slug: pixelgrid/section-team
 * Categories: pixelgrid-sections, team
 * Keywords: people, staff, team
 * Viewport Width: 1400
 * Description: Four people with square pictures, names and roles.
 *
 * @package pixelgrid
 */

$pixelgrid_people = array(
	array( 6, __( 'Daniel Okoro', 'pixelgrid' ), __( 'Co-founder, head of helpdesk', 'pixelgrid' ) ),
	array( 7, __( 'Marta Figueira', 'pixelgrid' ), __( 'Co-founder, head of security', 'pixelgrid' ) ),
	array( 8, __( 'Wei Ling Tan', 'pixelgrid' ), __( 'Cloud lead', 'pixelgrid' ) ),
	array( 2, __( 'Sofia Almeida', 'pixelgrid' ), __( 'Client onboarding', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'People you will talk to', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-cards pixelgrid-four"} -->
<div class="wp-block-columns pixelgrid-cards pixelgrid-four">
<?php foreach ( $pixelgrid_people as $pixelgrid_person ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/plate-' . $pixelgrid_person[0] . '.webp' ) ); ?>" alt="" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $pixelgrid_person[1] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_person[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
