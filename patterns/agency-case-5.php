<?php
/**
 * Title: Case study: Foxglove Festival
 * Slug: pixelgrid/agency-case-5
 * Categories: pixelgrid-agency
 * Inserter: no
 * Description: Case study page text for Foxglove Festival, on the shared case study layout.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_agency_case' ) ) {
	require get_parent_theme_file_path( 'inc/agency-case.php' );
}

pixelgrid_agency_case(
	array(
		'label'      => _x( 'Foxglove Festival, campaign', 'Case study client and discipline', 'pixelgrid' ),
		'title'      => _x( 'Posters for a festival that changes every year', 'Case study title', 'pixelgrid' ),
		'lead'       => _x( 'Foxglove is a ten-day arts festival with a new theme every summer. We designed a poster system that the in-house team can rework each year.', 'Case study introduction', 'pixelgrid' ),
		'image'      => 'assets/images/agency-05.webp',
		'alt'        => _x( 'An abstract grey picture for the Foxglove Festival case study', 'Image description', 'pixelgrid' ),
		'stats'      => array(
			array( __( '140', 'pixelgrid' ), __( 'posters made from one template set', 'pixelgrid' ) ),
			array( __( '10 days', 'pixelgrid' ), __( 'of events, each with its own poster', 'pixelgrid' ) ),
			array( __( '3 years', 'pixelgrid' ), __( 'the system has been in use', 'pixelgrid' ) ),
		),
		'challenge'  => _x( 'Each year the festival paid for a new campaign from scratch. The look changed so much that returning visitors did not recognise it, and the budget went on design instead of artists.', 'Case study text', 'pixelgrid' ),
		'approach'   => _x( 'We kept the parts people remembered, the name, the grid and a bold type style, and made everything else a slot: the theme colour, the picture and the dates. We trained the team to fill them in.', 'Case study text', 'pixelgrid' ),
		'outcome'    => _x( 'The festival team now makes every poster, flyer and social image in house. The look stays familiar from year to year, while each theme still feels new.', 'Case study text', 'pixelgrid' ),
		'quote'      => _x( '“We spend the money on artists now, and the posters still look like a professional made them.”', 'Sample client quote', 'pixelgrid' ),
		'cite'       => _x( 'Leo Brandt, festival director, Foxglove Festival', 'Sample client name and role', 'pixelgrid' ),
		'next_path'  => 'work/quarry-lane-wayfinding',
		'next_title' => _x( 'Signs that take you to the right shelf', 'Case study title', 'pixelgrid' ),
	)
);
