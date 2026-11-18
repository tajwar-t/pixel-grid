<?php
/**
 * Starter content tests: variant-aware create, markers, remove and 1.2.0 upgrade.
 */
// WP-CLI does not fire admin_init, so run the upgrade the way admin would on a fresh site.
pixelgrid_starter_upgrade();
pg_assert_same( 2, (int) get_option( 'pixelgrid_starter_schema' ), 'upgrade sets schema 2 on a fresh site' );
pg_assert_same( '', pixelgrid_starter_active_variant(), 'no active variant on a fresh site' );
pg_assert_same( 'it:page-home', pixelgrid_starter_key( 'it', 'page-home' ), 'starter key joins variant and key' );

$all = array(
	'design'  => false,
	'pages'   => true,
	'posts'   => true,
	'menu'    => true,
	'reading' => true,
);

$made = pixelgrid_starter_create( 'it', $all );
pg_assert_same(
	array(
		'pages' => 6,
		'posts' => 6,
		'menu'  => 1,
	),
	$made,
	'first create makes 6 pages, 6 posts and 1 menu'
);
$home = (int) get_option( 'page_on_front' );
pg_assert_same( 'it:page-home', get_post_meta( $home, '_pixelgrid_starter', true ), 'home page carries it:page-home' );
pg_assert_same( $home, pixelgrid_starter_find( 'page', 'it:page-home' ), 'find locates the home page by full key' );
pg_assert_same( 'page', get_option( 'show_on_front' ), 'reading shows a page on front' );
pg_assert( pixelgrid_starter_find( 'wp_navigation', 'it:menu' ) > 0, 'menu carries it:menu' );
pg_assert( pixelgrid_starter_find( 'attachment', 'it:image-3' ) > 0, 'image carries it:image-3' );
pg_assert_same( 'it', pixelgrid_starter_active_variant(), 'active variant is it after create' );

$made = pixelgrid_starter_create( 'it', $all );
pg_assert_same(
	array(
		'pages' => 0,
		'posts' => 0,
		'menu'  => 0,
	),
	$made,
	'second create makes nothing'
);

$contact = pixelgrid_starter_find( 'page', 'it:page-contact' );
wp_trash_post( $contact );
pg_assert_same( 0, pixelgrid_starter_find( 'page', 'it:page-contact' ), 'trashed contact page is not found' );
$made = pixelgrid_starter_create( 'it', $all );
pg_assert_same( 1, $made['pages'], 'create after trashing contact recreates 1 page' );

add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['agency'] = array(
			'slug'  => 'agency',
			'label' => 'Agency',
			'pages' => array( 'home' => array( 'Home', '', '' ) ),
			'front' => 'home',
		);
		return $variants;
	}
);
$busy = pixelgrid_starter_create( 'agency', $all );
pg_assert( is_wp_error( $busy ) && 'pixelgrid_variant_busy' === $busy->get_error_code(), 'another variant while it is active returns pixelgrid_variant_busy' );
$unknown = pixelgrid_starter_create( 'nope', $all );
pg_assert( is_wp_error( $unknown ) && 'pixelgrid_variant_unknown' === $unknown->get_error_code(), 'unknown variant returns pixelgrid_variant_unknown' );

$removed = pixelgrid_starter_remove();
pg_assert_same( 19, $removed['removed'], 'remove removes 19 items' );
pg_assert( array_key_exists( 'styles_restored', $removed ) && is_bool( $removed['styles_restored'] ), 'remove reports styles_restored' );
pg_assert( isset( $removed['kept'] ) && is_array( $removed['kept'] ), 'remove reports kept' );
pg_assert_same( 'posts', get_option( 'show_on_front' ), 'reading restored to posts' );
pg_assert_same( '', pixelgrid_starter_active_variant(), 'no active variant after remove' );
pg_assert_same( false, get_option( 'pixelgrid_starter_done' ), 'done flag cleared after remove' );

// Old 1.2.0 marker upgrade.
$old = wp_insert_post(
	array(
		'post_type'   => 'page',
		'post_title'  => 'Old home',
		'post_status' => 'publish',
	)
);
update_post_meta( $old, '_pixelgrid_starter', 'page-home' );
update_option( 'pixelgrid_starter_schema', 1 );
pixelgrid_starter_upgrade();
pg_assert_same( 'it:page-home', get_post_meta( $old, '_pixelgrid_starter', true ), 'upgrade rewrites page-home to it:page-home' );
pg_assert_same( 'it', pixelgrid_starter_active_variant(), 'upgrade sets the active variant to it' );
pg_assert_same( 2, (int) get_option( 'pixelgrid_starter_schema' ), 'upgrade sets schema 2' );
pixelgrid_starter_upgrade();
pg_assert_same( 'it:page-home', get_post_meta( $old, '_pixelgrid_starter', true ), 'second upgrade leaves the marker alone' );
$removed = pixelgrid_starter_remove();
pg_assert_same( 1, $removed['removed'], 'remove deletes the upgraded page' );
pg_assert_same( 'trash', get_post_status( $old ), 'upgraded page is in the trash' );

pg_done();
