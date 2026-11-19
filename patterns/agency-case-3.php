<?php
/**
 * Title: Case study: Copperleaf Tea
 * Slug: pixelgrid/agency-case-3
 * Categories: pixelgrid-agency
 * Inserter: no
 * Description: Case study page text for Copperleaf Tea, on the shared case study layout.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_agency_case' ) ) {
	require get_parent_theme_file_path( 'inc/agency-case.php' );
}

pixelgrid_agency_case(
	array(
		'label'      => _x( 'Copperleaf Tea, packaging', 'Case study client and discipline', 'pixelgrid' ),
		'title'      => _x( 'Tea boxes that tell the flavour at a glance', 'Case study title', 'pixelgrid' ),
		'lead'       => _x( 'Copperleaf sells eighteen teas. On a crowded shelf they all looked the same. We gave each flavour a pattern and a plain name.', 'Case study introduction', 'pixelgrid' ),
		'image'      => 'assets/images/agency-03.webp',
		'alt'        => _x( 'An abstract grey picture for the Copperleaf Tea case study', 'Image description', 'pixelgrid' ),
		'stats'      => array(
			array( __( '18', 'pixelgrid' ), __( 'flavours on one pattern system', 'pixelgrid' ) ),
			array( __( '27%', 'pixelgrid' ), __( 'more sales in shops that stocked the new boxes', 'pixelgrid' ) ),
			array( __( '0', 'pixelgrid' ), __( 'extra print cost per box', 'pixelgrid' ) ),
		),
		'challenge'  => _x( 'Every box used the same brown card and a small label. Shoppers picked a tea by reading the side of each box, and most gave up after three.', 'Case study text', 'pixelgrid' ),
		'approach'   => _x( 'We built a pattern system on a simple grid: one shape per tea family, one colour per strength. The flavour name moved to the front in large type, and the brewing time to the lid.', 'Case study text', 'pixelgrid' ),
		'outcome'    => _x( 'The range now reads as a set on the shelf, and each tea is easy to find. The new boxes print on the same press at the same price as the old ones.', 'Case study text', 'pixelgrid' ),
		'quote'      => _x( '“Shop owners started putting the whole range on display instead of hiding it on the bottom shelf.”', 'Sample client quote', 'pixelgrid' ),
		'cite'       => _x( 'Hana Olsen, founder, Copperleaf Tea', 'Sample client name and role', 'pixelgrid' ),
		'next_path'  => 'work/meridian-booking-app',
		'next_title' => _x( 'Booking a clinic visit in three taps', 'Case study title', 'pixelgrid' ),
	)
);
