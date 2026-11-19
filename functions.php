<?php
/**
 * Pixelgrid functions and definitions.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pixelgrid_setup' ) ) :
	/**
	 * Sets up theme defaults.
	 */
	function pixelgrid_setup() {
		add_editor_style( get_stylesheet_uri() );
	}
endif;
add_action( 'after_setup_theme', 'pixelgrid_setup' );

if ( ! function_exists( 'pixelgrid_enqueue_assets' ) ) :
	/**
	 * Enqueues the stylesheet and the grid script on the front end.
	 */
	function pixelgrid_enqueue_assets() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style( 'pixelgrid-style', get_stylesheet_uri(), array(), $version );

		wp_enqueue_script(
			'pixelgrid-grid',
			get_parent_theme_file_uri( 'assets/js/pixelgrid.js' ),
			array(),
			$version,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'pixelgrid_enqueue_assets' );

if ( ! function_exists( 'pixelgrid_register_block_styles' ) ) :
	/**
	 * Registers block styles. The CSS for every style is in style.css, which also loads in the editor.
	 */
	function pixelgrid_register_block_styles() {
		$tones = array(
			'tone-paper' => __( 'Light section', 'pixelgrid' ),
			'tone-blue'  => __( 'Bold section', 'pixelgrid' ),
			'tone-sun'   => __( 'Bright section', 'pixelgrid' ),
			'tone-night' => __( 'Dark section', 'pixelgrid' ),
			'tone-fog'   => __( 'Soft section', 'pixelgrid' ),
			'tone-red'   => __( 'Alert section', 'pixelgrid' ),
		);
		foreach ( $tones as $name => $label ) {
			register_block_style(
				'core/group',
				array(
					'name'  => $name,
					'label' => $label,
				)
			);
		}

		register_block_style(
			'core/group',
			array(
				'name'  => 'plan',
				'label' => __( 'Price plan', 'pixelgrid' ),
			)
		);
		register_block_style(
			'core/group',
			array(
				'name'  => 'plan-featured',
				'label' => __( 'Price plan, featured', 'pixelgrid' ),
			)
		);

		$mosaics = array(
			'mosaic-screen' => __( 'Pixel screen', 'pixelgrid' ),
			'mosaic-cloud'  => __( 'Pixel cloud', 'pixelgrid' ),
			'mosaic-lock'   => __( 'Pixel padlock', 'pixelgrid' ),
			'mosaic-chat'   => __( 'Pixel speech bubble', 'pixelgrid' ),
			'mosaic-chart'  => __( 'Pixel chart', 'pixelgrid' ),
			'mosaic-people' => __( 'Pixel people', 'pixelgrid' ),
			'mosaic-mail'   => __( 'Pixel envelope', 'pixelgrid' ),
			'mosaic-note'   => __( 'Pixel note', 'pixelgrid' ),
			'mosaic-lost'   => __( 'Pixel 404', 'pixelgrid' ),
			'mosaic-cycle'  => __( 'Pixel, changing', 'pixelgrid' ),
		);
		foreach ( $mosaics as $name => $label ) {
			register_block_style(
				'core/image',
				array(
					'name'  => $name,
					'label' => $label,
				)
			);
		}
		register_block_style(
			'core/image',
			array(
				'name'  => 'notch',
				'label' => __( 'Pixel notch', 'pixelgrid' ),
			)
		);

		register_block_style(
			'core/paragraph',
			array(
				'name'  => 'lead',
				'label' => __( 'Lead', 'pixelgrid' ),
			)
		);
		register_block_style(
			'core/paragraph',
			array(
				'name'  => 'stat',
				'label' => __( 'Big number', 'pixelgrid' ),
			)
		);

		register_block_style(
			'core/list',
			array(
				'name'  => 'pixel',
				'label' => __( 'Pixel bullets', 'pixelgrid' ),
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'  => 'wordmarks',
				'label' => __( 'Wordmarks row', 'pixelgrid' ),
			)
		);

		register_block_style(
			'core/post-terms',
			array(
				'name'  => 'chips',
				'label' => __( 'Chips', 'pixelgrid' ),
			)
		);
		register_block_style(
			'core/categories',
			array(
				'name'  => 'chips',
				'label' => __( 'Chips', 'pixelgrid' ),
			)
		);
	}
endif;
add_action( 'init', 'pixelgrid_register_block_styles' );

if ( ! function_exists( 'pixelgrid_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 */
	function pixelgrid_pattern_categories() {
		register_block_pattern_category(
			'pixelgrid-sections',
			array(
				'label'       => __( 'Pixelgrid sections', 'pixelgrid' ),
				'description' => __( 'Full-width colour sections built on the pixel grid.', 'pixelgrid' ),
			)
		);
		register_block_pattern_category(
			'pixelgrid-pages',
			array(
				'label'       => __( 'Pixelgrid pages', 'pixelgrid' ),
				'description' => __( 'Complete page layouts.', 'pixelgrid' ),
			)
		);
		register_block_pattern_category(
			'pixelgrid-agency',
			array(
				'label'       => __( 'Pixelgrid Agency', 'pixelgrid' ),
				'description' => __( 'Sections for a design studio: work, capabilities, clients and process.', 'pixelgrid' ),
			)
		);
	}
endif;
add_action( 'init', 'pixelgrid_pattern_categories' );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require get_parent_theme_file_path( 'inc/variants.php' );
	require get_parent_theme_file_path( 'inc/starter-design.php' );
	require get_parent_theme_file_path( 'inc/starter-content.php' );
}
if ( is_admin() ) {
	require get_parent_theme_file_path( 'inc/starter-screen.php' );
}
