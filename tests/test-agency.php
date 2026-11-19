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

pg_done();
