<?php
/**
 * Contrast helpers for palette tests.
 */

if ( ! function_exists( 'pixelgrid_test_contrast' ) ) {
	/**
	 * WCAG relative luminance of a #RRGGBB or #RGB colour.
	 *
	 * @param string $hex Colour.
	 * @return float
	 */
	function pixelgrid_test_luminance( $hex ) {
		$hex = ltrim( trim( $hex ), '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$channels = array();
		foreach ( array( 0, 2, 4 ) as $offset ) {
			$c          = hexdec( substr( $hex, $offset, 2 ) ) / 255;
			$channels[] = $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		}
		return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
	}

	/**
	 * WCAG contrast ratio between two colours.
	 *
	 * @param string $hex_a First colour.
	 * @param string $hex_b Second colour.
	 * @return float
	 */
	function pixelgrid_test_contrast( string $hex_a, string $hex_b ): float {
		$a = pixelgrid_test_luminance( $hex_a );
		$b = pixelgrid_test_luminance( $hex_b );
		return ( max( $a, $b ) + 0.05 ) / ( min( $a, $b ) + 0.05 );
	}

	/**
	 * Contrast ratios per tone of a theme.json style palette.
	 *
	 * @param array $palette List of array( slug, color, name ).
	 * @return array One array( tone, text_on_bg_ratio, on_hover_ratio ) per tone.
	 */
	function pixelgrid_test_palette_pairs( array $palette ): array {
		$colors = array();
		foreach ( $palette as $entry ) {
			$colors[ $entry['slug'] ] = $entry['color'];
		}
		$tones = array(
			'light'  => 'paper',
			'bold'   => 'blue',
			'bright' => 'sun',
			'dark'   => 'night',
			'soft'   => 'fog',
			'alert'  => 'red',
			'footer' => 'footer',
		);
		$pairs = array();
		foreach ( $tones as $tone => $slug ) {
			$bg       = $colors[ "$slug-background" ] ?? null;
			$text     = $colors[ "$slug-text" ] ?? null;
			$hover    = $colors[ "$slug-hover" ] ?? null;
			$on_hover = $colors[ "$slug-on-hover" ] ?? null;
			$pairs[]  = array(
				$tone,
				( $bg && $text ) ? pixelgrid_test_contrast( $text, $bg ) : 0.0,
				( $hover && $on_hover ) ? pixelgrid_test_contrast( $on_hover, $hover ) : 0.0,
			);
		}
		return $pairs;
	}
}
