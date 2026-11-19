<?php
/**
 * Variant registry tests.
 */
pg_assert_same( array( 'it', 'agency' ), array_keys( pixelgrid_variants() ), 'registry lists the it and agency variants' );
$it = pixelgrid_variant( 'it' );
pg_assert_same( 'home', $it['front'], 'it front page is home' );
pg_assert_same( 6, count( $it['posts'] ), 'it has 6 posts' );
pg_assert_same( null, pixelgrid_variant( 'nope' ), 'unknown slug returns null' );
foreach ( array( 'slug', 'label', 'description', 'style', 'parts', 'templates', 'pages', 'front', 'posts_page', 'menu', 'categories', 'posts', 'products', 'requires', 'preview', 'content_labels', 'design_note' ) as $key ) {
	pg_assert( array_key_exists( $key, $it ), "it has key $key" );
}
pg_assert( pixelgrid_variant_available( $it ), 'it is available' );

add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['shoppy'] = array( 'slug' => 'shoppy', 'requires' => array( 'woocommerce' ) );
		return $variants;
	}
);
pg_assert_same( ! class_exists( 'WooCommerce' ), ! pixelgrid_variant_available( pixelgrid_variant( 'shoppy' ) ), 'woocommerce-requiring variant availability follows plugin' );
if ( ! class_exists( 'WooCommerce' ) ) {
	pg_assert( false === pixelgrid_variant_available( pixelgrid_variant( 'shoppy' ) ), 'woocommerce variant unavailable without plugin' );
}
// A variant file that does not return an array is skipped, not fatal.
$fixture = get_parent_theme_file_path( 'tests/fixtures/variant-returns-one.php' );
pg_assert_same( null, function_exists( 'pixelgrid_variant_load_file' ) ? pixelgrid_variant_load_file( $fixture ) : 'missing', 'a variant file returning 1 is skipped by the loader' );
pg_assert( function_exists( 'pixelgrid_variant_load_file' ) && is_array( pixelgrid_variant_load_file( get_parent_theme_file_path( 'inc/variants/it.php' ) ) ), 'the loader returns the it definition' );

// IT carries its own setup wording.
foreach ( array( 'pages', 'posts', 'reading' ) as $key ) {
	pg_assert( isset( $it['content_labels'][ $key ] ) && '' !== $it['content_labels'][ $key ], "it has content label $key" );
}
pg_assert( isset( $it['design_note'] ) && '' !== $it['design_note'], 'it has a design note' );

// A filtered variant without the new keys gets empty defaults.
$shoppy = pixelgrid_variant( 'shoppy' );
pg_assert_same( array(), $shoppy['content_labels'] ?? null, 'filtered variant gets empty content_labels' );
pg_assert_same( '', $shoppy['design_note'] ?? null, 'filtered variant gets an empty design_note' );

if ( ! class_exists( 'WooCommerce' ) ) {
	$result = pixelgrid_starter_create( 'shoppy', array( 'pages' => true ) );
	pg_assert( is_wp_error( $result ) && 'pixelgrid_variant_unavailable' === $result->get_error_code(), 'create for a woocommerce variant without the plugin returns pixelgrid_variant_unavailable' );
}
pg_done();
