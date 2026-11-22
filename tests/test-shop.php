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
					'featured' => true,
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
pg_assert_same( '#B84A0E', $pg_shop_colors['paper-hover'] ?? null, 'shop light hover is burnt tangerine, dark enough for focus outlines on paper' );
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

// Shop images: 12 pixel-art products and 3 journal plates, all valid 1200x1200 WebP and small.
for ( $i = 1; $i <= 15; $i++ ) {
	$pg_rel  = sprintf( 'assets/images/shop-%02d.webp', $i );
	$pg_path = get_parent_theme_file_path( $pg_rel );
	pg_assert( file_exists( $pg_path ), "$pg_rel exists" );
	$pg_info = file_exists( $pg_path ) ? getimagesize( $pg_path ) : false;
	pg_assert( is_array( $pg_info ) && 'image/webp' === $pg_info['mime'], "$pg_rel is a valid WebP" );
	pg_assert( is_array( $pg_info ) && 1200 === $pg_info[0] && 1200 === $pg_info[1], "$pg_rel is 1200x1200" );
	pg_assert( file_exists( $pg_path ) && filesize( $pg_path ) <= 150 * 1024, "$pg_rel is at most 150 KB" );
	if ( $i > 12 ) {
		continue;
	}
	// Product pictures: sample the centre of each of the 16x16 drawing cells (64 px each, from 88 px in).
	$pg_img    = file_exists( $pg_path ) ? imagecreatefromwebp( $pg_path ) : false;
	$pg_colors = array();
	if ( $pg_img ) {
		for ( $py = 0; $py < 16; $py++ ) {
			for ( $px = 0; $px < 16; $px++ ) {
				$pg_colors[ imagecolorat( $pg_img, 88 + $px * 64 + 32, 88 + $py * 64 + 32 ) & 0xFFFFFF ] = true;
			}
		}
		pg_assert_same( 0xFBF6EC, imagecolorat( $pg_img, 2, 2 ) & 0xFFFFFF, "$pg_rel sits on a cream ground" );
	}
	printf( "INFO: %s colours %d size %d\n", $pg_rel, count( $pg_colors ), file_exists( $pg_path ) ? filesize( $pg_path ) : 0 );
	pg_assert( count( $pg_colors ) >= 3, "$pg_rel has at least 3 distinct colours" );
	pg_assert( file_exists( $pg_path ) && filesize( $pg_path ) <= 80 * 1024, "$pg_rel is at most 80 KB" );
}

// The Shop variant definition. These checks run with or without WooCommerce.
require_once get_parent_theme_file_path( 'inc/starter-screen.php' );
$pg_screen_with = function ( $get ) {
	$saved = $_GET;
	$_GET  = $get;
	ob_start();
	pixelgrid_starter_screen();
	$out  = ob_get_clean();
	$_GET = $saved;
	return $out;
};
$pg_def = pixelgrid_variant( 'shop' );
pg_assert( is_array( $pg_def ), 'the shop variant is registered' );
$pg_def = is_array( $pg_def ) ? $pg_def : pixelgrid_variant_defaults( array() );
pg_assert_same( 'Shop', $pg_def['label'], 'shop label is Shop' );
pg_assert_same( array( 'woocommerce' ), $pg_def['requires'], 'shop requires WooCommerce' );
pg_assert_same( 'styles/shop.json', $pg_def['style'], 'shop uses styles/shop.json' );
pg_assert_same(
	array(
		'header' => 'pixelgrid/shop-header',
		'footer' => 'pixelgrid/shop-footer',
	),
	$pg_def['parts'],
	'shop parts are the shop header and footer'
);
pg_assert_same( array( 'home' => 'pixelgrid/shop-template-home' ), $pg_def['templates'], 'shop templates: home' );
pg_assert_same( array( 'home', 'about', 'contact', 'journal' ), array_keys( $pg_def['pages'] ), 'shop pages: home, about, contact, journal' );
foreach ( array( 'home', 'about', 'contact' ) as $pg_slug ) {
	pg_assert_same( array( 'pixelgrid/shop-page-' . $pg_slug, 'page-sections' ), array_slice( (array) ( $pg_def['pages'][ $pg_slug ] ?? array() ), 1, 2 ), "shop $pg_slug page uses its pattern and page-sections" );
}
pg_assert_same( array( 'Journal', '', '' ), $pg_def['pages']['journal'] ?? null, 'shop journal page is empty' );
pg_assert_same( 'home', $pg_def['front'], 'shop front is home' );
pg_assert_same( 'journal', $pg_def['posts_page'], 'shop posts page is journal' );
pg_assert_same( array( 'woo:shop', 'about', 'journal', 'contact' ), $pg_def['menu'], 'shop menu: shop, about, journal, contact' );
pg_assert_same( array( 'news', 'guides' ), array_keys( $pg_def['categories'] ), 'shop categories: news, guides' );
pg_assert_same( array( 'kitchen', 'home', 'outdoor', 'tech' ), array_keys( (array) ( $pg_def['product_categories'] ?? array() ) ), 'shop product categories: kitchen, home, outdoor, tech' );
pg_assert_same( '', $pg_def['preview'], 'shop has no preview yet' );
pg_assert( '' !== $pg_def['design_note'] && ! empty( $pg_def['content_labels']['pages'] ) && ! empty( $pg_def['content_labels']['posts'] ) && ! empty( $pg_def['content_labels']['reading'] ), 'shop has its own content labels and design note' );
pg_assert_same( array(), pixelgrid_variant_defaults( array() )['product_categories'] ?? null, 'variant defaults include an empty product_categories' );

pg_assert_same( 3, count( $pg_def['posts'] ), 'shop has 3 journal posts' );
$pg_post_images = array();
foreach ( $pg_def['posts'] as $pg_post ) {
	$pg_post_images[] = $pg_post['image'];
	pg_assert( in_array( $pg_post['category'], array( 'news', 'guides' ), true ), "journal post {$pg_post['key']} is in news or guides" );
	pg_assert( substr_count( $pg_post['content'], '<!-- wp:paragraph -->' ) >= 3, "journal post {$pg_post['key']} has real text" );
}
pg_assert_same( array( 'assets/images/shop-13.webp', 'assets/images/shop-14.webp', 'assets/images/shop-15.webp' ), $pg_post_images, 'journal posts use shop-13 to shop-15' );
$pg_used_cats = array_values( array_unique( wp_list_pluck( $pg_def['posts'], 'category' ) ) );
sort( $pg_used_cats );
pg_assert_same( array( 'guides', 'news' ), $pg_used_cats, 'journal posts use both news and guides' );

$pg_names = array( 'Everyday stoneware mug', 'Round teapot', 'Deep serving bowl', 'Desk lamp', 'Wall clock', 'Ribbed vase', 'Potted fern', 'Watering can', 'Day backpack', 'Over-ear headphones', 'Simple watch', 'Pocket radio' );
pg_assert_same( $pg_names, wp_list_pluck( $pg_def['products'], 'name' ), 'shop has the 12 Common Goods products in picture order' );
$pg_images = array();
$pg_sale   = array();
$pg_feat   = array();
foreach ( $pg_def['products'] as $pg_item ) {
	$pg_images[] = $pg_item['image'];
	if ( isset( $pg_item['sale'] ) ) {
		$pg_sale[] = $pg_item['name'];
		pg_assert( (float) $pg_item['sale'] < (float) $pg_item['price'], "{$pg_item['name']} sale price is below its price" );
	}
	if ( ! empty( $pg_item['featured'] ) ) {
		$pg_feat[] = $pg_item['name'];
	}
	pg_assert( (bool) preg_match( '/^[0-9]+(\.[0-9]{1,2})?$/', (string) $pg_item['price'] ), "{$pg_item['name']} price is a plain number" );
	pg_assert( isset( $pg_def['product_categories'][ $pg_item['category'] ] ), "{$pg_item['name']} is in a shop product category" );
	pg_assert( '' !== $pg_item['short'] && false !== strpos( $pg_item['content'], '<!-- wp:paragraph -->' ), "{$pg_item['name']} has a short and a long description" );
	pg_assert( ! preg_match( '/[$\x{20AC}\x{00A3}\x{00A5}]|USD|EUR|GBP/u', $pg_item['short'] . $pg_item['content'] ), "{$pg_item['name']} copy names no currency" );
}
$pg_want_images = array();
for ( $pg_i = 1; $pg_i <= 12; $pg_i++ ) {
	$pg_want_images[] = sprintf( 'assets/images/shop-%02d.webp', $pg_i );
}
pg_assert_same( $pg_want_images, $pg_images, 'products use shop-01 to shop-12 in order' );
pg_assert_same( array( 'Round teapot', 'Desk lamp', 'Watering can', 'Over-ear headphones' ), $pg_sale, 'teapot, lamp, watering can and headphones are on sale' );
pg_assert_same( 4, count( $pg_feat ), '4 products are featured' );

// The success notice counts products when the variant has them.
pg_assert( function_exists( 'pixelgrid_starter_created_args' ), 'pixelgrid_starter_created_args exists' );
if ( function_exists( 'pixelgrid_starter_created_args' ) ) {
	pg_assert_same(
		array( 'pixelgrid_created' => '4-3-1' ),
		pixelgrid_starter_created_args(
			array(
				'pages' => 4,
				'posts' => 3,
				'menu'  => 1,
			)
		),
		'created args without products keep three counts'
	);
	pg_assert_same(
		array( 'pixelgrid_created' => '4-3-1-12' ),
		pixelgrid_starter_created_args(
			array(
				'pages'    => 4,
				'posts'    => 3,
				'menu'     => 1,
				'products' => 12,
			)
		),
		'created args carry the products count'
	);
}
$pg_html = $pg_screen_with( array( 'pixelgrid_created' => '4-3-1-12' ) );
pg_assert( false !== strpos( $pg_html, 'Starter content added: 4 pages, 3 posts and 1 menu.' ) && false !== strpos( $pg_html, '12 products were added to the shop.' ), 'success notice counts the products' );
$pg_html = $pg_screen_with( array( 'pixelgrid_created' => '4-3-1-1' ) );
pg_assert( false !== strpos( $pg_html, '1 product was added to the shop.' ), 'success notice uses the singular for one product' );
$pg_html = $pg_screen_with( array( 'pixelgrid_created' => '4-3-1' ) );
pg_assert( false === strpos( $pg_html, 'added to the shop' ), 'success notice has no products sentence for a variant without products' );

// The Shop card on the setup screen.
$pg_shop_card_of = function ( $html ) {
	foreach ( explode( '<label class="pixelgrid-card', $html ) as $pg_part ) {
		if ( false !== strpos( $pg_part, 'value="shop"' ) ) {
			return (string) strstr( $pg_part, '</label>', true );
		}
	}
	return '';
};
$pg_shop_radio_of = function ( $html ) {
	return preg_match( '/<input[^>]*value="shop"[^>]*>/', $html, $m ) ? $m[0] : '';
};
$pg_html       = $pg_screen_with( array() );
$pg_shop_card  = $pg_shop_card_of( $pg_html );
$pg_shop_radio = $pg_shop_radio_of( $pg_html );
pg_assert( false !== strpos( $pg_shop_card, '<strong>Shop</strong>' ), 'screen has the shop card labelled Shop' );

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

	// The Shop starting point without WooCommerce: shown, disabled, with the install link.
	pg_assert( '' !== $pg_shop_radio && false !== strpos( $pg_shop_radio, 'disabled' ), 'without WooCommerce, the shop radio is disabled' );
	pg_assert( false === strpos( $pg_shop_radio, 'checked' ), 'without WooCommerce, the shop radio is not chosen' );
	pg_assert( false !== strpos( $pg_shop_card, 'plugin-install.php?s=woocommerce' ) && false !== strpos( $pg_shop_card, 'Install the required plugin' ), 'without WooCommerce, the shop card has the install link' );
	$pg_err = pixelgrid_starter_create( 'shop', array( 'pages' => true ) );
	pg_assert( is_wp_error( $pg_err ) && 'pixelgrid_variant_unavailable' === $pg_err->get_error_code(), 'without WooCommerce, shop create gives pixelgrid_variant_unavailable' );
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
pg_assert_same( false, $pg_mug->get_featured(), 'mug, with no featured field, is not featured' );
pg_assert_same( true, $pg_print->get_featured(), 'print, with a truthy featured field, is featured' );
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

// The Shop starting point, end to end.
pg_assert( '' !== $pg_shop_radio && false === strpos( $pg_shop_radio, 'disabled' ), 'with WooCommerce, the shop radio is enabled' );
pg_assert( false === strpos( $pg_shop_card, 'Install the required plugin' ), 'with WooCommerce, the shop card has no install link' );

// Plain permalinks for the HTTP checks below.
$GLOBALS['wp_rewrite']->set_permalink_structure( '' );
flush_rewrite_rules( false );

$pg_get = function ( $url, $cookies = array() ) {
	$res = wp_remote_get(
		$url,
		array(
			'timeout' => 60,
			'cookies' => $cookies,
		)
	);
	return array( (int) wp_remote_retrieve_response_code( $res ), (string) wp_remote_retrieve_body( $res ) );
};
require_once ABSPATH . 'wp-admin/includes/plugin.php';
// The HTTP requests run with WP_DEBUG and WP_DEBUG_LOG on, so notices land in the log.
$pg_config_path = ABSPATH . 'wp-config.php';
$pg_config_orig = (string) file_get_contents( $pg_config_path );
$pg_debug_log   = WP_CONTENT_DIR . '/debug.log';
$pg_probe_path  = WP_CONTENT_DIR . '/mu-plugins/pixelgrid-test-probe.php';
register_shutdown_function(
	function () use ( $pg_config_path, $pg_config_orig, $pg_probe_path ) {
		file_put_contents( $pg_config_path, $pg_config_orig );
		if ( file_exists( $pg_probe_path ) ) {
			unlink( $pg_probe_path );
		}
		// WooCommerce is always active again when this file ends.
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			activate_plugin( 'woocommerce/woocommerce.php' );
		}
	}
);
$pg_debug_defs = "if ( ! defined( 'WP_DEBUG' ) ) { define( 'WP_DEBUG', true ); }\nif ( ! defined( 'WP_DEBUG_LOG' ) ) { define( 'WP_DEBUG_LOG', true ); }\nif ( ! defined( 'WP_DEBUG_DISPLAY' ) ) { define( 'WP_DEBUG_DISPLAY', false ); }\n";
$pg_config_new = preg_replace( "/define\(\s*'WP_DEBUG',\s*false\s*\);\s*/", '', $pg_config_orig );
$pg_config_new = preg_replace( '/^<\?php\s*/', "<?php\n" . $pg_debug_defs, $pg_config_new, 1 );
file_put_contents( $pg_config_path, $pg_config_new );
$pg_log_lines = function () use ( $pg_debug_log ) {
	if ( ! file_exists( $pg_debug_log ) ) {
		return array();
	}
	clearstatcache();
	$out = array();
	foreach ( preg_split( '/\R/', (string) file_get_contents( $pg_debug_log ) ) as $line ) {
		// Only PHP 8.5 deprecations raised inside WordPress core are noise; any other line counts.
		$core_deprecation = false !== strpos( $line, 'Deprecated' ) && preg_match( '#[\\\\/]wp-(includes|admin)[\\\\/]#', $line );
		// WooCommerce's Action Scheduler logs this core cron message once it is deactivated; it is not the theme's.
		$woo_cron = false !== strpos( $line, 'Cron reschedule event error for hook: action_scheduler_run_queue' );
		if ( '' !== trim( $line ) && ! $core_deprecation && ! $woo_cron ) {
			$out[] = $line;
		}
	}
	return $out;
};
// The log check proves itself: a probe notice raised over HTTP lands in the log.
if ( ! is_dir( dirname( $pg_probe_path ) ) ) {
	mkdir( dirname( $pg_probe_path ) );
}
file_put_contents( $pg_probe_path, "<?php\nif ( isset( \$_GET['pixelgrid_probe'] ) ) { trigger_error( 'pixelgrid test probe', E_USER_NOTICE ); }\n" );
$pg_probe_start = count( $pg_log_lines() );
$pg_get( add_query_arg( 'pixelgrid_probe', '1', home_url( '/' ) ) );
$pg_probe_new = array_slice( $pg_log_lines(), $pg_probe_start );
pg_assert( 1 === count( $pg_probe_new ) && false !== strpos( $pg_probe_new[0], 'pixelgrid test probe' ), 'the debug log catches a notice raised over HTTP' );
unlink( $pg_probe_path );
$pg_no_php_message = function ( $html ) {
	return false === strpos( $html, 'Warning:' ) && false === strpos( $html, 'Notice:' ) && false === strpos( $html, 'Fatal error' ) && false === strpos( $html, 'critical error' );
};

$pg_theme        = get_stylesheet();
$pg_header_block = '<!-- wp:template-part {"slug":"header","theme":"' . $pg_theme . '"} /-->';
$pg_styles_id    = (int) WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true )['ID'];
$pg_styles_prev  = get_post( $pg_styles_id )->post_content;
$pg_wc_pages     = array();
foreach ( array( 'shop', 'cart', 'checkout', 'myaccount' ) as $pg_slug ) {
	$pg_wc_pages[ $pg_slug ] = (int) wc_get_page_id( $pg_slug );
	pg_assert( $pg_wc_pages[ $pg_slug ] > 0 && 'publish' === get_post_status( $pg_wc_pages[ $pg_slug ] ), "the WooCommerce $pg_slug page exists before setup" );
}
$pg_all       = array(
	'design'  => true,
	'pages'   => true,
	'posts'   => true,
	'menu'    => true,
	'reading' => true,
);
$pg_log_start = count( $pg_log_lines() );

$pg_made = $pg_watch(
	function () use ( $pg_all ) {
		return pixelgrid_starter_create( 'shop', $pg_all );
	}
);
pg_assert_same(
	array(
		'pages'    => 4,
		'posts'    => 3,
		'menu'     => 1,
		'products' => 12,
	),
	$pg_made,
	'shop create makes 4 pages, 3 posts, 12 products and 1 menu'
);
pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'shop create raises no notice' );

// Products: prices, sale prices, featured flags and categories.
$pg_product_ids = array();
$pg_featured    = array();
foreach ( $pg_def['products'] as $pg_item ) {
	$pg_id                              = pixelgrid_starter_find( 'product', 'shop:product-' . $pg_item['key'] );
	$pg_product_ids[ $pg_item['key'] ] = $pg_id;
	$pg_p                               = $pg_id ? wc_get_product( $pg_id ) : null;
	pg_assert( $pg_p instanceof WC_Product, "product {$pg_item['key']} exists" );
	if ( ! $pg_p ) {
		continue;
	}
	pg_assert_same( (string) $pg_item['price'], $pg_p->get_regular_price(), "product {$pg_item['key']} price" );
	pg_assert_same( isset( $pg_item['sale'] ) ? (string) $pg_item['sale'] : '', $pg_p->get_sale_price(), "product {$pg_item['key']} sale price" );
	pg_assert_same( ! empty( $pg_item['featured'] ), $pg_p->get_featured(), "product {$pg_item['key']} featured flag" );
	$pg_cat = get_term_by( 'slug', $pg_item['category'], 'product_cat' );
	pg_assert( $pg_cat && array( (int) $pg_cat->term_id ) === array_map( 'intval', $pg_p->get_category_ids() ), "product {$pg_item['key']} is in {$pg_item['category']}" );
	pg_assert( (int) $pg_p->get_image_id() > 0, "product {$pg_item['key']} has its picture" );
	if ( $pg_p->get_featured() ) {
		$pg_featured[] = $pg_p->get_name();
	}
}
pg_assert_same( 4, count( $pg_featured ), '4 products are featured in WooCommerce' );

// The menu links to the WooCommerce shop page first.
$pg_menu_id = pixelgrid_starter_find( 'wp_navigation', 'shop:menu' );
$pg_menu    = $pg_menu_id ? parse_blocks( get_post_field( 'post_content', $pg_menu_id ) ) : array();
$pg_links   = array();
foreach ( $pg_menu as $pg_block ) {
	if ( 'core/navigation-link' === $pg_block['blockName'] ) {
		$pg_links[] = $pg_block['attrs'];
	}
}
pg_assert_same( 4, count( $pg_links ), 'the menu has 4 links' );
$pg_first_link = $pg_links[0] ?? array();
pg_assert_same( get_the_title( $pg_wc_pages['shop'] ), $pg_first_link['label'] ?? null, 'the first menu link is labelled with the shop page title' );
pg_assert_same( wc_get_page_permalink( 'shop' ), $pg_first_link['url'] ?? null, 'the first menu link goes to the WooCommerce shop page' );
pg_assert_same( $pg_wc_pages['shop'], $pg_first_link['id'] ?? null, 'the first menu link carries the shop page id' );
pg_assert( 'page' === ( $pg_first_link['type'] ?? '' ) && 'post-type' === ( $pg_first_link['kind'] ?? '' ), 'the first menu link is a page link' );
pg_assert_same( array( 'About', 'Journal', 'Contact' ), array_slice( wp_list_pluck( $pg_links, 'label' ), 1 ), 'the other links are About, Journal and Contact' );

// Home: 4 featured products and 4 category tiles.
list( $pg_code, $pg_home ) = $pg_get( home_url( '/' ) );
pg_assert_same( 200, $pg_code, 'Home returns 200' );
pg_assert( $pg_no_php_message( $pg_home ), 'Home shows no PHP message' );
// The featured collection is the first product collection on Home (the class also appears in an inline style).
$pg_featured_at = strpos( $pg_home, 'class="wp-block-woocommerce-product-collection' );
$pg_featured_to = false !== $pg_featured_at ? strpos( $pg_home, '</ul>', $pg_featured_at ) : false;
$pg_featured_ul = false !== $pg_featured_to ? substr( $pg_home, $pg_featured_at, $pg_featured_to - $pg_featured_at ) : '';
preg_match_all( '/<li[^>]*class="[^"]*wc-block-product[^"]*"/', $pg_featured_ul, $pg_items );
pg_assert_same( 4, count( $pg_items[0] ), 'Home featured section lists 4 products' );
foreach ( $pg_featured as $pg_name ) {
	pg_assert( false !== strpos( $pg_featured_ul, esc_html( $pg_name ) ), "Home featured section lists $pg_name" );
}
pg_assert_same( 4, substr_count( $pg_home, 'wp-block-column pixelgrid-shop-tile' ), 'Home has 4 category tiles' );
pg_assert( false !== strpos( $pg_home, 'wc-block-mini-cart' ), 'the header renders the mini-cart' );
pg_assert( false !== strpos( do_blocks( $pg_header_block ), 'wc-block-mini-cart' ), 'the header part holds the mini-cart' );

// The shop page lists the 12 products.
list( $pg_code, $pg_shop_html ) = $pg_get( home_url( '/?post_type=product' ) );
pg_assert_same( 200, $pg_code, 'the shop page returns 200' );
pg_assert( $pg_no_php_message( $pg_shop_html ), 'the shop page shows no PHP message' );
$pg_listed = 0;
foreach ( $pg_def['products'] as $pg_item ) {
	if ( false !== strpos( $pg_shop_html, esc_html( $pg_item['name'] ) ) ) {
		++$pg_listed;
	}
}
pg_assert_same( 12, $pg_listed, 'the shop page lists the 12 products' );

// A product page: its price and an add-to-cart form.
$pg_teapot                   = wc_get_product( $pg_product_ids['round-teapot'] ?? 0 );
$pg_teapot_url               = $pg_teapot ? get_permalink( $pg_teapot->get_id() ) : home_url( '/' );
list( $pg_code, $pg_single ) = $pg_get( $pg_teapot_url );
pg_assert_same( 200, $pg_code, 'the teapot page returns 200' );
pg_assert( $pg_no_php_message( $pg_single ), 'the teapot page shows no PHP message' );
pg_assert( false !== strpos( $pg_single, 'woocommerce-Price-amount' ) && false !== strpos( $pg_single, '29' ) && false !== strpos( $pg_single, '38' ), 'the teapot page shows its sale and regular price' );
pg_assert( (bool) preg_match( '/<form[^>]*class="[^"]*\bcart\b/', $pg_single ) && false !== strpos( $pg_single, 'add-to-cart' ), 'the teapot page has an add-to-cart form' );

// Cart and checkout.
list( $pg_code, $pg_cart ) = $pg_get( wc_get_page_permalink( 'cart' ) );
pg_assert_same( 200, $pg_code, 'the cart page returns 200' );
pg_assert( false !== strpos( $pg_cart, 'wp-block-woocommerce-cart' ), 'the cart page holds the cart block' );
list( $pg_code, $pg_checkout ) = $pg_get( wc_get_page_permalink( 'checkout' ) );
pg_assert_same( 200, $pg_code, 'the checkout page returns 200' );
pg_assert( false !== strpos( $pg_checkout, 'wp-block-woocommerce-checkout' ) || false !== strpos( $pg_checkout, 'wp-block-woocommerce-cart' ), 'the checkout page holds the WooCommerce block markup' );

// The user styles hold the Shop palette.
$pg_user_styles = json_decode( get_post( $pg_styles_id )->post_content, true );
$pg_user_colors = array();
foreach ( (array) ( $pg_user_styles['settings']['color']['palette'] ?? array() ) as $pg_entry ) {
	$pg_user_colors[ $pg_entry['slug'] ?? '' ] = strtoupper( $pg_entry['color'] ?? '' );
}
pg_assert_same( $pg_shop_colors, $pg_user_colors, 'the user styles hold the Shop palette' );
pg_assert_same( array(), array_slice( $pg_log_lines(), $pg_log_start ), 'no PHP notices logged during Shop setup and page views' );

// The setup screen counts the products in the redirect.
pg_assert_same( array( 'pixelgrid_created' => '4-3-1-12' ), pixelgrid_starter_created_args( $pg_made ), 'the redirect after Shop setup carries 12 products' );

// Another starting point cannot be added on top.
$pg_busy = pixelgrid_starter_create( 'news', array( 'pages' => true ) );
pg_assert( is_wp_error( $pg_busy ) && 'pixelgrid_variant_busy' === $pg_busy->get_error_code(), 'news on top of shop gives pixelgrid_variant_busy' );

// Remove.
$pg_cat_ids = array();
foreach ( array_keys( $pg_def['product_categories'] ) as $pg_slug ) {
	$pg_cat_ids[ $pg_slug ] = (int) get_term_by( 'slug', $pg_slug, 'product_cat' )->term_id;
}
$pg_removed = $pg_watch( 'pixelgrid_starter_remove' );
pg_assert( $pg_removed['removed'] >= 30, 'shop remove removes at least 30 items (got ' . $pg_removed['removed'] . ')' );
pg_assert_same( array(), $pg_removed['kept'], 'shop remove keeps nothing' );
pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'shop remove raises no notice' );
$pg_left = array();
foreach ( $pg_product_ids as $pg_key => $pg_id ) {
	if ( 'trash' !== get_post_status( $pg_id ) ) {
		$pg_left[] = $pg_key;
	}
}
pg_assert_same( array(), $pg_left, 'shop remove trashes the 12 products' );
foreach ( $pg_cat_ids as $pg_slug => $pg_term_id ) {
	pg_assert( null === get_term( $pg_term_id, 'product_cat' ), "shop remove deletes the empty product category $pg_slug" );
}
pg_assert_same( $pg_styles_prev, get_post( $pg_styles_id )->post_content, 'the user styles are back to the pre-setup content' );
$pg_header_now = do_blocks( $pg_header_block );
pg_assert( false !== strpos( $pg_header_now, 'Get a quote' ) && false === strpos( $pg_header_now, 'wc-block-mini-cart' ), 'the header renders the theme default after removal' );
foreach ( $pg_wc_pages as $pg_slug => $pg_id ) {
	pg_assert_same( 'publish', get_post_status( $pg_id ), "the WooCommerce $pg_slug page stays after remove" );
}

// Review Focus 1: WooCommerce is deactivated after setup.
pg_assert( is_array( pixelgrid_starter_create( 'shop', $pg_all ) ), 'shop created again' );
$pg_teapot_id  = pixelgrid_starter_find( 'product', 'shop:product-round-teapot' );
$pg_teapot_url = get_permalink( $pg_teapot_id );
deactivate_plugins( 'woocommerce/woocommerce.php' );
pg_assert( ! is_plugin_active( 'woocommerce/woocommerce.php' ), 'WooCommerce deactivated' );
$pg_log_before             = count( $pg_log_lines() );
list( $pg_code, $pg_after ) = $pg_get( home_url( '/' ) );
pg_assert_same( 200, $pg_code, 'without WooCommerce, Home returns 200' );
pg_assert( $pg_no_php_message( $pg_after ), 'without WooCommerce, Home shows no PHP message' );
pg_assert( false === strpos( $pg_after, 'wc-block-mini-cart' ), 'without WooCommerce, the header renders no mini-cart' );
list( $pg_code, $pg_after_product ) = $pg_get( $pg_teapot_url );
pg_assert( in_array( $pg_code, array( 200, 404 ), true ), 'without WooCommerce, the product address answers with 200 or 404 (got ' . $pg_code . ')' );
pg_assert( $pg_no_php_message( $pg_after_product ), 'without WooCommerce, the product address shows no PHP message' );
// The setup screen over HTTP, signed in: Shop is disabled with the install link, and the admin shows no PHP message.
$pg_uid     = get_current_user_id();
$pg_expires = time() + HOUR_IN_SECONDS;
$pg_cookies = array(
	AUTH_COOKIE      => wp_generate_auth_cookie( $pg_uid, $pg_expires, 'auth' ),
	LOGGED_IN_COOKIE => wp_generate_auth_cookie( $pg_uid, $pg_expires, 'logged_in' ),
);
list( $pg_code, $pg_admin ) = $pg_get( admin_url( 'themes.php?page=pixelgrid-setup' ), $pg_cookies );
pg_assert_same( 200, $pg_code, 'without WooCommerce, the setup screen returns 200' );
pg_assert( $pg_no_php_message( $pg_admin ), 'without WooCommerce, the setup screen shows no PHP message' );
$pg_off_radio = $pg_shop_radio_of( $pg_admin );
$pg_off_card  = $pg_shop_card_of( $pg_admin );
pg_assert( '' !== $pg_off_radio && false !== strpos( $pg_off_radio, 'disabled' ), 'without WooCommerce, the setup screen shows the shop radio disabled' );
pg_assert( false !== strpos( $pg_off_card, 'Install the required plugin' ), 'without WooCommerce, the setup screen shows the install link' );
pg_assert_same( array(), array_slice( $pg_log_lines(), $pg_log_before ), 'without WooCommerce, no PHP notice is logged for Home, the product address and the setup screen' );
$pg_removed = $pg_watch( 'pixelgrid_starter_remove' );
pg_assert_same( 'trash', get_post_status( $pg_teapot_id ), 'without WooCommerce, remove still trashes the products' );
pg_assert( $pg_removed['removed'] >= 30, 'without WooCommerce, remove still counts at least 30 items' );
pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'without WooCommerce, remove raises no notice' );
activate_plugin( 'woocommerce/woocommerce.php' );
pg_assert( is_plugin_active( 'woocommerce/woocommerce.php' ), 'WooCommerce reactivated' );

pg_done();
