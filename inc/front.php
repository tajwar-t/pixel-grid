<?php
/**
 * Front-end helpers for the theme's own blocks: the "today's date" binding source used by
 * mastheads, and the related-posts row under an article.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Today's date in the site's date format, for the pixelgrid/today binding source.
 * It needs no post, so it works in any template and outside the loop.
 *
 * @return string Escaped date.
 */
function pixelgrid_today_value() {
	return esc_html( wp_date( get_option( 'date_format' ) ) );
}

/**
 * Registers the pixelgrid/today block binding source.
 */
function pixelgrid_register_binding_sources() {
	if ( ! function_exists( 'register_block_bindings_source' ) ) {
		return;
	}
	register_block_bindings_source(
		'pixelgrid/today',
		array(
			'label'              => __( "Today's date", 'pixelgrid' ),
			'get_value_callback' => 'pixelgrid_today_value',
		)
	);
}
add_action( 'init', 'pixelgrid_register_binding_sources' );

/**
 * Whether a class attribute holds the given class name.
 *
 * @param mixed  $class_name Class attribute value.
 * @param string $wanted     Class name to look for.
 * @return bool
 */
function pixelgrid_has_class( $class_name, string $wanted ): bool {
	if ( ! is_string( $class_name ) || '' === $class_name ) {
		return false;
	}
	return in_array( $wanted, preg_split( '/\s+/', $class_name, -1, PREG_SPLIT_NO_EMPTY ), true );
}

/**
 * Marks the query context of a post template whose parent query block has the
 * pixelgrid-related class, so pixelgrid_related_query_vars() can tell the two apart.
 *
 * The post template only receives its query's attributes through context, and className
 * is not among them. Here the parent block is at hand, so its class is read and a flag is
 * added to the 'query' context the post template already uses. Saved markup is not changed.
 *
 * @param array         $context      Block context.
 * @param array         $parsed_block Block being rendered.
 * @param WP_Block|null $parent_block Parent block, if any.
 * @return array
 */
function pixelgrid_related_context( $context, $parsed_block, $parent_block = null ) {
	if (
		isset( $parsed_block['blockName'] ) && 'core/post-template' === $parsed_block['blockName'] &&
		$parent_block instanceof WP_Block && 'core/query' === $parent_block->name &&
		isset( $parent_block->parsed_block['attrs']['className'] ) &&
		pixelgrid_has_class( $parent_block->parsed_block['attrs']['className'], 'pixelgrid-related' ) &&
		isset( $context['query'] ) && is_array( $context['query'] )
	) {
		$context['query']['pixelgridRelated'] = true;
	}
	return $context;
}
add_filter( 'render_block_context', 'pixelgrid_related_context', 10, 3 );

/**
 * Fills a related-posts row: on a single post, a query block with the pixelgrid-related class
 * lists other posts that share the post's categories and never the post itself. A post with
 * no categories gets an empty row.
 *
 * This only narrows which posts a theme-styled query block shows; it creates and stores nothing.
 *
 * @param array    $query Query vars built from the query block.
 * @param WP_Block $block The post template block.
 * @return array
 */
function pixelgrid_related_query_vars( array $query, WP_Block $block ): array {
	if ( empty( $block->context['query']['pixelgridRelated'] ) || ! is_singular( 'post' ) ) {
		return $query;
	}
	$post_id = (int) get_queried_object_id();
	if ( ! $post_id ) {
		return $query;
	}
	$cats = array_map( 'intval', wp_get_post_categories( $post_id ) );

	$query['post__not_in']        = array_values( array_unique( array_merge( isset( $query['post__not_in'] ) ? (array) $query['post__not_in'] : array(), array( $post_id ) ) ) );
	$query['ignore_sticky_posts'] = true;
	if ( $cats ) {
		$query['category__in'] = $cats;
	} else {
		$query['post__in'] = array( 0 );
	}
	return $query;
}
add_filter( 'query_loop_block_query_vars', 'pixelgrid_related_query_vars', 10, 2 );
