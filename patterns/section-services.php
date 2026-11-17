<?php
/**
 * Title: Services grid
 * Slug: pixelgrid/section-services
 * Categories: pixelgrid-sections, services
 * Keywords: services, cards, grid
 * Viewport Width: 1400
 * Description: Page heading, introduction and six service cards in two rows of three.
 *
 * @package pixelgrid
 */

$pixelgrid_services = array(
	array( 2, __( 'Managed helpdesk', 'pixelgrid' ), __( 'A person who knows your setup answers in under two minutes, any hour.', 'pixelgrid' ) ),
	array( 3, __( 'Cloud and servers', 'pixelgrid' ), __( 'Moved to the cloud without a lost weekend, then watched day and night.', 'pixelgrid' ) ),
	array( 4, __( 'Security', 'pixelgrid' ), __( 'Sign-in protection, fast updates, phishing drills and the paperwork insurers ask for.', 'pixelgrid' ) ),
	array( 5, __( 'Backups and recovery', 'pixelgrid' ), __( 'Copies in two countries, tested every month by restoring a real file.', 'pixelgrid' ) ),
	array( 6, __( 'Custom software', 'pixelgrid' ), __( 'Internal tools and integrations for the job no off-the-shelf product does.', 'pixelgrid' ) ),
	array( 7, __( 'IT strategy', 'pixelgrid' ), __( 'A plain one-page plan for the next two years of equipment, licences and spend.', 'pixelgrid' ) ),
);
$pixelgrid_rows = array_chunk( $pixelgrid_services, 3 );
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Services', 'Services page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Six things we take off your hands. Take one, or hand us the lot for a single monthly price.', 'Services introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->
<?php foreach ( $pixelgrid_rows as $pixelgrid_row ) : ?>

<!-- wp:columns {"className":"pixelgrid-cards"} -->
<div class="wp-block-columns pixelgrid-cards">
	<?php foreach ( $pixelgrid_row as $pixelgrid_service ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"aspectRatio":"3/2","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/plate-' . $pixelgrid_service[0] . '.webp' ) ); ?>" alt="" style="aspect-ratio:3/2;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $pixelgrid_service[1] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_service[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
	<?php endforeach; ?>
</div>
<!-- /wp:columns -->
<?php endforeach; ?>
</section>
<!-- /wp:group -->
