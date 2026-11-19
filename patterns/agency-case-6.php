<?php
/**
 * Title: Case study: Quarry Lane Library
 * Slug: pixelgrid/agency-case-6
 * Categories: pixelgrid-agency
 * Inserter: no
 * Description: Case study page text for Quarry Lane Library, on the shared case study layout.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_agency_case' ) ) {
	require get_parent_theme_file_path( 'inc/agency-case.php' );
}

pixelgrid_agency_case(
	array(
		'label'      => _x( 'Quarry Lane Library, wayfinding', 'Case study client and discipline', 'pixelgrid' ),
		'title'      => _x( 'Signs that take you to the right shelf', 'Case study title', 'pixelgrid' ),
		'lead'       => _x( 'Quarry Lane is a five-floor public library in an old warehouse. We designed the signs that help people find their way around it.', 'Case study introduction', 'pixelgrid' ),
		'image'      => 'assets/images/agency-06.webp',
		'alt'        => _x( 'An abstract grey picture for the Quarry Lane Library case study', 'Image description', 'pixelgrid' ),
		'stats'      => array(
			array( __( '5', 'pixelgrid' ), __( 'floors with one numbering system', 'pixelgrid' ) ),
			array( __( '212', 'pixelgrid' ), __( 'signs designed, made and fitted', 'pixelgrid' ) ),
			array( __( '70%', 'pixelgrid' ), __( 'fewer directions asked at the front desk', 'pixelgrid' ) ),
		),
		'challenge'  => _x( 'The building had been extended three times. Floors were numbered from two different entrances, and the children\'s section was easier to find from the car park than from the front door.', 'Case study text', 'pixelgrid' ),
		'approach'   => _x( 'We walked the building with staff and first-time visitors. Then we renumbered the floors from the main door, gave each floor one colour and put a simple map at every lift and stair.', 'Case study text', 'pixelgrid' ),
		'outcome'    => _x( 'Visitors find their way without asking. Staff counted directions given at the front desk before and after: the number fell by more than two thirds in the first month.', 'Case study text', 'pixelgrid' ),
		'quote'      => _x( '“People used to arrive at the desk lost. Now they arrive with a book.”', 'Sample client quote', 'pixelgrid' ),
		'cite'       => _x( 'Ruth Adeyemi, library manager, Quarry Lane Library', 'Sample client name and role', 'pixelgrid' ),
		'next_path'  => 'work/northline-rebrand',
		'next_title' => _x( 'A ferry line you can read from the dock', 'Case study title', 'pixelgrid' ),
	)
);
