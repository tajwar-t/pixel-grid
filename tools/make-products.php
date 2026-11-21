<?php
/**
 * Generates the 12 pixel-art product pictures of the Shop starting point (own work, GPL).
 * Development tool only: it is not shipped in release zips.
 *
 * Usage: php tools/make-products.php <out-dir>
 * Writes <out-dir>/shop-01.webp ... shop-12.webp (1200x1200). Each 16x16 drawing is scaled
 * x64 with nearest neighbour to 1024x1024 and centred on a cream ground.
 * Needs PHP with GD and WebP support.
 */
if ( $argc < 2 ) {
	fwrite( STDERR, "Usage: php make-products.php <out-dir>\n" );
	exit( 1 );
}
$out = rtrim( $argv[1], "/\\" );
if ( ! is_dir( $out ) || ! function_exists( 'imagewebp' ) ) {
	fwrite( STDERR, "Need an existing out-dir and GD with WebP.\n" );
	exit( 1 );
}

// Shop palette: g deep green, o tangerine, c cream (also the ground).
// Two extras: d ink and l leaf. "." is the cream ground.
$palette = array(
	'g' => '#173D2E',
	'o' => '#E8621A',
	'c' => '#FBF6EC',
	'd' => '#0B1A13',
	'l' => '#4E9A5F',
);
$ground  = '#FBF6EC';

// In the order of the Shop products: mug, teapot, bowl, lamp, clock, vase, fern,
// watering can, backpack, headphones, watch, radio.
$products = array(
	'mug'          => array(
		'................',
		'.....g...g......',
		'......g...g.....',
		'.....g...g......',
		'................',
		'..oddddddddo....',
		'..oooooooooo....',
		'..ocooooooooooo.',
		'..ocoooooooo..o.',
		'..ocoooooooo..o.',
		'..oooooooooo..o.',
		'..ooooooooooooo.',
		'..oooooooooo....',
		'...oooooooo.....',
		'..gggggggggg....',
		'................',
	),
	'teapot'       => array(
		'................',
		'................',
		'......ggg.......',
		'.......g........',
		'.....ggggg......',
		'.....ooooo....o.',
		'.ggooooooooo..o.',
		'g.gocooooooo.oo.',
		'g.oocooooooooo..',
		'g.ooogggggooo...',
		'g.goooooooooo...',
		'.ggooooooooo....',
		'...ooooooooo....',
		'....ggggggg.....',
		'................',
		'................',
	),
	'bowl'         => array(
		'................',
		'................',
		'................',
		'......l..l......',
		'....llolllol....',
		'..llllolllloll..',
		'.gggggggggggggg.',
		'.oooooooooooooo.',
		'.ocoooooooooooo.',
		'..ocoooooooooo..',
		'...oooooooooo...',
		'....oooooooo....',
		'.....gggggg.....',
		'....gggggggg....',
		'................',
		'................',
	),
	'lamp'         => array(
		'................',
		'................',
		'................',
		'......gggooo....',
		'.....g..ooooo...',
		'....g..ooooooo..',
		'...g...ddddddd..',
		'..oo............',
		'...g............',
		'....g...........',
		'.....g..........',
		'......g.........',
		'....ggggg.......',
		'...ggggggg......',
		'................',
		'................',
	),
	'clock'        => array(
		'................',
		'.....gggggg.....',
		'...ggggoogggg...',
		'..gggccccccggg..',
		'..ggcccdccccgg..',
		'.ggccccdcccccgg.',
		'.ggccccdcccccgg.',
		'.goccccooccccog.',
		'.goccccooddccog.',
		'.ggccccccccccgg.',
		'.ggccccccccccgg.',
		'..ggccccccccgg..',
		'..gggccccccggg..',
		'...ggggoogggg...',
		'.....gggggg.....',
		'.....gg..gg.....',
	),
	'vase'         => array(
		'.......o........',
		'....o.odo..o....',
		'...odo.l..odo...',
		'....l..l..ll....',
		'....ll.l.l......',
		'......lll.......',
		'.......l........',
		'.....gggggg.....',
		'......gggg......',
		'.....gggggg.....',
		'....gggggggg....',
		'...gccccccccg...',
		'...gggggggggg...',
		'....gggggggg....',
		'.....gggggg.....',
		'................',
	),
	'fern'         => array(
		'................',
		'......llll......',
		'....llgllgll....',
		'..lllgllllglll..',
		'.llgllllllllgll.',
		'llgll.llll.llgll',
		'lgl..lllgll..lgl',
		'll..l..ll..l..ll',
		'l....l.ll.l....l',
		'..oooooooooooo..',
		'..gggggggggggg..',
		'...oooooooooo...',
		'...oooooooooo...',
		'....oooooooo....',
		'....oooooooo....',
		'................',
	),
	'watering-can' => array(
		'................',
		'................',
		'................',
		'...gggggg.......',
		'...g....g.......',
		'...g....g.....gg',
		'..oooooooo....gg',
		'ggoooooooo...oo.',
		'g.oooooooo..oo..',
		'g.gggggggg.oo...',
		'g.oooooooooo....',
		'g.oooooooo......',
		'ggoooooooo......',
		'..gggggggg......',
		'................',
		'................',
	),
	'backpack'     => array(
		'................',
		'......oooo......',
		'.....o....o.....',
		'....gggggggg....',
		'...gggggggggg...',
		'...gggggggggg...',
		'...gggggggggg...',
		'...dddddddddd...',
		'...gggggggggg...',
		'...ggddddddgg...',
		'..oggooccooggo..',
		'..oggooooooggo..',
		'..oggooooooggo..',
		'..oggooooooggo..',
		'....gggggggg....',
		'................',
	),
	'headphones'   => array(
		'................',
		'................',
		'....goooooog....',
		'...ggg....ggg...',
		'..gg........gg..',
		'.ggg........ggg.',
		'.gg..........gg.',
		'.gg..........gg.',
		'.ooo........ooo.',
		'.cood......dooc.',
		'.cood......dooc.',
		'.oood......dooo.',
		'.oood......dooo.',
		'.ooo........ooo.',
		'................',
		'................',
	),
	'watch'        => array(
		'.....gggggg.....',
		'.....gggggg.....',
		'.....ggddgg.....',
		'.....gggggg.....',
		'...oooooooooo...',
		'...ooddddddoo...',
		'...odddcddddooo.',
		'...odddcddddooo.',
		'...odddccccdo...',
		'...oddddddddo...',
		'...ooddddddoo...',
		'...oooooooooo...',
		'.....gggggg.....',
		'.....ggddgg.....',
		'.....gggggg.....',
		'.....gggggg.....',
	),
	'radio'        => array(
		'..............g.',
		'.............g..',
		'.............g..',
		'...gggggggggg...',
		'...g........g...',
		'.oooooooooooooo.',
		'.oggggggooocooo.',
		'.ogccccgoodccoo.',
		'.oggggggoccdcco.',
		'.ogccccgoocccoo.',
		'.oggggggooocooo.',
		'.ogccccgogggggo.',
		'.oggggggooooooo.',
		'.oooooooooooooo.',
		'..gg........gg..',
		'................',
	),
);

/**
 * Allocates a #RRGGBB colour on a GD image.
 *
 * @param resource|GdImage $im  Image.
 * @param string           $hex Colour.
 * @return int
 */
function products_color( $im, $hex ) {
	$hex = ltrim( $hex, '#' );
	return imagecolorallocate( $im, hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
}

$size  = 1200;
$scale = 64;
$draw  = 16 * $scale;
$off   = (int) ( ( $size - $draw ) / 2 );
$i     = 0;
foreach ( $products as $name => $rows ) {
	++$i;
	if ( 16 !== count( $rows ) ) {
		fwrite( STDERR, "$name: needs 16 rows\n" );
		exit( 1 );
	}
	$small = imagecreatetruecolor( 16, 16 );
	imagefill( $small, 0, 0, products_color( $small, $ground ) );
	foreach ( $rows as $y => $row ) {
		if ( 16 !== strlen( $row ) ) {
			fwrite( STDERR, "$name: row $y needs 16 pixels\n" );
			exit( 1 );
		}
		for ( $x = 0; $x < 16; $x++ ) {
			$key = $row[ $x ];
			if ( '.' === $key ) {
				continue;
			}
			if ( ! isset( $palette[ $key ] ) ) {
				fwrite( STDERR, "$name: unknown colour '$key' in row $y\n" );
				exit( 1 );
			}
			imagesetpixel( $small, $x, $y, products_color( $small, $palette[ $key ] ) );
		}
	}
	$big = imagecreatetruecolor( $size, $size );
	imagefill( $big, 0, 0, products_color( $big, $ground ) );
	// imagecopyresized does not interpolate: every source pixel becomes a 64x64 block.
	imagecopyresized( $big, $small, $off, $off, 0, 0, $draw, $draw, 16, 16 );
	$file = sprintf( '%s/shop-%02d.webp', $out, $i );
	imagewebp( $big, $file, defined( 'IMG_WEBP_LOSSLESS' ) ? IMG_WEBP_LOSSLESS : 100 );
	printf( "%s %s %d bytes\n", $file, $name, filesize( $file ) );
}
