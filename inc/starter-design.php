<?php
/**
 * Variant design: applies a variant's style variation, template parts and templates as
 * site-level customisations, and undoes them, restoring what was there before.
 *
 * Everything needed to undo is kept in the pixelgrid_starter_design option: the user global
 * styles before and after setup, and for each part or template the post, whether setup
 * created it, its content before setup and what setup wrote. Undo only puts things back when
 * they still hold exactly what setup wrote, so edits made after setup are never lost.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The user global styles post for the active theme, created when missing.
 *
 * @return WP_Post|null
 */
function pixelgrid_design_styles_post() {
	$data = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true );
	if ( empty( $data['ID'] ) ) {
		return null;
	}
	return get_post( (int) $data['ID'] );
}

/**
 * Clears the cached global styles so a change shows at once, as core does.
 */
function pixelgrid_design_clean_styles_cache() {
	WP_Theme_JSON_Resolver::clean_cached_data();
	if ( function_exists( 'wp_clean_theme_json_cache' ) ) {
		wp_clean_theme_json_cache();
	}
}

/**
 * Writes the user global styles content.
 *
 * @param int    $post_id Global styles post ID.
 * @param string $content JSON content.
 */
function pixelgrid_design_write_styles( $post_id, $content ) {
	wp_update_post(
		wp_slash(
			array(
				'ID'           => $post_id,
				'post_content' => $content,
			)
		)
	);
	pixelgrid_design_clean_styles_cache();
}

/**
 * Decodes JSON into arrays with object keys sorted, so equal data compares equal
 * whatever the key order or whitespace.
 *
 * @param string $json JSON.
 * @return mixed
 */
function pixelgrid_design_normalise( $json ) {
	$data = json_decode( (string) $json, true );
	return pixelgrid_design_sort_keys( $data );
}

/**
 * Sorts associative array keys recursively. Lists keep their order.
 *
 * @param mixed $data Data.
 * @return mixed
 */
function pixelgrid_design_sort_keys( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}
	foreach ( $data as $key => $value ) {
		$data[ $key ] = pixelgrid_design_sort_keys( $value );
	}
	if ( array_keys( $data ) !== range( 0, count( $data ) - 1 ) ) {
		ksort( $data );
	}
	return $data;
}

/**
 * The user global styles a variant writes: its style variation, or the theme default.
 *
 * @param array $variant Variant.
 * @return string JSON.
 */
function pixelgrid_design_style_json( array $variant ) {
	$data = array();
	if ( '' !== (string) $variant['style'] ) {
		$file = get_parent_theme_file_path( $variant['style'] );
		if ( is_readable( $file ) ) {
			$decoded = wp_json_file_decode( $file, array( 'associative' => true ) );
			if ( is_array( $decoded ) ) {
				$data = $decoded;
			}
		}
	}
	unset( $data['title'], $data['$schema'] );
	$data['version']                     = 3;
	$data['isGlobalStylesUserThemeJSON'] = true;
	return (string) wp_json_encode( $data );
}

/**
 * Post ID of the site-level customisation of a template or part, or 0.
 *
 * @param string $type wp_template_part or wp_template.
 * @param string $slug Slug.
 * @return int
 */
function pixelgrid_design_find_custom( $type, $slug ) {
	$template = get_block_template( get_stylesheet() . '//' . $slug, $type );
	if ( $template && 'custom' === $template->source && ! empty( $template->wp_id ) ) {
		return (int) $template->wp_id;
	}
	return 0;
}

/**
 * Creates a site-level customisation of a template or part.
 *
 * @param string $type    wp_template_part or wp_template.
 * @param string $slug    Slug.
 * @param string $content Block markup.
 * @return int Post ID or 0.
 */
function pixelgrid_design_insert( $type, $slug, $content ) {
	$id = wp_insert_post(
		wp_slash(
			array(
				'post_type'    => $type,
				'post_name'    => $slug,
				'post_title'   => ucwords( str_replace( array( '-', '_' ), ' ', $slug ) ),
				'post_status'  => 'publish',
				'post_content' => $content,
			)
		),
		true
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	wp_set_object_terms( $id, get_stylesheet(), 'wp_theme' );
	if ( 'wp_template_part' === $type ) {
		$area = in_array( $slug, array( 'header', 'footer' ), true ) ? $slug : 'uncategorized';
		wp_set_object_terms( $id, $area, 'wp_template_part_area' );
	}
	return (int) $id;
}

/**
 * Whether a post still exists and is not in the trash.
 *
 * @param int $id Post ID.
 * @return WP_Post|null
 */
function pixelgrid_design_live_post( $id ) {
	$post = $id ? get_post( (int) $id ) : null;
	return ( $post && 'trash' !== $post->post_status ) ? $post : null;
}

/**
 * Applies a variant's design: style variation, template parts and templates.
 *
 * Applying again while a design is applied keeps the first backups, so undo still returns
 * the site to how it was before the first apply.
 *
 * @param array $variant Variant.
 */
function pixelgrid_design_apply( array $variant ): void {
	$variant  = pixelgrid_variant_defaults( $variant );
	$previous = get_option( 'pixelgrid_starter_design', false );
	$previous = is_array( $previous ) ? $previous : null;

	$state = array(
		'styles_before'  => '',
		'styles_written' => '',
		'entities'       => array(),
	);

	$styles = pixelgrid_design_styles_post();
	if ( $styles ) {
		$written                 = pixelgrid_design_style_json( $variant );
		$state['styles_before']  = $previous ? (string) $previous['styles_before'] : (string) $styles->post_content;
		$state['styles_written'] = $written;
		pixelgrid_design_write_styles( $styles->ID, $written );
	}

	$old = array();
	if ( $previous ) {
		foreach ( (array) $previous['entities'] as $entity ) {
			$old[ $entity['type'] . '|' . $entity['slug'] ] = $entity;
		}
	}

	$items = array(
		'wp_template_part' => (array) $variant['parts'],
		'wp_template'      => (array) $variant['templates'],
	);
	foreach ( $items as $type => $map ) {
		foreach ( $map as $slug => $pattern ) {
			$slug    = (string) $slug;
			$content = pixelgrid_starter_expand( '<!-- wp:pattern {"slug":"' . $pattern . '"} /-->' );
			if ( '' === trim( $content ) ) {
				continue;
			}
			$key = $type . '|' . $slug;

			if ( isset( $old[ $key ] ) && pixelgrid_design_live_post( $old[ $key ]['post_id'] ) ) {
				// Already applied: keep the first backup and write again.
				$entity            = $old[ $key ];
				$entity['written'] = $content;
				pixelgrid_design_set_content( $entity['post_id'], $content );
				unset( $old[ $key ] );
				$state['entities'][] = $entity;
				continue;
			}
			unset( $old[ $key ] );

			$existing = pixelgrid_design_find_custom( $type, $slug );
			if ( $existing ) {
				$entity = array(
					'type'    => $type,
					'slug'    => $slug,
					'post_id' => $existing,
					'created' => false,
					'before'  => (string) get_post( $existing )->post_content,
					'written' => $content,
				);
				pixelgrid_design_set_content( $existing, $content );
			} else {
				$id = pixelgrid_design_insert( $type, $slug, $content );
				if ( ! $id ) {
					continue;
				}
				update_post_meta( $id, '_pixelgrid_starter', pixelgrid_starter_key( $variant['slug'], 'design-' . $type . '-' . $slug ) );
				$entity = array(
					'type'    => $type,
					'slug'    => $slug,
					'post_id' => $id,
					'created' => true,
					'before'  => '',
					'written' => $content,
				);
			}
			$state['entities'][] = $entity;
		}
	}

	// Entities from an earlier apply that this one does not write stay recorded so undo handles them.
	foreach ( $old as $entity ) {
		$state['entities'][] = $entity;
	}

	if ( false === get_option( 'pixelgrid_starter_design', false ) ) {
		add_option( 'pixelgrid_starter_design', $state, '', false );
	} else {
		update_option( 'pixelgrid_starter_design', $state, false );
	}
}

/**
 * Sets the content of a template or part post.
 *
 * @param int    $id      Post ID.
 * @param string $content Block markup.
 */
function pixelgrid_design_set_content( $id, $content ) {
	wp_update_post(
		wp_slash(
			array(
				'ID'           => (int) $id,
				'post_content' => $content,
			)
		)
	);
}

/**
 * Undoes the applied design. Styles, parts and templates are put back only when they still
 * hold exactly what setup wrote; anything changed after setup is left as it is.
 *
 * @return array Keys: styles_restored (bool), kept (string[], slugs left because they changed).
 */
function pixelgrid_design_undo(): array {
	$result = array(
		'styles_restored' => false,
		'kept'            => array(),
	);
	$state  = get_option( 'pixelgrid_starter_design', false );
	if ( ! is_array( $state ) ) {
		return $result;
	}

	$styles = pixelgrid_design_styles_post();
	if ( $styles && '' !== (string) $state['styles_written'] ) {
		if ( pixelgrid_design_normalise( $styles->post_content ) === pixelgrid_design_normalise( $state['styles_written'] ) ) {
			pixelgrid_design_write_styles( $styles->ID, (string) $state['styles_before'] );
			$result['styles_restored'] = true;
		}
	}

	foreach ( (array) $state['entities'] as $entity ) {
		$post = pixelgrid_design_live_post( $entity['post_id'] );
		if ( ! $post ) {
			// Gone: a created customisation needs nothing more; a backed-up one was removed after setup.
			if ( empty( $entity['created'] ) ) {
				$result['kept'][] = $entity['slug'];
			}
			continue;
		}
		if ( trim( $post->post_content ) !== trim( (string) $entity['written'] ) ) {
			$result['kept'][] = $entity['slug'];
			continue;
		}
		if ( ! empty( $entity['created'] ) ) {
			wp_delete_post( $post->ID, true );
		} else {
			pixelgrid_design_set_content( $post->ID, (string) $entity['before'] );
		}
	}

	$result['kept'] = array_values( array_unique( $result['kept'] ) );
	delete_option( 'pixelgrid_starter_design' );
	return $result;
}
