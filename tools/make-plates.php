<?php
/**
 * Generates original abstract greyscale "plates" for the Pixelgrid theme (own work, GPL).
 * Development tool only: it is not shipped in release zips.
 *
 * Usage: php tools/make-plates.php <out-dir> <prefix> <count> <seed>
 * Writes <out-dir>/<prefix>-NN.webp (1200x1200). The same seed gives the same images.
 * Needs PHP with GD and WebP support.
 */
if ( $argc < 5 ) {
	fwrite( STDERR, "Usage: php make-plates.php <out-dir> <prefix> <count> <seed>
" );
	exit( 1 );
}
$out    = rtrim( $argv[1], "/\\" );
$prefix = $argv[2];
$count  = (int) $argv[3];
$seed   = (int) $argv[4];
if ( ! is_dir( $out ) || $count < 1 || ! function_exists( 'imagewebp' ) ) {
	fwrite( STDERR, "Need an existing out-dir, a count of 1 or more and GD with WebP.
" );
	exit( 1 );
}
function plate_noise( $size, $cells, $seed ) {
	mt_srand( $seed );
	$grid = array();
	for ( $y = 0; $y <= $cells; $y++ ) {
		for ( $x = 0; $x <= $cells; $x++ ) {
			$grid[ $y ][ $x ] = mt_rand( 0, 1000 ) / 1000;
		}
	}
	$field = array();
	for ( $y = 0; $y < $size; $y++ ) {
		$gy = $y / $size * $cells; $y0 = (int) $gy; $ty = $gy - $y0; $ty = $ty * $ty * ( 3 - 2 * $ty );
		for ( $x = 0; $x < $size; $x++ ) {
			$gx = $x / $size * $cells; $x0 = (int) $gx; $tx = $gx - $x0; $tx = $tx * $tx * ( 3 - 2 * $tx );
			$a = $grid[ $y0 ][ $x0 ] + ( $grid[ $y0 ][ $x0 + 1 ] - $grid[ $y0 ][ $x0 ] ) * $tx;
			$b = $grid[ $y0 + 1 ][ $x0 ] + ( $grid[ $y0 + 1 ][ $x0 + 1 ] - $grid[ $y0 + 1 ][ $x0 ] ) * $tx;
			$field[ $y ][ $x ] = $a + ( $b - $a ) * $ty;
		}
	}
	return $field;
}

for ( $i = 1; $i <= $count; $i++ ) {
	$n = $seed * 1000 + $i;
	$s = 300;
	$o1 = plate_noise( $s, 3, $n * 11 );
	$o2 = plate_noise( $s, 7, $n * 23 );
	$o3 = plate_noise( $s, 19, $n * 37 );
	$im = imagecreatetruecolor( $s, $s );
	mt_srand( $n * 101 );
	$angle = mt_rand( 0, 628 ) / 100; $dx = cos( $angle ); $dy = sin( $angle );
	$bands = mt_rand( 0, 1 );
	$field = array();
	$all   = array();
	for ( $y = 0; $y < $s; $y++ ) {
		for ( $x = 0; $x < $s; $x++ ) {
			$v = 0.55 * $o1[ $y ][ $x ] + 0.3 * $o2[ $y ][ $x ] + 0.15 * $o3[ $y ][ $x ];
			$v += 0.35 * ( ( $x * $dx + $y * $dy ) / $s );
			if ( $bands ) {
				$v = $v + 0.08 * sin( $v * 40 );
			}
			$field[ $y ][ $x ] = $v;
			$all[]             = $v;
		}
	}
	// Stretch the 2nd to 98th percentile to the full range so every plate keeps real mid-tone variation.
	sort( $all );
	$lo   = $all[ (int) ( count( $all ) * 0.02 ) ];
	$hi   = $all[ (int) ( count( $all ) * 0.98 ) ];
	$span = max( 0.0001, $hi - $lo );
	for ( $y = 0; $y < $s; $y++ ) {
		for ( $x = 0; $x < $s; $x++ ) {
			$v = max( 0, min( 1, ( $field[ $y ][ $x ] - $lo ) / $span ) );
			$g = (int) ( 20 + $v * 225 );
			imagesetpixel( $im, $x, $y, ( $g << 16 ) | ( $g << 8 ) | $g );
		}
	}
	// A few hard-edged shapes so the plates read as compositions, not fog.
	for ( $k = 0; $k < 4; $k++ ) {
		$g = mt_rand( 0, 1 ) ? mt_rand( 15, 60 ) : mt_rand( 200, 245 );
		$c = imagecolorallocatealpha( $im, $g, $g, $g, mt_rand( 20, 70 ) );
		$w = mt_rand( 30, 140 ); $h = mt_rand( 30, 140 ); $px = mt_rand( 0, $s - 40 ); $py = mt_rand( 0, $s - 40 );
		if ( 0 === $k % 2 ) {
			imagefilledrectangle( $im, $px, $py, $px + $w, $py + $h, $c );
		} else {
			imagefilledellipse( $im, $px, $py, $w * 2, $w * 2, $c );
		}
	}
	$big = imagecreatetruecolor( 1200, 1200 );
	imagecopyresampled( $big, $im, 0, 0, 0, 0, 1200, 1200, $s, $s );
	// Fine grain.
	for ( $p = 0; $p < 90000; $p++ ) {
		$x = mt_rand( 0, 1199 ); $y = mt_rand( 0, 1199 );
		$rgb = imagecolorat( $big, $x, $y ) & 0xFF;
		$g = max( 0, min( 255, $rgb + mt_rand( -22, 22 ) ) );
		imagesetpixel( $big, $x, $y, ( $g << 16 ) | ( $g << 8 ) | $g );
	}
	$file = sprintf( '%s/%s-%02d.webp', $out, $prefix, $i );
	imagewebp( $big, $file, 78 );
	echo basename( $file ) . " " . filesize( $file ) . "\n";
}
