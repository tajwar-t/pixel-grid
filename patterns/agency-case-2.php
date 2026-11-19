<?php
/**
 * Title: Case study: Tidewater Museum
 * Slug: pixelgrid/agency-case-2
 * Categories: pixelgrid-agency
 * Inserter: no
 * Description: Case study page text for Tidewater Museum, on the shared case study layout.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_agency_case' ) ) {
	require get_parent_theme_file_path( 'inc/agency-case.php' );
}

pixelgrid_agency_case(
	array(
		'label'      => _x( 'Tidewater Museum, website', 'Case study client and discipline', 'pixelgrid' ),
		'title'      => _x( 'A museum website that opens on today', 'Case study title', 'pixelgrid' ),
		'lead'       => _x( 'Most visitors wanted to know one thing: what is on, and when. We rebuilt the Tidewater Museum site around that question.', 'Case study introduction', 'pixelgrid' ),
		'image'      => 'assets/images/agency-02.webp',
		'alt'        => _x( 'An abstract grey picture for the Tidewater Museum case study', 'Image description', 'pixelgrid' ),
		'stats'      => array(
			array( __( '2x', 'pixelgrid' ), __( 'tickets sold online', 'pixelgrid' ) ),
			array( __( '64%', 'pixelgrid' ), __( 'of visits now on phones, up from 41%', 'pixelgrid' ) ),
			array( __( '1 page', 'pixelgrid' ), __( 'per exhibition, kept by the museum team', 'pixelgrid' ) ),
		),
		'challenge'  => _x( 'The old site had grown to more than 300 pages. Opening times were in four places and two of them were wrong. Staff were afraid to edit it in case something broke.', 'Case study text', 'pixelgrid' ),
		'approach'   => _x( 'We cut the site to the pages people actually used, put today\'s opening hours and exhibitions on the front page and built a simple page layout the museum team can fill in themselves.', 'Case study text', 'pixelgrid' ),
		'outcome'    => _x( 'The museum now publishes a new exhibition page in an afternoon. Online ticket sales doubled in the first year, and the front desk gets far fewer calls about opening times.', 'Case study text', 'pixelgrid' ),
		'quote'      => _x( '“Our curators update the site themselves now. Nobody waits for an agency to change a date.”', 'Sample client quote', 'pixelgrid' ),
		'cite'       => _x( 'Tomas Reyes, head of visitor services, Tidewater Museum', 'Sample client name and role', 'pixelgrid' ),
		'next_path'  => 'work/copperleaf-packaging',
		'next_title' => _x( 'Tea boxes that tell the flavour at a glance', 'Case study title', 'pixelgrid' ),
	)
);
