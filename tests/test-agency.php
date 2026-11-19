<?php
/**
 * Agency look tests: style variation, contrast and display font.
 */
require_once __DIR__ . '/lib/contrast.php';

$theme_json = json_decode( file_get_contents( get_parent_theme_file_path( 'theme.json' ) ), true );
$it_palette = $theme_json['settings']['color']['palette'];
$agency_raw = file_exists( get_parent_theme_file_path( 'styles/agency.json' ) ) ? file_get_contents( get_parent_theme_file_path( 'styles/agency.json' ) ) : false;
$agency     = false === $agency_raw ? null : json_decode( $agency_raw, true );

pg_assert( is_array( $agency ), 'styles/agency.json exists and decodes' );
$agency = is_array( $agency ) ? $agency : array();
pg_assert_same( 3, $agency['version'] ?? null, 'agency is version 3' );
pg_assert_same( 'Agency', $agency['title'] ?? null, 'agency is titled Agency' );

$agency_palette = $agency['settings']['color']['palette'] ?? array();
$names_of       = function ( $palette ) {
	$out = array();
	foreach ( $palette as $entry ) {
		$out[ $entry['slug'] ] = $entry['name'];
	}
	ksort( $out );
	return $out;
};
pg_assert_same( 28, count( $it_palette ), 'theme.json has 28 palette slugs' );
pg_assert_same( $names_of( $it_palette ), $names_of( $agency_palette ), 'agency has the 28 theme.json slugs with the same names and no others' );
foreach ( $agency_palette as $entry ) {
	pg_assert( (bool) preg_match( '/^#[0-9A-Fa-f]{6}$/', $entry['color'] ?? '' ), "agency {$entry['slug']} is a hex colour" );
}

foreach ( array( 'Agency' => $agency_palette, 'IT' => $it_palette ) as $label => $palette ) {
	foreach ( pixelgrid_test_palette_pairs( $palette ) as $pair ) {
		list( $tone, $text_ratio, $hover_ratio ) = $pair;
		printf( "INFO: %s %s text/bg %.2f on-hover/hover %.2f\n", $label, $tone, $text_ratio, $hover_ratio );
		pg_assert( $text_ratio >= 4.5, "$label $tone text on background reaches 4.5:1" );
		pg_assert( $hover_ratio >= 4.5, "$label $tone text on hover reaches 4.5:1" );
	}
}

pg_assert_same( 'var:preset|font-family|display', $agency['styles']['elements']['heading']['typography']['fontFamily'] ?? null, 'agency headings use the display family' );
pg_assert( false === strpos( (string) $agency_raw, 'fontFace' ), 'agency variation declares no fontFace' );

// The display face is far wider than the IT condensed face, so the theme.json heading sizes
// break words in Agency. Agency sets its own h1 and h2 sizes in grid units, small enough that the
// longest heading word fits a half-width column (8 units) at every width the unit scales with.
foreach ( array( 'h1' => 1.9, 'h2' => 1.7 ) as $pg_level => $pg_max ) {
	$pg_size = $agency['styles']['elements'][ $pg_level ]['typography']['fontSize'] ?? '';
	$pg_line = $agency['styles']['elements'][ $pg_level ]['typography']['lineHeight'] ?? '';
	pg_assert( '' !== $pg_size, "agency sets an $pg_level font size" );
	pg_assert( $pg_size !== ( $theme_json['styles']['elements'][ $pg_level ]['typography']['fontSize'] ?? '' ), "agency $pg_level font size differs from theme.json" );
	pg_assert( '' !== $pg_line && $pg_line !== ( $theme_json['styles']['elements'][ $pg_level ]['typography']['lineHeight'] ?? '' ), "agency $pg_level line height is its own" );
	pg_assert( (bool) preg_match( '/^calc\(var\(--pixelgrid-u\) \* ([0-9.]+)\)$/', $pg_size, $pg_m ) && (float) $pg_m[1] <= $pg_max, "agency $pg_level size is at most $pg_max grid units" );
}
// Patterns pick the Headline and Huge presets by class, which beat the element sizes, so Agency
// redefines those presets too.
$pg_agency_sizes = array();
foreach ( (array) ( $agency['settings']['typography']['fontSizes'] ?? array() ) as $pg_entry ) {
	$pg_agency_sizes[ $pg_entry['slug'] ] = $pg_entry['size'];
}
pg_assert_same( $agency['styles']['elements']['h2']['typography']['fontSize'] ?? null, $pg_agency_sizes['xx-large'] ?? '', 'agency Headline preset matches its h2 size' );
pg_assert( false !== strpos( $pg_agency_sizes['huge'] ?? '', 'vw' ), 'agency Huge preset is capped by the window width for phones' );
// The hero heading sits in a half-width column; at 1.9 units a word of more than seven letters
// does not fit there in the display face.
preg_match( '#<h1[^>]*>(.*?)</h1>#s', pixelgrid_starter_expand( '<!-- wp:pattern {"slug":"pixelgrid/agency-hero"} /-->' ), $pg_hero_h1 );
$pg_longest = 0;
foreach ( preg_split( '/\s+/', wp_strip_all_tags( $pg_hero_h1[1] ?? '' ) ) as $pg_word ) {
	$pg_longest = max( $pg_longest, strlen( preg_replace( '/[^A-Za-z]/', '', $pg_word ) ) );
}
pg_assert( $pg_longest > 0 && $pg_longest <= 7, "agency hero heading words are at most 7 letters (longest: $pg_longest)" );

$families = wp_get_global_settings( array( 'typography', 'fontFamilies', 'theme' ) );
$display  = null;
foreach ( (array) $families as $family ) {
	if ( 'display' === ( $family['slug'] ?? '' ) ) {
		$display = $family;
	}
}
pg_assert( is_array( $display ), 'theme font families include display' );
pg_assert_same( 'Display', $display['name'] ?? null, 'display family is named Display' );
$faces = $display['fontFace'] ?? array();
pg_assert( count( $faces ) >= 2, 'display family has latin and latin-ext faces' );
foreach ( $faces as $face ) {
	foreach ( (array) $face['src'] as $src ) {
		$rel = 0 === strpos( $src, 'file:./' ) ? substr( $src, 7 ) : null;
		if ( null === $rel ) {
			$theme_uri = trailingslashit( get_template_directory_uri() );
			$rel       = 0 === strpos( $src, $theme_uri ) ? substr( $src, strlen( $theme_uri ) ) : $src;
		}
		pg_assert( file_exists( get_parent_theme_file_path( $rel ) ), "display font file $rel exists" );
	}
}

$titles = array();
foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
	$titles[] = $variation['title'] ?? '';
}
pg_assert( in_array( 'Agency', $titles, true ), 'style variations list Agency' );

// Agency images: generated plates exist, are valid 1200x1200 WebP and stay small.
for ( $i = 1; $i <= 12; $i++ ) {
	$img_rel  = sprintf( 'assets/images/agency-%02d.webp', $i );
	$img_path = get_parent_theme_file_path( $img_rel );
	pg_assert( file_exists( $img_path ), "$img_rel exists" );
	$img_info = file_exists( $img_path ) ? getimagesize( $img_path ) : false;
	pg_assert( is_array( $img_info ) && 'image/webp' === $img_info['mime'], "$img_rel is a valid WebP" );
	pg_assert( is_array( $img_info ) && 1200 === $img_info[0] && 1200 === $img_info[1], "$img_rel is 1200x1200" );
	pg_assert( file_exists( $img_path ) && filesize( $img_path ) <= 150 * 1024, "$img_rel is at most 150 KB" );
}

// Agency images keep real mid-tone variation (not washed out, not near-black).
for ( $i = 1; $i <= 12; $i++ ) {
	$img_rel  = sprintf( 'assets/images/agency-%02d.webp', $i );
	$img_path = get_parent_theme_file_path( $img_rel );
	$src      = file_exists( $img_path ) ? imagecreatefromwebp( $img_path ) : false;
	$sd       = 0;
	if ( $src ) {
		$small = imagecreatetruecolor( 100, 100 );
		imagecopyresampled( $small, $src, 0, 0, 0, 0, 100, 100, 1200, 1200 );
		$vals = array();
		for ( $py = 0; $py < 100; $py++ ) {
			for ( $px = 0; $px < 100; $px++ ) {
				$vals[] = imagecolorat( $small, $px, $py ) & 0xFF;
			}
		}
		$mean = array_sum( $vals ) / count( $vals );
		$var  = 0;
		foreach ( $vals as $val ) {
			$var += ( $val - $mean ) ** 2;
		}
		$sd = sqrt( $var / count( $vals ) );
	}
	pg_assert( $sd >= 35, sprintf( '%s luminance stddev is at least 35 (got %.1f)', $img_rel, $sd ) );
}

// Agency patterns: registered, expandable, valid markup, real images, no database ids.
$agency_slugs = array(
	'agency-header',
	'agency-footer',
	'agency-hero',
	'agency-work-grid',
	'agency-capabilities',
	'agency-clients',
	'agency-process',
	'agency-journal-rows',
	'agency-cta',
	'agency-page-home',
	'agency-page-work',
	'agency-page-services',
	'agency-page-studio',
	'agency-page-contact',
	'agency-case-study',
	'agency-case-1',
	'agency-case-2',
	'agency-case-3',
	'agency-case-4',
	'agency-case-5',
	'agency-case-6',
	'agency-blog-heading',
	'agency-template-home',
);
$registry     = WP_Block_Patterns_Registry::get_instance();
$theme_uri    = trailingslashit( get_template_directory_uri() );
$expanded     = array();
foreach ( $agency_slugs as $slug ) {
	$pattern = $registry->get_registered( 'pixelgrid/' . $slug );
	pg_assert( is_array( $pattern ), "pixelgrid/$slug is registered" );
	$content = is_array( $pattern ) ? pixelgrid_starter_expand( $pattern['content'] ) : '';
	$expanded[ $slug ] = $content;
	pg_assert( '' !== trim( $content ), "pixelgrid/$slug has expanded content" );
	pg_assert( false === strpos( $content, '<!-- wp:pattern' ), "pixelgrid/$slug has no unexpanded pattern refs" );
	pg_assert( ! preg_match( '/"(id|ref)":\s*\d/', $content ), "pixelgrid/$slug has no numeric id or ref attribute" );
	pg_assert( false === stripos( $content, 'notes' ), "pixelgrid/$slug never says Notes" );

	$junk  = array();
	$walk  = function ( $blocks ) use ( &$walk, &$junk ) {
		foreach ( $blocks as $block ) {
			if ( null === $block['blockName'] && '' !== trim( $block['innerHTML'] ) ) {
				$junk[] = trim( $block['innerHTML'] );
			}
			$walk( $block['innerBlocks'] );
		}
	};
	$walk( parse_blocks( $content ) );
	pg_assert( array() === $junk, "pixelgrid/$slug parses with no freeform junk between blocks" . ( $junk ? ': ' . substr( $junk[0], 0, 80 ) : '' ) );

	preg_match_all( '/<img[^>]+src="([^"]+)"/', $content, $imgs );
	foreach ( $imgs[1] as $src ) {
		$rel = 0 === strpos( $src, $theme_uri ) ? substr( $src, strlen( $theme_uri ) ) : $src;
		pg_assert( 0 === strpos( $rel, 'assets/images/agency-' ) && file_exists( get_parent_theme_file_path( $rel ) ), "pixelgrid/$slug image $rel is an Agency theme file" );
	}
}

$agency_cats = WP_Block_Pattern_Categories_Registry::get_instance()->get_registered( 'pixelgrid-agency' );
pg_assert_same( 'Pixelgrid Agency', $agency_cats['label'] ?? null, 'pattern category pixelgrid-agency is labelled Pixelgrid Agency' );

pg_assert( false !== strpos( $expanded['agency-header'], 'Start a project' ), 'agency header has a Start a project button' );
pg_assert( false !== strpos( $expanded['agency-header'], esc_url( add_query_arg( 'pagename', 'contact', home_url( '/' ) ) ) ), 'agency header button links to contact' );
pg_assert( false !== strpos( $expanded['agency-footer'], 'Have a project in mind?' ), 'agency footer has the closing line' );
pg_assert( false !== strpos( $expanded['agency-blog-heading'], 'Journal' ), 'agency blog heading says Journal' );
pg_assert( false !== strpos( $expanded['agency-template-home'], '"slug":"header"' ) && false !== strpos( $expanded['agency-template-home'], '"slug":"footer"' ), 'agency home template has header and footer parts' );
pg_assert( false !== strpos( $expanded['agency-template-home'], 'Journal' ), 'agency home template includes the Journal heading' );
pg_assert( false !== strpos( $expanded['agency-hero'], 'is-style-mosaic-cycle' ), 'agency hero has a changing mosaic' );
pg_assert_same( 4, substr_count( $expanded['agency-process'], '<!-- wp:column -->' ), 'agency process has 4 steps' );
pg_assert( false !== strpos( $expanded['agency-journal-rows'], '"perPage":3' ), 'agency journal rows show the latest 3 posts' );

$case_slugs = array();
preg_match_all( '/pagename=work(?:\/|%2F)([a-z0-9-]+)/', $expanded['agency-work-grid'], $work_links );
$case_slugs = array_values( array_unique( $work_links[1] ) );
pg_assert_same( 6, count( $case_slugs ), 'agency work grid links 6 distinct case studies' );
$case_images = array();
for ( $n = 1; $n <= 6; $n++ ) {
	$case = $expanded[ 'agency-case-' . $n ];
	preg_match( '/assets\/images\/(agency-\d\d)\.webp/', $case, $img );
	$case_images[] = $img[1] ?? '';
	$next = $case_slugs[ $n % 6 ] ?? 'missing';
	pg_assert( false !== strpos( $case, esc_url( add_query_arg( 'pagename', 'work/' . $next, home_url( '/' ) ) ) ), "agency case $n links to the next case study ($next)" );
	pg_assert( false !== strpos( $case, 'wp:pullquote' ), "agency case $n has a pull quote" );
	pg_assert( false !== strpos( $case, 'is-style-stat' ), "agency case $n has results stats" );
}
pg_assert_same( 6, count( array_unique( array_filter( $case_images ) ) ), 'each agency case study has its own image' );
foreach ( array( 'agency-page-home', 'agency-page-work', 'agency-page-services', 'agency-page-studio', 'agency-page-contact', 'agency-case-study' ) as $slug ) {
	$pattern = $registry->get_registered( 'pixelgrid/' . $slug );
	pg_assert( in_array( 'pixelgrid-pages', $pattern['categories'] ?? array(), true ), "pixelgrid/$slug is in Pixelgrid pages" );
	pg_assert( in_array( 'core/post-content', $pattern['blockTypes'] ?? array(), true ), "pixelgrid/$slug is a post-content pattern" );
	pg_assert_same( array( 'page' ), $pattern['postTypes'] ?? null, "pixelgrid/$slug is for pages" );
}
foreach ( array( 'agency-case-1', 'agency-blog-heading', 'agency-template-home' ) as $slug ) {
	$pattern = $registry->get_registered( 'pixelgrid/' . $slug );
	pg_assert( isset( $pattern['inserter'] ) && false === $pattern['inserter'], "pixelgrid/$slug is hidden from the inserter" );
}

// Agency variant: definition, end-to-end setup and removal.
$pg_agency = pixelgrid_variant( 'agency' );
pg_assert( is_array( $pg_agency ), 'agency variant exists' );
pg_assert( is_array( $pg_agency ) && pixelgrid_variant_available( $pg_agency ), 'agency variant is available' );
$pg_agency = is_array( $pg_agency ) ? $pg_agency : pixelgrid_variant_defaults( array() );
pg_assert_same( 'Agency', $pg_agency['label'], 'agency is labelled Agency' );
pg_assert_same( 'styles/agency.json', $pg_agency['style'], 'agency uses styles/agency.json' );
pg_assert_same(
	array(
		'header' => 'pixelgrid/agency-header',
		'footer' => 'pixelgrid/agency-footer',
	),
	$pg_agency['parts'],
	'agency parts are its header and footer'
);
pg_assert_same( array( 'home' => 'pixelgrid/agency-template-home' ), $pg_agency['templates'], 'agency home template' );
pg_assert_same( 'home', $pg_agency['front'], 'agency front page is home' );
pg_assert_same( 'journal', $pg_agency['posts_page'], 'agency posts page is journal' );
pg_assert_same( array( 'work', 'services', 'studio', 'journal', 'contact' ), $pg_agency['menu'], 'agency menu order' );
pg_assert_same( array( 'process', 'design', 'technology', 'culture' ), array_keys( $pg_agency['categories'] ), 'agency categories' );
pg_assert_same( '', $pg_agency['preview'], 'agency preview is empty until Task 7' );
foreach ( array( 'pages', 'posts', 'reading' ) as $pg_key ) {
	pg_assert( ! empty( $pg_agency['content_labels'][ $pg_key ] ), "agency has content label $pg_key" );
}
pg_assert( '' !== $pg_agency['design_note'], 'agency has a design note' );
foreach ( array( 'home', 'work', 'services', 'studio', 'contact' ) as $pg_slug ) {
	$pg_page = $pg_agency['pages'][ $pg_slug ] ?? array();
	pg_assert_same( 'pixelgrid/agency-page-' . $pg_slug, $pg_page[1] ?? null, "page $pg_slug uses agency-page-$pg_slug" );
	pg_assert_same( 'page-sections', $pg_page[2] ?? null, "page $pg_slug uses page-sections" );
}
pg_assert_same( '', $pg_agency['pages']['journal'][1] ?? null, 'journal page is empty' );
pg_assert_same( '', $pg_agency['pages']['journal'][2] ?? null, 'journal page has no template' );
$pg_case_slugs = array( 'northline-rebrand', 'tidewater-museum', 'copperleaf-packaging', 'meridian-booking-app', 'foxglove-festival', 'quarry-lane-wayfinding' );
foreach ( $pg_case_slugs as $pg_i => $pg_slug ) {
	$pg_page = $pg_agency['pages'][ $pg_slug ] ?? array();
	pg_assert_same( 'pixelgrid/agency-case-' . ( $pg_i + 1 ), $pg_page[1] ?? null, "case $pg_slug uses agency-case-" . ( $pg_i + 1 ) );
	pg_assert_same( 'page-sections', $pg_page[2] ?? null, "case $pg_slug uses page-sections" );
	pg_assert_same( 'work', $pg_page[3] ?? null, "case $pg_slug is a child of work" );
}
pg_assert_same( 12, count( $pg_agency['pages'] ), 'agency has 12 pages' );
pg_assert_same( 6, count( $pg_agency['posts'] ), 'agency has 6 posts' );
$pg_post_images = array();
foreach ( $pg_agency['posts'] as $pg_post ) {
	$pg_post_images[] = $pg_post['image'];
	pg_assert( isset( $pg_agency['categories'][ $pg_post['category'] ] ), "post {$pg_post['key']} has an agency category" );
	pg_assert( false === stripos( $pg_post['content'] . $pg_post['title'] . $pg_post['excerpt'], 'notes' ), "post {$pg_post['key']} never says notes" );
	$pg_junk = array_filter(
		parse_blocks( $pg_post['content'] ),
		function ( $block ) {
			return null === $block['blockName'] && '' !== trim( $block['innerHTML'] );
		}
	);
	pg_assert( array() === $pg_junk, "post {$pg_post['key']} is clean block markup" );
}
sort( $pg_post_images );
$pg_expected_images = array();
for ( $pg_i = 7; $pg_i <= 12; $pg_i++ ) {
	$pg_expected_images[] = sprintf( 'assets/images/agency-%02d.webp', $pg_i );
}
pg_assert_same( $pg_expected_images, $pg_post_images, 'agency posts use images agency-07 to agency-12' );
pg_assert_same( 4, count( array_unique( wp_list_pluck( $pg_agency['posts'], 'category' ) ) ), 'agency posts cover all 4 categories' );

$pg_theme         = get_stylesheet();
$pg_header_block  = '<!-- wp:template-part {"slug":"header","theme":"' . $pg_theme . '"} /-->';
$pg_styles_id     = (int) WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true )['ID'];
$pg_styles_before = get_post( $pg_styles_id )->post_content;
$pg_page_count    = function () {
	return (int) wp_count_posts( 'page' )->publish;
};

$pg_all  = array(
	'design'  => true,
	'pages'   => true,
	'posts'   => true,
	'menu'    => true,
	'reading' => true,
);
$pg_made = pixelgrid_starter_create( 'agency', $pg_all );
pg_assert_same(
	array(
		'pages' => 12,
		'posts' => 6,
		'menu'  => 1,
	),
	$pg_made,
	'agency create makes 12 pages, 6 posts and 1 menu'
);
$pg_work = pixelgrid_starter_find( 'page', 'agency:page-work' );
$pg_home = pixelgrid_starter_find( 'page', 'agency:page-home' );
foreach ( $pg_case_slugs as $pg_slug ) {
	$pg_id = pixelgrid_starter_find( 'page', 'agency:page-' . $pg_slug );
	pg_assert( $pg_id > 0 && $pg_work > 0 && $pg_work === (int) get_post( $pg_id )->post_parent, "case $pg_slug is a child of the Work page" );
}
foreach ( array( 'Work' => $pg_work, 'Home' => $pg_home ) as $pg_name => $pg_id ) {
	$pg_content = (string) get_post_field( 'post_content', $pg_id );
	pg_assert( '' !== $pg_content && false === strpos( $pg_content, 'pagename=' ), "$pg_name page has no ?pagename= links left" );
	preg_match_all( '/href="([^"]+)"/', $pg_content, $pg_hrefs );
	$pg_hrefs = array_unique( $pg_hrefs[1] );
	pg_assert( count( $pg_hrefs ) > 0, "$pg_name page has links" );
	foreach ( $pg_hrefs as $pg_href ) {
		$pg_href = html_entity_decode( $pg_href );
		if ( 0 !== strpos( $pg_href, home_url() ) ) {
			continue;
		}
		$pg_code = wp_remote_retrieve_response_code( wp_remote_get( $pg_href, array( 'timeout' => 20 ) ) );
		pg_assert_same( 200, $pg_code, "$pg_name link $pg_href returns 200" );
	}
}
pg_assert_same( $pg_home, (int) get_option( 'page_on_front' ), 'front page is Home' );
pg_assert_same( pixelgrid_starter_find( 'page', 'agency:page-journal' ), (int) get_option( 'page_for_posts' ), 'posts page is Journal' );
pg_assert( false !== strpos( do_blocks( $pg_header_block ), 'Start a project' ), 'header renders the Agency header' );
$pg_agency_json = json_decode( file_get_contents( get_parent_theme_file_path( 'styles/agency.json' ) ), true );
$pg_blue_hover  = '';
foreach ( $pg_agency_json['settings']['color']['palette'] as $pg_entry ) {
	if ( 'blue-hover' === $pg_entry['slug'] ) {
		$pg_blue_hover = $pg_entry['color'];
	}
}
$pg_user_styles = json_decode( get_post( $pg_styles_id )->post_content, true );
$pg_user_blue   = '';
foreach ( (array) ( $pg_user_styles['settings']['color']['palette'] ?? array() ) as $pg_entry ) {
	if ( 'blue-hover' === ( $pg_entry['slug'] ?? '' ) ) {
		$pg_user_blue = $pg_entry['color'];
	}
}
pg_assert( '' !== $pg_blue_hover && $pg_blue_hover === $pg_user_blue, 'user styles carry the Agency blue-hover colour' );

$pg_removed = pixelgrid_starter_remove();
pg_assert( $pg_removed['removed'] >= 25, 'agency remove removes at least 25 items (got ' . $pg_removed['removed'] . ')' );
pg_assert_same( array(), $pg_removed['kept'], 'agency remove keeps nothing' );
pg_assert( false !== strpos( do_blocks( $pg_header_block ), 'Get a quote' ), 'header renders the theme default after removal' );
pg_assert_same( $pg_styles_before, get_post( $pg_styles_id )->post_content, 'user styles are back to the pre-setup content' );
$pg_published = array();
foreach ( get_posts(
	array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
) as $pg_id ) {
	if ( 0 === strpos( (string) get_post_meta( $pg_id, '_pixelgrid_starter', true ), 'agency:' ) ) {
		$pg_published[] = $pg_id;
	}
}
pg_assert_same( array(), $pg_published, 'no Agency page is published after removal' );

// Review Focus 2: one variant at a time.
pixelgrid_starter_create( 'it', array( 'pages' => true ) );
$pg_count_it = $pg_page_count();
$pg_busy     = pixelgrid_starter_create( 'agency', $pg_all );
pg_assert( is_wp_error( $pg_busy ) && 'pixelgrid_variant_busy' === $pg_busy->get_error_code(), 'agency while it is active returns pixelgrid_variant_busy' );
pg_assert_same( $pg_count_it, $pg_page_count(), 'busy agency create leaves the page count unchanged' );
pixelgrid_starter_remove();

// Re-applying the Agency design keeps a header edited after setup, and removal keeps it too.
$pg_custom = function ( $slug, $type ) {
	$tpl = get_block_template( get_stylesheet() . '//' . $slug, $type );
	return ( $tpl && 'custom' === $tpl->source && $tpl->wp_id ) ? $tpl : null;
};
pg_assert( is_array( pixelgrid_starter_create( 'agency', $pg_all ) ), 're-apply: agency created' );
$pg_header = $pg_custom( 'header', 'wp_template_part' );
pg_assert( null !== $pg_header, 're-apply: agency setup made a custom header' );
pg_assert( null !== $pg_custom( 'home', 'wp_template' ), 're-apply: agency setup made a custom home template' );
wp_update_post(
	wp_slash(
		array(
			'ID'           => $pg_header ? (int) $pg_header->wp_id : 0,
			'post_content' => ( $pg_header ? $pg_header->content : '' ) . "\n\n<!-- wp:paragraph -->\n<p>PIXELGRID-EDITED</p>\n<!-- /wp:paragraph -->",
		)
	)
);
$pg_again = pixelgrid_starter_create( 'agency', array( 'design' => true ) );
pg_assert( is_array( $pg_again ), 're-apply: create with design again succeeds' );
pg_assert( false !== strpos( do_blocks( $pg_header_block ), 'PIXELGRID-EDITED' ), 're-apply: header still renders the edit' );
// pixelgrid_starter_create() returns counts only; the skipped list comes from pixelgrid_design_apply().
$pg_applied = pixelgrid_design_apply( pixelgrid_variant( 'agency' ) );
pg_assert( is_array( $pg_applied ) && in_array( 'wp_template_part:header', $pg_applied['skipped'], true ), 're-apply: design apply reports the edited header as skipped' );
pg_assert( false !== strpos( do_blocks( $pg_header_block ), 'PIXELGRID-EDITED' ), 're-apply: header keeps the edit after another apply' );
$pg_removed = pixelgrid_starter_remove();
pg_assert_same( array( 'wp_template_part:header' ), $pg_removed['kept'], 're-apply: remove keeps only the edited header' );
pg_assert( null === $pg_custom( 'home', 'wp_template' ), 're-apply: the Agency home template customisation is gone' );
pg_assert( false !== strpos( do_blocks( $pg_header_block ), 'PIXELGRID-EDITED' ), 're-apply: edited header survives removal' );

pg_done();
