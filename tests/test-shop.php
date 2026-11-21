<?php
/**
 * Shop starter content tests: products and product categories.
 *
 * Run on a site made with `bash tests/bin/site.sh up --woo` for the full set. Without
 * WooCommerce only the "without WooCommerce" checks run.
 */

// Collects PHP notices and warnings raised by the code under test.
$GLOBALS['pg_shop_notices'] = array();
$pg_watch                   = function ( callable $run ) {
	$GLOBALS['pg_shop_notices'] = array();
	set_error_handler(
		function ( $errno, $errstr, $errfile, $errline ) {
			$GLOBALS['pg_shop_notices'][] = "$errstr ($errfile:$errline)";
			return true;
		},
		E_NOTICE | E_WARNING | E_USER_NOTICE | E_USER_WARNING
	);
	try {
		$result = $run();
	} finally {
		restore_error_handler();
	}
	return $result;
};

add_filter(
	'pixelgrid_variants',
	function ( $variants ) {
		$variants['shopfix'] = array(
			'slug'               => 'shopfix',
			'label'              => 'Shop fixture',
			'product_categories' => array(
				'fix-mugs'   => 'Fixture mugs',
				'fix-prints' => 'Fixture prints',
			),
			'products'           => array(
				array(
					'key'      => 'pixel-mug',
					'name'     => 'Pixel mug',
					'category' => 'fix-mugs',
					'price'    => '18.00',
					'short'    => 'A mug with a pixel.',
					'content'  => '<!-- wp:paragraph --><p>Holds coffee.</p><!-- /wp:paragraph -->',
					'image'    => 'assets/images/agency-01.webp',
				),
				array(
					'key'      => 'grid-print',
					'name'     => 'Grid print',
					'category' => 'fix-prints',
					'price'    => '40.00',
					'sale'     => '32.00',
					'short'    => 'A print of the grid.',
					'content'  => '<!-- wp:paragraph --><p>Hangs on a wall.</p><!-- /wp:paragraph -->',
					'image'    => 'assets/images/agency-02.webp',
				),
			),
		);
		return $variants;
	}
);

$pg_marked_products = function ( $statuses = array( 'publish', 'draft', 'private' ) ) {
	return get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => $statuses,
			'meta_key'       => '_pixelgrid_starter',
			'posts_per_page' => 50,
			'fields'         => 'ids',
		)
	);
};

pg_assert( function_exists( 'pixelgrid_starter_product_categories' ), 'pixelgrid_starter_product_categories exists' );

if ( ! class_exists( 'WC_Product_Simple' ) ) {
	echo "INFO: WooCommerce is not active; running only the without-WooCommerce checks. Use 'site.sh up --woo' for the rest.\n";

	$made = $pg_watch(
		function () {
			return pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
		}
	);
	pg_assert( is_array( $made ), 'without WooCommerce, create with products gives no error' );
	pg_assert_same( 0, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'without WooCommerce, products is 0' );
	pg_assert_same( array(), $pg_marked_products(), 'without WooCommerce, no product is created' );
	pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'without WooCommerce, create raises no notice' );
	pg_assert_same( array(), (array) get_option( 'pixelgrid_starter_product_cats', array() ), 'without WooCommerce, no product category is recorded' );

	// A site where Shop setup ran and WooCommerce was later turned off: a marked product row
	// and a recorded product category are left. Remove trashes the product by post type and
	// keeps the category record for a later run, with no notice.
	$pg_product = wp_insert_post(
		array(
			'post_type'   => 'product',
			'post_title'  => 'Orphan product',
			'post_status' => 'publish',
		)
	);
	update_post_meta( $pg_product, '_pixelgrid_starter', 'shopfix:product-orphan' );
	update_option( 'pixelgrid_starter_product_cats', array( 987654 ), false );
	update_option( 'pixelgrid_starter_variant', 'shopfix', false );

	$removed = $pg_watch( 'pixelgrid_starter_remove' );
	pg_assert_same( 'trash', get_post_status( $pg_product ), 'without WooCommerce, remove trashes the marked product' );
	pg_assert( $removed['removed'] >= 1, 'without WooCommerce, remove counts the product' );
	pg_assert_same( array( 987654 ), get_option( 'pixelgrid_starter_product_cats' ), 'without WooCommerce, the product category record is kept for a later run' );
	pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'without WooCommerce, remove raises no notice' );
	pg_done();
}

echo "INFO: WooCommerce is active; running the full shop checks.\n";

// A product category the user already has, with the same slug as one setup wants.
$pg_existing = wp_insert_term( 'My mugs', 'product_cat', array( 'slug' => 'fix-mugs' ) );
$pg_existing = is_wp_error( $pg_existing ) ? 0 : (int) $pg_existing['term_id'];
pg_assert( $pg_existing > 0, 'existing product category made before setup' );
// A product the user already has.
$pg_own = new WC_Product_Simple();
$pg_own->set_name( 'My own product' );
$pg_own->set_regular_price( '5.00' );
$pg_own->set_status( 'publish' );
$pg_own_id = $pg_own->save();

$made = $pg_watch(
	function () {
		return pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
	}
);
pg_assert( is_array( $made ), 'create with products returns counts' );
pg_assert_same( 2, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'create makes 2 products' );
pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'create raises no notice' );

$pg_mug_id   = pixelgrid_starter_find( 'product', 'shopfix:product-pixel-mug' );
$pg_print_id = pixelgrid_starter_find( 'product', 'shopfix:product-grid-print' );
pg_assert( $pg_mug_id > 0 && $pg_print_id > 0, 'both products carry the shopfix:product-{key} marker' );
$pg_mug   = wc_get_product( $pg_mug_id );
$pg_print = wc_get_product( $pg_print_id );
$pg_cats  = get_term_by( 'slug', 'fix-prints', 'product_cat' );
$pg_new   = $pg_cats ? (int) $pg_cats->term_id : 0;

pg_assert_same( 'Pixel mug', $pg_mug->get_name(), 'mug name' );
pg_assert_same( 'pixel-mug', $pg_mug->get_slug(), 'mug slug is its key' );
pg_assert_same( 'publish', $pg_mug->get_status(), 'mug is published' );
pg_assert_same( '18.00', $pg_mug->get_regular_price(), 'mug price' );
pg_assert_same( '', $pg_mug->get_sale_price(), 'mug has no sale price' );
pg_assert_same( '40.00', $pg_print->get_regular_price(), 'print regular price' );
pg_assert_same( '32.00', $pg_print->get_sale_price(), 'print sale price' );
pg_assert_same( 'A mug with a pixel.', $pg_mug->get_short_description(), 'mug short description' );
pg_assert( false !== strpos( $pg_print->get_description(), 'Hangs on a wall.' ), 'print description holds its block markup' );
pg_assert_same( array( $pg_existing ), array_map( 'intval', $pg_mug->get_category_ids() ), 'mug is in the existing, reused category' );
pg_assert( $pg_new > 0 && $pg_new !== $pg_existing, 'setup created the missing product category' );
pg_assert_same( array( $pg_new ), array_map( 'intval', $pg_print->get_category_ids() ), 'print is in the created category' );
pg_assert_same( 'Fixture prints', $pg_cats ? $pg_cats->name : '', 'created category has its name' );
pg_assert_same( array( $pg_new ), get_option( 'pixelgrid_starter_product_cats' ), 'only the created product category is recorded' );
pg_assert_same( 'My mugs', get_term( $pg_existing, 'product_cat' )->name, 'the existing category is not edited' );

$pg_image = (int) $pg_mug->get_image_id();
pg_assert( $pg_image > 0, 'mug has a featured image' );
pg_assert_same( 'shopfix:image-agency-01', get_post_meta( $pg_image, '_pixelgrid_starter', true ), 'mug image is a starter image' );
pg_assert( (int) $pg_print->get_image_id() > 0 && (int) $pg_print->get_image_id() !== $pg_image, 'print has its own featured image' );
pg_assert_same( 'shopfix', pixelgrid_starter_active_variant(), 'the variant is recorded' );

// The user changes a price, then runs setup again.
$pg_mug->set_regular_price( '21.00' );
$pg_mug->save();
$pg_attachments = count(
	get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 50,
			'fields'         => 'ids',
		)
	)
);
$pg_term_count  = (int) wp_count_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
$made           = pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
pg_assert_same( 0, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'second create makes 0 products' );
pg_assert_same( 2, count( $pg_marked_products() ), 'second create makes no duplicate products' );
pg_assert_same( '21.00', wc_get_product( $pg_mug_id )->get_regular_price(), 'the price the user changed is kept' );
pg_assert_same(
	$pg_attachments,
	count(
		get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 50,
				'fields'         => 'ids',
			)
		)
	),
	'second create makes no duplicate images'
);
pg_assert_same( $pg_term_count, (int) wp_count_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ), 'second create makes no duplicate product categories' );
pg_assert_same( array( $pg_new ), get_option( 'pixelgrid_starter_product_cats' ), 'second create records no duplicates' );

// Posts off: no products.
pixelgrid_starter_remove();
$made = pixelgrid_starter_create( 'shopfix', array( 'pages' => true ) );
pg_assert_same( 0, is_array( $made ) && isset( $made['products'] ) ? $made['products'] : null, 'without the posts option no product is made' );
pg_assert_same( array(), $pg_marked_products(), 'without the posts option no product exists' );
pixelgrid_starter_remove();

// Remove.
$pg_existing = (int) get_term_by( 'slug', 'fix-mugs', 'product_cat' )->term_id;
pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
$pg_mug_id   = pixelgrid_starter_find( 'product', 'shopfix:product-pixel-mug' );
$pg_print_id = pixelgrid_starter_find( 'product', 'shopfix:product-grid-print' );
$pg_new      = (int) get_term_by( 'slug', 'fix-prints', 'product_cat' )->term_id;
$removed     = $pg_watch( 'pixelgrid_starter_remove' );
pg_assert_same( 'trash', get_post_status( $pg_mug_id ), 'remove trashes the mug' );
pg_assert_same( 'trash', get_post_status( $pg_print_id ), 'remove trashes the print' );
pg_assert( $removed['removed'] >= 4, 'remove counts the products and their images' );
pg_assert( null === get_term( $pg_new, 'product_cat' ), 'remove deletes the created product category' );
pg_assert( get_term( $pg_existing, 'product_cat' ) instanceof WP_Term, 'the existing product category survives remove' );
pg_assert_same( false, get_option( 'pixelgrid_starter_product_cats' ), 'remove deletes pixelgrid_starter_product_cats' );
pg_assert_same( 'publish', get_post_status( $pg_own_id ), 'the user\'s own product is untouched' );
pg_assert_same( '', pixelgrid_starter_active_variant(), 'no active variant after remove' );
pg_assert_same( array(), $GLOBALS['pg_shop_notices'], 'remove raises no notice' );

// A created product category that a user's draft product still uses survives remove.
pixelgrid_starter_create( 'shopfix', array( 'posts' => true ) );
$pg_new   = (int) get_term_by( 'slug', 'fix-prints', 'product_cat' )->term_id;
$pg_draft = new WC_Product_Simple();
$pg_draft->set_name( 'My draft product' );
$pg_draft->set_status( 'draft' );
$pg_draft->set_category_ids( array( $pg_new ) );
$pg_draft->save();
pixelgrid_starter_remove();
pg_assert( get_term( $pg_new, 'product_cat' ) instanceof WP_Term, 'a starter product category used by a draft survives remove' );

pg_done();
