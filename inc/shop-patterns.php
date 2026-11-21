<?php
/**
 * Shop patterns: the header, footer, home page sections and pages of the Shop starting point.
 *
 * They use WooCommerce blocks, so they are registered in PHP and only when WooCommerce is
 * active, never from the patterns/ folder. The editor then never shows a broken block.
 * The shop in the copy, Common Goods, is fictional. Prices and amounts are left to the store
 * settings: no pattern prints a currency.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Address of a product category archive. The categories are made by the Shop setup; a missing
 * one shows an empty archive.
 *
 * @param string $slug Product category slug.
 * @return string
 */
function pixelgrid_shop_category_url( $slug ) {
	return add_query_arg( 'product_cat', $slug, home_url( '/' ) );
}

/**
 * Address of every product, which works with or without a Shop page.
 *
 * @return string
 */
function pixelgrid_shop_products_url() {
	return add_query_arg( 'post_type', 'product', home_url( '/' ) );
}

/**
 * Address of a page by its slug, as the other variants link to pages.
 *
 * @param string $slug Page slug.
 * @return string
 */
function pixelgrid_shop_page_url( $slug ) {
	return add_query_arg( 'pagename', $slug, home_url( '/' ) );
}

/**
 * Address of a picture in assets/images.
 *
 * @param string $name File name, for example shop-01.webp.
 * @return string
 */
function pixelgrid_shop_image( $name ) {
	return get_parent_theme_file_uri( 'assets/images/' . $name );
}

/**
 * The four product categories of the Shop starting point.
 *
 * @return array Slug => array( name, picture, what is in it ).
 */
function pixelgrid_shop_categories() {
	return array(
		'kitchen' => array( _x( 'Kitchen', 'Product category', 'pixelgrid' ), 'shop-01.webp', _x( 'Mugs, teapots and bowls', 'Product category contents', 'pixelgrid' ) ),
		'home'    => array( _x( 'Home', 'Product category', 'pixelgrid' ), 'shop-04.webp', _x( 'Lamps, clocks and vases', 'Product category contents', 'pixelgrid' ) ),
		'outdoor' => array( _x( 'Outdoor', 'Product category', 'pixelgrid' ), 'shop-07.webp', _x( 'Ferns, cans and bags', 'Product category contents', 'pixelgrid' ) ),
		'tech'    => array( _x( 'Tech', 'Product category', 'pixelgrid' ), 'shop-10.webp', _x( 'Headphones, watches and radios', 'Product category contents', 'pixelgrid' ) ),
	);
}

/**
 * A section heading with a link to all products beside it.
 *
 * @param string $heading Heading text.
 * @param string $link    Link text.
 * @return string
 */
function pixelgrid_shop_section_head( $heading, $link ) {
	ob_start();
	?>
<!-- wp:group {"className":"pixelgrid-shop-head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group pixelgrid-shop-head"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html( $heading ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"pixelgrid-shop-label"} -->
<p class="pixelgrid-shop-label"><a href="<?php echo esc_url( pixelgrid_shop_products_url() ); ?>"><?php echo esc_html( $link ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * A product collection of four products, in the grid the WooCommerce templates use.
 *
 * @param array  $query      Query settings that differ from the defaults.
 * @param string $collection Collection name, for example woocommerce/product-collection/featured.
 * @param array  $hide       Controls the collection hides in the editor.
 * @param int    $query_id   Query ID, unique on the page.
 * @param string $empty_text Text shown when no product matches.
 * @return string
 */
function pixelgrid_shop_collection( $query, $collection, $hide, $query_id, $empty_text ) {
	$attrs = array(
		'queryId'              => (int) $query_id,
		'query'                => array_merge(
			array(
				'perPage'                       => 4,
				'pages'                         => 1,
				'offset'                        => 0,
				'postType'                      => 'product',
				'order'                         => 'asc',
				'orderBy'                       => 'title',
				'search'                        => '',
				'exclude'                       => array(),
				'inherit'                       => false,
				'taxQuery'                      => (object) array(),
				'isProductCollectionBlock'      => true,
				'featured'                      => false,
				'woocommerceOnSale'             => false,
				'woocommerceStockStatus'        => array( 'instock', 'outofstock', 'onbackorder' ),
				'woocommerceAttributes'         => array(),
				'woocommerceHandPickedProducts' => array(),
				'filterable'                    => false,
			),
			$query
		),
		'tagName'              => 'div',
		'displayLayout'        => array(
			'type'          => 'flex',
			'columns'       => 4,
			'shrinkColumns' => true,
		),
		'dimensions'           => array( 'widthType' => 'fill' ),
		'collection'           => $collection,
		'hideControls'         => $hide,
		'queryContextIncludes' => array( 'collection' ),
		'className'            => 'pixelgrid-products pixelgrid-products-four',
	);
	ob_start();
	?>
<!-- wp:woocommerce/product-collection <?php echo wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?> -->
<div class="wp-block-woocommerce-product-collection pixelgrid-products pixelgrid-products-four"><!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"showSaleBadge":false,"imageSizing":"thumbnail","className":"is-style-notch"} -->
<!-- wp:woocommerce/product-sale-badge {"align":"right"} /-->
<!-- /wp:woocommerce/product-image -->

<!-- wp:post-title {"level":3,"isLink":true,"fontSize":"large","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:woocommerce/product-price /-->

<!-- wp:woocommerce/product-button /-->
<!-- /wp:woocommerce/product-template -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html( $empty_text ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Header: an announcement strip, then the site title, the menu, product search, account and mini-cart.
 *
 * @return string
 */
function pixelgrid_shop_pattern_header() {
	$search = array(
		'label'       => _x( 'Search products', 'search form label', 'pixelgrid' ),
		'showLabel'   => false,
		'placeholder' => _x( 'Search products', 'search form placeholder', 'pixelgrid' ),
		'buttonText'  => _x( 'Search', 'search button text', 'pixelgrid' ),
		'query'       => array( 'post_type' => 'product' ),
		'namespace'   => 'woocommerce/product-search',
		'className'   => 'pixelgrid-shop-search',
	);
	ob_start();
	?>
<!-- wp:group {"align":"full","className":"pixelgrid-shop-header","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-shop-header"><!-- wp:group {"align":"full","className":"pixelgrid-shop-announce","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-shop-announce"><!-- wp:paragraph {"className":"pixelgrid-shop-label"} -->
<p class="pixelgrid-shop-label"><?php echo esc_html_x( 'Free delivery on larger orders · 30-day returns', 'Shop announcement strip', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"pixelgrid-header pixelgrid-shop-bar","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group pixelgrid-header pixelgrid-shop-bar"><!-- wp:site-title {"level":0,"fontSize":"large"} /-->

<!-- wp:navigation {"overlayMenu":"mobile","className":"pixelgrid-shop-nav","layout":{"type":"flex","justifyContent":"left"}} /-->

<!-- wp:group {"className":"pixelgrid-shop-tools","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group pixelgrid-shop-tools"><!-- wp:search <?php echo wp_json_encode( $search, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?> /-->

<!-- wp:woocommerce/customer-account {"displayStyle":"icon_only","iconStyle":"line","iconClass":"wc-block-customer-account__account-icon"} /-->

<!-- wp:woocommerce/mini-cart /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * A footer column: a heading and a list of links.
 *
 * @param string $heading Column heading.
 * @param array  $links   Link text => address.
 * @return string
 */
function pixelgrid_shop_footer_column( $heading, $links ) {
	ob_start();
	?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html( $heading ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
	<?php foreach ( $links as $pixelgrid_text => $pixelgrid_url ) : ?>
<!-- wp:list-item -->
<li><a href="<?php echo esc_url( $pixelgrid_url ); ?>"><?php echo esc_html( $pixelgrid_text ); ?></a></li>
<!-- /wp:list-item -->
	<?php endforeach; ?>
</ul>
<!-- /wp:list --></div>
<!-- /wp:column -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Footer: shop links by product category, help links, the visit details, a payment note and the copyright.
 *
 * @return string
 */
function pixelgrid_shop_pattern_footer() {
	$shop = array( _x( 'All products', 'Footer link', 'pixelgrid' ) => pixelgrid_shop_products_url() );
	foreach ( pixelgrid_shop_categories() as $pixelgrid_slug => $pixelgrid_cat ) {
		$shop[ $pixelgrid_cat[0] ] = pixelgrid_shop_category_url( $pixelgrid_slug );
	}
	$help = array(
		_x( 'About us', 'Footer link', 'pixelgrid' ) => pixelgrid_shop_page_url( 'about' ),
		_x( 'Journal', 'Footer link', 'pixelgrid' )  => pixelgrid_shop_page_url( 'journal' ),
		_x( 'Contact', 'Footer link', 'pixelgrid' )  => pixelgrid_shop_page_url( 'contact' ),
		_x( 'Delivery and returns', 'Footer link', 'pixelgrid' ) => pixelgrid_shop_page_url( 'contact' ) . '#questions',
	);
	ob_start();
	?>
<!-- wp:group {"align":"full","className":"pixelgrid-footer","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull pixelgrid-footer"><!-- wp:columns {"className":"pixelgrid-shop-footer-cols"} -->
<div class="wp-block-columns pixelgrid-shop-footer-cols"><!-- wp:column {"width":"34%"} -->
<div class="wp-block-column" style="flex-basis:34%"><!-- wp:site-title {"level":0,"fontSize":"large"} /-->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Plain, well-made things for the kitchen, the house, the garden and the desk. Drawn in pixels, packed in paper.', 'Shop footer text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

	<?php
	echo pixelgrid_shop_footer_column( _x( 'Shop', 'Footer heading', 'pixelgrid' ), $shop ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
	echo "\n\n";
	echo pixelgrid_shop_footer_column( _x( 'Help', 'Footer heading', 'pixelgrid' ), $help ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
	?>


<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Visit', 'Footer heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( '14 Mill Lane, Your City', 'Footer placeholder address', 'pixelgrid' ); ?><br><?php echo esc_html_x( 'hello@example.com', 'Footer placeholder email address', 'pixelgrid' ); ?><br><?php echo esc_html_x( 'Open Tuesday to Saturday', 'Footer opening days', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"className":"pixelgrid-footer-bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group pixelgrid-footer-bottom"><!-- wp:paragraph {"className":"pixelgrid-shop-payment"} -->
<p class="pixelgrid-shop-payment"><?php echo esc_html_x( 'Secure checkout. You can pay with any of the methods shown at checkout.', 'Shop footer payment note', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>
	<?php
	printf(
		/* translators: %s: current year. */
		esc_html__( '© %s, all rights reserved.', 'pixelgrid' ),
		esc_html( gmdate( 'Y' ) )
	);
	?>
</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Hero: the offer beside a product picture drawn through a pixel heart.
 *
 * @return string
 */
function pixelgrid_shop_pattern_hero() {
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"pixelgrid-shop-label"} -->
<p class="pixelgrid-shop-label"><?php echo esc_html_x( 'New season', 'Shop hero label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Plain things, well made', 'Shop hero heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Common Goods sells mugs, lamps, ferns and radios for people who like their things to last. Every product is drawn in 256 squares.', 'Shop hero introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( pixelgrid_shop_products_url() ); ?>"><?php echo esc_html_x( 'Shop everything', 'Shop hero button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( pixelgrid_shop_page_url( 'about' ) ); ?>"><?php echo esc_html_x( 'About the shop', 'Shop hero button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-heart"} -->
<figure class="wp-block-image size-full is-style-mosaic-heart"><img src="<?php echo esc_url( pixelgrid_shop_image( 'shop-01.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'A pixel-art mug seen through a pixel heart', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Featured products: four products marked as featured in WooCommerce.
 *
 * @return string
 */
function pixelgrid_shop_pattern_featured() {
	$collection = pixelgrid_shop_collection(
		array( 'featured' => true ),
		'woocommerce/product-collection/featured',
		array( 'featured', 'filterable' ),
		11,
		_x( 'No featured products yet. Mark a product as featured in Products and it shows here.', 'Shop featured products empty message', 'pixelgrid' )
	);
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><?php echo pixelgrid_shop_section_head( _x( 'Featured this week', 'Section heading', 'pixelgrid' ), _x( 'All products', 'Section link', 'pixelgrid' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>


	<?php echo $collection; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>
</section>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Shop by category: four tiles, each a product picture and a link to a product category.
 *
 * @return string
 */
function pixelgrid_shop_pattern_categories() {
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-blue pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-blue pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Shop by category', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-shop-tiles"} -->
<div class="wp-block-columns pixelgrid-shop-tiles">
	<?php foreach ( pixelgrid_shop_categories() as $pixelgrid_slug => $pixelgrid_cat ) : ?>
<!-- wp:column {"className":"pixelgrid-shop-tile"} -->
<div class="wp-block-column pixelgrid-shop-tile"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch pixelgrid-shop-pic"} -->
<figure class="wp-block-image size-full is-style-notch pixelgrid-shop-pic"><img src="<?php echo esc_url( pixelgrid_shop_image( $pixelgrid_cat[1] ) ); ?>" alt="" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:heading {"level":3,"fontSize":"large"} -->
<h3 class="wp-block-heading has-large-font-size"><a href="<?php echo esc_url( pixelgrid_shop_category_url( $pixelgrid_slug ) ); ?>"><?php echo esc_html( $pixelgrid_cat[0] ); ?></a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"pixelgrid-shop-label"} -->
<p class="pixelgrid-shop-label"><?php echo esc_html( $pixelgrid_cat[2] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
	<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Promo: the sale beside a product picture, then free repairs, in two tones.
 *
 * @return string
 */
function pixelgrid_shop_pattern_promo() {
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-red pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-red pixelgrid-stack"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch pixelgrid-shop-pic"} -->
<figure class="wp-block-image size-full is-style-notch pixelgrid-shop-pic"><img src="<?php echo esc_url( pixelgrid_shop_image( 'shop-04.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'A pixel-art desk lamp', 'Image description', 'pixelgrid' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"pixelgrid-shop-label"} -->
<p class="pixelgrid-shop-label"><?php echo esc_html_x( 'Sale', 'Shop promo label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Four favourites, up to 25% off', 'Shop promo heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'The teapot, the desk lamp, the watering can and the headphones are cheaper until the end of the month.', 'Shop promo text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( pixelgrid_shop_products_url() ); ?>"><?php echo esc_html_x( 'Shop the sale', 'Shop promo button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:columns {"verticalAlignment":"bottom","className":"pixelgrid-split"} -->
<div class="wp-block-columns are-vertically-aligned-bottom pixelgrid-split"><!-- wp:column {"verticalAlignment":"bottom"} -->
<div class="wp-block-column is-vertically-aligned-bottom"><!-- wp:paragraph {"className":"pixelgrid-shop-label"} -->
<p class="pixelgrid-shop-label"><?php echo esc_html_x( 'Repairs', 'Shop promo label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Mended, not replaced', 'Shop promo heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Broke a handle or a hinge? Anything we sold gets a free repair or a swap, for as long as we have the parts.', 'Shop promo text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom","width":"34%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:34%"><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( pixelgrid_shop_page_url( 'contact' ) ); ?>"><?php echo esc_html_x( 'Ask about a repair', 'Shop promo button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Best sellers: the four products sold most often.
 *
 * @return string
 */
function pixelgrid_shop_pattern_best_sellers() {
	$collection = pixelgrid_shop_collection(
		array(
			'orderBy' => 'popularity',
			'order'   => 'desc',
		),
		'woocommerce/product-collection/best-sellers',
		array( 'order', 'filterable' ),
		12,
		_x( 'No products yet. Add a few in Products and the ones sold most often show here.', 'Shop best sellers empty message', 'pixelgrid' )
	);
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><?php echo pixelgrid_shop_section_head( _x( 'Best sellers', 'Section heading', 'pixelgrid' ), _x( 'See all', 'Section link', 'pixelgrid' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>


	<?php echo $collection; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>
</section>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Three columns of short points on a coloured section.
 *
 * @param string $tone    Tone slug, for example sun.
 * @param string $heading Section heading.
 * @param array  $items   Each an array( heading, text ).
 * @return string
 */
function pixelgrid_shop_points( $tone, $heading, $items ) {
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-<?php echo esc_attr( $tone ); ?> pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-<?php echo esc_attr( $tone ); ?> pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html( $heading ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-cards"} -->
<div class="wp-block-columns pixelgrid-cards">
	<?php foreach ( $items as $pixelgrid_item ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php echo esc_html( $pixelgrid_item[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_item[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
	<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Values: delivery, returns and support.
 *
 * @return string
 */
function pixelgrid_shop_pattern_values() {
	return pixelgrid_shop_points(
		'sun',
		_x( 'Buying should be easy', 'Section heading', 'pixelgrid' ),
		array(
			array( __( 'Free delivery on larger orders', 'pixelgrid' ), __( 'Smaller orders pay one flat rate, shown in the cart before you pay. Parcels leave within two working days.', 'pixelgrid' ) ),
			array( __( '30-day returns', 'pixelgrid' ), __( 'Changed your mind? Send it back unused and we refund the full price.', 'pixelgrid' ) ),
			array( __( 'Real human support', 'pixelgrid' ), __( 'Write to us and a person from the shop floor answers, usually the same day.', 'pixelgrid' ) ),
		)
	);
}

/**
 * A closing section with a large heading, a short text and one button.
 *
 * @param string $tone    Tone slug.
 * @param string $heading Heading.
 * @param string $text    Text.
 * @param string $button  Button text.
 * @param string $url     Button address.
 * @return string
 */
function pixelgrid_shop_closing( $tone, $heading, $text, $button, $url ) {
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-<?php echo esc_attr( $tone ); ?> pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-<?php echo esc_attr( $tone ); ?> pixelgrid-stack"><!-- wp:heading {"fontSize":"huge"} -->
<h2 class="wp-block-heading has-huge-font-size"><?php echo esc_html( $heading ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html( $text ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $button ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Call to action: come to the shop.
 *
 * @return string
 */
function pixelgrid_shop_pattern_cta() {
	return pixelgrid_shop_closing(
		'night',
		_x( 'Come and see them in person', 'Call to action heading', 'pixelgrid' ),
		_x( 'Our little shop on Mill Lane is open Tuesday to Saturday. Pick things up, turn them over, drink tea from the mug.', 'Call to action text', 'pixelgrid' ),
		_x( 'Find the shop', 'Call to action button', 'pixelgrid' ),
		pixelgrid_shop_page_url( 'contact' )
	);
}

/**
 * About page: the shop's story beside a picture, what it cares about and a way to get in touch.
 *
 * @return string
 */
function pixelgrid_shop_pattern_page_about() {
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"pixelgrid-shop-label"} -->
<p class="pixelgrid-shop-label"><?php echo esc_html_x( 'About', 'Shop section label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'A small shop for ordinary things', 'Shop about page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Common Goods began as one table at a Saturday market, selling nine mugs a neighbour had made. Today it is a shop on Mill Lane and a short list of products we have tested ourselves.', 'Shop about page introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'We pick things by living with them first. The teapot has poured in our office for a year. The backpack went on three camping trips before we put it on the shelf. If something annoys us, it does not make the list.', 'Shop about page text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( pixelgrid_shop_products_url() ); ?>"><?php echo esc_html_x( 'See what we sell', 'Shop about page button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( pixelgrid_shop_image( 'shop-14.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture for the about page', 'Image description', 'pixelgrid' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

	<?php
	echo pixelgrid_shop_points( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
		'blue',
		_x( 'What we care about', 'Section heading', 'pixelgrid' ),
		array(
			array( __( 'Made to last', 'pixelgrid' ), __( 'We would rather sell you one lamp for ten years than three lamps for three years each.', 'pixelgrid' ) ),
			array( __( 'Plain and honest', 'pixelgrid' ), __( 'Descriptions say the size, the weight and the material. No adjectives we cannot back up.', 'pixelgrid' ) ),
			array( __( 'Kind to the street', 'pixelgrid' ), __( 'Paper packing only, no plastic fill, and a free repair or swap for anything we sold.', 'pixelgrid' ) ),
		)
	);
	echo "\n\n";
	echo pixelgrid_shop_closing( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
		'sun',
		_x( 'Say hello', 'Call to action heading', 'pixelgrid' ),
		_x( 'Questions about a product, a delivery or a broken handle? We read every message.', 'Call to action text', 'pixelgrid' ),
		_x( 'Contact us', 'Call to action button', 'pixelgrid' ),
		pixelgrid_shop_page_url( 'contact' )
	);
	return trim( (string) ob_get_clean() );
}

/**
 * Contact page: where the shop is, when it is open and how to write, then common questions.
 *
 * @return string
 */
function pixelgrid_shop_pattern_page_contact() {
	$details = array(
		_x( '14 Mill Lane, Your City', 'Placeholder address', 'pixelgrid' ),
		_x( 'hello@example.com', 'Placeholder email address', 'pixelgrid' ),
		_x( 'Tuesday to Friday, 10:00 to 18:00', 'Shop opening hours', 'pixelgrid' ),
		_x( 'Saturday, 10:00 to 16:00', 'Shop opening hours', 'pixelgrid' ),
	);
	$faq     = array(
		array( __( 'How much is delivery?', 'pixelgrid' ), __( 'Delivery is free on larger orders and one flat rate below that. You see the exact cost in the cart before you pay. Parcels leave within two working days.', 'pixelgrid' ) ),
		array( __( 'How do returns work?', 'pixelgrid' ), __( 'You have 30 days from the day it arrives. Send the product back unused in its box and we refund the full price within five days.', 'pixelgrid' ) ),
		array( __( 'Can I pick up in the shop?', 'pixelgrid' ), __( 'Yes. Choose pick-up when you order and we hold it on the shelf by the till for seven days.', 'pixelgrid' ) ),
		array( __( 'What if something arrives broken?', 'pixelgrid' ), __( 'Write to us with a photo and we send a replacement straight away. You do not need to return the broken one.', 'pixelgrid' ) ),
	);
	ob_start();
	?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"pixelgrid-shop-label"} -->
<p class="pixelgrid-shop-label"><?php echo esc_html_x( 'Contact', 'Shop section label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Come and say hello', 'Shop contact page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'The shop is small and so is the team. Drop in, or write to us.', 'Shop contact page introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel">
	<?php foreach ( $details as $pixelgrid_line ) : ?>
<!-- wp:list-item -->
<li><?php echo esc_html( $pixelgrid_line ); ?></li>
<!-- /wp:list-item -->
	<?php endforeach; ?>
</ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:hello@example.com"><?php echo esc_html_x( 'Email the shop', 'Shop contact button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-mail"} -->
<figure class="wp-block-image size-full is-style-mosaic-mail"><img src="<?php echo esc_url( pixelgrid_shop_image( 'shop-15.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel envelope', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","anchor":"questions","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section id="questions" class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Questions people ask', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group">
	<?php foreach ( $faq as $pixelgrid_qa ) : ?>
<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $pixelgrid_qa[0] ); ?></summary><!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_qa[1] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
	<?php endforeach; ?>
</div>
<!-- /wp:group --></section>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Journal heading for the posts page.
 *
 * @return string
 */
function pixelgrid_shop_pattern_blog_heading() {
	ob_start();
	?>
<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Journal', 'Shop blog page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Notes from the shop floor: how we pick things, how to look after them and what is coming next.', 'Shop blog page introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:categories {"className":"is-style-chips"} /--></div>
<!-- /wp:group -->
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Posts page template: header, the Journal heading, post cards with pagination, and footer.
 *
 * @return string
 */
function pixelgrid_shop_pattern_template_home() {
	return '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","className":"pixelgrid-main pixelgrid-stack","layout":{"type":"constrained"}} -->
<main class="wp-block-group pixelgrid-main pixelgrid-stack"><!-- wp:pattern {"slug":"pixelgrid/shop-blog-heading"} /-->

<!-- wp:query {"queryId":1,"query":{"perPage":9,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true},"className":"pixelgrid-cards"} -->
<div class="wp-block-query pixelgrid-cards"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2"} /-->

<!-- wp:post-terms {"term":"category"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":20} /-->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"none","layout":{"type":"flex","justifyContent":"left"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:pattern {"slug":"pixelgrid/no-results"} /-->
<!-- /wp:query-no-results --></div>
<!-- /wp:query -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->';
}

/**
 * A page pattern made of section pattern references.
 *
 * @param array $slugs Section slugs without the pixelgrid/ prefix.
 * @return string
 */
function pixelgrid_shop_refs( $slugs ) {
	$out = array();
	foreach ( $slugs as $slug ) {
		$out[] = '<!-- wp:pattern ' . wp_json_encode( array( 'slug' => 'pixelgrid/' . $slug ), JSON_UNESCAPED_SLASHES ) . ' /-->';
	}
	return implode( "\n\n", $out );
}

if ( ! function_exists( 'pixelgrid_register_shop_patterns' ) ) :
	/**
	 * Registers the Pixelgrid Shop pattern category and the Shop patterns, only when WooCommerce is active.
	 */
	function pixelgrid_register_shop_patterns() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		register_block_pattern_category(
			'pixelgrid-shop',
			array(
				'label'       => __( 'Pixelgrid Shop', 'pixelgrid' ),
				'description' => __( 'Sections for a shop: offer, featured products, categories, sale, best sellers and how buying works.', 'pixelgrid' ),
			)
		);

		$section = function ( $title, $description, $keywords, $content, $extra = array() ) {
			return array_merge(
				array(
					'title'         => $title,
					'description'   => $description,
					'categories'    => array( 'pixelgrid-shop' ),
					'keywords'      => $keywords,
					'viewportWidth' => 1400,
					'content'       => $content,
				),
				$extra
			);
		};
		$page    = function ( $title, $description, $sections ) {
			return array(
				'title'         => $title,
				'description'   => $description,
				'categories'    => array( 'pixelgrid-pages' ),
				'keywords'      => array( __( 'page', 'pixelgrid' ), __( 'layout', 'pixelgrid' ), __( 'shop', 'pixelgrid' ) ),
				'blockTypes'    => array( 'core/post-content' ),
				'postTypes'     => array( 'page' ),
				'viewportWidth' => 1400,
				'content'       => pixelgrid_shop_refs( $sections ),
			);
		};

		$patterns = array(
			'shop-header'        => $section(
				__( 'Shop header', 'pixelgrid' ),
				__( 'An announcement strip, then the site title, the menu, product search, account and mini-cart.', 'pixelgrid' ),
				array( __( 'header', 'pixelgrid' ), __( 'cart', 'pixelgrid' ), __( 'search', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_header(),
				array(
					'categories' => array( 'header', 'pixelgrid-shop' ),
					'blockTypes' => array( 'core/template-part/header' ),
				)
			),
			'shop-footer'        => $section(
				__( 'Shop footer', 'pixelgrid' ),
				__( 'Links to every product category and to help pages, the shop address, a payment note and the copyright line.', 'pixelgrid' ),
				array( __( 'footer', 'pixelgrid' ), __( 'categories', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_footer(),
				array(
					'categories' => array( 'footer', 'pixelgrid-shop' ),
					'blockTypes' => array( 'core/template-part/footer' ),
				)
			),
			'shop-hero'          => $section(
				__( 'Shop hero with pixel picture', 'pixelgrid' ),
				__( 'The shop offer with two buttons, beside a product picture drawn through a pixel heart.', 'pixelgrid' ),
				array( __( 'hero', 'pixelgrid' ), __( 'offer', 'pixelgrid' ), __( 'mosaic', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_hero(),
				array( 'categories' => array( 'pixelgrid-shop', 'banner' ) )
			),
			'shop-featured'      => $section(
				__( 'Featured products', 'pixelgrid' ),
				__( 'Four products marked as featured in WooCommerce, with a link to all products.', 'pixelgrid' ),
				array( __( 'products', 'pixelgrid' ), __( 'featured', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_featured()
			),
			'shop-categories'    => $section(
				__( 'Shop by category', 'pixelgrid' ),
				__( 'Four tiles, each a product picture and a link to a product category.', 'pixelgrid' ),
				array( __( 'categories', 'pixelgrid' ), __( 'tiles', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_categories()
			),
			'shop-promo'         => $section(
				__( 'Shop promotion', 'pixelgrid' ),
				__( 'A sale beside a product picture, then a note about free repairs, in two tones.', 'pixelgrid' ),
				array( __( 'sale', 'pixelgrid' ), __( 'promotion', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_promo()
			),
			'shop-best-sellers'  => $section(
				__( 'Best sellers', 'pixelgrid' ),
				__( 'The four products sold most often, with a link to all products.', 'pixelgrid' ),
				array( __( 'products', 'pixelgrid' ), __( 'best sellers', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_best_sellers()
			),
			'shop-values'        => $section(
				__( 'Shop promises', 'pixelgrid' ),
				__( 'Delivery, returns and support, in three columns on a bright section.', 'pixelgrid' ),
				array( __( 'delivery', 'pixelgrid' ), __( 'returns', 'pixelgrid' ), __( 'support', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_values()
			),
			'shop-cta'           => $section(
				__( 'Shop call to action', 'pixelgrid' ),
				__( 'A dark section with a very large headline, a short invitation to visit and one button.', 'pixelgrid' ),
				array( __( 'cta', 'pixelgrid' ), __( 'visit', 'pixelgrid' ) ),
				pixelgrid_shop_pattern_cta(),
				array( 'categories' => array( 'pixelgrid-shop', 'call-to-action' ) )
			),
			'shop-page-home'     => $page(
				__( 'Shop home page', 'pixelgrid' ),
				__( 'Offer with a pixel picture, featured products, category tiles, a sale, best sellers, delivery and returns, and a call to visit.', 'pixelgrid' ),
				array( 'shop-hero', 'shop-featured', 'shop-categories', 'shop-promo', 'shop-best-sellers', 'shop-values', 'shop-cta' )
			),
			'shop-page-about'    => array(
				'title'         => __( 'Shop about page', 'pixelgrid' ),
				'description'   => __( 'The shop story beside a picture, what the shop cares about and a way to get in touch.', 'pixelgrid' ),
				'categories'    => array( 'pixelgrid-pages' ),
				'keywords'      => array( __( 'page', 'pixelgrid' ), __( 'about', 'pixelgrid' ), __( 'shop', 'pixelgrid' ) ),
				'blockTypes'    => array( 'core/post-content' ),
				'postTypes'     => array( 'page' ),
				'viewportWidth' => 1400,
				'content'       => pixelgrid_shop_pattern_page_about(),
			),
			'shop-page-contact'  => array(
				'title'         => __( 'Shop contact page', 'pixelgrid' ),
				'description'   => __( 'Address, opening hours and email beside a pixel envelope, then common questions about delivery and returns.', 'pixelgrid' ),
				'categories'    => array( 'pixelgrid-pages' ),
				'keywords'      => array( __( 'page', 'pixelgrid' ), __( 'contact', 'pixelgrid' ), __( 'shop', 'pixelgrid' ) ),
				'blockTypes'    => array( 'core/post-content' ),
				'postTypes'     => array( 'page' ),
				'viewportWidth' => 1400,
				'content'       => pixelgrid_shop_pattern_page_contact(),
			),
			'shop-blog-heading'  => array(
				'title'       => __( 'Shop journal heading', 'pixelgrid' ),
				'description' => __( 'The Journal heading, a short introduction and category chips for the posts page.', 'pixelgrid' ),
				'categories'  => array( 'pixelgrid-shop' ),
				'inserter'    => false,
				'content'     => pixelgrid_shop_pattern_blog_heading(),
			),
			'shop-template-home' => array(
				'title'       => __( 'Shop posts page template', 'pixelgrid' ),
				'description' => __( 'Full posts page template: header, the Journal heading, a grid of post cards with pagination, and footer.', 'pixelgrid' ),
				'categories'  => array( 'pixelgrid-shop' ),
				'inserter'    => false,
				'content'     => pixelgrid_shop_pattern_template_home(),
			),
		);

		foreach ( $patterns as $slug => $properties ) {
			register_block_pattern( 'pixelgrid/' . $slug, $properties );
		}
	}
endif;
add_action( 'init', 'pixelgrid_register_shop_patterns' );
