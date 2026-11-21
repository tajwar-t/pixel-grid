<?php
/**
 * Front-end helpers: the pixelgrid/today binding source and the related-posts query filter.
 */

// The today binding, with no post and outside the loop (a News style picked without setup).
pg_assert( function_exists( 'get_block_bindings_source' ) && null !== get_block_bindings_source( 'pixelgrid/today' ), 'pixelgrid/today binding source is registered' );
pg_assert( null === get_post() && ! in_the_loop(), 'binding test runs outside the loop with no post' );
$today = wp_date( get_option( 'date_format' ) );
$html  = do_blocks( '<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"pixelgrid/today"}}}} --><p></p><!-- /wp:paragraph -->' );
pg_assert( false !== strpos( $html, $today ), 'a paragraph bound to pixelgrid/today renders today\'s date outside the loop' );

// Related posts.
$x = wp_insert_term( 'Related X', 'category', array( 'slug' => 'pg-related-x' ) );
$w = wp_insert_term( 'Related W', 'category', array( 'slug' => 'pg-related-w' ) );
$x = (int) $x['term_id'];
$w = (int) $w['term_id'];

$make = function ( $title, $cats, $days ) {
	$id = wp_insert_post(
		array(
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => $title,
			'post_category' => $cats,
			'post_date'     => gmdate( 'Y-m-d H:i:s', time() - $days * DAY_IN_SECONDS ),
		)
	);
	if ( ! $cats ) {
		// wp_insert_post falls back to the default category; clear it for a post with none.
		wp_set_object_terms( $id, array(), 'category' );
	}
	return (int) $id;
};
$x1 = $make( 'Alphapost', array( $x ), 1 );
$x2 = $make( 'Bravopost', array( $x ), 2 );
$x3 = $make( 'Charliepost', array( $x ), 3 );
$y  = $make( 'Yankeepost', array(), 4 );
$z  = $make( 'Zulupost', array( $w ), 5 );
pg_assert_same( array(), wp_get_post_categories( $y ), 'post Y has no categories' );

$view = function ( $args ) {
	$GLOBALS['wp_query']     = new WP_Query( $args );
	$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
};

$query = function ( $class ) {
	$attrs = array(
		'queryId' => 7,
		'query'   => array(
			'perPage'  => 5,
			'postType' => 'post',
			'inherit'  => false,
		),
	);
	$extra = '';
	if ( $class ) {
		$attrs['className'] = $class;
		$extra              = ' ' . $class;
	}
	return '<!-- wp:query ' . wp_json_encode( $attrs ) . ' --><div class="wp-block-query' . $extra . '"><!-- wp:post-template --><!-- wp:post-title /--><!-- /wp:post-template --></div><!-- /wp:query -->';
};
$related = $query( 'pixelgrid-related' );
$nested  = '<!-- wp:group --><div class="wp-block-group">' . $query( 'is-style-x pixelgrid-related' ) . '</div><!-- /wp:group -->';
$plain   = $query( '' );

$view( array( 'p' => $x1 ) );
pg_assert( is_singular( 'post' ), 'viewing post X1 is singular' );
$out = do_blocks( $related );
pg_assert( false !== strpos( $out, 'Bravopost' ) && false !== strpos( $out, 'Charliepost' ), 'related row on X1 lists the other two X posts' );
pg_assert( false === strpos( $out, 'Alphapost' ), 'related row on X1 does not list X1 itself' );
pg_assert( false === strpos( $out, 'Yankeepost' ) && false === strpos( $out, 'Zulupost' ) && false === strpos( $out, 'Hello world' ), 'related row on X1 lists no post from another category' );
$out = do_blocks( $nested );
pg_assert( false !== strpos( $out, 'Bravopost' ) && false !== strpos( $out, 'Charliepost' ) && false === strpos( $out, 'Alphapost' ), 'a related query nested in a group, with a second class, also filters' );
$out = do_blocks( $plain );
pg_assert( false !== strpos( $out, 'Alphapost' ) && false !== strpos( $out, 'Yankeepost' ), 'a query without the pixelgrid-related class is untouched' );

$view( array( 'p' => $y ) );
$out = do_blocks( $related );
pg_assert( false === strpos( $out, 'wp-block-post-title' ), 'related row on a post with no categories lists nothing' );

$view( array( 'p' => $z ) );
$out = do_blocks( $related );
pg_assert( false === strpos( $out, 'wp-block-post-title' ), 'related row on the only post in its category lists nothing, not the post itself' );
$with_empty = str_replace( '<!-- /wp:post-template -->', '<!-- /wp:post-template --><!-- wp:query-no-results --><!-- wp:paragraph --><p>Norelatedmessage</p><!-- /wp:paragraph --><!-- /wp:query-no-results -->', $related );
$out        = do_blocks( $with_empty );
pg_assert( false !== strpos( $out, 'Norelatedmessage' ), 'on the only post in its category, the related query\'s no-results block renders' );
// Pagination links sit one level deeper, inside core/query-pagination: they get the flag too.
$paged = str_replace(
	array( '"perPage":5', '<!-- /wp:post-template -->' ),
	array( '"perPage":1', '<!-- /wp:post-template --><!-- wp:query-pagination --><!-- wp:query-pagination-next {"label":"Nextrelated"} /--><!-- /wp:query-pagination -->' ),
	$related
);
$out   = do_blocks( $paged );
pg_assert( false === strpos( $out, 'Nextrelated' ), 'on the only post in its category, the related query shows no next-page link' );
$view( array( 'p' => $x1 ) );
$out = do_blocks( $with_empty );
pg_assert( false === strpos( $out, 'Norelatedmessage' ) && false !== strpos( $out, 'Bravopost' ), 'with related posts, the no-results block stays hidden' );
$out = do_blocks( $paged );
pg_assert( false !== strpos( $out, 'Nextrelated' ), 'with two related posts and one per page, the next-page link shows' );

$view( array( 'page_id' => 2 ) );
$out = do_blocks( $related );
pg_assert( false !== strpos( $out, 'Alphapost' ), 'related class outside a single post leaves the query untouched' );

// Direct call: a post-template block whose query has no related flag is returned as is.
$view( array( 'p' => $x1 ) );
$block = new WP_Block(
	array(
		'blockName'    => 'core/post-template',
		'attrs'        => array(),
		'innerBlocks'  => array(),
		'innerHTML'    => '',
		'innerContent' => array(),
	),
	array( 'query' => array( 'perPage' => 3 ) )
);
$vars  = array(
	'post_type'      => 'post',
	'posts_per_page' => 3,
);
pg_assert_same( $vars, pixelgrid_related_query_vars( $vars, $block ), 'related filter leaves an unmarked query unchanged' );
pg_assert_same( array(), pixelgrid_related_query_vars( array(), null ), 'related filter given no block returns the query unchanged' );

// Most read is a list, not a section: post-terms labels never show it, but the post keeps it.
$pg_business  = (int) wp_insert_term( 'Business', 'category', array( 'slug' => 'business' ) )['term_id'];
$pg_most_read = (int) wp_insert_term( 'Most read', 'category', array( 'slug' => 'most-read' ) )['term_id'];
$pg_opinion   = (int) wp_insert_term( 'Opinion', 'category', array( 'slug' => 'opinion' ) )['term_id'];
$pg_mr_post   = wp_insert_post(
	array(
		'post_title'    => 'Mostreadpost',
		'post_status'   => 'publish',
		'post_category' => array( $pg_business, $pg_most_read, $pg_opinion ),
	)
);
// Inside a parent block, as in a post template: the block is rendered by WP_Block::render().
$pg_render_terms = function () use ( $pg_mr_post ) {
	return ( new WP_Block(
		parse_blocks( '<!-- wp:group --><div class="wp-block-group"><!-- wp:post-terms {"term":"category"} /--></div><!-- /wp:group -->' )[0],
		array(
			'postId'   => $pg_mr_post,
			'postType' => 'post',
		)
	) )->render();
};
$pg_terms_out    = $pg_render_terms();
pg_assert( false !== strpos( $pg_terms_out, 'Business' ), 'post-terms shows Business' );
pg_assert( false !== strpos( $pg_terms_out, 'Opinion' ), 'post-terms still shows Opinion' );
pg_assert( false === strpos( $pg_terms_out, 'Most read' ), 'post-terms hides Most read' );
$pg_slugs = wp_list_pluck( (array) get_the_terms( $pg_mr_post, 'category' ), 'slug' );
sort( $pg_slugs );
pg_assert_same( array( 'business', 'most-read', 'opinion' ), $pg_slugs, 'get_the_terms outside the block still returns Most read' );
pg_assert( false === has_filter( 'get_the_terms', 'pixelgrid_hide_most_read_term' ), 'the Most read filter is removed after the block renders' );
$pg_terms_out = $pg_render_terms();
pg_assert( false !== strpos( $pg_terms_out, 'Business' ) && false === strpos( $pg_terms_out, 'Most read' ), 'a second post-terms render hides Most read too' );
// As a top-level block, as in the single template: render_block() with the global post.
$GLOBALS['post'] = get_post( $pg_mr_post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- test setup.
$pg_terms_out    = do_blocks( '<!-- wp:post-terms {"term":"category"} /-->' );
pg_assert( false !== strpos( $pg_terms_out, 'Business' ) && false === strpos( $pg_terms_out, 'Most read' ), 'a top-level post-terms block hides Most read' );
pg_assert( false === has_filter( 'get_the_terms', 'pixelgrid_hide_most_read_term' ), 'the Most read filter is removed after a top-level render' );

wp_reset_query();
pg_done();
