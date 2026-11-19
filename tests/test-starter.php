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

// A create that makes nothing must not mark the site busy.
$none = array(
	'design'  => false,
	'pages'   => false,
	'posts'   => false,
	'menu'    => false,
	'reading' => false,
);
$made = pixelgrid_starter_create( 'it', $none );
pg_assert( is_array( $made ), 'create with nothing ticked returns counts' );
pg_assert_same( '', pixelgrid_starter_active_variant(), 'create with nothing ticked sets no active variant' );
pg_assert( ! is_wp_error( pixelgrid_starter_create( 'agency', $none ) ), 'second variant is not busy after an empty create' );

// Starter content trashed by hand leaves no stale busy state.
pixelgrid_starter_create(
	'it',
	array(
		'design'  => false,
		'pages'   => true,
		'posts'   => true,
		'menu'    => true,
		'reading' => false,
	)
);
pg_assert_same( 'it', pixelgrid_starter_active_variant(), 'active variant is it after creating content again' );
foreach ( pixelgrid_starter_created() as $type => $ids ) {
	foreach ( $ids as $id ) {
		if ( 'attachment' === $type ) {
			wp_delete_attachment( $id, true );
		} else {
			wp_trash_post( $id );
		}
	}
}
pg_assert_same( '', pixelgrid_starter_active_variant(), 'active variant is empty once every starter item is trashed by hand' );
pg_assert_same( false, get_option( 'pixelgrid_starter_variant' ), 'stale variant option is cleared' );
pg_assert( ! is_wp_error( pixelgrid_starter_create( 'agency', $none ) ), 'second variant is not busy after starter content was trashed by hand' );

// A full 1.2.0 site: markers without a variant prefix, schema 1 and no variant option.
pixelgrid_starter_remove();
$no_design = array(
	'design'  => false,
	'pages'   => true,
	'posts'   => true,
	'menu'    => true,
	'reading' => true,
);
pixelgrid_starter_create( 'it', $no_design );
global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = SUBSTRING( meta_value, 4 ) WHERE meta_key = %s AND meta_value LIKE %s", '_pixelgrid_starter', 'it:%' ) );
wp_cache_flush();
delete_option( 'pixelgrid_starter_variant' );
update_option( 'pixelgrid_starter_schema', 1 );
$old_markers = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s ORDER BY meta_value", '_pixelgrid_starter' ) );
pg_assert( in_array( 'page-home', $old_markers, true ) && in_array( 'post-password-clean-up', $old_markers, true ) && in_array( 'image-1', $old_markers, true ) && in_array( 'menu', $old_markers, true ), '1.2.0 site carries old markers page-, post-, image- and menu' );
pg_assert_same( array(), array_values( array_filter( $old_markers, function ( $v ) { return false !== strpos( $v, ':' ); } ) ), '1.2.0 site has no prefixed markers' );
$attachments = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment'" );
pg_assert( $attachments > 0, '1.2.0 site has starter images' );
pixelgrid_starter_upgrade();
pg_assert_same( 'it', pixelgrid_starter_active_variant(), 'upgraded 1.2.0 site has it as the active variant' );
$made = pixelgrid_starter_create( 'it', $no_design );
pg_assert_same(
	array(
		'pages' => 0,
		'posts' => 0,
		'menu'  => 0,
	),
	$made,
	'create on an upgraded 1.2.0 site makes no duplicates'
);
pg_assert_same( $attachments, (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment'" ), 'create on an upgraded 1.2.0 site adds no images' );
$removed = pixelgrid_starter_remove();
pg_assert( $removed['removed'] >= 19, 'upgraded 1.2.0 content is removed' );

// Child pages, links to them and images given by path.
register_block_pattern(
	'pixelgrid-test/work',
	array(
		'title'   => 'Work test',
		'content' => '<!-- wp:paragraph --><p><a href="' . esc_url( add_query_arg( 'pagename', 'work/work-one', home_url( '/' ) ) ) . '">One</a></p><!-- /wp:paragraph -->',
	)
);
add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['kids'] = array(
			'slug'  => 'kids',
			'label' => 'Kids',
			// The child is listed first on purpose: create must still insert the parent first.
			'pages' => array(
				'work-one' => array( 'Work one', '', '', 'work' ),
				'work'     => array( 'Work', 'pixelgrid-test/work', '' ),
			),
			'posts' => array(
				array(
					'key'      => 'imaged',
					'title'    => 'Imaged',
					'excerpt'  => '',
					'content'  => '',
					'category' => '',
					'tags'     => array(),
					'image'    => 'tests/fixtures/test-image.webp',
				),
			),
		);
		return $variants;
	}
);
$made = pixelgrid_starter_create(
	'kids',
	array(
		'pages' => true,
		'posts' => true,
	)
);
pg_assert( is_array( $made ) && 2 === $made['pages'] && 1 === $made['posts'], 'kids variant creates 2 pages and 1 post' );
$work     = pixelgrid_starter_find( 'page', 'kids:page-work' );
$work_one = pixelgrid_starter_find( 'page', 'kids:page-work-one' );
pg_assert( $work > 0 && $work_one > 0, 'child and parent pages carry page-{slug} markers' );
pg_assert_same( $work, (int) get_post_field( 'post_parent', $work_one ), 'work-one post_parent is the work page' );
$content = (string) get_post_field( 'post_content', $work );
pg_assert( false !== strpos( $content, esc_url( get_permalink( $work_one ) ) ) && false === strpos( $content, 'pagename=work/work-one' ), 'a ?pagename=work/work-one link gets the child permalink' );
$imaged = pixelgrid_starter_find( 'post', 'kids:post-imaged' );
$thumb  = (int) get_post_thumbnail_id( $imaged );
pg_assert( $thumb > 0 && $thumb === pixelgrid_starter_find( 'attachment', 'kids:image-test-image' ), 'a post image given by path gets a featured image marked kids:image-test-image' );

$trash_order = array();
add_action(
	'wp_trash_post',
	function ( $post_id ) use ( &$trash_order ) {
		$trash_order[] = (int) $post_id;
	}
);
pixelgrid_starter_remove();
$child_at  = array_search( $work_one, $trash_order, true );
$parent_at = array_search( $work, $trash_order, true );
pg_assert( false !== $child_at && false !== $parent_at && $child_at < $parent_at, 'remove trashes the child page before its parent' );
pg_assert( 'trash' === get_post_status( $work ) && 'trash' === get_post_status( $work_one ), 'remove trashes both pages and leaves no orphan published' );

pg_done();
