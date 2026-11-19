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
 * The parameters are the binding source callback's signature; the date needs none of them.
 *
 * @param array         $source_args    Binding arguments from the block markup.
 * @param WP_Block|null $block_instance The block being rendered.
 * @param string        $attribute_name The bound attribute.
 * @return string Escaped date.
 */
function pixelgrid_today_value( $source_args = array(), $block_instance = null, $attribute_name = '' ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter -- documents the callback signature.
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
 * Marks the query context of every block inside a query block with the pixelgrid-related
 * class, so pixelgrid_related_query_vars() can tell a related query from any other.
 *
 * The blocks inside a query (post template, no results, pagination, total) only receive the
 * query's attributes through context, and className is not among them. Here the parent block
 * is at hand: a child of a related core/query gets a flag in the 'query' context it already
 * uses, and a deeper child (for example a pagination link inside core/query-pagination)
 * inherits the flag from its parent's context. Saved markup is not changed.
 *
 * @param array         $context      Block context.
 * @param array         $parsed_block Block being rendered.
 * @param WP_Block|null $parent_block Parent block, if any.
 * @return array
 */
function pixelgrid_related_context( $context, $parsed_block, $parent_block = null ) {
	if ( ! isset( $context['query'] ) || ! is_array( $context['query'] ) || ! $parent_block instanceof WP_Block ) {
		return $context;
	}
	$related = ! empty( $parent_block->context['query']['pixelgridRelated'] ) || (
		'core/query' === $parent_block->name &&
		isset( $parent_block->parsed_block['attrs']['className'] ) &&
		pixelgrid_has_class( $parent_block->parsed_block['attrs']['className'], 'pixelgrid-related' )
	);
	if ( $related ) {
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
 * @param WP_Block $block A block inside the query: post template, no results, pagination or total.
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
