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
			'posts_per_page' => 200,
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
		function ( $match ) use ( $registry, $depth ) {
			$pattern = $registry->get_registered( $match[1] );
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
 * @param string $variant Variant slug.
 * @param int    $number  Image number, 1 to 8.
 * @return int Attachment ID or 0.
 */
function pixelgrid_starter_image( $variant, $number ) {
	$key      = pixelgrid_starter_key( $variant, 'image-' . (int) $number );
	$existing = pixelgrid_starter_find( 'attachment', $key );
	if ( $existing ) {
		return $existing;
	}
	$source = get_parent_theme_file_path( 'assets/images/plate-' . (int) $number . '.webp' );
	if ( ! is_readable( $source ) ) {
		return 0;
	}
	$upload = wp_upload_bits( 'pixelgrid-plate-' . (int) $number . '.webp', null, file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file bundled with the theme.
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/webp',
			/* translators: %d: image number. */
			'post_title'     => sprintf( __( 'Pixelgrid sample image %d', 'pixelgrid' ), (int) $number ),
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
 * swaps those for the pages' real addresses so the links read cleanly.
 *
 * @param array $pages Page IDs keyed by slug.
 */
function pixelgrid_starter_fix_links( $pages ) {
	$map = array();
	foreach ( $pages as $slug => $id ) {
		if ( $id ) {
			$map[ esc_url( add_query_arg( 'pagename', $slug, home_url( '/' ) ) ) ] = esc_url( get_permalink( $id ) );
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
 * Creates the starter content the user picked for one variant.
 *
 * @param string $variant Variant slug.
 * @param array  $options Keys: design, pages, posts, menu, reading (booleans).
 * @return array|WP_Error Counts of what was created (pages, posts, menu), or an error when
 *                        the variant is unknown, unavailable or another variant is in use.
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

	if ( $options['design'] ) {
		pixelgrid_design_apply( $def );
		$any = true;
	}

	$made  = array(
		'pages' => 0,
		'posts' => 0,
		'menu'  => 0,
	);
	$pages = array();

	if ( $options['pages'] ) {
		foreach ( $def['pages'] as $slug => $page ) {
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
 * @return array Keys: removed (int), styles_restored (bool), kept (string[]).
 */
function pixelgrid_starter_remove(): array {
	$count = 0;
	foreach ( pixelgrid_starter_created() as $type => $ids ) {
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

/**
 * Adds Appearance > Pixelgrid setup.
 */
function pixelgrid_starter_menu() {
	add_theme_page(
		__( 'Pixelgrid setup', 'pixelgrid' ),
		__( 'Pixelgrid setup', 'pixelgrid' ),
		'edit_theme_options',
		'pixelgrid-setup',
		'pixelgrid_starter_screen'
	);
}
add_action( 'admin_menu', 'pixelgrid_starter_menu' );

/**
 * Reads a ticked checkbox from the submitted setup form. Called only after the nonce check.
 *
 * @param string $name Field name.
 * @return bool
 */
function pixelgrid_starter_ticked( $name ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in pixelgrid_starter_handle() before this runs.
	return isset( $_POST[ $name ] ) && '1' === sanitize_key( wp_unslash( $_POST[ $name ] ) );
}

/**
 * Handles the create and remove buttons.
 */
function pixelgrid_starter_handle() {
	if ( ! pixelgrid_starter_can() ) {
		wp_die( esc_html__( 'You do not have permission to set up starter content.', 'pixelgrid' ) );
	}
	check_admin_referer( 'pixelgrid_starter' );
	$task = isset( $_POST['pixelgrid_task'] ) ? sanitize_key( wp_unslash( $_POST['pixelgrid_task'] ) ) : '';
	$back = admin_url( 'themes.php?page=pixelgrid-setup' );

	if ( 'create' === $task ) {
		$made = pixelgrid_starter_create(
			'it',
			array(
				'design'  => false,
				'pages'   => pixelgrid_starter_ticked( 'pixelgrid_pages' ),
				'posts'   => pixelgrid_starter_ticked( 'pixelgrid_posts' ),
				'menu'    => pixelgrid_starter_ticked( 'pixelgrid_menu' ),
				'reading' => pixelgrid_starter_ticked( 'pixelgrid_reading' ),
			)
		);
		if ( is_wp_error( $made ) ) {
			wp_safe_redirect( add_query_arg( array( 'pixelgrid_error' => str_replace( 'pixelgrid_variant_', '', $made->get_error_code() ) ), $back ) );
			exit;
		}
		wp_safe_redirect( add_query_arg( array( 'pixelgrid_created' => $made['pages'] . '-' . $made['posts'] . '-' . $made['menu'] ), $back ) );
		exit;
	}
	if ( 'remove' === $task && pixelgrid_starter_ticked( 'pixelgrid_confirm' ) ) {
		$result = pixelgrid_starter_remove();
		wp_safe_redirect( add_query_arg( array( 'pixelgrid_removed' => $result['removed'] ), $back ) );
		exit;
	}
	wp_safe_redirect( add_query_arg( array( 'pixelgrid_removed' => 'unconfirmed' ), $back ) );
	exit;
}
add_action( 'admin_post_pixelgrid_starter', 'pixelgrid_starter_handle' );

/**
 * Prints the setup page with core admin markup only.
 */
function pixelgrid_starter_screen() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$created = pixelgrid_starter_created();
	$total   = count( $created['page'] ) + count( $created['post'] ) + count( $created['wp_navigation'] );
	$can     = pixelgrid_starter_can();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Pixelgrid setup', 'pixelgrid' ); ?></h1>
		<?php
		// Status messages after a redirect; they only pick which message to show.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['pixelgrid_created'] ) ) {
			$counts = array_map( 'absint', explode( '-', sanitize_text_field( wp_unslash( $_GET['pixelgrid_created'] ) ) ) + array( 0, 0, 0 ) );
			echo '<div class="notice notice-success"><p>';
			printf(
				/* translators: 1: number of pages, 2: number of notes, 3: number of menus. */
				esc_html__( 'Starter content added: %1$d pages, %2$d notes and %3$d menu. Anything that already existed was left as it was.', 'pixelgrid' ),
				(int) $counts[0],
				(int) $counts[1],
				(int) $counts[2]
			);
			echo ' <a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'View the site', 'pixelgrid' ) . '</a></p></div>';
		}
		if ( isset( $_GET['pixelgrid_removed'] ) ) {
			$removed = sanitize_key( wp_unslash( $_GET['pixelgrid_removed'] ) );
			if ( 'unconfirmed' === $removed ) {
				echo '<div class="notice notice-warning"><p>' . esc_html__( 'Nothing was removed. Tick the confirmation box first.', 'pixelgrid' ) . '</p></div>';
			} else {
				echo '<div class="notice notice-success"><p>';
				/* translators: %d: number of items removed. */
				printf( esc_html__( 'Removed %d starter items.', 'pixelgrid' ), absint( $removed ) );
				echo '</p></div>';
			}
		}
		if ( isset( $_GET['pixelgrid_error'] ) ) {
			$error    = sanitize_key( wp_unslash( $_GET['pixelgrid_error'] ) );
			$messages = array(
				'busy'        => __( 'Nothing was added. Remove the current starter content to switch.', 'pixelgrid' ),
				'unavailable' => __( 'Nothing was added. This variant needs a plugin that is not active.', 'pixelgrid' ),
				'unknown'     => __( 'Nothing was added. That variant does not exist.', 'pixelgrid' ),
			);
			if ( isset( $messages[ $error ] ) ) {
				echo '<div class="notice notice-error"><p>' . esc_html( $messages[ $error ] ) . '</p></div>';
			}
		}
		// phpcs:enable
		?>

		<p><?php esc_html_e( 'Pixelgrid can add the pages shown in its preview, so you can start by editing instead of building from nothing. Choose what to add. Nothing is downloaded: everything is built from the theme\'s own patterns and images.', 'pixelgrid' ); ?></p>

		<h2><?php esc_html_e( 'Add starter content', 'pixelgrid' ); ?></h2>
		<?php if ( ! $can ) : ?>
			<p><?php esc_html_e( 'Your account cannot publish pages, posts and images, so starter content cannot be added. Ask a site administrator.', 'pixelgrid' ); ?></p>
		<?php else : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="pixelgrid_starter">
			<input type="hidden" name="pixelgrid_task" value="create">
			<?php wp_nonce_field( 'pixelgrid_starter' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Content', 'pixelgrid' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><?php esc_html_e( 'Content to add', 'pixelgrid' ); ?></legend>
							<label><input type="checkbox" name="pixelgrid_pages" value="1" checked> <?php esc_html_e( 'Pages: Home, Services, About, Pricing, Contact and Notes', 'pixelgrid' ); ?></label><br>
							<label><input type="checkbox" name="pixelgrid_posts" value="1" checked> <?php esc_html_e( 'Six sample notes (posts) with categories, tags and images', 'pixelgrid' ); ?></label><br>
							<label><input type="checkbox" name="pixelgrid_menu" value="1" checked> <?php esc_html_e( 'A menu linking the new pages', 'pixelgrid' ); ?></label>
							<p class="description"><?php esc_html_e( 'The header and footer show the newest menu, so this replaces the menu they show now. You can switch back in the Navigation block.', 'pixelgrid' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Homepage', 'pixelgrid' ); ?></th>
					<td>
						<label><input type="checkbox" name="pixelgrid_reading" value="1" checked> <?php esc_html_e( 'Use the new Home page as the homepage and Notes as the posts page', 'pixelgrid' ); ?></label>
						<p class="description"><?php esc_html_e( 'This changes Settings, Reading. Your current choice is saved and put back if you remove the starter content.', 'pixelgrid' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Add starter content', 'pixelgrid' ), 'primary', 'pixelgrid_submit', false ); ?>
		</form>
		<?php endif; ?>

		<?php if ( $total || $created['attachment'] ) : ?>
			<h2><?php esc_html_e( 'Starter content on this site', 'pixelgrid' ); ?></h2>
			<?php
			$groups = array(
				'page' => __( 'Pages', 'pixelgrid' ),
				'post' => __( 'Notes', 'pixelgrid' ),
			);
			foreach ( $groups as $type => $label ) :
				if ( ! $created[ $type ] ) {
					continue;
				}
				?>
				<h3><?php echo esc_html( $label ); ?></h3>
				<ul class="ul-disc">
					<?php foreach ( $created[ $type ] as $id ) : ?>
						<li><a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>"><?php echo esc_html( get_the_title( $id ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endforeach; ?>

			<?php if ( $can ) : ?>
			<h2><?php esc_html_e( 'Remove starter content', 'pixelgrid' ); ?></h2>
			<p><?php esc_html_e( 'Moves the starter pages, notes and menu to the trash, deletes the starter images, removes empty starter categories and restores your homepage setting. Content you created yourself is not touched, but changes you made to starter pages are lost with them.', 'pixelgrid' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="pixelgrid_starter">
				<input type="hidden" name="pixelgrid_task" value="remove">
				<?php wp_nonce_field( 'pixelgrid_starter' ); ?>
				<p><label><input type="checkbox" name="pixelgrid_confirm" value="1"> <?php esc_html_e( 'Yes, remove the starter content', 'pixelgrid' ); ?></label></p>
				<?php submit_button( __( 'Remove starter content', 'pixelgrid' ), 'secondary delete', 'pixelgrid_submit', false ); ?>
			</form>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Shows a one-time, permanently dismissible notice on fresh sites, on the Dashboard and Themes screens only.
 */
function pixelgrid_starter_notice() {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes' ), true ) ) {
		return;
	}
	if ( ! pixelgrid_starter_can() || get_option( 'pixelgrid_starter_done' ) || get_user_meta( get_current_user_id(), 'pixelgrid_starter_dismissed', true ) ) {
		return;
	}
	// Fresh site: WordPress has only its own sample content.
	if ( ! get_option( 'fresh_site' ) && wp_count_posts( 'page' )->publish > 2 ) {
		return;
	}
	$dismiss = wp_nonce_url( admin_url( 'admin-post.php?action=pixelgrid_starter_dismiss' ), 'pixelgrid_starter_dismiss' );
	?>
	<div class="notice notice-info">
		<p><strong><?php esc_html_e( 'Pixelgrid is ready.', 'pixelgrid' ); ?></strong> <?php esc_html_e( 'Start from the pages shown in the theme preview: home, services, about, pricing, contact and sample notes. You choose what gets added.', 'pixelgrid' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=pixelgrid-setup' ) ); ?>"><?php esc_html_e( 'Set up starter content', 'pixelgrid' ); ?></a>
			<a class="button-link" style="margin-inline-start:12px" href="<?php echo esc_url( $dismiss ); ?>"><?php esc_html_e( 'Dismiss this notice', 'pixelgrid' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'pixelgrid_starter_notice' );

/**
 * Remembers that the current user dismissed the notice.
 */
function pixelgrid_starter_dismiss() {
	check_admin_referer( 'pixelgrid_starter_dismiss' );
	update_user_meta( get_current_user_id(), 'pixelgrid_starter_dismissed', 1 );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
	exit;
}
add_action( 'admin_post_pixelgrid_starter_dismiss', 'pixelgrid_starter_dismiss' );
