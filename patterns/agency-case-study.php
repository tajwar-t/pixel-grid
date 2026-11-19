<?php
/**
 * Title: Case study page
 * Slug: pixelgrid/agency-case-study
 * Categories: pixelgrid-pages
 * Keywords: case study, project, portfolio, work
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1400
 * Inserter: yes
 * Description: One project: a lead with a picture, three results, the challenge, approach and outcome, a client quote and a link to the next case study.
 *
 * @package pixelgrid
 */

if ( ! function_exists( 'pixelgrid_agency_case' ) ) {
	require get_parent_theme_file_path( 'inc/agency-case.php' );
}

pixelgrid_agency_case(
	array(
		'label'      => _x( 'Client name, discipline', 'Case study placeholder', 'pixelgrid' ),
		'title'      => _x( 'What changed for the client, in one line', 'Case study placeholder', 'pixelgrid' ),
		'lead'       => _x( 'Two sentences on who the client is and what you made for them. Keep it to what a new client would want to know first.', 'Case study placeholder', 'pixelgrid' ),
		'image'      => 'assets/images/agency-01.webp',
		'alt'        => _x( 'An abstract grey picture standing in for a project photo', 'Image description', 'pixelgrid' ),
		'stats'      => array(
			array( __( '3', 'pixelgrid' ), __( 'results that matter to the client', 'pixelgrid' ) ),
			array( __( '40%', 'pixelgrid' ), __( 'a change you can measure', 'pixelgrid' ) ),
			array( __( '8 weeks', 'pixelgrid' ), __( 'from brief to launch', 'pixelgrid' ) ),
		),
		'challenge'  => _x( 'What was not working before you started, in the client’s words where you can. Name the problem, not the deliverable.', 'Case study placeholder', 'pixelgrid' ),
		'approach'   => _x( 'What you did and why: the research, the decisions that shaped the work and what you chose to leave out.', 'Case study placeholder', 'pixelgrid' ),
		'outcome'    => _x( 'What changed after launch. Numbers help, and so does a sentence on how the client uses the work today.', 'Case study placeholder', 'pixelgrid' ),
		'quote'      => _x( '“A short quote from the client about working with the studio.”', 'Case study placeholder', 'pixelgrid' ),
		'cite'       => _x( 'Name, role, company', 'Case study placeholder', 'pixelgrid' ),
		'next_path'  => 'work',
		'next_title' => _x( 'See more of our work', 'Case study placeholder', 'pixelgrid' ),
	)
);
