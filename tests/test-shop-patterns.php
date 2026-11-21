<?php
/**
 * Shop patterns: registered in PHP (inc/shop-patterns.php), only when WooCommerce is active.
 *
 * Runs on both sites: `site.sh up` (WooCommerce inactive) and `site.sh up --woo`.
 */

$pg_woo = class_exists( 'WooCommerce' );
echo $pg_woo ? "INFO: WooCommerce is active.\n" : "INFO: WooCommerce is not active.\n";

$pg_slugs = array(
	'shop-header',
	'shop-footer',
	'shop-hero',
	'shop-featured',
	'shop-categories',
	'shop-promo',
	'shop-best-sellers',
	'shop-values',
	'shop-cta',
	'shop-page-home',
	'shop-page-about',
	'shop-page-contact',
	'shop-blog-heading',
	'shop-template-home',
);

// The file exists, is required from functions.php, and nothing is in patterns/.
pg_assert( file_exists( get_parent_theme_file_path( 'inc/shop-patterns.php' ) ), 'inc/shop-patterns.php exists' );
$pg_functions = (string) file_get_contents( get_parent_theme_file_path( 'functions.php' ) );
pg_assert( false !== strpos( $pg_functions, "require get_parent_theme_file_path( 'inc/shop-patterns.php' );" ), 'functions.php requires inc/shop-patterns.php' );
pg_assert_same( array(), glob( get_parent_theme_file_path( 'patterns' ) . '/shop-*.php' ), 'no shop pattern file in patterns/' );
pg_assert( false === strpos( (string) file_get_contents( get_parent_theme_file_path( 'inc/shop-patterns.php' ) ), '"theme":' ), 'inc/shop-patterns.php sets no theme attribute' );

$pg_registry = WP_Block_Patterns_Registry::get_instance();
$pg_cat      = WP_Block_Pattern_Categories_Registry::get_instance()->get_registered( 'pixelgrid-shop' );

if ( ! $pg_woo ) {
	$pg_found = array();
	foreach ( $pg_registry->get_all_registered() as $pg_pattern ) {
		if ( 0 === strpos( $pg_pattern['name'], 'pixelgrid/shop-' ) ) {
			$pg_found[] = $pg_pattern['name'];
		}
	}
	pg_assert_same( array(), $pg_found, 'without WooCommerce no pixelgrid/shop-* pattern is registered' );
	pg_assert_same( null, $pg_cat, 'without WooCommerce the pixelgrid-shop category is not registered' );
	pg_done();
}

pg_assert_same( 'Pixelgrid Shop', $pg_cat['label'] ?? null, 'pattern category pixelgrid-shop is labelled Pixelgrid Shop' );

$pg_theme_uri  = trailingslashit( get_template_directory_uri() );
$pg_block_reg  = WP_Block_Type_Registry::get_instance();
$pg_shop       = array();
$pg_patterns   = array();
$pg_all_blocks = array();
foreach ( $pg_slugs as $pg_slug ) {
	$pg_pattern = $pg_registry->get_registered( 'pixelgrid/' . $pg_slug );
	pg_assert( is_array( $pg_pattern ), "pixelgrid/$pg_slug is registered" );
	$pg_patterns[ $pg_slug ] = is_array( $pg_pattern ) ? $pg_pattern : array();
	$pg_content              = is_array( $pg_pattern ) ? pixelgrid_starter_expand( $pg_pattern['content'] ) : '';
	$pg_shop[ $pg_slug ]     = $pg_content;
	pg_assert( '' !== trim( $pg_content ), "pixelgrid/$pg_slug has expanded content" );
	pg_assert( false === strpos( $pg_content, '<!-- wp:pattern' ), "pixelgrid/$pg_slug has no unexpanded pattern refs" );
	pg_assert( ! preg_match( '/"(id|ref)":\s*\d/', $pg_content ), "pixelgrid/$pg_slug has no numeric id or ref attribute" );
	pg_assert( ! preg_match( '/[€£¥]|\$\s?\d/u', wp_strip_all_tags( $pg_content ) . $pg_content ), "pixelgrid/$pg_slug has no hardcoded currency" );

	$pg_junk  = array();
	$pg_names = array();
	$pg_walk  = function ( $blocks ) use ( &$pg_walk, &$pg_junk, &$pg_names, &$pg_all_blocks, $pg_slug ) {
		foreach ( $blocks as $block ) {
			if ( null === $block['blockName'] ) {
				if ( '' !== trim( $block['innerHTML'] ) ) {
					$pg_junk[] = trim( $block['innerHTML'] );
				}
				continue;
			}
			$pg_names[]                  = $block['blockName'];
			$pg_all_blocks[ $pg_slug ][] = $block;
			$pg_walk( $block['innerBlocks'] );
		}
	};
	$pg_walk( parse_blocks( $pg_content ) );
	pg_assert( array() === $pg_junk, "pixelgrid/$pg_slug parses with no freeform text" . ( $pg_junk ? ': ' . substr( $pg_junk[0], 0, 80 ) : '' ) );
	$pg_missing = array_values(
		array_filter(
			array_unique( $pg_names ),
			function ( $name ) use ( $pg_block_reg ) {
				return ! $pg_block_reg->is_registered( $name );
			}
		)
	);
	pg_assert_same( array(), $pg_missing, "pixelgrid/$pg_slug uses only registered blocks" );

	preg_match_all( '/<img[^>]+src="([^"]+)"/', $pg_content, $pg_imgs );
	foreach ( $pg_imgs[1] as $pg_src ) {
		$pg_rel = 0 === strpos( $pg_src, $pg_theme_uri ) ? substr( $pg_src, strlen( $pg_theme_uri ) ) : $pg_src;
		pg_assert( 0 === strpos( $pg_rel, 'assets/images/shop-' ) && file_exists( get_parent_theme_file_path( $pg_rel ) ), "pixelgrid/$pg_slug image $pg_rel is a Shop theme file" );
	}

	// Rendering raises no PHP notice or warning.
	$pg_errors = array();
	set_error_handler(
		function ( $errno, $errstr, $errfile, $errline ) use ( &$pg_errors ) {
			if ( in_array( $errno, array( E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE ), true ) ) {
				$pg_errors[] = "$errstr ($errfile:$errline)";
			}
			return true;
		}
	);
	$pg_html = '' !== $pg_content ? do_blocks( $pg_content ) : '';
	restore_error_handler();
	pg_assert( array() === $pg_errors, "pixelgrid/$pg_slug renders with no PHP notices" . ( $pg_errors ? ': ' . $pg_errors[0] : '' ) );
	pg_assert( false === strpos( $pg_html, 'Warning:' ) && false === strpos( $pg_html, 'Notice:' ), "pixelgrid/$pg_slug output has no PHP messages" );
}

$pg_has = function ( $slug, $needle ) use ( $pg_shop ) {
	return false !== strpos( $pg_shop[ $slug ] ?? '', $needle );
};
$pg_blocks_named = function ( $slug, $name ) use ( $pg_all_blocks ) {
	return array_values(
		array_filter(
			$pg_all_blocks[ $slug ] ?? array(),
			function ( $block ) use ( $name ) {
				return $name === $block['blockName'];
			}
		)
	);
};

// Categories, inserter and block types.
foreach ( array( 'shop-hero', 'shop-featured', 'shop-categories', 'shop-promo', 'shop-best-sellers', 'shop-values', 'shop-cta' ) as $pg_slug ) {
	pg_assert( in_array( 'pixelgrid-shop', $pg_patterns[ $pg_slug ]['categories'] ?? array(), true ), "$pg_slug is in pixelgrid-shop" );
	pg_assert( ! empty( $pg_patterns[ $pg_slug ]['description'] ), "$pg_slug has a description" );
}
foreach ( array( 'shop-page-home', 'shop-page-about', 'shop-page-contact' ) as $pg_slug ) {
	pg_assert( in_array( 'pixelgrid-pages', $pg_patterns[ $pg_slug ]['categories'] ?? array(), true ), "$pg_slug is in pixelgrid-pages" );
	pg_assert_same( array( 'page' ), $pg_patterns[ $pg_slug ]['postTypes'] ?? null, "$pg_slug is for pages" );
}
foreach ( array( 'shop-blog-heading', 'shop-template-home' ) as $pg_slug ) {
	pg_assert_same( false, $pg_patterns[ $pg_slug ]['inserter'] ?? null, "$pg_slug is hidden from the inserter" );
}
pg_assert_same( array( 'core/template-part/header' ), $pg_patterns['shop-header']['blockTypes'] ?? null, 'shop-header is a header pattern' );
pg_assert_same( array( 'core/template-part/footer' ), $pg_patterns['shop-footer']['blockTypes'] ?? null, 'shop-footer is a footer pattern' );

// Header: announcement strip, then title, menu, product search, account and mini-cart.
pg_assert( $pg_has( 'shop-header', 'pixelgrid-shop-announce' ), 'shop-header has the announcement strip' );
pg_assert( $pg_has( 'shop-header', '30-day returns' ), 'the announcement mentions 30-day returns' );
$pg_order = array( 'wp:site-title', 'wp:navigation', '"namespace":"woocommerce/product-search"', 'wp:woocommerce/customer-account', 'wp:woocommerce/mini-cart' );
$pg_last  = -1;
$pg_ok    = true;
foreach ( $pg_order as $pg_needle ) {
	$pg_pos = strpos( $pg_shop['shop-header'], $pg_needle );
	$pg_ok  = $pg_ok && false !== $pg_pos && $pg_pos > $pg_last;
	$pg_last = false === $pg_pos ? $pg_last : $pg_pos;
}
pg_assert( $pg_ok, 'shop-header has the title, menu, product search, account and mini-cart in order' );
pg_assert( strpos( $pg_shop['shop-header'], 'pixelgrid-shop-announce' ) < strpos( $pg_shop['shop-header'], 'wp:site-title' ), 'the announcement strip comes first' );
$pg_search = $pg_blocks_named( 'shop-header', 'core/search' );
pg_assert_same( array( 'post_type' => 'product' ), $pg_search[0]['attrs']['query'] ?? null, 'the header search looks for products' );

// Footer: shop link columns by product category, a payment note and the copyright.
$pg_cats = array( 'kitchen', 'home', 'outdoor', 'tech' );
foreach ( $pg_cats as $pg_c ) {
	$pg_url = esc_url( add_query_arg( 'product_cat', $pg_c, home_url( '/' ) ) );
	pg_assert( $pg_has( 'shop-footer', 'href="' . $pg_url . '"' ), "shop-footer links to the $pg_c product category" );
	pg_assert( $pg_has( 'shop-categories', 'href="' . $pg_url . '"' ), "shop-categories has a $pg_c tile" );
}
pg_assert( $pg_has( 'shop-footer', 'pixelgrid-shop-payment' ), 'shop-footer has a payment note' );
pg_assert( $pg_has( 'shop-footer', gmdate( 'Y' ) ), 'shop-footer has the copyright year' );
pg_assert_same( 4, substr_count( $pg_shop['shop-categories'], '<div class="wp-block-column pixelgrid-shop-tile">' ), 'shop-categories has 4 tiles' );

// Product collections: featured and best sellers, 4 items, notched colour pictures.
$pg_collection = function ( $slug ) use ( $pg_blocks_named ) {
	$found = $pg_blocks_named( $slug, 'woocommerce/product-collection' );
	return $found ? $found[0]['attrs'] : array();
};
$pg_feat = $pg_collection( 'shop-featured' );
pg_assert_same( 'woocommerce/product-collection/featured', $pg_feat['collection'] ?? null, 'shop-featured uses the featured collection' );
pg_assert_same( true, $pg_feat['query']['featured'] ?? null, 'shop-featured queries featured products' );
pg_assert_same( 4, $pg_feat['query']['perPage'] ?? null, 'shop-featured shows 4 products' );
$pg_best = $pg_collection( 'shop-best-sellers' );
pg_assert_same( 'woocommerce/product-collection/best-sellers', $pg_best['collection'] ?? null, 'shop-best-sellers uses the best-sellers collection' );
pg_assert_same( 'popularity', $pg_best['query']['orderBy'] ?? null, 'shop-best-sellers orders by sales' );
pg_assert_same( 'desc', $pg_best['query']['order'] ?? null, 'shop-best-sellers puts the most sold first' );
pg_assert_same( 4, $pg_best['query']['perPage'] ?? null, 'shop-best-sellers shows 4 products' );
foreach ( array( 'shop-featured' => $pg_feat, 'shop-best-sellers' => $pg_best ) as $pg_slug => $pg_attrs ) {
	pg_assert( false !== strpos( $pg_attrs['className'] ?? '', 'pixelgrid-products' ), "$pg_slug collection uses the pixelgrid-products grid" );
	pg_assert_same( false, $pg_attrs['query']['inherit'] ?? null, "$pg_slug does not inherit the page query" );
	$pg_imgs = $pg_blocks_named( $pg_slug, 'woocommerce/product-image' );
	pg_assert( count( $pg_imgs ) > 0, "$pg_slug shows product pictures" );
	foreach ( $pg_imgs as $pg_img ) {
		pg_assert( false !== strpos( $pg_img['attrs']['className'] ?? '', 'is-style-notch' ), "$pg_slug product image has is-style-notch" );
	}
	pg_assert( count( $pg_blocks_named( $pg_slug, 'woocommerce/product-collection-no-results' ) ) > 0, "$pg_slug says something when there are no products" );
}
$pg_wcss = (string) file_get_contents( get_parent_theme_file_path( 'assets/css/woocommerce.css' ) );
pg_assert( (bool) preg_match( '/\.wp-block-woocommerce-product-collection img[^{]*\{[^}]*filter: none[^}]*image-rendering: pixelated/s', $pg_wcss ), 'product collection pictures stay in colour and pixelated' );

// Hero: an offer beside a mosaic of a product picture.
pg_assert( (bool) preg_match( '/is-style-mosaic-[a-z]+"><img src="[^"]*assets\/images\/shop-0\d\.webp"/', $pg_shop['shop-hero'] ), 'shop-hero has a mosaic of a product picture' );
pg_assert( count( $pg_blocks_named( 'shop-hero', 'core/heading' ) ) > 0 && $pg_has( 'shop-hero', '<h1' ), 'shop-hero has the page heading' );
pg_assert( $pg_has( 'shop-hero', esc_url( add_query_arg( 'post_type', 'product', home_url( '/' ) ) ) ), 'shop-hero links to the products' );

// Promo in two tones, values with delivery, returns and support.
preg_match_all( '/is-style-tone-([a-z]+)/', $pg_shop['shop-promo'], $pg_tones );
pg_assert_same( 2, count( array_unique( $pg_tones[1] ) ), 'shop-promo uses two tones' );
pg_assert_same( 3, count( $pg_blocks_named( 'shop-values', 'core/column' ) ), 'shop-values has three columns' );

// Pages.
$pg_home_order = array( 'shop-hero', 'shop-featured', 'shop-categories', 'shop-promo', 'shop-best-sellers', 'shop-values', 'shop-cta' );
$pg_home_raw   = $pg_patterns['shop-page-home']['content'] ?? '';
$pg_last       = -1;
$pg_ok         = true;
foreach ( $pg_home_order as $pg_section ) {
	$pg_pos  = strpos( $pg_home_raw, '<!-- wp:pattern {"slug":"pixelgrid/' . $pg_section . '"} /-->' );
	$pg_ok   = $pg_ok && false !== $pg_pos && $pg_pos > $pg_last;
	$pg_last = false === $pg_pos ? $pg_last : $pg_pos;
}
pg_assert( $pg_ok, 'shop-page-home holds the seven sections in order' );
pg_assert( $pg_has( 'shop-page-about', '<h1' ) && $pg_has( 'shop-page-contact', '<h1' ), 'about and contact pages have a page heading' );
pg_assert( count( $pg_blocks_named( 'shop-page-contact', 'core/details' ) ) >= 3, 'the contact page answers common questions' );
pg_assert( $pg_has( 'shop-blog-heading', '>Journal</h1>' ), 'shop-blog-heading says Journal' );
pg_assert( (bool) preg_match( '/<!-- wp:template-part \{"slug":"header","tagName":"header"[^}]*\} \/-->/', $pg_shop['shop-template-home'] ) && (bool) preg_match( '/<!-- wp:template-part \{"slug":"footer","tagName":"footer"[^}]*\} \/-->/', $pg_shop['shop-template-home'] ), 'shop-template-home uses the header and footer parts' );
pg_assert( $pg_has( 'shop-template-home', '>Journal</h1>' ) && $pg_has( 'shop-template-home', 'wp:query ' ), 'shop-template-home has the Journal heading and the posts query' );

// The fictional shop name, no stray brands.
pg_assert( $pg_has( 'shop-page-about', 'Common Goods' ) || $pg_has( 'shop-hero', 'Common Goods' ), 'the copy names the fictional shop, Common Goods' );

pg_done();
