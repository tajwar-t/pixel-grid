<?php
/**
 * Title: Agency work grid
 * Slug: pixelgrid/agency-work-grid
 * Categories: pixelgrid-agency, portfolio
 * Keywords: case studies, work, portfolio, cards
 * Viewport Width: 1400
 * Description: Six case study cards with pictures, each linking to its case study page.
 *
 * @package pixelgrid
 */

$pixelgrid_cases = array(
	array( 'northline-rebrand', 1, __( 'Northline Ferries, brand identity', 'pixelgrid' ), __( 'A ferry line you can read from the dock', 'pixelgrid' ), __( 'One name, one sign system and a timetable that fits on a postcard.', 'pixelgrid' ) ),
	array( 'tidewater-museum', 2, __( 'Tidewater Museum, website', 'pixelgrid' ), __( 'A museum website that opens on today', 'pixelgrid' ), __( 'Three hundred pages cut to the ones visitors use. Online ticket sales doubled.', 'pixelgrid' ) ),
	array( 'copperleaf-packaging', 3, __( 'Copperleaf Tea, packaging', 'pixelgrid' ), __( 'Tea boxes that tell the flavour at a glance', 'pixelgrid' ), __( 'Eighteen teas, one pattern system, no extra print cost.', 'pixelgrid' ) ),
	array( 'meridian-booking-app', 4, __( 'Meridian Clinics, product design', 'pixelgrid' ), __( 'Booking a clinic visit in three taps', 'pixelgrid' ), __( 'Free times first, details later. Half of all bookings now come through the app.', 'pixelgrid' ) ),
	array( 'foxglove-festival', 5, __( 'Foxglove Festival, campaign', 'pixelgrid' ), __( 'Posters for a festival that changes every year', 'pixelgrid' ), __( 'A poster system the festival team reworks in house each summer.', 'pixelgrid' ) ),
	array( 'quarry-lane-wayfinding', 6, __( 'Quarry Lane Library, wayfinding', 'pixelgrid' ), __( 'Signs that take you to the right shelf', 'pixelgrid' ), __( 'Five floors, one numbering system and far fewer lost visitors.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Selected work', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<?php foreach ( array_chunk( $pixelgrid_cases, 3 ) as $pixelgrid_row ) : ?>
<!-- wp:columns {"className":"pixelgrid-cards"} -->
<div class="wp-block-columns pixelgrid-cards">
	<?php foreach ( $pixelgrid_row as $pixelgrid_case ) : ?>
		<?php $pixelgrid_url = add_query_arg( 'pagename', 'work/' . $pixelgrid_case[0], home_url( '/' ) ); ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"aspectRatio":"3/2","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( get_parent_theme_file_uri( sprintf( 'assets/images/agency-%02d.webp', $pixelgrid_case[1] ) ) ); ?>" alt="" style="aspect-ratio:3/2;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html( $pixelgrid_case[2] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><a href="<?php echo esc_url( $pixelgrid_url ); ?>"><?php echo esc_html( $pixelgrid_case[3] ); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_case[4] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->

<?php endforeach; ?>
</section>
<!-- /wp:group -->
