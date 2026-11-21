<?php
/**
 * Shop starter content tests: products and product categories.
 *
 * Run on a site made with `bash tests/bin/site.sh up --woo` for the full set. Without
 * WooCommerce only the "without WooCommerce" checks run. The Shop style checks always run.
 */

// Collects PHP notices and warnings raised by the code under test.
$GLOBALS['pg_shop_notices'] = array();
$pg_watch                   = function ( callable $run ) {
	$GLOBALS['pg_shop_notices'] = array();
	set_error_handler(
		function ( $errno, $errstr, $errfile, $errline ) {
			$GLOBALS['pg_shop_notices'][] = "$errstr ($errfile:$errline)";
			return true;
		},
		E_NOTICE | E_WARNING | E_USER_NOTICE | E_USER_WARNING
	);
	try {
		$result = $run();
	} finally {
		restore_error_handler();
	}
	return $result;
};

add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['shopfix'] = array(
			'slug'               => 'shopfix',
			'label'              => 'Shop fixture',
			'product_categories' => array(
				'fix-mugs'   => 'Fixture mugs',
				'fix-prints' => 'Fixture prints',
			),
			'products'           => array(
				array(
					'key'      => 'pixel-mug',
					'name'     => 'Pixel mug',
					'category' => 'fix-mugs',
					'price'    => '18.00',
					'short'    => 'A mug with a pixel.',
					'content'  => '<!-- wp:paragraph --><p>Holds coffee.</p><!-- /wp:paragraph -->',
					'image'    => 'assets/images/agency-01.webp',
				),
				array(
					'key'      => 'grid-print',
					'name'     => 'Grid print',
					'category' => 'fix-prints',
					'price'    => '40.00',
					'sale'     => '32.00',
					'short'    => 'A print of the grid.',
					'content'  => '<!-- wp:paragraph --><p>Hangs on a wall.</p><!-- /wp:paragraph -->',
					'image'    => 'assets/images/agency-02.webp',
				),
			),
		);
		return $variants;
	}
);

$pg_marked_products = function ( $statuses = array( 'publish', 'draft', 'private' ) ) {
	return get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => $statuses,
			'meta_key'       => '_pixelgrid_starter',
			'posts_per_page' => 50,
			'fields'         => 'ids',
		)
	);
};

pg_assert( function_exists( 'pixelgrid_starter_product_categories' ), 'pixelgrid_starter_product_categories exists' );

// Shop look: style variation, contrast and fonts. These run with or without WooCommerce.
require_once __DIR__ . '/lib/contrast.php';
$pg_theme_json = json_decode( file_get_contents( get_parent_theme_file_path( 'theme.json' ) ), true );
$pg_agency     = json_decode( file_get_contents( get_parent_theme_file_path( 'styles/agency.json' ) ), true );
$pg_shop_path  = get_parent_theme_file_path( 'styles/shop.json' );
$pg_shop_raw   = file_exists( $pg_shop_path ) ? (string) file_get_contents( $pg_shop_path ) : '';
$pg_shop       = json_decode( $pg_shop_raw, true );
pg_assert( is_array( $pg_shop ), 'styles/shop.json exists and decodes' );
$pg_shop = is_array( $pg_shop ) ? $pg_shop : array();
pg_assert_same( 3, $pg_shop['version'] ?? null, 'shop is version 3' );
pg_assert_same( 'Shop', $pg_shop['title'] ?? null, 'shop is titled Shop' );

$pg_shop_palette = $pg_shop['settings']['color']['palette'] ?? array();
$pg_names_of     = function ( $palette ) {
	$out = array();
	foreach ( $palette as $entry ) {
		$out[ $entry['slug'] ] = $entry['name'];
	}
	ksort( $out );
	return $out;
};
pg_assert_same( 28, count( $pg_shop_palette ), 'shop has 28 palette entries' );
pg_assert_same( $pg_names_of( $pg_agency['settings']['color']['palette'] ), $pg_names_of( $pg_shop_palette ), 'shop has the agency.json slugs with the same names and no others' );
$pg_shop_colors = array();
foreach ( $pg_shop_palette as $entry ) {
	pg_assert( (bool) preg_match( '/^#[0-9A-Fa-f]{6}$/', $entry['color'] ?? '' ), "shop {$entry['slug']} is a hex colour" );
	$pg_shop_colors[ $entry['slug'] ] = strtoupper( $entry['color'] ?? '' );
}

$pg_min = INF;
foreach ( pixelgrid_test_palette_pairs( $pg_shop_palette ) as $pair ) {
	list( $tone, $text_ratio, $hover_ratio ) = $pair;
	$pg_min = min( $pg_min, $text_ratio, $hover_ratio );
	printf( "INFO: Shop %s text/bg %.2f on-hover/hover %.2f\n", $tone, $text_ratio, $hover_ratio );
	pg_assert( $text_ratio >= 4.5, "Shop $tone text on background reaches 4.5:1" );
	pg_assert( $hover_ratio >= 4.5, "Shop $tone text on hover reaches 4.5:1" );
}
printf( "INFO: Shop minimum contrast %.2f\n", $pg_min );

// The direction: warm paper, deep green, tangerine and cream.
pg_assert_same( '#F4EEE3', $pg_shop_colors['paper-background'] ?? null, 'shop light background is warm paper' );
pg_assert_same( '#173D2E', $pg_shop_colors['paper-text'] ?? null, 'shop light text is deep green' );
pg_assert_same( '#E8621A', $pg_shop_colors['paper-hover'] ?? null, 'shop light hover is tangerine' );
pg_assert_same( '#173D2E', $pg_shop_colors['blue-background'] ?? null, 'shop bold background is deep green' );
pg_assert_same( '#FBF6EC', $pg_shop_colors['blue-text'] ?? null, 'shop bold text is cream' );

// Typography: Instrument Sans (the body family) at 700 for headings, sentence case; labels and prices on the condensed face.
$pg_heading = $pg_shop['styles']['elements']['heading']['typography'] ?? array();
pg_assert_same( 'var:preset|font-family|body', $pg_heading['fontFamily'] ?? null, 'shop headings use the body family' );
pg_assert_same( '700', $pg_heading['fontWeight'] ?? null, 'shop headings are weight 700' );
pg_assert_same( 'none', $pg_heading['textTransform'] ?? null, 'shop headings are sentence case' );
pg_assert_same( '-0.03em', $pg_heading['letterSpacing'] ?? null, 'shop headings letter spacing is -0.03em' );
pg_assert( false === strpos( $pg_shop_raw, 'fontFace' ), 'shop variation declares no fontFace' );
$pg_shop_css = (string) ( $pg_shop['styles']['css'] ?? '' );
foreach ( array( '.pixelgrid-shop-label', '.pixelgrid-shop-price', '.wc-block-components-product-price', '.wc-block-components-product-sale-badge' ) as $pg_sel ) {
	pg_assert( false !== strpos( $pg_shop_css, $pg_sel ), "shop css styles $pg_sel" );
}
pg_assert( false !== strpos( $pg_shop_css, 'font-family:var(--wp--preset--font-family--heading)' ), 'shop labels and prices use the condensed heading family' );

// Sizes safe at 390px: Instrument Sans 700 is about 0.58em per letter and a phone column is
// about 11 grid units, so h1 and h2 stay small enough for a 14-letter word on one line.
foreach ( array( 'h1' => 1.35, 'h2' => 1.2 ) as $pg_level => $pg_max ) {
	$pg_size = $pg_shop['styles']['elements'][ $pg_level ]['typography']['fontSize'] ?? '';
	$pg_line = $pg_shop['styles']['elements'][ $pg_level ]['typography']['lineHeight'] ?? '';
	pg_assert( '' !== $pg_line && $pg_line !== ( $pg_theme_json['styles']['elements'][ $pg_level ]['typography']['lineHeight'] ?? '' ), "shop $pg_level line height is its own" );
	pg_assert( (bool) preg_match( '/^calc\(var\(--pixelgrid-u\) \* ([0-9.]+)\)$/', $pg_size, $pg_m ) && (float) $pg_m[1] <= $pg_max, "shop $pg_level size is at most $pg_max grid units" );
}
$pg_sizes = array();
foreach ( (array) ( $pg_shop['settings']['typography']['fontSizes'] ?? array() ) as $pg_entry ) {
	$pg_sizes[ $pg_entry['slug'] ] = $pg_entry['size'];
}
pg_assert_same( $pg_shop['styles']['elements']['h2']['typography']['fontSize'] ?? null, $pg_sizes['xx-large'] ?? '', 'shop Headline preset matches its h2 size' );
pg_assert( false !== strpos( $pg_sizes['display'] ?? '', 'vw' ), 'shop Display preset is capped by the window width for phones' );
pg_assert( false !== strpos( $pg_sizes['huge'] ?? '', 'vw' ), 'shop Huge preset is capped by the window width for phones' );

// Every font family the variation uses is registered in theme.json, and the body family has a 700 face.
$pg_families = wp_get_global_settings( array( 'typography', 'fontFamilies', 'theme' ) );
$pg_by_slug  = array();
foreach ( (array) $pg_families as $family ) {
	$pg_by_slug[ $family['slug'] ?? '' ] = $family;
}
preg_match_all( '/var:preset\|font-family\|([a-z0-9-]+)|--wp--preset--font-family--([a-z0-9-]+)/', $pg_shop_raw, $pg_uses );
$pg_used = array_values( array_unique( array_filter( array_merge( $pg_uses[1], $pg_uses[2] ) ) ) );
sort( $pg_used );
pg_assert_same( array( 'body', 'heading' ), $pg_used, 'shop uses the body and heading families' );
foreach ( $pg_used as $pg_slug ) {
	pg_assert( isset( $pg_by_slug[ $pg_slug ] ), "shop font family $pg_slug is registered in theme.json" );
}
pg_assert( false !== strpos( $pg_by_slug['body']['fontFamily'] ?? '', 'Instrument Sans' ), 'the body family is Instrument Sans' );
$pg_body_700 = false;
foreach ( (array) ( $pg_by_slug['body']['fontFace'] ?? array() ) as $face ) {
	$pg_w = array_map( 'intval', explode( ' ', (string) ( $face['fontWeight'] ?? '' ) ) );
	if ( min( $pg_w ) <= 700 && max( $pg_w ) >= 700 ) {
		$pg_body_700 = true;
	}
}
pg_assert( $pg_body_700, 'the body family has a face that covers weight 700' );

$pg_titles = array();
foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
	$pg_titles[] = $variation['title'] ?? '';
}
pg_assert( in_array( 'Shop', $pg_titles, true ), 'style variations list Shop' );

if ( ! class_exists( 'WC_Product_Simple' ) ) {
	echo "INFO: WooCommerce is not active; running only the without-WooCommerce checks. Use 'site.sh up --woo' for the rest.\n";

	$made = $pg_watch(
		function () {
			return pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
		}
	);
	pg_assert( is_array( $made ), 'without WooCommerce, create with products gives no error' );
	pg_assert_same( 0, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'without WooCommerce, products is 0' );
	pg_assert_same( array(), $pg_marked_products(), 'without WooCommerce, no product is created' );
	pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'without WooCommerce, create raises no notice' );
	pg_assert_same( array(), (array) get_option( 'pixelgrid_starter_product_cats', array() ), 'without WooCommerce, no product category is recorded' );

	// A site where Shop setup ran and WooCommerce was later turned off: a marked product row
	// and a recorded product category are left. Remove trashes the product by post type and
	// keeps the category record for a later run, with no notice.
	$pg_product = wp_insert_post(
		array(
			'post_type'   => 'product',
			'post_title'  => 'Orphan product',
			'post_status' => 'publish',
		)
	);
	update_post_meta( $pg_product, '_pixelgrid_starter', 'shopfix:product-orphan' );
	update_option( 'pixelgrid_starter_product_cats', array( 987654 ), false );
	update_option( 'pixelgrid_starter_variant', 'shopfix', false );

	$removed = $pg_watch( 'pixelgrid_starter_remove' );
	pg_assert_same( 'trash', get_post_status( $pg_product ), 'without WooCommerce, remove trashes the marked product' );
	pg_assert( $removed['removed'] >= 1, 'without WooCommerce, remove counts the product' );
	pg_assert_same( array( 987654 ), get_option( 'pixelgrid_starter_product_cats' ), 'without WooCommerce, the product category record is kept for a later run' );
	pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'without WooCommerce, remove raises no notice' );
	pg_done();
}

echo "INFO: WooCommerce is active; running the full shop checks.\n";

// A product category the user already has, with the same slug as one setup wants.
$pg_existing = wp_insert_term( 'My mugs', 'product_cat', array( 'slug' => 'fix-mugs' ) );
$pg_existing = is_wp_error( $pg_existing ) ? 0 : (int) $pg_existing['term_id'];
pg_assert( $pg_existing > 0, 'existing product category made before setup' );
// A product the user already has.
$pg_own = new WC_Product_Simple();
$pg_own->set_name( 'My own product' );
$pg_own->set_regular_price( '5.00' );
$pg_own->set_status( 'publish' );
$pg_own_id = $pg_own->save();

$made = $pg_watch(
	function () {
		return pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
	}
);
pg_assert( is_array( $made ), 'create with products returns counts' );
pg_assert_same( 2, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'create makes 2 products' );
pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'create raises no notice' );

$pg_mug_id   = pixelgrid_starter_find( 'product', 'shopfix:product-pixel-mug' );
$pg_print_id = pixelgrid_starter_find( 'product', 'shopfix:product-grid-print' );
pg_assert( $pg_mug_id > 0 && $pg_print_id > 0, 'both products carry the shopfix:product-{key} marker' );
$pg_mug   = wc_get_product( $pg_mug_id );
$pg_print = wc_get_product( $pg_print_id );
$pg_cats  = get_term_by( 'slug', 'fix-prints', 'product_cat' );
$pg_new   = $pg_cats ? (int) $pg_cats->term_id : 0;

pg_assert_same( 'Pixel mug', $pg_mug->get_name(), 'mug name' );
pg_assert_same( 'pixel-mug', $pg_mug->get_slug(), 'mug slug is its key' );
pg_assert_same( 'publish', $pg_mug->get_status(), 'mug is published' );
pg_assert_same( '18.00', $pg_mug->get_regular_price(), 'mug price' );
pg_assert_same( '', $pg_mug->get_sale_price(), 'mug has no sale price' );
pg_assert_same( '40.00', $pg_print->get_regular_price(), 'print regular price' );
pg_assert_same( '32.00', $pg_print->get_sale_price(), 'print sale price' );
pg_assert_same( 'A mug with a pixel.', $pg_mug->get_short_description(), 'mug short description' );
pg_assert( false !== strpos( $pg_print->get_description(), 'Hangs on a wall.' ), 'print description holds its block markup' );
pg_assert_same( array( $pg_existing ), array_map( 'intval', $pg_mug->get_category_ids() ), 'mug is in the existing, reused category' );
pg_assert( $pg_new > 0 && $pg_new !== $pg_existing, 'setup created the missing product category' );
pg_assert_same( array( $pg_new ), array_map( 'intval', $pg_print->get_category_ids() ), 'print is in the created category' );
pg_assert_same( 'Fixture prints', $pg_cats ? $pg_cats->name : '', 'created category has its name' );
pg_assert_same( array( $pg_new ), get_option( 'pixelgrid_starter_product_cats' ), 'only the created product category is recorded' );
pg_assert_same( 'My mugs', get_term( $pg_existing, 'product_cat' )->name, 'the existing category is not edited' );

$pg_image = (int) $pg_mug->get_image_id();
pg_assert( $pg_image > 0, 'mug has a featured image' );
pg_assert_same( 'shopfix:image-agency-01', get_post_meta( $pg_image, '_pixelgrid_starter', true ), 'mug image is a starter image' );
pg_assert( (int) $pg_print->get_image_id() > 0 && (int) $pg_print->get_image_id() !== $pg_image, 'print has its own featured image' );
pg_assert_same( 'shopfix', pixelgrid_starter_active_variant(), 'the variant is recorded' );

// The user changes a price, then runs setup again.
$pg_mug->set_regular_price( '21.00' );
$pg_mug->save();
$pg_attachments = count(
	get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 50,
			'fields'         => 'ids',
		)
	)
);
$pg_term_count  = (int) wp_count_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
$made           = pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
pg_assert_same( 0, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'second create makes 0 products' );
pg_assert_same( 2, count( $pg_marked_products() ), 'second create makes no duplicate products' );
pg_assert_same( '21.00', wc_get_product( $pg_mug_id )->get_regular_price(), 'the price the user changed is kept' );
pg_assert_same(
	$pg_attachments,
	count(
		get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 50,
				'fields'         => 'ids',
			)
		)
	),
	'second create makes no duplicate images'
);
pg_assert_same( $pg_term_count, (int) wp_count_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ), 'second create makes no duplicate product categories' );
pg_assert_same( array( $pg_new ), get_option( 'pixelgrid_starter_product_cats' ), 'second create records no duplicates' );

// Posts off: no products.
pixelgrid_starter_remove();
$made = pixelgrid_starter_create( 'shopfix', array( 'pages' => true ) );
pg_assert_same( 0, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'without the posts option no product is made' );
pg_assert_same( array(), $pg_marked_products(), 'without the posts option no product exists' );
pixelgrid_starter_remove();

// Remove.
$pg_existing = (int) get_term_by( 'slug', 'fix-mugs', 'product_cat' )->term_id;
pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
$pg_mug_id   = pixelgrid_starter_find( 'product', 'shopfix:product-pixel-mug' );
$pg_print_id = pixelgrid_starter_find( 'product', 'shopfix:product-grid-print' );
$pg_new      = (int) get_term_by( 'slug', 'fix-prints', 'product_cat' )->term_id;
$removed     = $pg_watch( 'pixelgrid_starter_remove' );
pg_assert_same( 'trash', get_post_status( $pg_mug_id ), 'remove trashes the mug' );
pg_assert_same( 'trash', get_post_status( $pg_print_id ), 'remove trashes the print' );
pg_assert( $removed['removed'] >= 4, 'remove counts the products and their images' );
pg_assert( null === get_term( $pg_new, 'product_cat' ), 'remove deletes the created product category' );
pg_assert( get_term( $pg_existing, 'product_cat' ) instanceof WP_Term, 'the existing product category survives remove' );
pg_assert_same( false, get_option( 'pixelgrid_starter_product_cats' ), 'remove deletes pixelgrid_starter_product_cats' );
pg_assert_same( 'publish', get_post_status( $pg_own_id ), 'the user\'s own product is untouched' );
pg_assert_same( '', pixelgrid_starter_active_variant(), 'no active variant after remove' );
pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'remove raises no notice' );

// A created product category that a user's draft product still uses survives remove.
pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
$pg_new   = (int) get_term_by( 'slug', 'fix-prints', 'product_cat' )->term_id;
$pg_draft = new WC_Product_Simple();
$pg_draft->set_name( 'My draft product' );
$pg_draft->set_status( 'draft' );
$pg_draft->set_category_ids( array( $pg_new ) );
$pg_draft->save();
pixelgrid_starter_remove();
pg_assert( get_term( $pg_new, 'product_cat' ) instanceof WP_Term, 'a starter product category used by a draft survives remove' );

// Clean slate for the checks below.
$pg_draft_id = $pg_draft->get_id();
wp_delete_post( $pg_draft_id, true );
wp_delete_term( $pg_new, 'product_cat' );

// A starter product the user set to pending is found on a re-run, not made again.
pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
$pg_mug_id = pixelgrid_starter_find( 'product', 'shopfix:product-pixel-mug' );
wp_update_post(
	array(
		'ID'          => $pg_mug_id,
		'post_status' => 'pending',
	)
);
pg_assert_same( $pg_mug_id, pixelgrid_starter_find( 'product', 'shopfix:product-pixel-mug' ), 'find locates a pending starter product' );
$made = pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
pg_assert_same( 0, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'a re-run with a pending product makes 0 products' );
pg_assert_same( 2, count( $pg_marked_products( array( 'publish', 'draft', 'pending', 'private' ) ) ), 'a re-run with a pending product makes no duplicate' );
pixelgrid_starter_remove();
pg_assert_same( 'trash', get_post_status( $pg_mug_id ), 'remove trashes the pending product' );

// A user category with the same name under another slug. Core makes a second term with the
// starter slug here; setup records only that one, and the user's category survives remove.
$pg_named = wp_insert_term( 'Fixture prints', 'product_cat', array( 'slug' => 'my-prints' ) );
$pg_named = is_wp_error( $pg_named ) ? 0 : (int) $pg_named['term_id'];
pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
$pg_print_id = pixelgrid_starter_find( 'product', 'shopfix:product-grid-print' );
$pg_print    = wc_get_product( $pg_print_id );
pg_assert( $pg_named > 0 && 1 === count( $pg_print->get_category_ids() ), 'the print gets one category when the name is taken' );
pg_assert( ! in_array( $pg_named, array_map( 'intval', (array) get_option( 'pixelgrid_starter_product_cats', array() ) ), true ), 'the user category with the same name is not recorded' );
pixelgrid_starter_remove();
pg_assert( get_term( $pg_named, 'product_cat' ) instanceof WP_Term, 'the user category with the same name survives remove' );

// When inserting reports term_exists (as it does for a same-name category in some setups),
// the existing term is used for the product and never recorded as created.
$pg_block = function ( $term, $taxonomy ) use ( $pg_named ) {
	if ( 'product_cat' === $taxonomy && 'Fixture prints' === $term ) {
		return new WP_Error( 'term_exists', 'A term with the name provided already exists.', $pg_named );
	}
	return $term;
};
add_filter( 'pre_insert_term', $pg_block, 10, 2 );
$made = $pg_watch(
	function () {
		return pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
	}
);
remove_filter( 'pre_insert_term', $pg_block, 10 );
$pg_print_id = pixelgrid_starter_find( 'product', 'shopfix:product-grid-print' );
pg_assert_same( 2, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'term_exists: both products are made' );
pg_assert_same( array( $pg_named ), array_map( 'intval', wc_get_product( $pg_print_id )->get_category_ids() ), 'term_exists: the print gets the existing category' );
pg_assert_same( array(), get_option( 'pixelgrid_starter_product_cats' ), 'term_exists: the existing category is not recorded' );
pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'term_exists: no notice' );
pixelgrid_starter_remove();
pg_assert( get_term( $pg_named, 'product_cat' ) instanceof WP_Term, 'term_exists: the existing category survives remove' );

pg_done();
