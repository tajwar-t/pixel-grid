<?php
/**
 * Setup screen: variant picker, handler and notices.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
 * Readable name for a part or template left in place on removal.
 *
 * @param string $entry "{type}:{slug}", for example wp_template_part:header or wp_template:home.
 * @return string
 */
function pixelgrid_starter_kept_label( string $entry ): string {
	$parts = explode( ':', $entry, 2 );
	$type  = 2 === count( $parts ) ? $parts[0] : '';
	$slug  = 2 === count( $parts ) ? $parts[1] : $entry;
	if ( 'wp_template_part' === $type && 'header' === $slug ) {
		return __( 'Header', 'pixelgrid' );
	}
	if ( 'wp_template_part' === $type && 'footer' === $slug ) {
		return __( 'Footer', 'pixelgrid' );
	}
	if ( 'wp_template' === $type ) {
		$defaults = get_default_block_template_types();
		if ( isset( $defaults[ $slug ]['title'] ) ) {
			return (string) $defaults[ $slug ]['title'];
		}
	}
	return $slug;
}

/**
 * Query args for the redirect after removal. Kept entries travel as "{type}__{slug}" so they
 * survive sanitize_key(); the screen turns them back into "{type}:{slug}".
 *
 * @param array $result Result of pixelgrid_starter_remove().
 * @return array
 */
function pixelgrid_starter_removed_args( array $result ): array {
	$args = array( 'pixelgrid_removed' => (int) $result['removed'] );
	if ( ! empty( $result['kept'] ) ) {
		$kept                   = array_map(
			function ( $entry ) {
				return sanitize_key( str_replace( ':', '__', (string) $entry ) );
			},
			(array) $result['kept']
		);
		$args['pixelgrid_kept'] = implode( ',', $kept );
	}
	// Only when the design wrote the styles and they changed afterwards.
	if ( ! empty( $result['styles_written'] ) && empty( $result['styles_restored'] ) ) {
		$args['pixelgrid_styles'] = 'kept';
		if ( ! empty( $result['revisions'] ) ) {
			$args['pixelgrid_revisions'] = '1';
		}
	}
	return $args;
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
		$variant = isset( $_POST['pixelgrid_variant'] ) ? sanitize_key( wp_unslash( $_POST['pixelgrid_variant'] ) ) : '';
		if ( ! isset( pixelgrid_variants()[ $variant ] ) ) {
			wp_safe_redirect( add_query_arg( array( 'pixelgrid_error' => 'unknown' ), $back ) );
			exit;
		}
		$made = pixelgrid_starter_create(
			$variant,
			array(
				'design'  => pixelgrid_starter_ticked( 'pixelgrid_design' ),
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
		wp_safe_redirect( add_query_arg( pixelgrid_starter_removed_args( pixelgrid_starter_remove() ), $back ) );
		exit;
	}
	wp_safe_redirect( add_query_arg( array( 'pixelgrid_removed' => 'unconfirmed' ), $back ) );
	exit;
}
add_action( 'admin_post_pixelgrid_starter', 'pixelgrid_starter_handle' );

/**
 * The sentences the setup screen shows for one variant: its own content_labels and
 * design_note, or generic sentences built from its pages and posts when it has none.
 *
 * @param array $variant Variant.
 * @return array Keys: pages, posts, reading, design.
 */
function pixelgrid_starter_labels( array $variant ): array {
	$variant = pixelgrid_variant_defaults( $variant );
	$own     = is_array( $variant['content_labels'] ) ? $variant['content_labels'] : array();
	$pages   = (array) $variant['pages'];
	$labels  = array();

	if ( ! empty( $own['pages'] ) ) {
		$labels['pages'] = (string) $own['pages'];
	} else {
		/* translators: %d: number of pages. */
		$labels['pages'] = sprintf( _n( '%d page', '%d pages', count( $pages ), 'pixelgrid' ), count( $pages ) );
	}

	if ( ! empty( $own['posts'] ) ) {
		$labels['posts'] = (string) $own['posts'];
	} else {
		$count = count( (array) $variant['posts'] );
		/* translators: %d: number of sample posts. */
		$labels['posts'] = sprintf( _n( '%d sample post with categories, tags and images', '%d sample posts with categories, tags and images', $count, 'pixelgrid' ), $count );
	}

	$front      = (string) $variant['front'];
	$posts_page = (string) $variant['posts_page'];
	if ( ! empty( $own['reading'] ) ) {
		$labels['reading'] = (string) $own['reading'];
	} elseif ( isset( $pages[ $front ][0] ) && isset( $pages[ $posts_page ][0] ) ) {
		/* translators: 1: title of the homepage, 2: title of the posts page. */
		$labels['reading'] = sprintf( __( 'Use the new %1$s page as the homepage and %2$s as the posts page', 'pixelgrid' ), $pages[ $front ][0], $pages[ $posts_page ][0] );
	} elseif ( isset( $pages[ $front ][0] ) ) {
		/* translators: %s: title of the homepage. */
		$labels['reading'] = sprintf( __( 'Use the new %s page as the homepage', 'pixelgrid' ), $pages[ $front ][0] );
	} else {
		$labels['reading'] = __( 'This starting point does not change the homepage setting', 'pixelgrid' );
	}

	$labels['design'] = '' !== (string) $variant['design_note']
		? (string) $variant['design_note']
		: __( 'This changes the site\'s header, footer and some templates, and its styles when the starting point has its own. Your current ones are saved and put back when you remove the starter content. Applying again only changes what you have not edited since setup, and edits you make after setup are kept on removal.', 'pixelgrid' );

	return $labels;
}

/**
 * Prints one sentence per variant, each marked with its variant so the label switcher can
 * show only the chosen one. Without JavaScript, all show, each after the variant's name
 * (the name is left out when there is only one variant).
 *
 * @param array  $variants Variants keyed by slug.
 * @param string $field    pages, posts, reading or design.
 * @param string $tag      span (inside a checkbox label) or p (a description paragraph).
 */
function pixelgrid_starter_variant_text( array $variants, string $field, string $tag = 'span' ): void {
	$many  = count( $variants ) > 1;
	$first = true;
	foreach ( $variants as $slug => $variant ) {
		$labels = pixelgrid_starter_labels( $variant );
		$text   = isset( $labels[ $field ] ) ? $labels[ $field ] : '';
		if ( 'p' === $tag ) {
			echo '<p class="description" data-pixelgrid-for="' . esc_attr( $slug ) . '">';
		} else {
			echo '<span data-pixelgrid-for="' . esc_attr( $slug ) . '">';
			if ( $many && ! $first ) {
				echo '<br class="pixelgrid-for-name">';
			}
		}
		if ( $many ) {
			/* translators: %s: name of the starting point, for example IT services. */
			echo '<strong class="pixelgrid-for-name">' . esc_html( sprintf( __( 'For %s:', 'pixelgrid' ), $variant['label'] ) ) . '</strong> ';
		}
		echo esc_html( $text );
		echo 'p' === $tag ? '</p>' : '</span>';
		$first = false;
	}
}

/**
 * Loads the small script that shows only the chosen variant's sentences, on the setup page only.
 *
 * @param string $hook_suffix Current admin page.
 */
function pixelgrid_starter_enqueue( $hook_suffix ) {
	if ( 'appearance_page_pixelgrid-setup' !== $hook_suffix ) {
		return;
	}
	wp_register_script( 'pixelgrid-setup', false, array(), wp_get_theme( get_template() )->get( 'Version' ), true );
	wp_enqueue_script( 'pixelgrid-setup' );
	wp_add_inline_script(
		'pixelgrid-setup',
		'( function () {
	var root = document.querySelector( ".pixelgrid-setup" );
	if ( ! root ) {
		return;
	}
	function pick() {
		var radio = root.querySelector( "input[name=\"pixelgrid_variant\"]:checked" );
		var slug = radio ? radio.value : "";
		var i, list = root.querySelectorAll( ".pixelgrid-for-name" );
		for ( i = 0; i < list.length; i++ ) {
			list[ i ].hidden = "" !== slug;
		}
		list = root.querySelectorAll( "[data-pixelgrid-for]" );
		for ( i = 0; i < list.length; i++ ) {
			list[ i ].hidden = "" !== slug && list[ i ].getAttribute( "data-pixelgrid-for" ) !== slug;
		}
	}
	root.addEventListener( "change", function ( event ) {
		if ( event.target && "pixelgrid_variant" === event.target.name ) {
			pick();
		}
	} );
	pick();
}() );'
	);
}
add_action( 'admin_enqueue_scripts', 'pixelgrid_starter_enqueue' );

/**
 * Prints the setup page with core admin markup only.
 */
function pixelgrid_starter_screen() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$created  = pixelgrid_starter_created();
	$total    = count( $created['page'] ) + count( $created['post'] ) + count( $created['wp_navigation'] );
	$can      = pixelgrid_starter_can();
	$variants = pixelgrid_variants();
	$active   = pixelgrid_starter_active_variant();
	$applied  = false !== get_option( 'pixelgrid_starter_design', false );
	?>
	<style>
		.pixelgrid-setup .pixelgrid-cards { display: flex; flex-wrap: wrap; gap: 12px; margin: 0; padding: 0; border: 0; }
		.pixelgrid-setup .pixelgrid-card { display: block; width: 260px; padding: 12px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; }
		.pixelgrid-setup .pixelgrid-card.is-disabled { opacity: .65; }
		.pixelgrid-setup .pixelgrid-card img { display: block; max-width: 100%; height: auto; margin-bottom: 8px; }
		.pixelgrid-setup .pixelgrid-card .description { display: block; margin: 4px 0 0; }
	</style>
	<div class="wrap pixelgrid-setup">
		<h1><?php esc_html_e( 'Pixelgrid setup', 'pixelgrid' ); ?></h1>
		<?php
		// Status messages after a redirect; they only pick which message to show.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['pixelgrid_created'] ) ) {
			$counts = array_map( 'absint', explode( '-', sanitize_text_field( wp_unslash( $_GET['pixelgrid_created'] ) ) ) + array( 0, 0, 0 ) );
			echo '<div class="notice notice-success"><p>';
			printf(
				/* translators: 1: number of pages, 2: number of posts, 3: number of menus. */
				esc_html__( 'Starter content added: %1$d pages, %2$d posts and %3$d menu. Anything that already existed was left as it was.', 'pixelgrid' ),
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
				echo '</p>';
				if ( isset( $_GET['pixelgrid_kept'] ) ) {
					$kept = array_filter( array_map( 'sanitize_key', explode( ',', sanitize_text_field( wp_unslash( $_GET['pixelgrid_kept'] ) ) ) ) );
					// Back from "{type}__{slug}" to "{type}:{slug}".
					$kept = array_map(
						function ( $entry ) {
							return implode( ':', explode( '__', $entry, 2 ) );
						},
						$kept
					);
					if ( $kept ) {
						echo '<p>';
						printf(
							/* translators: %s: list of names, for example Header, Footer. */
							esc_html__( 'These were left as they are because they changed after setup: %s.', 'pixelgrid' ),
							esc_html( implode( ', ', array_map( 'pixelgrid_starter_kept_label', $kept ) ) )
						);
						echo '</p>';
					}
				}
				if ( isset( $_GET['pixelgrid_styles'] ) && 'kept' === sanitize_key( wp_unslash( $_GET['pixelgrid_styles'] ) ) ) {
					echo '<p>' . esc_html__( 'Your style changes were kept because they changed after setup.', 'pixelgrid' );
					// The hint only helps when the styles have revisions to go back to.
					if ( isset( $_GET['pixelgrid_revisions'] ) && '1' === sanitize_key( wp_unslash( $_GET['pixelgrid_revisions'] ) ) ) {
						echo ' ' . esc_html__( 'The styles you had before setup can be recovered in Styles, Revisions.', 'pixelgrid' );
					}
					echo '</p>';
				}
				echo '</div>';
			}
		}
		if ( isset( $_GET['pixelgrid_error'] ) ) {
			$error    = sanitize_key( wp_unslash( $_GET['pixelgrid_error'] ) );
			$messages = array(
				'busy'                    => __( 'Nothing was added. Remove the current starter content to switch.', 'pixelgrid' ),
				'unavailable'             => __( 'Nothing was added. This variant needs a plugin that is not active.', 'pixelgrid' ),
				'unknown'                 => __( 'Nothing was added. That variant does not exist.', 'pixelgrid' ),
				'pixelgrid_style_missing' => __( 'Nothing was added. This starting point\'s style file is missing or damaged.', 'pixelgrid' ),
			);
			if ( isset( $messages[ $error ] ) ) {
				echo '<div class="notice notice-error"><p>' . esc_html( $messages[ $error ] ) . '</p></div>';
			}
		}
		// phpcs:enable
		?>

		<p><?php esc_html_e( 'Pixelgrid can add the pages shown in its preview, so you can start by editing instead of building from nothing. Choose a starting point and what to add. Nothing is downloaded: everything is built from the theme\'s own patterns and images.', 'pixelgrid' ); ?></p>

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
					<th scope="row"><?php esc_html_e( 'Starting point', 'pixelgrid' ); ?></th>
					<td>
						<fieldset class="pixelgrid-cards">
							<legend class="screen-reader-text"><?php esc_html_e( 'Choose a starting point', 'pixelgrid' ); ?></legend>
							<?php
							$first = true;
							foreach ( $variants as $slug => $variant ) :
								$ready    = pixelgrid_variant_available( $variant );
								$busy     = '' !== $active && $slug !== $active;
								$disabled = ! $ready || $busy;
								$checked  = '' !== $active ? $slug === $active : ( $first && $ready );
								if ( $ready ) {
									$first = false;
								}
								?>
								<label class="pixelgrid-card<?php echo $disabled ? ' is-disabled' : ''; ?>">
									<?php if ( ! empty( $variant['preview'] ) ) : ?>
										<img src="<?php echo esc_url( get_parent_theme_file_uri( $variant['preview'] ) ); ?>" alt="">
									<?php endif; ?>
									<input type="radio" name="pixelgrid_variant" value="<?php echo esc_attr( $slug ); ?>"<?php checked( $checked ); ?><?php disabled( $disabled ); ?>>
									<strong><?php echo esc_html( $variant['label'] ); ?></strong>
									<span class="description"><?php echo esc_html( $variant['description'] ); ?></span>
									<?php if ( ! $ready ) : ?>
										<span class="description"><a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>"><?php esc_html_e( 'Install the required plugin', 'pixelgrid' ); ?></a></span>
									<?php elseif ( $busy ) : ?>
										<span class="description"><?php esc_html_e( 'Remove the current starter content to switch', 'pixelgrid' ); ?></span>
									<?php endif; ?>
								</label>
							<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Design', 'pixelgrid' ); ?></th>
					<td>
						<?php if ( $applied ) : ?>
							<label><input type="checkbox" name="pixelgrid_design" value="1"> <?php esc_html_e( 'Apply this variant\'s design again (already applied)', 'pixelgrid' ); ?></label>
							<p class="description"><?php esc_html_e( 'The design is already applied. Applying it again only changes the styles, header, footer and templates you have not edited since setup; your edits are left as they are.', 'pixelgrid' ); ?></p>
						<?php else : ?>
							<label><input type="checkbox" name="pixelgrid_design" value="1" checked> <?php esc_html_e( 'Apply this variant\'s design', 'pixelgrid' ); ?></label>
							<?php pixelgrid_starter_variant_text( $variants, 'design', 'p' ); ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Content', 'pixelgrid' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><?php esc_html_e( 'Content to add', 'pixelgrid' ); ?></legend>
							<label><input type="checkbox" name="pixelgrid_pages" value="1" checked> <?php pixelgrid_starter_variant_text( $variants, 'pages' ); ?></label><br>
							<label><input type="checkbox" name="pixelgrid_posts" value="1" checked> <?php pixelgrid_starter_variant_text( $variants, 'posts' ); ?></label><br>
							<label><input type="checkbox" name="pixelgrid_menu" value="1" checked> <?php esc_html_e( 'A menu linking the new pages', 'pixelgrid' ); ?></label>
							<p class="description"><?php esc_html_e( 'The header and footer show the newest menu, so this replaces the menu they show now. You can switch back in the Navigation block.', 'pixelgrid' ); ?></p>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Homepage', 'pixelgrid' ); ?></th>
					<td>
						<label><input type="checkbox" name="pixelgrid_reading" value="1" checked> <?php pixelgrid_starter_variant_text( $variants, 'reading' ); ?></label>
						<p class="description"><?php esc_html_e( 'This changes Settings, Reading. Your current choice is saved and put back if you remove the starter content.', 'pixelgrid' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Add starter content', 'pixelgrid' ), 'primary', 'pixelgrid_submit', false ); ?>
		</form>
		<?php endif; ?>

		<?php if ( $total || $created['attachment'] || '' !== $active ) : ?>
			<h2><?php esc_html_e( 'Starter content on this site', 'pixelgrid' ); ?></h2>
			<?php if ( '' !== $active ) : ?>
				<p>
					<?php
					printf(
						/* translators: %s: name of the starting point, for example IT services. */
						esc_html__( 'Starting point in use: %s.', 'pixelgrid' ),
						esc_html( isset( $variants[ $active ] ) ? $variants[ $active ]['label'] : $active )
					);
					if ( $applied ) {
						echo ' ' . esc_html__( 'Its design is applied.', 'pixelgrid' );
					}
					?>
				</p>
			<?php endif; ?>
			<?php
			$groups = array(
				'page' => __( 'Pages', 'pixelgrid' ),
				'post' => __( 'Posts', 'pixelgrid' ),
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
			<p><?php esc_html_e( 'Moves the starter pages, posts and menu to the trash, deletes the starter images, removes empty starter categories, restores your homepage setting and puts back the styles, header, footer and templates that were replaced, unless you changed them after setup. Content you created yourself is not touched, but changes you made to starter pages are lost with them.', 'pixelgrid' ); ?></p>
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
	$count   = count( pixelgrid_variants() );
	?>
	<div class="notice notice-info">
		<p><strong><?php esc_html_e( 'Pixelgrid is ready.', 'pixelgrid' ); ?></strong>
		<?php
		printf(
			/* translators: %d: number of starting points. */
			esc_html( _n( 'Start from the pages shown in the theme preview. %d starting point is available. You choose what gets added.', 'Start from the pages shown in the theme previews. %d starting points are available. You choose what gets added.', $count, 'pixelgrid' ) ),
			(int) $count
		);
		?>
		</p>
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
