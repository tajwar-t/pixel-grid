<?php
/**
 * Starter content: an Appearance page that creates the demo pages, notes and menu
 * from the theme's own patterns, only when the user asks for it.
 *
 * Nothing runs on theme activation. Nothing is downloaded and no external calls are made.
 * Everything created is marked with the _pixelgrid_starter meta key so it can be listed and removed.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Who may use the setup page.
 *
 * @return bool
 */
function pixelgrid_starter_can() {
	return current_user_can( 'edit_theme_options' ) && current_user_can( 'publish_pages' ) && current_user_can( 'publish_posts' ) && current_user_can( 'upload_files' );
}

/**
 * Builds the marker stored in _pixelgrid_starter: the variant slug and the item key.
 *
 * @param string $variant Variant slug.
 * @param string $key     Item key, for example page-home.
 * @return string
 */
function pixelgrid_starter_key( string $variant, string $key ): string {
	return $variant . ':' . $key;
}

/**
 * The variant whose starter content is on the site.
 *
 * @return string Variant slug, or an empty string when there is none.
 */
function pixelgrid_starter_active_variant(): string {
	$variant = (string) get_option( 'pixelgrid_starter_variant', '' );
	if ( '' === $variant ) {
		return '';
	}
	if ( pixelgrid_starter_exists() || false !== get_option( 'pixelgrid_starter_design', false ) ) {
		return $variant;
	}
	// Nothing setup made is left (for example, it was all trashed by hand): the variant is stale.
	delete_option( 'pixelgrid_starter_variant' );
	return '';
}

/**
 * Whether any post of any type that is not in the trash carries a starter marker.
 *
 * @return bool
 */
function pixelgrid_starter_exists(): bool {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- runs only on the setup page and setup actions.
	$found = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT pm.post_id FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND p.post_status NOT IN ( 'trash', 'auto-draft' ) LIMIT 1",
			'_pixelgrid_starter'
		)
	);
	return ! empty( $found );
}

/**
 * Finds an item created earlier by its starter marker. Trashed items are not found.
 *
 * @param string $post_type Post type.
 * @param string $full_key  Starter marker, for example it:page-home.
 * @return int Post ID or 0.
 */
function pixelgrid_starter_find( string $post_type, string $full_key ): int {
	$found = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => array( 'publish', 'draft', 'private', 'inherit' ),
			'meta_key'       => '_pixelgrid_starter', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- runs only on the setup page.
			'meta_value'     => $full_key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- runs only on the setup page.
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * Lists everything the setup created, grouped by post type.
 *
 * @return array
 */
function pixelgrid_starter_created() {
	$ids = get_posts(
		array(
			'post_type'      => array( 'page', 'post', 'attachment', 'wp_navigation' ),
			'post_status'    => array( 'publish', 'draft', 'private', 'inherit' ),
			'meta_key'       => '_pixelgrid_starter', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- runs only on the setup page.
			'posts_per_page' => 200, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- bounded list of starter items, admin setup page only.
			'fields'         => 'ids',
		)
	);
	$out = array(
		'page'          => array(),
		'post'          => array(),
		'attachment'    => array(),
		'wp_navigation' => array(),
	);
	foreach ( $ids as $id ) {
		$out[ get_post_type( $id ) ][] = (int) $id;
	}
	return $out;
}

/**
 * Replaces pattern references with the pattern markup, so pages hold editable blocks.
 *
 * @param string $content Block markup.
 * @param int    $depth   Nesting guard.
 * @return string
 */
function pixelgrid_starter_expand( $content, $depth = 0 ) {
	if ( $depth > 5 ) {
		return $content;
	}
	$registry = WP_Block_Patterns_Registry::get_instance();
	return preg_replace_callback(
		'/<!-- wp:pattern \{"slug":"([^"]+)"\} \/-->/',
		function ( $matches ) use ( $registry, $depth ) {
			$pattern = $registry->get_registered( $matches[1] );
			return $pattern ? pixelgrid_starter_expand( trim( $pattern['content'] ), $depth + 1 ) : '';
		},
		$content
	);
}

/**
 * Inserts a post once and marks it as starter content.
 *
 * @param string $full_key Starter marker, for example it:page-home.
 * @param array  $args     wp_insert_post() arguments.
 * @return int Post ID or 0.
 */
function pixelgrid_starter_insert( $full_key, $args ) {
	$existing = pixelgrid_starter_find( $args['post_type'], $full_key );
	if ( $existing ) {
		return $existing;
	}
	$id = wp_insert_post( wp_slash( array_merge( array( 'post_status' => 'publish' ), $args ) ), true );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	update_post_meta( $id, '_pixelgrid_starter', $full_key );
	return (int) $id;
}

/**
 * Copies one of the theme's bundled images into the media library.
 *
 * An image number (IT's plates) keeps the 1.2.0 file and marker: assets/images/plate-N.webp,
 * marked image-N. A theme-relative path is marked image-{file name without extension}.
 *
 * @param string     $variant Variant slug.
 * @param int|string $image   Plate number, 1 to 8, or a theme-relative image path.
 * @return int Attachment ID or 0.
 */
function pixelgrid_starter_image( $variant, $image ) {
	if ( is_int( $image ) || ctype_digit( (string) $image ) ) {
		$number = (int) $image;
		$name   = (string) $number;
		$path   = 'assets/images/plate-' . $number . '.webp';
		$file   = 'pixelgrid-plate-' . $number . '.webp';
		/* translators: %d: image number. */
		$title = sprintf( __( 'Pixelgrid sample image %d', 'pixelgrid' ), $number );
	} else {
		$path = ltrim( (string) $image, '/' );
		$name = sanitize_key( pathinfo( $path, PATHINFO_FILENAME ) );
		if ( '' === $path || '' === $name || 0 !== validate_file( $path ) ) {
			return 0;
		}
		$file = 'pixelgrid-' . sanitize_file_name( basename( $path ) );
		/* translators: %s: image name, for example agency-03. */
		$title = sprintf( __( 'Pixelgrid sample image %s', 'pixelgrid' ), $name );
	}
	$type = wp_check_filetype( $file );
	if ( empty( $type['type'] ) || 0 !== strpos( $type['type'], 'image/' ) ) {
		return 0;
	}
	$key      = pixelgrid_starter_key( $variant, 'image-' . $name );
	$existing = pixelgrid_starter_find( 'attachment', $key );
	if ( $existing ) {
		return $existing;
	}
	$source = get_parent_theme_file_path( $path );
	if ( ! is_readable( $source ) ) {
		return 0;
	}
	$upload = wp_upload_bits( $file, null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file bundled with the theme.
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => $type['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_pixelgrid_starter', $key );
	return (int) $id;
}

/**
 * Patterns link to pages as ?pagename=slug, which works on any site. On the new pages,
 * swaps those for the pages' real addresses so the links read cleanly. Child pages are
 * also matched as ?pagename={parent}/{slug}, built from the slugs in $pages.
 *
 * @param array $pages Page IDs keyed by slug.
 */
function pixelgrid_starter_fix_links( $pages ) {
	$map   = array();
	$slugs = array_flip( array_filter( $pages ) );
	foreach ( $pages as $slug => $id ) {
		if ( ! $id ) {
			continue;
		}
		$link = esc_url( get_permalink( $id ) );

		$map[ esc_url( add_query_arg( 'pagename', $slug, home_url( '/' ) ) ) ] = $link;

		// Walk up through parents that are in the list: work/work-one, and so on.
		$path   = (string) $slug;
		$parent = (int) wp_get_post_parent_id( $id );
		$guard  = 0;
		while ( $parent && isset( $slugs[ $parent ] ) && $guard < 10 ) {
			$path   = $slugs[ $parent ] . '/' . $path;
			$parent = (int) wp_get_post_parent_id( $parent );
			++$guard;
		}
		if ( (string) $slug !== $path ) {
			$map[ esc_url( add_query_arg( 'pagename', $path, home_url( '/' ) ) ) ] = $link;
		}
	}
	foreach ( $pages as $id ) {
		if ( ! $id ) {
			continue;
		}
		$content = get_post_field( 'post_content', $id );
		$fixed   = strtr( $content, $map );
		if ( $fixed !== $content ) {
			wp_update_post(
				wp_slash(
					array(
						'ID'           => $id,
						'post_content' => $fixed,
					)
				)
			);
		}
	}
}

/**
 * Orders a variant's pages so every page whose parent is in the list comes after that parent.
 * Pages keep their listed order otherwise. A parent loop is broken by keeping the rest as listed.
 *
 * @param array $pages Pages keyed by slug: array( title, pattern, template, parent slug ).
 * @return array
 */
function pixelgrid_starter_page_order( array $pages ): array {
	$ordered = array();
	$left    = $pages;
	while ( $left ) {
		$progress = false;
		foreach ( $left as $slug => $page ) {
			$parent = ! empty( $page[3] ) ? (string) $page[3] : '';
			if ( '' === $parent || (string) $slug === $parent || ! isset( $pages[ $parent ] ) || isset( $ordered[ $parent ] ) ) {
				$ordered[ $slug ] = $page;
				unset( $left[ $slug ] );
				$progress = true;
			}
		}
		if ( ! $progress ) {
			$ordered += $left;
			break;
		}
	}
	return $ordered;
}

/**
 * Creates the starter content the user picked for one variant.
 *
 * @param string $variant Variant slug.
 * @param array  $options Keys: design, pages, posts, menu, reading (booleans).
 * @return array|WP_Error Counts of what was created (pages, posts, menu), or an error when
 *                        the variant is unknown, unavailable or another variant is in use, or
 *                        pixelgrid_style_missing from the design apply (nothing is created).
 */
function pixelgrid_starter_create( string $variant, array $options ) {
	$def = pixelgrid_variant( $variant );
	if ( ! $def ) {
		return new WP_Error( 'pixelgrid_variant_unknown', __( 'That variant does not exist.', 'pixelgrid' ) );
	}
	if ( ! pixelgrid_variant_available( $def ) ) {
		return new WP_Error( 'pixelgrid_variant_unavailable', __( 'That variant needs a plugin that is not active.', 'pixelgrid' ) );
	}
	$active = pixelgrid_starter_active_variant();
	if ( '' !== $active && $variant !== $active ) {
		return new WP_Error( 'pixelgrid_variant_busy', __( 'Remove the current starter content to switch.', 'pixelgrid' ) );
	}
	$options = wp_parse_args(
		$options,
		array(
			'design'  => false,
			'pages'   => false,
			'posts'   => false,
			'menu'    => false,
			'reading' => false,
		)
	);

	// True once an item was made or found, or the design applied; only then is the variant recorded.
	$any = false;

	// The design goes first: a style file it cannot use stops setup before anything is created.
	if ( $options['design'] ) {
		$applied = pixelgrid_design_apply( $def );
		if ( is_wp_error( $applied ) ) {
			return $applied;
		}
		$any = true;
	}

	$made  = array(
		'pages' => 0,
		'posts' => 0,
		'menu'  => 0,
	);
	$pages = array();

	if ( $options['pages'] ) {
		foreach ( pixelgrid_starter_page_order( $def['pages'] ) as $slug => $page ) {
			$key      = pixelgrid_starter_key( $variant, 'page-' . $slug );
			$before   = pixelgrid_starter_find( 'page', $key );
			$pattern  = ! empty( $page[1] ) ? WP_Block_Patterns_Registry::get_instance()->get_registered( $page[1] ) : null;
			$template = ! empty( $page[2] ) ? $page[2] : '';
			$args     = array(
				'post_type'    => 'page',
				'post_title'   => $page[0],
				'post_name'    => $slug,
				'post_content' => $pattern ? pixelgrid_starter_expand( $pattern['content'] ) : '',
			);
			if ( $template ) {
				$args['page_template'] = $template;
			}
			$parent = ! empty( $page[3] ) ? (string) $page[3] : '';
			if ( '' !== $parent && ! empty( $pages[ $parent ] ) ) {
				$args['post_parent'] = (int) $pages[ $parent ];
			}
			$pages[ $slug ] = pixelgrid_starter_insert( $key, $args );
			if ( $pages[ $slug ] ) {
				$any = true;
			}
			if ( $pages[ $slug ] && ! $before ) {
				++$made['pages'];
			}
		}
		pixelgrid_starter_fix_links( $pages );
	}

	if ( $options['posts'] ) {
		$terms = get_option( 'pixelgrid_starter_terms', array() );
		$cats  = array();
		foreach ( $def['categories'] as $slug => $name ) {
			$term = get_term_by( 'slug', $slug, 'category' );
			if ( $term ) {
				$cats[ $slug ] = (int) $term->term_id;
				continue;
			}
			$new = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
			if ( ! is_wp_error( $new ) ) {
				$cats[ $slug ] = (int) $new['term_id'];
				$terms[]       = (int) $new['term_id'];
			}
		}
		update_option( 'pixelgrid_starter_terms', array_values( array_unique( $terms ) ), false );

		$step = 0;
		foreach ( $def['posts'] as $item ) {
			$key    = pixelgrid_starter_key( $variant, 'post-' . $item['key'] );
			$before = pixelgrid_starter_find( 'post', $key );
			$id     = pixelgrid_starter_insert(
				$key,
				array(
					'post_type'     => 'post',
					'post_title'    => $item['title'],
					'post_name'     => $item['key'],
					'post_excerpt'  => $item['excerpt'],
					'post_content'  => $item['content'],
					'post_category' => isset( $cats[ $item['category'] ] ) ? array( $cats[ $item['category'] ] ) : array(),
					'post_date'     => gmdate( 'Y-m-d H:i:s', time() - ( $step + 1 ) * 3 * DAY_IN_SECONDS ),
				)
			);
			++$step;
			if ( $id ) {
				$any = true;
			}
			if ( ! $id || $before ) {
				continue;
			}
			++$made['posts'];
			if ( ! empty( $item['tags'] ) ) {
				wp_set_post_tags( $id, $item['tags'] );
			}
			$image = ! empty( $item['image'] ) ? pixelgrid_starter_image( $variant, $item['image'] ) : 0;
			if ( $image ) {
				set_post_thumbnail( $id, $image );
			}
		}
	}

	if ( $options['menu'] && $pages ) {
		$links = array();
		foreach ( $def['menu'] as $slug ) {
			if ( empty( $pages[ $slug ] ) ) {
				continue;
			}
			$links[] = '<!-- wp:navigation-link ' . wp_json_encode(
				array(
					'label' => get_the_title( $pages[ $slug ] ),
					'type'  => 'page',
					'id'    => $pages[ $slug ],
					'url'   => get_permalink( $pages[ $slug ] ),
					'kind'  => 'post-type',
				)
			) . ' /-->';
		}
		$key    = pixelgrid_starter_key( $variant, 'menu' );
		$before = pixelgrid_starter_find( 'wp_navigation', $key );
		$menu   = $links ? pixelgrid_starter_insert(
			$key,
			array(
				'post_type'    => 'wp_navigation',
				'post_title'   => __( 'Pixelgrid menu', 'pixelgrid' ),
				'post_content' => implode( "\n", $links ),
			)
		) : 0;
		if ( $menu ) {
			$any = true;
		}
		if ( $menu && ! $before ) {
			$made['menu'] = 1;
		}
	}

	$front = $def['front'];
	if ( $options['reading'] && $front && ! empty( $pages[ $front ] ) ) {
		$posts_page = ( $def['posts_page'] && ! empty( $pages[ $def['posts_page'] ] ) ) ? (int) $pages[ $def['posts_page'] ] : 0;
		if ( ! get_option( 'pixelgrid_starter_reading' ) ) {
			update_option(
				'pixelgrid_starter_reading',
				array(
					'show_on_front'  => get_option( 'show_on_front' ),
					'page_on_front'  => (int) get_option( 'page_on_front' ),
					'page_for_posts' => (int) get_option( 'page_for_posts' ),
				),
				false
			);
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $pages[ $front ] );
		update_option( 'page_for_posts', $posts_page );
	}

	if ( $any ) {
		update_option( 'pixelgrid_starter_variant', $variant, false );
	}
	update_option( 'pixelgrid_starter_done', 1, false );
	return $made;
}

/**
 * Removes everything the setup created, restores the reading settings it changed and
 * undoes the design change when it can.
 *
 * @return array Keys: removed (int), styles_restored (bool), kept (string[], "{type}:{slug}"),
 *               styles_written (bool, whether the applied design had written the styles),
 *               revisions (bool, whether the user global styles have revisions to recover).
 */
function pixelgrid_starter_remove(): array {
	$count   = 0;
	$created = pixelgrid_starter_created();
	// Children before parents, deepest first, so no child is left published under a trashed parent.
	$depth = array();
	foreach ( $created['page'] as $id ) {
		$depth[ $id ] = count( get_post_ancestors( $id ) );
	}
	usort(
		$created['page'],
		function ( $a, $b ) use ( $depth ) {
			return $depth[ $b ] - $depth[ $a ];
		}
	);
	foreach ( $created as $type => $ids ) {
		foreach ( $ids as $id ) {
			$done = 'attachment' === $type ? wp_delete_attachment( $id, true ) : wp_trash_post( $id );
			if ( $done ) {
				++$count;
			}
		}
	}
	foreach ( get_option( 'pixelgrid_starter_terms', array() ) as $term_id ) {
		$term = get_term( (int) $term_id, 'category' );
		if ( $term && ! is_wp_error( $term ) && 0 === (int) $term->count ) {
			wp_delete_term( (int) $term_id, 'category' );
		}
	}
	$reading = get_option( 'pixelgrid_starter_reading' );
	if ( is_array( $reading ) ) {
		update_option( 'show_on_front', $reading['show_on_front'] );
		update_option( 'page_on_front', $reading['page_on_front'] );
		update_option( 'page_for_posts', $reading['page_for_posts'] );
	}

	$design = pixelgrid_design_undo();

	delete_option( 'pixelgrid_starter_terms' );
	delete_option( 'pixelgrid_starter_reading' );
	delete_option( 'pixelgrid_starter_done' );
	delete_option( 'pixelgrid_starter_variant' );

	return array(
		'removed'         => $count,
		'styles_restored' => (bool) $design['styles_restored'],
		'kept'            => array_values( (array) $design['kept'] ),
		'styles_written'  => (bool) $design['styles_written'],
		'revisions'       => (bool) $design['revisions'],
	);
}

/**
 * Upgrades starter content made by 1.2.0, whose markers had no variant prefix, to the IT variant.
 * Runs once, when the stored schema is below 2, and is safe to run again.
 */
function pixelgrid_starter_upgrade(): void {
	if ( (int) get_option( 'pixelgrid_starter_schema', 0 ) >= 2 ) {
		return;
	}
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-time upgrade of the theme's own markers.
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value NOT LIKE %s",
			'_pixelgrid_starter',
			'%' . $wpdb->esc_like( ':' ) . '%'
		)
	);
	foreach ( (array) $rows as $row ) {
		update_post_meta( (int) $row->post_id, '_pixelgrid_starter', pixelgrid_starter_key( 'it', $row->meta_value ), $row->meta_value );
	}

	if ( '' === pixelgrid_starter_active_variant() ) {
		$created = pixelgrid_starter_created();
		if ( $created['page'] || $created['post'] || $created['attachment'] || $created['wp_navigation'] ) {
			update_option( 'pixelgrid_starter_variant', 'it', false );
		}
	}
	update_option( 'pixelgrid_starter_schema', 2 );
}
add_action( 'admin_init', 'pixelgrid_starter_upgrade' );
