<?php
/**
 * News variant: a city newspaper with section pages, five sections and fifteen sample articles.
 *
 * Returns the variant definition. Block markup for the articles is built from translatable
 * strings. The paper, its city and everyone quoted are fictional.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pixelgrid_p = function ( $text ) {
	return "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
};
$pixelgrid_h = function ( $text, $level = 2 ) {
	$attrs = 2 === $level ? '' : ' {"level":' . (int) $level . '}';
	return '<!-- wp:heading' . $attrs . " -->\n<h" . (int) $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . (int) $level . ">\n<!-- /wp:heading -->\n\n";
};
$pixelgrid_q = function ( $text, $cite ) {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph --><cite>" . esc_html( $cite ) . "</cite></blockquote>\n<!-- /wp:quote -->\n\n";
};

return array(
	'slug'           => 'news',
	'label'          => __( 'News', 'pixelgrid' ),
	'description'    => __( 'A city newspaper with five sections, most read, opinion and a page for tips.', 'pixelgrid' ),
	'content_labels' => array(
		'pages'   => __( 'Pages: Home, About, Contact, Tips and Latest', 'pixelgrid' ),
		'posts'   => __( 'Fifteen sample articles in World, Business, Technology, Culture and Sport, with standfirsts, tags and images', 'pixelgrid' ),
		'reading' => __( 'Use the new Home page as the homepage and Latest as the posts page', 'pixelgrid' ),
	),
	'design_note'    => __( 'This replaces your Styles with the News colours and fonts, and changes the site\'s header, footer and the templates for the posts page, single articles and category pages. Your current ones are saved and put back when you remove the starter content. Applying again only changes what you have not edited since setup, and edits you make after setup are kept on removal.', 'pixelgrid' ),
	'style'          => 'styles/news-portal.json',
	'parts'          => array(
		'header' => 'pixelgrid/news-header',
		'footer' => 'pixelgrid/news-footer',
	),
	'templates'      => array(
		'home'     => 'pixelgrid/news-template-home',
		'single'   => 'pixelgrid/news-template-single',
		'category' => 'pixelgrid/news-template-category',
	),
	'pages'          => array(
		'home'    => array( __( 'Home', 'pixelgrid' ), 'pixelgrid/news-page-home', 'page-sections' ),
		'about'   => array( __( 'About', 'pixelgrid' ), 'pixelgrid/news-page-about', 'page-sections' ),
		'contact' => array( __( 'Contact', 'pixelgrid' ), 'pixelgrid/news-page-contact', 'page-sections' ),
		'tips'    => array( __( 'Tips', 'pixelgrid' ), 'pixelgrid/news-page-tips', 'page-sections' ),
		'latest'  => array( __( 'Latest', 'pixelgrid' ), '', '' ),
	),
	'front'          => 'home',
	'posts_page'     => 'latest',
	'menu'           => array( 'cat:world', 'cat:business', 'cat:technology', 'cat:culture', 'cat:sport', 'about', 'tips' ),
	'categories'     => array(
		'world'      => __( 'World', 'pixelgrid' ),
		'business'   => __( 'Business', 'pixelgrid' ),
		'technology' => __( 'Technology', 'pixelgrid' ),
		'culture'    => __( 'Culture', 'pixelgrid' ),
		'sport'      => __( 'Sport', 'pixelgrid' ),
		'opinion'    => __( 'Opinion', 'pixelgrid' ),
		'most-read'  => __( 'Most read', 'pixelgrid' ),
	),
	'posts'          => array(
		array(
			'key'      => 'council-budget-libraries',
			'title'    => __( 'Council passes a budget that keeps every library branch open', 'pixelgrid' ),
			'excerpt'  => __( 'Councillors voted 31 to 18 for a spending plan that closes a gap of 4.2 million euros without shutting a single branch.', 'pixelgrid' ),
			'category' => array( 'business', 'most-read' ),
			'tags'     => array( __( 'City council', 'pixelgrid' ), __( 'Budget', 'pixelgrid' ), __( 'Libraries', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-01.webp',
			'content'  => $pixelgrid_p( __( 'The city council approved next year\'s budget late on Tuesday after a debate that ran for almost five hours. The final vote was 31 in favour and 18 against.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The plan closes a gap of 4.2 million euros by delaying two road resurfacing projects, raising parking charges in the centre by 20 euro cents an hour and freezing hiring in the planning office until spring.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'What changed since the draft', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'An earlier draft would have closed the Eastbank and Mill Road library branches. After more than 2,000 written comments, the finance committee found the money elsewhere, mostly from the road budget.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'We heard from parents, pensioners and students. The libraries are where this city does its homework, so they stay open.', 'pixelgrid' ), __( 'Marta Ilić, chair of the finance committee', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Opposition councillors said the delayed roadworks only move the cost to the following year. They proposed a smaller rise in parking charges, which was defeated.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The new charges start on the first of next month. The full budget is published on the council website and in the reading room of the central library.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'ferry-winter-timetable',
			'title'    => __( 'Cross-strait ferry adds a late sailing to its winter timetable', 'pixelgrid' ),
			'excerpt'  => __( 'From December the last boat to the far shore leaves Pier 2 at 22:40, an hour later than last winter.', 'pixelgrid' ),
			'category' => array( 'world' ),
			'tags'     => array( __( 'Ferry', 'pixelgrid' ), __( 'Transport', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-02.webp',
			'content'  => $pixelgrid_p( __( 'The ferry company that runs the crossing to the far shore will keep a late boat on its winter timetable for the first time in six years.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The new sailing leaves Pier 2 at 22:40 from Thursday to Sunday and returns at 23:30. On other nights the last boat stays at 21:40.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Why the late boat came back', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Restaurant and hospital staff who live across the strait had asked for it since the timetable was cut. A survey by the port authority found that one in five weekend passengers travels after nine in the evening.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'I finish my shift at ten. Until now that meant a hotel or a two-hour drive round the bay.', 'pixelgrid' ), __( 'A night nurse at the harbour hospital', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The company says the extra sailing is a trial until the end of March. If the boats are less than a third full, it will be dropped again next winter.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'heat-pump-startup-funding',
			'title'    => __( 'Riverside startup that repairs heat pumps raises its first funding', 'pixelgrid' ),
			'excerpt'  => __( 'Four engineers who started in a rented garage now have money to hire eight more and open a second workshop.', 'pixelgrid' ),
			'category' => array( 'technology', 'most-read' ),
			'tags'     => array( __( 'Startups', 'pixelgrid' ), __( 'Energy', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-03.webp',
			'content'  => $pixelgrid_p( __( 'Coilwork, a company that diagnoses and repairs domestic heat pumps, has raised 1.5 million euros from a group of local investors and the regional development fund.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The firm began two years ago in a garage on Riverside Lane. Its four founders built a small sensor that clips onto a heat pump and reports faults to a technician before the house goes cold.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Eight new jobs this year', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The money will pay for eight more technicians and a second workshop in the old tram depot. The company says it will train apprentices from the city\'s technical college.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'Thousands of heat pumps went in during the last three years. Very few people know how to fix them when they stop.', 'pixelgrid' ), __( 'Ines Brandt, co-founder', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Coilwork now looks after about 1,100 homes. It plans to reach 4,000 by the end of next year.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'eastbank-library-opens',
			'title'    => __( 'New Eastbank library opens with a workshop and a night reading room', 'pixelgrid' ),
			'excerpt'  => __( 'The branch in the former bus garage lends tools as well as books and stays open until ten on weekdays.', 'pixelgrid' ),
			'category' => array( 'culture', 'most-read' ),
			'tags'     => array( __( 'Libraries', 'pixelgrid' ), __( 'Eastbank', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-04.webp',
			'content'  => $pixelgrid_p( __( 'Several hundred people queued outside the old bus garage on Saturday morning for the opening of the new Eastbank library.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The building keeps the garage\'s steel roof and big doors. Inside there are 40,000 books, a children\'s floor, a recording booth and a workshop that lends drills, sewing machines and ladders.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Open late on weekdays', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The reading room on the first floor stays open until ten on weekdays, staffed by two librarians and a security guard. It is the first branch in the city with evening hours.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'Students told us they had nowhere quiet to work after the cafes close. So we kept the lights on.', 'pixelgrid' ), __( 'Tomas Reyes, branch manager', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Library cards are free for anyone who lives, works or studies in the city. The tool library needs a card and a deposit of 10 euros.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'harbour-fc-promotion',
			'title'    => __( 'Harbour FC win promotion with a stoppage-time header', 'pixelgrid' ),
			'excerpt'  => __( 'A 2-1 win over Northfield in front of a record crowd lifts the club into the second division for the first time.', 'pixelgrid' ),
			'category' => array( 'sport', 'most-read' ),
			'tags'     => array( __( 'Football', 'pixelgrid' ), __( 'Harbour FC', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-05.webp',
			'content'  => $pixelgrid_p( __( 'Harbour FC are going up. Centre back Dani Okafor headed in a corner in the fourth minute of stoppage time to beat Northfield 2-1 at the Quay Ground on Sunday.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'A crowd of 6,240, the largest in the club\'s 92-year history, saw Northfield take the lead just before half time. Winger Sami Laine equalised from the edge of the box after an hour.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'A club that nearly folded', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Five years ago the club was close to bankruptcy and played in front of a few hundred people. A supporters\' trust now owns a majority of the shares.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'Half of this squad have day jobs. They trained at seven in the evening all winter, in the rain, for this.', 'pixelgrid' ), __( 'Lena Varga, head coach', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The club must add 800 seats to the Quay Ground before the new season to meet second-division rules. The trust will present its plan to members next week.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'twin-city-night-market',
			'title'    => __( 'Our twin city showed us how to run a night market', 'pixelgrid' ),
			'excerpt'  => __( 'Across the strait, a weekly market fills an empty square every Friday, and there is no reason ours cannot do the same.', 'pixelgrid' ),
			'category' => array( 'world', 'opinion' ),
			'tags'     => array( __( 'Twin city', 'pixelgrid' ), __( 'Markets', 'pixelgrid' ), __( 'Column', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-06.webp',
			'content'  => $pixelgrid_p( __( 'Last month I took the late ferry to our twin city to see the Friday night market that everyone over there talks about. I came back convinced we should copy it.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The market takes over a car park beside the old customs house from six until midnight. There are forty stalls, a stage for school bands and long tables where strangers share food.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'What it would take here', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'We have the space: Pier Square sits empty every evening once the offices close. We have the traders, who already run a market there on Saturday mornings. What we lack is a permit that allows it after dark.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'The first year we lost money. The second year the bars around the square asked us to add a second night.', 'pixelgrid' ), __( 'The market\'s organiser across the strait', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The council\'s events committee meets next month. A one-summer trial would cost the city very little, and we would know by September whether it works.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'market-hall-rents',
			'title'    => __( 'Market hall traders win a two-year freeze on rents', 'pixelgrid' ),
			'excerpt'  => __( 'The council agreed to hold stall rents at this year\'s level while the hall\'s roof is repaired.', 'pixelgrid' ),
			'category' => array( 'business' ),
			'tags'     => array( __( 'Market hall', 'pixelgrid' ), __( 'Small business', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-07.webp',
			'content'  => $pixelgrid_p( __( 'The 64 traders in the central market hall will pay the same rent for the next two years, after the council dropped a planned rise of 12 percent.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The traders\' association had argued that repairs to the leaking roof would close parts of the hall for months and cut their takings.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Repairs start in January', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Work on the roof starts in the second week of January and runs in three phases, so that no more than a third of the stalls are closed at once. Displaced traders will move to temporary stands in the courtyard.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'A frozen rent will not make us rich. It means we can plan, and keep the same people working the counter.', 'pixelgrid' ), __( 'Pavel Novak, cheese seller and chair of the traders\' association', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The council will review rents again once the roof is finished.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'bus-app-live-times',
			'title'    => __( 'City buses now show live arrival times at every stop', 'pixelgrid' ),
			'excerpt'  => __( 'Small solar screens have replaced the printed timetables at 410 stops, and the same times appear in the transit app.', 'pixelgrid' ),
			'category' => array( 'technology' ),
			'tags'     => array( __( 'Buses', 'pixelgrid' ), __( 'Transport', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-08.webp',
			'content'  => $pixelgrid_p( __( 'Waiting for a bus in the city should involve less guessing from this week. The transit authority has finished fitting live arrival screens at all 410 of its stops.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The screens run on small solar panels and show the next three buses on each route, updated every 20 seconds from a tracker on each vehicle.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Built by the city\'s own team', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The software was written by a team of five inside the transit authority rather than bought in. The authority says that saved about 300,000 euros a year in licence fees.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'The data was always there. We just had never put it on the pole where people are standing.', 'pixelgrid' ), __( 'Head of digital services, city transit authority', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Riders can report a broken screen by scanning the code on its side. The authority aims to fix faults within two working days.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'lantern-theatre-season',
			'title'    => __( 'Lantern Theatre opens its season with a play written by local pupils', 'pixelgrid' ),
			'excerpt'  => __( 'Twelve teenagers from three schools wrote the opening production over a year of Saturday workshops.', 'pixelgrid' ),
			'category' => array( 'culture' ),
			'tags'     => array( __( 'Theatre', 'pixelgrid' ), __( 'Schools', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-09.webp',
			'content'  => $pixelgrid_p( __( 'The Lantern Theatre will open its autumn season with The Last Tram, a play written by twelve pupils aged 14 to 17 from three schools in the north of the city.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The play follows a driver on the final night of the old tram line in 1968. The pupils based it on interviews with their grandparents and on photographs from the city archive.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'A year of Saturdays', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The writers met every Saturday morning for a year with the theatre\'s resident playwright. Professional actors will perform it, with two of the pupils in small roles.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'They argued about every line. That is exactly what a writers\' room should sound like.', 'pixelgrid' ), __( 'Resident playwright at the Lantern Theatre', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The Last Tram runs for three weeks from the 14th. Tickets for under-18s cost 5 euros.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'harbour-rowing-regatta',
			'title'    => __( 'Rowing regatta returns to the inner harbour after nine years', 'pixelgrid' ),
			'excerpt'  => __( 'Cleaner water and a new pontoon mean 300 rowers will race between the bridges on Saturday.', 'pixelgrid' ),
			'category' => array( 'sport' ),
			'tags'     => array( __( 'Rowing', 'pixelgrid' ), __( 'Harbour', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-10.webp',
			'content'  => $pixelgrid_p( __( 'The harbour regatta is back. About 300 rowers from 22 clubs will race a 500-metre course between the Swing Bridge and the Iron Bridge on Saturday.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The event stopped nine years ago when tests showed poor water quality in the inner harbour. Since then, two storm overflows have been closed and the water now passes the bathing standard most weeks.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Where to watch', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The best views are from the quay steps beside the maritime museum. Heats begin at nine and the finals start at three. The Swing Bridge will stay closed to traffic for the day.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'My father rowed in the last one. I will be in the same lane, nine years later.', 'pixelgrid' ), __( 'A junior rower with the Quayside club', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Entry for spectators is free. Food stalls will be set up along the north quay.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'research-ship-pier-four',
			'title'    => __( 'Polar research ship docks at Pier 4 for a month of repairs', 'pixelgrid' ),
			'excerpt'  => __( 'The crew of 40 from six countries will open the ship to visitors on two weekends before it sails north again.', 'pixelgrid' ),
			'category' => array( 'world', 'most-read' ),
			'tags'     => array( __( 'Harbour', 'pixelgrid' ), __( 'Science', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-11.webp',
			'content'  => $pixelgrid_p( __( 'A red-hulled research ship arrived at Pier 4 on Wednesday after eight months in polar waters. It will spend a month in the city for repairs to its propeller and hull.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The ship carries a crew of 40 scientists and sailors from six countries. They have been measuring sea ice and collecting water samples along the edge of the ice shelf.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Open days for visitors', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The ship will be open to the public on the next two weekends from ten until four. Visitors can see the laboratories, the bridge and the ice cores kept in the freezer hold.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'Your shipyard is one of the few in the region that can lift a hull this size. We will be good customers of your cafes too.', 'pixelgrid' ), __( 'The ship\'s captain', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Places on the tours are free but limited to 400 a day. They can be booked at the harbour office on the quay.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'loading-bays-opinion',
			'title'    => __( 'Small shops need their loading bays more than we need parking', 'pixelgrid' ),
			'excerpt'  => __( 'Turning delivery bays on the high street into parking spaces would cost shopkeepers far more than it saves drivers.', 'pixelgrid' ),
			'category' => array( 'business', 'opinion' ),
			'tags'     => array( __( 'High street', 'pixelgrid' ), __( 'Small business', 'pixelgrid' ), __( 'Column', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-12.webp',
			'content'  => $pixelgrid_p( __( 'The council wants to turn eleven loading bays on the high street into short-stay parking. On paper it sounds like help for shops. In practice it would make their mornings much harder.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Most of the shops on the street are too small to have a back entrance. Their stock comes through the front door, from a van that needs to stop right outside for ten minutes.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Where the vans would go', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Without the bays, delivery drivers will stop in the bus lane or on the pavement, as they already do on Mill Road. That slows the buses and blocks pushchairs and wheelchairs.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'I get four deliveries before nine. If the van cannot stop, the bread does not arrive.', 'pixelgrid' ), __( 'Owner of a bakery on the high street', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'A better plan is to keep the bays for deliveries until eleven and open them to cars for the rest of the day. Several streets in the old town already work this way.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'school-laptops-repair',
			'title'    => __( 'Pupils learn to repair the school laptops they used to throw away', 'pixelgrid' ),
			'excerpt'  => __( 'A repair club at Northgate Secondary has fixed 600 old laptops this year and given them to families who need one.', 'pixelgrid' ),
			'category' => array( 'technology' ),
			'tags'     => array( __( 'Schools', 'pixelgrid' ), __( 'Repair', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-13.webp',
			'content'  => $pixelgrid_p( __( 'In a classroom at Northgate Secondary, a dozen pupils spend their Wednesday afternoons taking laptops apart. Most of the machines were heading for the skip.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The repair club started last year when the school replaced its old laptops. A computing teacher asked whether the pupils could save some of them instead.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Six hundred machines so far', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Since then the club has fixed about 600 laptops, mostly by replacing batteries, keyboards and hard drives. The finished machines go to families referred by the city\'s social services.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'The first one took me three weeks. Now I can swap a screen in twenty minutes.', 'pixelgrid' ), __( 'A Year 10 pupil in the repair club', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Two other secondary schools plan to start clubs of their own next term, using spare parts collected by the city recycling centre.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'mural-festival-walls',
			'title'    => __( 'Mural festival will paint twenty walls in the old dock streets', 'pixelgrid' ),
			'excerpt'  => __( 'Artists picked by residents will spend ten days painting the blank walls of warehouses and flats around Dock Lane.', 'pixelgrid' ),
			'category' => array( 'culture' ),
			'tags'     => array( __( 'Art', 'pixelgrid' ), __( 'Festivals', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-14.webp',
			'content'  => $pixelgrid_p( __( 'Twenty blank walls in the streets around Dock Lane will be covered in murals next month, in the first festival of its kind in the city.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Residents chose the artists from 140 proposals in a public vote held at the community centre. Eight of the twenty artists live in the city.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Owners had the final say', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Every building owner approved the design for their wall before it was confirmed. Two proposals were changed after tenants asked for brighter colours.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'The street has been grey my whole life. My kids will grow up next to a whale.', 'pixelgrid' ), __( 'A resident of Dock Lane', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Painting runs for ten days from the 3rd. Guided walks start from the community centre every evening at six.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'city-half-marathon',
			'title'    => __( 'City half marathon moves to a flatter route along the river', 'pixelgrid' ),
			'excerpt'  => __( 'Organisers dropped the climb up Castle Hill after two hot years, and expect faster times and more first-time runners.', 'pixelgrid' ),
			'category' => array( 'sport' ),
			'tags'     => array( __( 'Running', 'pixelgrid' ), __( 'Half marathon', 'pixelgrid' ) ),
			'image'    => 'assets/images/news-15.webp',
			'content'  => $pixelgrid_p( __( 'The city half marathon will follow a new route this spring, staying beside the river for almost the whole 21 kilometres.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The old course climbed Castle Hill twice. In the last two years, hot weather on race day led to more than 60 runners being treated by medics on the hill.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Fewer road closures', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Because most of the new route uses the riverside path, only four roads will close on race morning instead of eleven. Buses will run as normal after ten.', 'pixelgrid' ) )
				. $pixelgrid_q( __( 'We want this to be a race people finish with a smile, whether it takes them ninety minutes or three hours.', 'pixelgrid' ), __( 'Race director', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Entries open on the first of the month and are capped at 5,000. Places for local running clubs are held back until the week before.', 'pixelgrid' ) ),
		),
	),
	'preview'        => '',
);
