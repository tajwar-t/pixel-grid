<?php
/**
 * Title: Case study: Meridian Clinics
 * Slug: pixelgrid/agency-case-4
 * Categories: pixelgrid-agency
 * Inserter: no
 * Description: Case study page text for Meridian Clinics, on the shared case study layout.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_agency_case' ) ) {
	require get_parent_theme_file_path( 'inc/agency-case.php' );
}

pixelgrid_agency_case(
	array(
		'label'      => _x( 'Meridian Clinics, product design', 'Case study client and discipline', 'pixelgrid' ),
		'title'      => _x( 'Booking a clinic visit in three taps', 'Case study title', 'pixelgrid' ),
		'lead'       => _x( 'Meridian runs six physiotherapy clinics. Patients booked by phone because the old app asked for too much. We designed one that asks for less.', 'Case study introduction', 'pixelgrid' ),
		'image'      => 'assets/images/agency-04.webp',
		'alt'        => _x( 'An abstract grey picture for the Meridian Clinics case study', 'Image description', 'pixelgrid' ),
		'stats'      => array(
			array( __( '3 taps', 'pixelgrid' ), __( 'to book a repeat visit', 'pixelgrid' ) ),
			array( __( '52%', 'pixelgrid' ), __( 'of bookings now made in the app', 'pixelgrid' ) ),
			array( __( '6', 'pixelgrid' ), __( 'clinics on one shared calendar', 'pixelgrid' ) ),
		),
		'challenge'  => _x( 'The old app asked for a full medical history before showing a single free slot. Most patients gave up and phoned, and reception spent half the day on calls.', 'Case study text', 'pixelgrid' ),
		'approach'   => _x( 'We tested paper prototypes with patients in the waiting rooms. The new app shows free times first, remembers your therapist and asks for details only on a first visit.', 'Case study text', 'pixelgrid' ),
		'outcome'    => _x( 'Half of all bookings now come through the app. Reception spends its time with the people in front of it, and missed appointments fell after we added reminders patients can reply to.', 'Case study text', 'pixelgrid' ),
		'quote'      => _x( '“Patients tell us the app is the easiest part of their treatment. That was not true a year ago.”', 'Sample client quote', 'pixelgrid' ),
		'cite'       => _x( 'Dr Amara Osei, clinical director, Meridian Clinics', 'Sample client name and role', 'pixelgrid' ),
		'next_path'  => 'work/foxglove-festival',
		'next_title' => _x( 'Posters for a festival that changes every year', 'Case study title', 'pixelgrid' ),
	)
);
