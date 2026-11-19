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

pg_done();
