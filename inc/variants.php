<?php
/**
 * Variant registry.
 *
 * A variant is a complete site flavour (pages, categories, sample posts and
 * optionally a style, parts and templates) that the setup page can apply.
 * Each variant lives in inc/variants/{slug}.php and returns an array.
 *
 * Pages are slug => array( title, pattern slug or '', template or '', parent slug or '' ).
 * A post's image is a plate number (assets/images/plate-N.webp) or a theme-relative file path.
 * content_labels ('pages', 'posts', 'reading') and design_note are the sentences the setup
 * screen shows for the variant; empty ones fall back to generic sentences.
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
		'slug'               => '',
		'label'              => '',
		'description'        => '',
		'style'              => '',
		'parts'              => array(),
		'templates'          => array(),
		'pages'              => array(),
		'front'              => '',
		'posts_page'         => '',
		'menu'               => array(),
		'categories'         => array(),
		'posts'              => array(),
		'products'           => array(),
		'product_categories' => array(),
		'requires'           => array(),
		'preview'            => '',
		'content_labels'     => array(),
		'design_note'        => '',
	);
	return array_merge( $defaults, (array) $variant );
}

/**
 * Loads one variant file.
 *
 * @param string $path Full path to the variant file.
 * @return array|null The variant, or null when the file is missing or does not return an array.
 */
function pixelgrid_variant_load_file( string $path ): ?array {
	if ( ! is_readable( $path ) ) {
		return null;
	}
	$variant = require $path;
	return is_array( $variant ) ? $variant : null;
}

/**
 * Lists the available variants, keyed by slug.
 *
 * @return array
 */
function pixelgrid_variants() {
	$variants = array();
	foreach ( array( 'it', 'agency', 'news', 'shop' ) as $slug ) {
		$variant = pixelgrid_variant_load_file( get_parent_theme_file_path( 'inc/variants/' . $slug . '.php' ) );
		if ( null === $variant ) {
			continue;
		}
		$variant           = pixelgrid_variant_defaults( $variant );
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
