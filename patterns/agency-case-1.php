<?php
/**
 * Title: Case study: Northline Ferries
 * Slug: pixelgrid/agency-case-1
 * Categories: pixelgrid-agency
 * Inserter: no
 * Description: Case study page text for Northline Ferries, on the shared case study layout.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_agency_case' ) ) {
	require get_parent_theme_file_path( 'inc/agency-case.php' );
}

pixelgrid_agency_case(
	array(
		'label'      => _x( 'Northline Ferries, brand identity', 'Case study client and discipline', 'pixelgrid' ),
		'title'      => _x( 'A ferry line you can read from the dock', 'Case study title', 'pixelgrid' ),
		'lead'       => _x( 'Northline runs four routes across a busy estuary. We gave it one name, one sign system and a timetable that fits on a postcard.', 'Case study introduction', 'pixelgrid' ),
		'image'      => 'assets/images/agency-01.webp',
		'alt'        => _x( 'An abstract grey picture for the Northline Ferries case study', 'Image description', 'pixelgrid' ),
		'stats'      => array(
			array( __( '4', 'pixelgrid' ), __( 'routes on one simple map', 'pixelgrid' ) ),
			array( __( '38%', 'pixelgrid' ), __( 'more tickets booked online in the first season', 'pixelgrid' ) ),
			array( __( '11 weeks', 'pixelgrid' ), __( 'from first meeting to launch', 'pixelgrid' ) ),
		),
		'challenge'  => _x( 'Northline had grown by buying two smaller operators. Each kept its own name, colours and timetable, so passengers at the same pier saw three brands and could not tell which boat was theirs.', 'Case study text', 'pixelgrid' ),
		'approach'   => _x( 'We spent two days on the piers watching people buy tickets. The new identity starts from what they needed: a route colour you can spot from fifty metres, large departure times and one name on every hull, sign and ticket.', 'Case study text', 'pixelgrid' ),
		'outcome'    => _x( 'The new signs went up over one winter. Staff now answer fewer questions about which boat to take, and online bookings rose because the website finally matched what people saw at the dock.', 'Case study text', 'pixelgrid' ),
		'quote'      => _x( '“For the first time, the boat, the sign and the ticket all say the same thing.”', 'Sample client quote', 'pixelgrid' ),
		'cite'       => _x( 'Ines Marlow, operations manager, Northline Ferries', 'Sample client name and role', 'pixelgrid' ),
		'next_path'  => 'work/tidewater-museum',
		'next_title' => _x( 'A museum website that opens on today', 'Case study title', 'pixelgrid' ),
	)
);
