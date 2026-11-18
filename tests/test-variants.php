<?php
/**
 * Variant registry tests.
 */
pg_assert_same( array( 'it' ), array_keys( pixelgrid_variants() ), 'registry lists only the it variant' );
$it = pixelgrid_variant( 'it' );
pg_assert_same( 'home', $it['front'], 'it front page is home' );
pg_assert_same( 6, count( $it['posts'] ), 'it has 6 posts' );
pg_assert_same( null, pixelgrid_variant( 'nope' ), 'unknown slug returns null' );
foreach ( array( 'slug', 'label', 'description', 'style', 'parts', 'templates', 'pages', 'front', 'posts_page', 'menu', 'categories', 'posts', 'products', 'requires', 'preview' ) as $key ) {
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
pg_done();
