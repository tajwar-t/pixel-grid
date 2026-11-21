<?php
/**
 * WooCommerce templates, mini-cart part and grid styles.
 *
 * Runs on both sites: `site.sh up` (WooCommerce inactive) and `site.sh up --woo`.
 */

$pg_woo = class_exists( 'WooCommerce' );
echo $pg_woo ? "INFO: WooCommerce is active.\n" : "INFO: WooCommerce is not active.\n";

// Templates and the mini-cart part: present, parsed, no freeform text, the theme frame.
$pg_templates = array(
	'templates/archive-product.html',
	'templates/single-product.html',
	'templates/taxonomy-product_cat.html',
	'templates/taxonomy-product_tag.html',
	'templates/product-search-results.html',
	'templates/page-cart.html',
	'templates/page-checkout.html',
	'templates/order-confirmation.html',
	'parts/mini-cart.html',
);

// Walks parsed blocks: collects block names, freeform text, and the attrs of each block.
$pg_walk = function ( $blocks, &$names, &$freeform, &$all ) use ( &$pg_walk ) {
	foreach ( $blocks as $block ) {
		if ( null === $block['blockName'] ) {
			if ( '' !== trim( $block['innerHTML'] ) ) {
				$freeform[] = trim( $block['innerHTML'] );
			}
			continue;
		}
		$names[] = $block['blockName'];
		$all[]   = $block;
		$pg_walk( $block['innerBlocks'], $names, $freeform, $all );
	}
};

$pg_parsed = array();
foreach ( $pg_templates as $pg_rel ) {
	$pg_path = get_parent_theme_file_path( $pg_rel );
	pg_assert( file_exists( $pg_path ), "$pg_rel exists" );
	$pg_raw   = file_exists( $pg_path ) ? (string) file_get_contents( $pg_path ) : '';
	$names    = array();
	$freeform = array();
	$all      = array();
	$pg_walk( parse_blocks( $pg_raw ), $names, $freeform, $all );
	pg_assert( count( $names ) > 0, "$pg_rel has blocks" );
	pg_assert_same( array(), $freeform, "$pg_rel has no freeform text" );
	$pg_parsed[ $pg_rel ] = array(
		'raw'    => $pg_raw,
		'names'  => array_values( array_unique( $names ) ),
		'blocks' => $all,
	);
	if ( 0 === strpos( $pg_rel, 'templates/' ) ) {
		pg_assert( false !== strpos( $pg_raw, '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->' ), "$pg_rel uses the header part" );
		pg_assert( false !== strpos( $pg_raw, '<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->' ), "$pg_rel uses the footer part" );
		pg_assert( (bool) preg_match( '/<!-- wp:group \{"tagName":"main","className":"pixelgrid-main[^"]*","layout":\{"type":"constrained"\}\} -->/', $pg_raw ), "$pg_rel has the constrained pixelgrid-main group" );
		pg_assert( false !== strpos( $pg_raw, '<main class="wp-block-group pixelgrid-main' ), "$pg_rel main group markup matches its attributes" );
	}
	// Every product image carries the pixel notch.
	foreach ( $all as $pg_block ) {
		if ( 'woocommerce/product-image' === $pg_block['blockName'] ) {
			pg_assert( false !== strpos( $pg_block['attrs']['className'] ?? '', 'is-style-notch' ), "$pg_rel product image has is-style-notch" );
		}
	}
}

$pg_has = function ( $rel, $name ) use ( $pg_parsed ) {
	return isset( $pg_parsed[ $rel ] ) && in_array( $name, $pg_parsed[ $rel ]['names'], true );
};
foreach ( array( 'archive-product', 'taxonomy-product_cat', 'taxonomy-product_tag', 'product-search-results' ) as $pg_slug ) {
	$pg_rel = "templates/$pg_slug.html";
	foreach ( array( 'woocommerce/store-notices', 'woocommerce/breadcrumbs', 'woocommerce/catalog-sorting', 'woocommerce/product-collection', 'woocommerce/product-template', 'woocommerce/product-image', 'woocommerce/product-price', 'woocommerce/product-button', 'core/query-pagination' ) as $pg_name ) {
		pg_assert( $pg_has( $pg_rel, $pg_name ), "$pg_rel uses $pg_name" );
	}
}
foreach ( array( 'woocommerce/product-image-gallery', 'core/post-title', 'woocommerce/product-price', 'woocommerce/add-to-cart-form', 'woocommerce/product-details', 'woocommerce/product-collection' ) as $pg_name ) {
	pg_assert( $pg_has( 'templates/single-product.html', $pg_name ), "single-product uses $pg_name" );
}
pg_assert( false !== strpos( $pg_parsed['templates/single-product.html']['raw'] ?? '', '"collection":"woocommerce/product-collection/related"' ), 'single-product shows related products' );
foreach ( array( 'page-cart', 'page-checkout' ) as $pg_slug ) {
	foreach ( array( 'woocommerce/page-content-wrapper', 'woocommerce/store-notices', 'core/post-content' ) as $pg_name ) {
		pg_assert( $pg_has( "templates/$pg_slug.html", $pg_name ), "$pg_slug uses $pg_name" );
	}
}
foreach ( array( 'woocommerce/order-confirmation-status', 'woocommerce/order-confirmation-summary', 'woocommerce/order-confirmation-totals', 'woocommerce/order-confirmation-billing-address' ) as $pg_name ) {
	pg_assert( $pg_has( 'templates/order-confirmation.html', $pg_name ), "order-confirmation uses $pg_name" );
}
foreach ( array( 'woocommerce/mini-cart-contents', 'woocommerce/filled-mini-cart-contents-block', 'woocommerce/mini-cart-products-table-block', 'woocommerce/empty-mini-cart-contents-block' ) as $pg_name ) {
	pg_assert( $pg_has( 'parts/mini-cart.html', $pg_name ), "mini-cart part uses $pg_name" );
}

// The mini-cart part is declared in theme.json, area uncategorized.
$pg_theme_json = json_decode( (string) file_get_contents( get_parent_theme_file_path( 'theme.json' ) ), true );
$pg_part       = null;
foreach ( (array) ( $pg_theme_json['templateParts'] ?? array() ) as $pg_entry ) {
	if ( 'mini-cart' === ( $pg_entry['name'] ?? '' ) ) {
		$pg_part = $pg_entry;
	}
}
pg_assert_same( 'uncategorized', $pg_part['area'] ?? null, 'theme.json declares the mini-cart part, area uncategorized' );

// With WooCommerce active, every block the files use is registered: no invented names.
if ( $pg_woo ) {
	$pg_registry = WP_Block_Type_Registry::get_instance();
	foreach ( $pg_parsed as $pg_rel => $pg_info ) {
		$pg_missing = array();
		foreach ( $pg_info['names'] as $pg_name ) {
			if ( ! $pg_registry->is_registered( $pg_name ) ) {
				$pg_missing[] = $pg_name;
			}
		}
		pg_assert_same( array(), $pg_missing, "$pg_rel uses only registered blocks" );
	}
}

// The stylesheet: theme tokens only, square corners, condensed face, colour product images.
$pg_css_path = get_parent_theme_file_path( 'assets/css/woocommerce.css' );
pg_assert( file_exists( $pg_css_path ), 'assets/css/woocommerce.css exists' );
$pg_css      = file_exists( $pg_css_path ) ? (string) file_get_contents( $pg_css_path ) : '';
$pg_css_code = (string) preg_replace( '#/\*.*?\*/#s', '', $pg_css );
pg_assert( ! preg_match( '/#[0-9a-fA-F]{3,8}\b/', $pg_css_code ), 'woocommerce.css has no hex colours' );
pg_assert( ! preg_match( '/\b(rgba?|hsla?)\(/', $pg_css_code ), 'woocommerce.css has no rgb or hsl colours' );
preg_match_all( '/font-family\s*:\s*([^;}]+)/', $pg_css_code, $pg_fonts );
$pg_bad_fonts = array_filter(
	$pg_fonts[1],
	function ( $value ) {
		return ! preg_match( '/^var\(--wp--preset--font-family--[a-z-]+\)$/', trim( $value ) ) && 'inherit' !== trim( $value );
	}
);
pg_assert( count( $pg_fonts[1] ) > 0 && array() === $pg_bad_fonts, 'woocommerce.css sets fonts only through presets' );
pg_assert( false !== strpos( $pg_css_code, 'font-family: var(--wp--preset--font-family--heading)' ), 'woocommerce.css uses the condensed face' );
preg_match_all( '/border-radius\s*:\s*([^;}]+)/', $pg_css_code, $pg_radii );
pg_assert( count( $pg_radii[1] ) > 0 && array( '0' ) === array_values( array_unique( array_map( 'trim', $pg_radii[1] ) ) ), 'woocommerce.css makes every corner square' );
pg_assert( false !== strpos( $pg_css_code, 'var(--pixelgrid-fg)' ) && false !== strpos( $pg_css_code, 'var(--pixelgrid-ac)' ), 'woocommerce.css uses the tone tokens' );
foreach ( array( '.wc-block-components-product-price', '.wc-block-components-product-sale-badge', '.wc-block-components-button', '.wc-block-components-text-input', '.wc-block-components-notice-banner', '.wc-block-components-product-image', '.woocommerce-product-gallery', '.wc-block-cart-item__image', '.wc-block-mini-cart' ) as $pg_sel ) {
	pg_assert( false !== strpos( $pg_css_code, $pg_sel ), "woocommerce.css styles $pg_sel" );
}
// Product pictures stay in colour and crisp: a rule on product images that sets filter: none and pixelated rendering.
preg_match_all( '/([^{}]+)\{([^}]*)\}/', $pg_css_code, $pg_rules, PREG_SET_ORDER );
$pg_colour_sels = '';
foreach ( $pg_rules as $pg_rule ) {
	if ( preg_match( '/filter\s*:\s*none/', $pg_rule[2] ) && preg_match( '/image-rendering\s*:\s*pixelated/', $pg_rule[2] ) ) {
		$pg_colour_sels .= $pg_rule[1];
	}
}
foreach ( array( '.wc-block-components-product-image', '.woocommerce-product-gallery', '.wc-block-cart-item__image', '.wc-block-mini-cart-items', '.wp-block-woocommerce-product-collection', '.wc-block-components-order-summary-item__image' ) as $pg_sel ) {
	pg_assert( false !== strpos( $pg_colour_sels, $pg_sel ), "product images under $pg_sel stay in colour and pixelated" );
}
$pg_unscoped = array_filter(
	array_map( 'trim', explode( ',', $pg_colour_sels ) ),
	function ( $sel ) {
		return '' !== $sel && ! preg_match( '/wc-block|woocommerce/', $sel );
	}
);
pg_assert_same( array(), array_values( $pg_unscoped ), 'the colour exception is scoped to WooCommerce selectors' );
// style.css still greys post thumbnails and notch images, so the exception is needed and narrow.
$pg_style = (string) file_get_contents( get_stylesheet_directory() . '/style.css' );
pg_assert( false !== strpos( $pg_style, 'filter: grayscale(1)' ), 'style.css keeps black and white thumbnails elsewhere' );

// The enqueue: only with WooCommerce, with the theme version.
global $wp_styles;
$wp_styles = new WP_Styles();
do_action( 'wp_enqueue_scripts' );
pg_assert_same( $pg_woo, wp_style_is( 'pixelgrid-woocommerce', 'enqueued' ), $pg_woo ? 'woocommerce.css is enqueued with WooCommerce' : 'woocommerce.css is not enqueued without WooCommerce' );
if ( $pg_woo ) {
	$pg_handle = wp_styles()->registered['pixelgrid-woocommerce'] ?? null;
	pg_assert_same( wp_get_theme()->get( 'Version' ), $pg_handle ? $pg_handle->ver : null, 'woocommerce.css carries the theme version' );
	pg_assert( $pg_handle && false !== strpos( $pg_handle->src, 'assets/css/woocommerce.css' ), 'the pixelgrid-woocommerce handle points at assets/css/woocommerce.css' );
}

// No shop pattern without WooCommerce.
if ( ! $pg_woo ) {
	$pg_shop_patterns = array();
	foreach ( WP_Block_Patterns_Registry::get_instance()->get_all_registered() as $pg_pattern ) {
		if ( 0 === strpos( $pg_pattern['name'], 'pixelgrid/shop-' ) ) {
			$pg_shop_patterns[] = $pg_pattern['name'];
		}
	}
	pg_assert_same( array(), $pg_shop_patterns, 'no pixelgrid/shop-* pattern is registered without WooCommerce' );
}

// The IT front page over HTTP, with WP_DEBUG_LOG on.
$pg_get         = function ( $url ) {
	$res = wp_remote_get( $url, array( 'timeout' => 60 ) );
	return array( (int) wp_remote_retrieve_response_code( $res ), (string) wp_remote_retrieve_body( $res ) );
};
$pg_config_path = ABSPATH . 'wp-config.php';
$pg_config_orig = (string) file_get_contents( $pg_config_path );
$pg_debug_log   = WP_CONTENT_DIR . '/debug.log';
$pg_plugins     = get_option( 'active_plugins' );
register_shutdown_function(
	function () use ( $pg_config_path, $pg_config_orig, $pg_plugins ) {
		file_put_contents( $pg_config_path, $pg_config_orig );
		update_option( 'active_plugins', $pg_plugins );
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
		// PHP 8.5 deprecations raised inside WordPress core or WooCommerce are noise; any other line counts.
		$vendor_deprecation = false !== strpos( $line, 'Deprecated' ) && preg_match( '#[\\\\/](wp-includes|wp-admin|plugins[\\\\/]woocommerce)[\\\\/]#', $line );
		if ( '' !== trim( $line ) && ! $vendor_deprecation && ! preg_match( '/^\s*(#|Stack trace|thrown)/', $line ) ) {
			$out[] = $line;
		}
	}
	return $out;
};

pixelgrid_starter_create(
	'it',
	array(
		'design'  => true,
		'pages'   => true,
		'posts'   => true,
		'menu'    => true,
		'reading' => true,
	)
);
pg_assert_same( 'it', pixelgrid_starter_active_variant(), 'IT starter content is set up' );

// The part of the page the theme draws: the site blocks, without scripts, styles and nonces.
$pg_body = function ( $html ) {
	$at   = strpos( $html, '<div class="wp-site-blocks">' );
	$end  = strrpos( $html, '</body>' );
	$body = false === $at || false === $end ? '' : substr( $html, $at, $end - $at );
	$body = preg_replace( '#<script\b.*?</script>#s', '', $body );
	$body = preg_replace( '#<style\b.*?</style>#s', '', $body );
	$body = preg_replace( '#<link\b[^>]*>#', '', $body );
	return trim( preg_replace( '/\s+/', ' ', $body ) );
};

$pg_log_start             = count( $pg_log_lines() );
list( $pg_code, $pg_html ) = $pg_get( home_url( '/' ) );
$pg_new                   = array_slice( $pg_log_lines(), $pg_log_start );
pg_assert_same( 200, $pg_code, 'IT Home returns 200' );
pg_assert_same( array(), $pg_new, 'IT Home renders with no notice' );
pg_assert( '' !== $pg_body( $pg_html ), 'IT Home has the site blocks' );
pg_assert_same( $pg_woo, false !== strpos( $pg_html, "id='pixelgrid-woocommerce-css'" ), $pg_woo ? 'IT Home links woocommerce.css' : 'IT Home does not link woocommerce.css' );

if ( $pg_woo ) {
	// Review Focus 5: the same Home with WooCommerce switched off draws the same page.
	update_option( 'active_plugins', array_values( array_diff( $pg_plugins, array( 'woocommerce/woocommerce.php' ) ) ) );
	list( $pg_code_off, $pg_html_off ) = $pg_get( home_url( '/' ) );
	update_option( 'active_plugins', $pg_plugins );
	pg_assert_same( 200, $pg_code_off, 'IT Home without WooCommerce returns 200' );
	pg_assert( false === strpos( $pg_html_off, "id='pixelgrid-woocommerce-css'" ), 'IT Home without WooCommerce does not link woocommerce.css' );
	$pg_on  = $pg_body( $pg_html );
	$pg_off = $pg_body( $pg_html_off );
	if ( $pg_on !== $pg_off ) {
		$pg_i = 0;
		while ( $pg_i < strlen( $pg_on ) && $pg_i < strlen( $pg_off ) && $pg_on[ $pg_i ] === $pg_off[ $pg_i ] ) {
			++$pg_i;
		}
		echo 'INFO: first difference: with=' . substr( $pg_on, max( 0, $pg_i - 80 ), 240 ) . "\n      without=" . substr( $pg_off, max( 0, $pg_i - 80 ), 240 ) . "\n";
	}
	pg_assert( $pg_on === $pg_off, 'IT Home with WooCommerce draws the same page as without it' );
	pg_assert( false === strpos( $pg_on, 'pixelgrid/shop-' ) && false === strpos( $pg_on, 'wc-block' ), 'IT Home holds no shop pattern and no WooCommerce block' );
}

pg_done();
