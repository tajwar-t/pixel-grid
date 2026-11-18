<?php
/**
 * Variant registry.
 *
 * A variant is a complete site flavour (pages, categories, sample posts and
 * optionally a style, parts and templates) that the setup page can apply.
 * Each variant lives in inc/variants/{slug}.php and returns an array.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fills every documented variant key with an empty default.
 *
 * @param array $variant Variant as returned by its file.
 * @return array
 */
function pixelgrid_variant_defaults( $variant ) {
	$defaults = array(
		'slug'        => '',
		'label'       => '',
		'description' => '',
		'style'       => '',
		'parts'       => array(),
		'templates'   => array(),
		'pages'       => array(),
		'front'       => '',
		'posts_page'  => '',
		'menu'        => array(),
		'categories'  => array(),
		'posts'       => array(),
		'products'    => array(),
		'requires'    => array(),
		'preview'     => '',
	);
	return array_merge( $defaults, (array) $variant );
}

/**
 * Lists the available variants, keyed by slug.
 *
 * @return array
 */
function pixelgrid_variants() {
	$variants = array();
	foreach ( array( 'it', 'agency', 'news', 'shop' ) as $slug ) {
		$file = get_parent_theme_file_path( 'inc/variants/' . $slug . '.php' );
		if ( ! file_exists( $file ) ) {
			continue;
		}
		$variant           = pixelgrid_variant_defaults( require $file );
		$variant['slug']   = $slug;
		$variants[ $slug ] = $variant;
	}

	/**
	 * Filters the registered variants.
	 *
	 * @param array $variants Variants keyed by slug.
	 */
	$variants = apply_filters( 'pixelgrid_variants', $variants );

	foreach ( $variants as $slug => $variant ) {
		$variants[ $slug ] = pixelgrid_variant_defaults( $variant );
	}
	return $variants;
}

/**
 * Gets one variant by slug.
 *
 * @param string $slug Variant slug.
 * @return array|null
 */
function pixelgrid_variant( $slug ) {
	$variants = pixelgrid_variants();
	return isset( $variants[ $slug ] ) ? $variants[ $slug ] : null;
}

/**
 * Whether a variant can be applied on this site.
 *
 * @param array $variant Variant.
 * @return bool
 */
function pixelgrid_variant_available( $variant ) {
	$requires = isset( $variant['requires'] ) ? (array) $variant['requires'] : array();
	if ( in_array( 'woocommerce', $requires, true ) && ! class_exists( 'WooCommerce' ) ) {
		return false;
	}
	return true;
}
