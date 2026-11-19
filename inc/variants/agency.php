<?php
/**
 * Agency variant: pages, case studies, categories and sample journal posts.
 *
 * Returns the variant definition. Block markup for the journal posts is built from
 * translatable strings.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pixelgrid_p  = function ( $text ) {
	return "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
};
$pixelgrid_h  = function ( $text, $level = 2 ) {
	$attrs = 2 === $level ? '' : ' {"level":' . (int) $level . '}';
	return '<!-- wp:heading' . $attrs . " -->\n<h" . (int) $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . (int) $level . ">\n<!-- /wp:heading -->\n\n";
};
$pixelgrid_ul = function ( $items ) {
	$out = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>" . esc_html( $item ) . "</li>\n<!-- /wp:list-item -->";
	}
	return $out . "</ul>\n<!-- /wp:list -->\n\n";
};
$pixelgrid_q  = function ( $text, $cite ) {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph --><cite>" . esc_html( $cite ) . "</cite></blockquote>\n<!-- /wp:quote -->\n\n";
};

return array(
	'slug'           => 'agency',
	'label'          => __( 'Agency', 'pixelgrid' ),
	'description'    => __( 'A small design studio with case studies, services and a journal.', 'pixelgrid' ),
	'content_labels' => array(
		'pages'   => __( 'Pages: Home, Work, Services, Studio, Contact and Journal, plus six case studies under Work', 'pixelgrid' ),
		'posts'   => __( 'Six sample journal posts with categories, tags and images', 'pixelgrid' ),
		'reading' => __( 'Use the new Home page as the homepage and Journal as the posts page', 'pixelgrid' ),
	),
	'design_note'    => __( 'This replaces your Styles with the Agency colours and fonts, and changes the site\'s header, footer and posts page template. Your current ones are saved and put back when you remove the starter content. Applying again only changes what you have not edited since setup, and edits you make after setup are kept on removal.', 'pixelgrid' ),
	'style'          => 'styles/agency.json',
	'parts'          => array(
		'header' => 'pixelgrid/agency-header',
		'footer' => 'pixelgrid/agency-footer',
	),
	'templates'      => array(
		'home' => 'pixelgrid/agency-template-home',
	),
	'pages'          => array(
		'home'                   => array( __( 'Home', 'pixelgrid' ), 'pixelgrid/agency-page-home', 'page-sections' ),
		'work'                   => array( __( 'Work', 'pixelgrid' ), 'pixelgrid/agency-page-work', 'page-sections' ),
		'services'               => array( __( 'Services', 'pixelgrid' ), 'pixelgrid/agency-page-services', 'page-sections' ),
		'studio'                 => array( __( 'Studio', 'pixelgrid' ), 'pixelgrid/agency-page-studio', 'page-sections' ),
		'contact'                => array( __( 'Contact', 'pixelgrid' ), 'pixelgrid/agency-page-contact', 'page-sections' ),
		'journal'                => array( __( 'Journal', 'pixelgrid' ), '', '' ),
		'northline-rebrand'      => array( _x( 'A ferry line you can read from the dock', 'Case study title', 'pixelgrid' ), 'pixelgrid/agency-case-1', 'page-sections', 'work' ),
		'tidewater-museum'       => array( _x( 'A museum website that opens on today', 'Case study title', 'pixelgrid' ), 'pixelgrid/agency-case-2', 'page-sections', 'work' ),
		'copperleaf-packaging'   => array( _x( 'Tea boxes that tell the flavour at a glance', 'Case study title', 'pixelgrid' ), 'pixelgrid/agency-case-3', 'page-sections', 'work' ),
		'meridian-booking-app'   => array( _x( 'Booking a clinic visit in three taps', 'Case study title', 'pixelgrid' ), 'pixelgrid/agency-case-4', 'page-sections', 'work' ),
		'foxglove-festival'      => array( _x( 'Posters for a festival that changes every year', 'Case study title', 'pixelgrid' ), 'pixelgrid/agency-case-5', 'page-sections', 'work' ),
		'quarry-lane-wayfinding' => array( _x( 'Signs that take you to the right shelf', 'Case study title', 'pixelgrid' ), 'pixelgrid/agency-case-6', 'page-sections', 'work' ),
	),
	'front'          => 'home',
	'posts_page'     => 'journal',
	'menu'           => array( 'work', 'services', 'studio', 'journal', 'contact' ),
	'categories'     => array(
		'process'    => __( 'Process', 'pixelgrid' ),
		'design'     => __( 'Design', 'pixelgrid' ),
		'technology' => __( 'Technology', 'pixelgrid' ),
		'culture'    => __( 'Culture', 'pixelgrid' ),
	),
	'posts'          => array(
		array(
			'key'      => 'one-sentence-brief',
			'title'    => __( 'Every project starts with one sentence', 'pixelgrid' ),
			'excerpt'  => __( 'Why our first workshop ends with the problem written down, and nothing else.', 'pixelgrid' ),
			'category' => 'process',
			'tags'     => array( __( 'Workshops', 'pixelgrid' ), __( 'Briefs', 'pixelgrid' ) ),
			'image'    => 'assets/images/agency-07.webp',
			'content'  => $pixelgrid_p( __( 'Most briefs we receive are long. They list every channel, every audience and every hope for the next five years. Our first job is to make them short.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'What happens in the room', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'We spend half a day with the people who will live with the work. By lunch, we want one sentence on the wall that everyone agrees describes the problem.', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Who is this for, in plain words?', 'pixelgrid' ), __( 'What do they do today instead?', 'pixelgrid' ), __( 'What would tell us, a year from now, that it worked?', 'pixelgrid' ) ) )
				. $pixelgrid_q( __( 'If we cannot say it in one sentence, we are not ready to draw anything.', 'pixelgrid' ), __( 'Nadia Kerr, creative director', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Why it saves time', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Every later decision gets checked against that sentence. It ends debates about colour and type faster than any mood board.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'logo-small-sizes',
			'title'    => __( 'Draw the logo small first', 'pixelgrid' ),
			'excerpt'  => __( 'A mark that works on a phone tab works on a billboard. The reverse is rarely true.', 'pixelgrid' ),
			'category' => 'design',
			'tags'     => array( __( 'Identity', 'pixelgrid' ) ),
			'image'    => 'assets/images/agency-08.webp',
			'content'  => $pixelgrid_p( __( 'Logos are usually presented huge, on a clean wall. They are usually seen tiny, in a browser tab or on a receipt.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Our three size tests', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Sixteen pixels square, as a browser icon', 'pixelgrid' ), __( 'One centimetre wide, printed in black only', 'pixelgrid' ), __( 'Across a room, on a sign seen from ten metres', 'pixelgrid' ) ) )
				. $pixelgrid_p( __( 'If a sketch fails any of them, it does not reach the presentation. It saves the client from falling for something that will not survive real use.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'Detail is lovely. Detail you cannot see at the size people meet you is decoration.', 'pixelgrid' ), __( 'Oskar Lind, design lead', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'fast-pages',
			'title'    => __( 'A website that loads in a second is a design decision', 'pixelgrid' ),
			'excerpt'  => __( 'Speed is set in the first sketches, not fixed at the end.', 'pixelgrid' ),
			'category' => 'technology',
			'tags'     => array( __( 'Websites', 'pixelgrid' ), __( 'Performance', 'pixelgrid' ) ),
			'image'    => 'assets/images/agency-09.webp',
			'content'  => $pixelgrid_p( __( 'Slow sites are rarely slow because of the server. They are slow because the design asked for six fonts, a video behind the headline and a slider nobody clicks.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Budgets we agree before drawing', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Two font files, three at most', 'pixelgrid' ), __( 'No image larger than it is shown', 'pixelgrid' ), __( 'The first screen readable before any script runs', 'pixelgrid' ) ) )
				. $pixelgrid_p( __( 'On the Tidewater Museum site, those three rules took the home page from four seconds to under one on a mid-range phone.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'What we test on', 'pixelgrid' ), 3 )
				. $pixelgrid_p( __( 'A three-year-old phone on a slow connection. If it works there, it works everywhere.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'four-people',
			'title'    => __( 'Why we stay at four people', 'pixelgrid' ),
			'excerpt'  => __( 'Growing would mean handing clients to people they never met.', 'pixelgrid' ),
			'category' => 'culture',
			'tags'     => array( __( 'Studio', 'pixelgrid' ) ),
			'image'    => 'assets/images/agency-10.webp',
			'content'  => $pixelgrid_p( __( 'Every year someone asks when we will hire. The honest answer is that we have decided not to.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'At four, the person who runs the first workshop also hands over the final files. Nothing gets lost between a pitch team and a delivery team, because there is only one team.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'What it costs us', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'We turn down work, and we take on six or seven projects a year rather than twenty. We think the work is better for it, and so do the clients who come back.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'weekly-show',
			'title'    => __( 'Show unfinished work every week', 'pixelgrid' ),
			'excerpt'  => __( 'Rough versions shown early cost less than polished ones shown late.', 'pixelgrid' ),
			'category' => 'process',
			'tags'     => array( __( 'Workshops', 'pixelgrid' ) ),
			'image'    => 'assets/images/agency-11.webp',
			'content'  => $pixelgrid_p( __( 'Every Thursday, each client sees what we made that week, however rough. A pencil sketch counts. So does a page with grey boxes.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'How the call runs', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Fifteen minutes, never more', 'pixelgrid' ), __( 'We show, then we ask one question', 'pixelgrid' ), __( 'Feedback goes in a shared document the same day', 'pixelgrid' ) ) )
				. $pixelgrid_p( __( 'It means a wrong turn costs a week, not a month, and there are no surprises at the final presentation.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'handover-files',
			'title'    => __( 'What should be in your brand handover folder', 'pixelgrid' ),
			'excerpt'  => __( 'The files a team needs to carry on without calling the designer.', 'pixelgrid' ),
			'category' => 'design',
			'tags'     => array( __( 'Identity', 'pixelgrid' ), __( 'Checklists', 'pixelgrid' ) ),
			'image'    => 'assets/images/agency-12.webp',
			'content'  => $pixelgrid_p( __( 'A brand is only finished when someone else can use it. This is the folder we hand over at the end of every identity project.', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'The logo in colour, black and white, as SVG and PNG', 'pixelgrid' ), __( 'Colour values for screen and for print', 'pixelgrid' ), __( 'The font files, with their licences', 'pixelgrid' ), __( 'Ten pages of guidelines, not a hundred', 'pixelgrid' ), __( 'Editable templates for the things made most often', 'pixelgrid' ) ) )
				. $pixelgrid_p( __( 'Then we spend an hour with whoever will use it most. Usually that is not the marketing lead, it is the person who makes the posters.', 'pixelgrid' ) ),
		),
	),
	'preview'        => '',
);
