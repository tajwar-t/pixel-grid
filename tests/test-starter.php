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

// Categories: created before pages, posts in several, category menu items, IDs filled into pages.
$cat_query = '<!-- wp:query {"queryId":1,"query":{"perPage":3,"postType":"post"},"className":"pixelgrid-cat-one"} --><div class="wp-block-query pixelgrid-cat-one"><!-- wp:post-template --><!-- wp:post-title /--><!-- /wp:post-template --></div><!-- /wp:query -->';
register_block_pattern(
	'pixelgrid-test/cat-query',
	array(
		'title'   => 'Category query test',
		'content' => $cat_query,
	)
);
add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['cats'] = array(
			'slug'       => 'cats',
			'label'      => 'Cats',
			'pages'      => array(
				'a' => array( 'A', 'pixelgrid-test/cat-query', '' ),
				'b' => array( 'B', '', '' ),
			),
			'menu'       => array( 'a', 'cat:one', 'cat:missing' ),
			'categories' => array(
				'one' => 'One',
				'two' => 'Two',
			),
			'posts'      => array(
				array(
					'key'      => 'p1',
					'title'    => 'P1',
					'excerpt'  => '',
					'content'  => '',
					'category' => array( 'one', 'two' ),
					'tags'     => array(),
				),
			),
		);
		return $variants;
	}
);
$cat_page_query = function () {
	$page   = pixelgrid_starter_find( 'page', 'cats:page-a' );
	$blocks = parse_blocks( (string) get_post_field( 'post_content', $page ) );
	foreach ( $blocks as $b ) {
		if ( 'core/query' === $b['blockName'] ) {
			return $b['attrs']['query'];
		}
	}
	return array();
};

// Pages ticked, Posts not: the categories still exist and the page gets the real ID.
pixelgrid_starter_create(
	'cats',
	array(
		'pages' => true,
		'posts' => false,
	)
);
$one = get_term_by( 'slug', 'one', 'category' );
$two = get_term_by( 'slug', 'two', 'category' );
pg_assert( $one && $two, 'with pages and not posts, categories one and two exist' );
$one_id = $one ? (int) $one->term_id : -1;
$two_id = $two ? (int) $two->term_id : -1;
$q      = $cat_page_query();
pg_assert_same( array( $one_id ), isset( $q['taxQuery']['category'] ) ? $q['taxQuery']['category'] : null, 'page a query gets taxQuery.category = [one id]' );
$a_content = (string) get_post_field( 'post_content', pixelgrid_starter_find( 'page', 'cats:page-a' ) );
pg_assert( false !== strpos( $a_content, '"taxQuery":{"category":[' . $one_id . ']}' ), 'page a markup holds "taxQuery":{"category":[one id]}' );

// A page made earlier without the ID (for example before the category existed) is filled on the next run.
wp_update_post(
	wp_slash(
		array(
			'ID'           => pixelgrid_starter_find( 'page', 'cats:page-a' ),
			'post_content' => $cat_query,
		)
	)
);
$all_cats = array(
	'pages' => true,
	'posts' => true,
	'menu'  => true,
);
pixelgrid_starter_create( 'cats', $all_cats );
$q = $cat_page_query();
pg_assert_same( array( $one_id ), isset( $q['taxQuery']['category'] ) ? $q['taxQuery']['category'] : null, 'running setup again fills the ID into a page made earlier' );

$p1      = pixelgrid_starter_find( 'post', 'cats:post-p1' );
$p1_cats = wp_get_post_categories( $p1 );
pg_assert( in_array( $one_id, $p1_cats, true ) && in_array( $two_id, $p1_cats, true ), 'post p1 is in categories one and two' );

$nav       = pixelgrid_starter_find( 'wp_navigation', 'cats:menu' );
$nav_links = parse_blocks( (string) get_post_field( 'post_content', $nav ) );
$page_n    = 0;
$cat_links = array();
foreach ( $nav_links as $b ) {
	if ( 'core/navigation-link' !== $b['blockName'] ) {
		continue;
	}
	if ( 'page' === $b['attrs']['type'] ) {
		++$page_n;
	} elseif ( 'category' === $b['attrs']['type'] ) {
		$cat_links[] = $b['attrs'];
	}
}
pg_assert_same( 1, $page_n, 'menu has one page link' );
pg_assert_same( 1, count( $cat_links ), 'menu has one category link and none for missing' );
pg_assert(
	isset( $cat_links[0] ) && $one_id === $cat_links[0]['id'] && 'taxonomy' === $cat_links[0]['kind'] && get_category_link( $one_id ) === $cat_links[0]['url'],
	'category link has the one term id, kind taxonomy and the category url'
);

pixelgrid_starter_create( 'cats', $all_cats );
$q = $cat_page_query();
pg_assert_same( array( $one_id ), isset( $q['taxQuery']['category'] ) ? $q['taxQuery']['category'] : null, 'running create again keeps one ID, no duplicates' );
pg_assert_same( 1, substr_count( (string) get_post_field( 'post_content', pixelgrid_starter_find( 'page', 'cats:page-a' ) ), '"category":[' ), 'page a holds a single category list' );

// Posts ticked, Pages not: categories exist; pages created later get the real IDs.
pixelgrid_starter_remove();
pg_assert( ! get_term_by( 'slug', 'one', 'category' ), 'remove deletes the empty starter categories' );
pixelgrid_starter_create(
	'cats',
	array(
		'pages' => false,
		'posts' => true,
	)
);
$one = get_term_by( 'slug', 'one', 'category' );
pg_assert( $one && get_term_by( 'slug', 'two', 'category' ), 'with posts and not pages, categories one and two exist' );
pixelgrid_starter_create(
	'cats',
	array(
		'pages' => true,
		'posts' => false,
	)
);
$q = $cat_page_query();
pg_assert_same( array( $one ? (int) $one->term_id : -1 ), isset( $q['taxQuery']['category'] ) ? $q['taxQuery']['category'] : null, 'a page created after the posts gets the real category ID' );
pixelgrid_starter_remove();

// Content with no marker block comes back byte for byte.
$plain = "<!-- wp:query {\"queryId\":1,\"query\":{\"perPage\":3},\"className\":\"other\"} -->\n<div class=\"wp-block-query other\">  odd   spacing </div>\n<!-- /wp:query -->\n\n<!-- wp:paragraph {\"x\":\"\\u003c\"} --><p>a</p><!-- /wp:paragraph -->";
pg_assert_same( $plain, pixelgrid_starter_fill_categories( $plain, array( 'one' => 5 ) ), 'content with no marker class is unchanged' );
$unknown = str_replace( 'other', 'pixelgrid-cat-nope', $plain );
pg_assert_same( $unknown, pixelgrid_starter_fill_categories( $unknown, array( 'one' => 5 ) ), 'content whose marker slug is not in the map is unchanged' );
$longer = str_replace( 'other', 'pixelgrid-cat-one-more', $plain );
pg_assert_same( $longer, pixelgrid_starter_fill_categories( $longer, array( 'one' => 5 ) ), 'a class that only starts with a marker is not a match' );

// The include/exclude taxQuery form (WordPress 6.8+, migrated by the editor) is filled in place.
$include_form = '<!-- wp:query {"queryId":1,"query":{"perPage":3,"taxQuery":{"include":{"category":[99]}}},"className":"pixelgrid-cat-one"} --><div class="wp-block-query pixelgrid-cat-one"></div><!-- /wp:query -->';
$filled       = parse_blocks( pixelgrid_starter_fill_categories( $include_form, array( 'one' => 5 ) ) );
pg_assert_same( array( 'include' => array( 'category' => array( 5 ) ) ), $filled[0]['attrs']['query']['taxQuery'], 'include form gets include.category = [id] and no sibling category key' );
$include_done = str_replace( '[99]', '[5]', $include_form );
pg_assert_same( $include_done, pixelgrid_starter_fill_categories( $include_done, array( 'one' => 5 ) ), 'include form already holding the ID is unchanged' );
$exclude_form = str_replace( '"include"', '"exclude"', $include_form );
$filled       = parse_blocks( pixelgrid_starter_fill_categories( $exclude_form, array( 'one' => 5 ) ) );
pg_assert_same(
	array(
		'exclude' => array( 'category' => array( 99 ) ),
		'include' => array( 'category' => array( 5 ) ),
	),
	$filled[0]['attrs']['query']['taxQuery'],
	'exclude form keeps exclude and gets include.category, with no legacy key'
);

// IT and Agency, Pages only: their pages and menus use no categories, so none are created.
add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		foreach ( array( 'it', 'agency' ) as $slug ) {
			$variants[ $slug ]         = pixelgrid_variant_load_file( get_parent_theme_file_path( 'inc/variants/' . $slug . '.php' ) );
			$variants[ $slug ]['slug'] = $slug;
		}
		return $variants;
	},
	100
);
foreach ( array( 'it', 'agency' ) as $slug ) {
	$def  = pixelgrid_variant( $slug );
	$made = pixelgrid_starter_create( $slug, array( 'pages' => true ) );
	pg_assert( is_array( $made ) && $made['pages'] > 0, "$slug pages-only creates its pages" );
	$found = array();
	foreach ( array_keys( $def['categories'] ) as $cat_slug ) {
		if ( get_term_by( 'slug', $cat_slug, 'category' ) ) {
			$found[] = $cat_slug;
		}
	}
	pg_assert( count( $def['categories'] ) > 0, "$slug defines categories" );
	pg_assert_same( array(), $found, "$slug pages-only creates no categories" );
	pg_assert_same( array(), get_option( 'pixelgrid_starter_terms', array() ), "$slug pages-only records no starter terms" );
	pixelgrid_starter_remove();
}

pg_done();
