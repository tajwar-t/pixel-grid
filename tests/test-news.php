<?php
/**
 * News look tests: style variation, contrast and the News serif.
 */
require_once __DIR__ . '/lib/contrast.php';

$theme_json = json_decode( file_get_contents( get_parent_theme_file_path( 'theme.json' ) ), true );
$agency     = json_decode( file_get_contents( get_parent_theme_file_path( 'styles/agency.json' ) ), true );
$news_path  = get_parent_theme_file_path( 'styles/news-portal.json' );
$news_raw   = file_exists( $news_path ) ? file_get_contents( $news_path ) : false;
$news       = false === $news_raw ? null : json_decode( $news_raw, true );

pg_assert( is_array( $news ), 'styles/news-portal.json exists and decodes' );
$news     = is_array( $news ) ? $news : array();
$news_raw = (string) $news_raw;
pg_assert_same( 3, $news['version'] ?? null, 'news is version 3' );
pg_assert_same( 'News', $news['title'] ?? null, 'news is titled News' );

$news_palette = $news['settings']['color']['palette'] ?? array();
$names_of     = function ( $palette ) {
	$out = array();
	foreach ( $palette as $entry ) {
		$out[ $entry['slug'] ] = $entry['name'];
	}
	ksort( $out );
	return $out;
};
pg_assert_same( 28, count( $news_palette ), 'news has 28 palette entries' );
pg_assert_same( $names_of( $agency['settings']['color']['palette'] ), $names_of( $news_palette ), 'news has the agency.json slugs with the same names and no others' );
foreach ( $news_palette as $entry ) {
	pg_assert( (bool) preg_match( '/^#[0-9A-Fa-f]{6}$/', $entry['color'] ?? '' ), "news {$entry['slug']} is a hex colour" );
}

$pg_min = INF;
foreach ( pixelgrid_test_palette_pairs( $news_palette ) as $pair ) {
	list( $tone, $text_ratio, $hover_ratio ) = $pair;
	$pg_min = min( $pg_min, $text_ratio, $hover_ratio );
	printf( "INFO: News %s text/bg %.2f on-hover/hover %.2f\n", $tone, $text_ratio, $hover_ratio );
	pg_assert( $text_ratio >= 4.5, "News $tone text on background reaches 4.5:1" );
	pg_assert( $hover_ratio >= 4.5, "News $tone text on hover reaches 4.5:1" );
}
printf( "INFO: News minimum contrast %.2f\n", $pg_min );

// The light tone is newsprint and ink, with the red accent on hover.
$news_colors = array();
foreach ( $news_palette as $entry ) {
	$news_colors[ $entry['slug'] ] = strtoupper( $entry['color'] );
}
pg_assert_same( '#F7F5F0', $news_colors['paper-background'] ?? null, 'news light background is newsprint' );
pg_assert_same( '#141414', $news_colors['paper-text'] ?? null, 'news light text is ink' );
pg_assert_same( '#C8102E', $news_colors['paper-hover'] ?? null, 'news light hover is the red accent' );
pg_assert_same( '#E6E3DC', $news_colors['fog-background'] ?? null, 'news soft background is grey' );

// Typography: the serif for body, headings and the site title; sentence-case headings.
$serif   = 'var:preset|font-family|news-serif';
$heading = $news['styles']['elements']['heading']['typography'] ?? array();
pg_assert_same( $serif, $news['styles']['typography']['fontFamily'] ?? null, 'news body uses news-serif' );
pg_assert_same( $serif, $heading['fontFamily'] ?? null, 'news headings use news-serif' );
pg_assert_same( '700', $heading['fontWeight'] ?? null, 'news headings are weight 700' );
pg_assert_same( 'none', $heading['textTransform'] ?? null, 'news headings are sentence case' );
pg_assert_same( '-0.01em', $heading['letterSpacing'] ?? null, 'news headings letter spacing is -0.01em' );
$title = $news['styles']['blocks']['core/site-title']['typography'] ?? array();
pg_assert_same( $serif, $title['fontFamily'] ?? null, 'news site title uses news-serif' );
pg_assert_same( '800', $title['fontWeight'] ?? null, 'news site title is weight 800' );
pg_assert_same( 'none', $title['textTransform'] ?? null, 'news site title is not uppercased' );
pg_assert( false === strpos( $news_raw, 'fontFace' ), 'news variation declares no fontFace' );

// Headline sizes: Newsreader 700 is about 0.55em per letter, and a phone column at 390px is
// about 11 grid units wide. h1 and h2 stay small enough that a 14-letter word fits on one line.
foreach ( array( 'h1' => 1.4, 'h2' => 1.2 ) as $pg_level => $pg_max ) {
	$pg_size = $news['styles']['elements'][ $pg_level ]['typography']['fontSize'] ?? '';
	$pg_line = $news['styles']['elements'][ $pg_level ]['typography']['lineHeight'] ?? '';
	pg_assert( $pg_size !== ( $theme_json['styles']['elements'][ $pg_level ]['typography']['fontSize'] ?? '' ), "news $pg_level font size differs from theme.json" );
	pg_assert( '' !== $pg_line && $pg_line !== ( $theme_json['styles']['elements'][ $pg_level ]['typography']['lineHeight'] ?? '' ), "news $pg_level line height is its own" );
	pg_assert( (bool) preg_match( '/^calc\(var\(--pixelgrid-u\) \* ([0-9.]+)\)$/', $pg_size, $pg_m ) && (float) $pg_m[1] <= $pg_max, "news $pg_level size is at most $pg_max grid units" );
}
$pg_sizes = array();
foreach ( (array) ( $news['settings']['typography']['fontSizes'] ?? array() ) as $pg_entry ) {
	$pg_sizes[ $pg_entry['slug'] ] = $pg_entry['size'];
}
pg_assert_same( $news['styles']['elements']['h2']['typography']['fontSize'] ?? null, $pg_sizes['xx-large'] ?? '', 'news Headline preset matches its h2 size' );
pg_assert( false !== strpos( $pg_sizes['huge'] ?? '', 'vw' ), 'news Huge preset is capped by the window width for phones' );

// The news-serif family is registered in base theme.json with its bundled faces.
$families = wp_get_global_settings( array( 'typography', 'fontFamilies', 'theme' ) );
$by_slug  = array();
foreach ( (array) $families as $family ) {
	$by_slug[ $family['slug'] ?? '' ] = $family;
}
$news_serif = $by_slug['news-serif'] ?? null;
pg_assert( is_array( $news_serif ), 'theme font families include news-serif' );
pg_assert_same( 'News serif', $news_serif['name'] ?? null, 'news-serif family is named News serif' );
pg_assert_same( 'Newsreader, Georgia, "Times New Roman", serif', $news_serif['fontFamily'] ?? null, 'news-serif falls back to Georgia' );
$faces = $news_serif['fontFace'] ?? array();
pg_assert_same( 2, count( $faces ), 'news-serif has latin and latin-ext faces' );
$pg_files = array();
foreach ( $faces as $face ) {
	pg_assert_same( '400 800', $face['fontWeight'] ?? null, 'news-serif face covers weights 400 to 800' );
	foreach ( (array) $face['src'] as $src ) {
		$rel = 0 === strpos( $src, 'file:./' ) ? substr( $src, 7 ) : null;
		if ( null === $rel ) {
			$theme_uri = trailingslashit( get_template_directory_uri() );
			$rel       = 0 === strpos( $src, $theme_uri ) ? substr( $src, strlen( $theme_uri ) ) : $src;
		}
		$pg_files[] = $rel;
	}
}
sort( $pg_files );
pg_assert_same( array( 'assets/fonts/newsreader-400-800-latin-ext.woff2', 'assets/fonts/newsreader-400-800-latin.woff2' ), $pg_files, 'news-serif uses the two Newsreader files' );
foreach ( $pg_files as $rel ) {
	pg_assert( file_exists( get_parent_theme_file_path( $rel ) ) && filesize( get_parent_theme_file_path( $rel ) ) > 10000, "font file $rel exists" );
}

// Review Focus 1: picking News in Browse styles without setup still finds every font it uses.
preg_match_all( '/var:preset\|font-family\|([a-z0-9-]+)|--wp--preset--font-family--([a-z0-9-]+)/', $news_raw, $pg_uses );
$pg_used = array_unique( array_filter( array_merge( $pg_uses[1], $pg_uses[2] ) ) );
pg_assert( in_array( 'news-serif', $pg_used, true ), 'news uses news-serif' );
foreach ( $pg_used as $pg_slug ) {
	pg_assert( isset( $by_slug[ $pg_slug ] ), "news font family $pg_slug is registered in theme.json" );
}

$titles = array();
foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
	$titles[] = $variation['title'] ?? '';
}
pg_assert( in_array( 'News', $titles, true ), 'style variations list News' );

// Rendered with the News variation, the stylesheet carries the serif and the faces.
$pg_css = wp_get_global_stylesheet();
pg_assert( false !== strpos( $pg_css, '--wp--preset--font-family--news-serif' ), 'global stylesheet defines the news-serif preset' );

pg_assert( isset( $news['styles']['css'] ) && false !== strpos( $news['styles']['css'], 'p.is-style-lead' ) && false !== strpos( $news['styles']['css'], 'news-serif' ), 'News lead paragraphs use the News serif' );

pg_done();
