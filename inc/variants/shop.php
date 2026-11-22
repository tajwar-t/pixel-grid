<?php
/**
 * Shop variant: Common Goods, a small shop for everyday things, with twelve products in four
 * categories, a journal and pages for the shop story and visits. Needs WooCommerce.
 *
 * Returns the variant definition. Block markup for the products and journal posts is built
 * from translatable strings. Prices are plain numbers: the currency is a WooCommerce setting.
 * The shop, its street and everyone in it are fictional.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pixelgrid_p    = function ( $text ) {
	return "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
};
$pixelgrid_h    = function ( $text, $level = 2 ) {
	$attrs = 2 === $level ? '' : ' {"level":' . (int) $level . '}';
	return '<!-- wp:heading' . $attrs . " -->\n<h" . (int) $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . (int) $level . ">\n<!-- /wp:heading -->\n\n";
};
$pixelgrid_list = function ( array $items ) {
	$out = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>" . esc_html( $item ) . "</li>\n<!-- /wp:list-item -->";
	}
	return $out . "</ul>\n<!-- /wp:list -->\n\n";
};

return array(
	'slug'               => 'shop',
	'label'              => __( 'Shop', 'pixelgrid' ),
	'description'        => __( 'A small shop for everyday things, with twelve products, featured items, category tiles, a journal and WooCommerce cart and checkout.', 'pixelgrid' ),
	'requires'           => array( 'woocommerce' ),
	'content_labels'     => array(
		'pages'   => __( 'Pages: Home, About, Contact and Journal, plus a Shop link to your WooCommerce shop page', 'pixelgrid' ),
		'posts'   => __( 'Twelve sample products in Kitchen, Home, Outdoor and Tech, four on sale and four featured, and three journal posts with images', 'pixelgrid' ),
		'reading' => __( 'Use the new Home page as the homepage and Journal as the posts page', 'pixelgrid' ),
	),
	'design_note'        => __( 'This replaces your Styles with the Shop colours and fonts, and changes the site\'s header, footer and the template for the posts page. The WooCommerce pages keep the theme\'s own grid templates. Your current ones are saved and put back when you remove the starter content. Applying again only changes what you have not edited since setup, and edits you make after setup are kept on removal.', 'pixelgrid' ),
	'style'              => 'styles/shop.json',
	'parts'              => array(
		'header' => 'pixelgrid/shop-header',
		'footer' => 'pixelgrid/shop-footer',
	),
	'templates'          => array(
		'home' => 'pixelgrid/shop-template-home',
	),
	'pages'              => array(
		'home'    => array( __( 'Home', 'pixelgrid' ), 'pixelgrid/shop-page-home', 'page-sections' ),
		'about'   => array( __( 'About', 'pixelgrid' ), 'pixelgrid/shop-page-about', 'page-sections' ),
		'contact' => array( __( 'Contact', 'pixelgrid' ), 'pixelgrid/shop-page-contact', 'page-sections' ),
		'journal' => array( __( 'Journal', 'pixelgrid' ), '', '' ),
	),
	'front'              => 'home',
	'posts_page'         => 'journal',
	'menu'               => array( 'woo:shop', 'about', 'journal', 'contact' ),
	'categories'         => array(
		'news'   => __( 'News', 'pixelgrid' ),
		'guides' => __( 'Guides', 'pixelgrid' ),
	),
	'posts'              => array(
		array(
			'key'      => 'a-mug-that-lasts',
			'title'    => __( 'How to pick a mug that lasts ten years', 'pixelgrid' ),
			'excerpt'  => __( 'Weight, handle and glaze: three things to check before you buy another mug.', 'pixelgrid' ),
			'category' => 'guides',
			'tags'     => array( __( 'Kitchen', 'pixelgrid' ), __( 'Buying guide', 'pixelgrid' ) ),
			'image'    => 'assets/images/shop-13.webp',
			'content'  => $pixelgrid_p( __( 'Most mugs break because of the handle, not the cup. A handle that is glued on, or pinched thin where it meets the body, is where cracks start. Ours is pulled from the same lump of clay as the cup and is as thick as your thumb.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Pick it up empty', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'A good mug feels a little heavier than you expect, around 330 grams for a 350 ml cup. Thin walls cool your tea fast and chip at the rim the first time they meet a tap.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Look inside', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The glaze should be smooth all the way down to the floor, with no pinholes. Pinholes hold tea and coffee and turn into brown specks that no sponge can reach.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Run a fingernail round the foot. Unglazed clay there should be sanded flat, otherwise the mug scratches your table and sits with a slight rock.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Last, check that it goes in the dishwasher. We fire our mugs at 1,240 degrees so the glaze is hard enough to survive about 3,000 washes. That is roughly ten years of a daily cup.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'a-fern-that-forgives',
			'title'    => __( 'A fern that forgives you', 'pixelgrid' ),
			'excerpt'  => __( 'Plain advice for keeping a potted fern alive, written by someone who has killed a few.', 'pixelgrid' ),
			'category' => 'guides',
			'tags'     => array( __( 'Plants', 'pixelgrid' ), __( 'Care', 'pixelgrid' ) ),
			'image'    => 'assets/images/shop-14.webp',
			'content'  => $pixelgrid_p( __( 'Ferns have a reputation for being fussy. In our experience they only fuss about two things: direct afternoon sun and a pot that dries out completely.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Light', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Put the pot where you would be happy to read a book without a lamp, but not on a sunny sill. A north-facing window, a porch or the shade of a wall is ideal.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Water', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Water when the top two centimetres of soil feel dry. Push a finger in to check, since the surface can look dry while the roots are still wet. Most pots need a drink about once a week in summer and once a fortnight in winter.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Terracotta breathes, which helps if you tend to over-water. Stand the pot on a saucer and tip out any water left after an hour, so the roots never sit in a puddle.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Brown tips mean dry air. Move the fern away from the radiator and flick a little water over the fronds. Cut off any fully brown fronds at the base and new ones will unroll in spring.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'why-we-draw-in-pixels',
			'title'    => __( 'Why we draw every product in pixels', 'pixelgrid' ),
			'excerpt'  => __( 'Sixteen squares by sixteen squares: the small rule that keeps our shop honest.', 'pixelgrid' ),
			'category' => 'news',
			'tags'     => array( __( 'Behind the scenes', 'pixelgrid' ) ),
			'image'    => 'assets/images/shop-15.webp',
			'content'  => $pixelgrid_p( __( 'Every product picture in this shop is a grid of 16 by 16 squares. That is 256 squares in total, in a handful of colours: paper, green, tangerine, cream and a leaf green for the plants.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'We started because photographing twelve products properly takes a studio and a lot of editing. A pixel drawing takes an afternoon and cannot hide anything. If you can tell it is a teapot from 256 squares, the shape is doing its job.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'A rule that changes what we stock', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The limit also changes how we choose what to sell. A product with a clear outline, like a watering can with its long spout, draws well. A tangle of cables does not, so we do not stock tangles of cables.', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'The drawing is a promise about shape and colour. The description under it is a promise about size, weight and materials. If something arrives and does not match either, send it back within 30 days and we pay the postage.', 'pixelgrid' ) ),
		),
	),
	'product_categories' => array(
		'kitchen' => _x( 'Kitchen', 'Product category', 'pixelgrid' ),
		'home'    => _x( 'Home', 'Product category', 'pixelgrid' ),
		'outdoor' => _x( 'Outdoor', 'Product category', 'pixelgrid' ),
		'tech'    => _x( 'Tech', 'Product category', 'pixelgrid' ),
	),
	'products'           => array(
		array(
			'key'      => 'stoneware-mug',
			'name'     => __( 'Everyday stoneware mug', 'pixelgrid' ),
			'category' => 'kitchen',
			'price'    => '18',
			'short'    => __( 'A 350 ml mug with a thick handle and a glaze that hides coffee rings. Dishwasher and microwave safe.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-01.webp',
			'content'  => $pixelgrid_p( __( 'Thrown in stoneware and fired hot, so the glaze survives years of daily washing. The handle is pulled from the same clay as the cup and fits four fingers.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( 'Holds 350 ml', 'pixelgrid' ), __( 'Weighs about 330 g', 'pixelgrid' ), __( 'Dishwasher and microwave safe', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'round-teapot',
			'name'     => __( 'Round teapot', 'pixelgrid' ),
			'category' => 'kitchen',
			'price'    => '38',
			'sale'     => '29',
			'featured' => true,
			'short'    => __( 'Holds four cups. The lid stays put when you pour and the spout does not drip.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-02.webp',
			'content'  => $pixelgrid_p( __( 'A round stoneware pot with a removable steel strainer for loose tea. A small tab under the lid holds it in place, so you can pour with one hand.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( 'Holds 1 litre, about four cups', 'pixelgrid' ), __( 'Removable steel strainer', 'pixelgrid' ), __( 'Dishwasher safe, strainer included', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'serving-bowl',
			'name'     => __( 'Deep serving bowl', 'pixelgrid' ),
			'category' => 'kitchen',
			'price'    => '26',
			'short'    => __( 'Big enough for a family salad, steady enough to mix in. Glazed inside and out.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-03.webp',
			'content'  => $pixelgrid_p( __( 'A wide, deep bowl with a heavy foot, so it does not slide while you toss a salad or fold a batter. It goes from the oven to the table.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( '24 cm across, 11 cm deep', 'pixelgrid' ), __( 'Oven safe up to 220 degrees', 'pixelgrid' ), __( 'Dishwasher safe', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'desk-lamp',
			'name'     => __( 'Desk lamp', 'pixelgrid' ),
			'category' => 'home',
			'price'    => '64',
			'sale'     => '52',
			'featured' => true,
			'short'    => __( 'A warm, low light on a bendy neck. Takes a standard bulb, braided cable included.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-04.webp',
			'content'  => $pixelgrid_p( __( 'A steel shade on a flexible neck that stays where you bend it. The weighted base keeps it steady on a desk or a bedside table.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( 'Takes a standard E27 bulb, up to 10 W LED', 'pixelgrid' ), __( 'Braided cable, 1.8 m, with an inline switch', 'pixelgrid' ), __( 'Stands 42 cm tall', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'wall-clock',
			'name'     => __( 'Wall clock', 'pixelgrid' ),
			'category' => 'home',
			'price'    => '34',
			'short'    => __( 'A silent, sweep-hand clock with big numerals you can read from the sofa. One AA battery.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-05.webp',
			'content'  => $pixelgrid_p( __( 'The second hand sweeps instead of ticking, so the clock is quiet enough for a bedroom. A single hook on the back holds it flat against the wall.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( '30 cm across', 'pixelgrid' ), __( 'Silent sweep movement', 'pixelgrid' ), __( 'Runs on one AA battery, not included', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'ribbed-vase',
			'name'     => __( 'Ribbed vase', 'pixelgrid' ),
			'category' => 'home',
			'price'    => '29',
			'short'    => __( 'A heavy glazed vase for a few stems or none at all. Waterproof, with a flat felt base.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-06.webp',
			'content'  => $pixelgrid_p( __( 'Pressed ribs run from the neck to the foot and catch the light. The narrow neck holds three or four stems upright without a frog.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( '22 cm tall', 'pixelgrid' ), __( 'Glazed inside, so it holds water', 'pixelgrid' ), __( 'Felt base that will not scratch', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'potted-fern',
			'name'     => __( 'Potted fern', 'pixelgrid' ),
			'category' => 'outdoor',
			'price'    => '24',
			'featured' => true,
			'short'    => __( 'A hardy fern in a terracotta pot. It likes shade, a weekly drink and being left alone.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-07.webp',
			'content'  => $pixelgrid_p( __( 'A Boston fern grown by a nursery an hour from the shop, potted in plain terracotta with a drainage hole. It is happy on a shady porch or by a north window.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( 'About 40 cm tall with the pot', 'pixelgrid' ), __( '15 cm terracotta pot with a saucer', 'pixelgrid' ), __( 'Care card in the box', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'watering-can',
			'name'     => __( 'Watering can', 'pixelgrid' ),
			'category' => 'outdoor',
			'price'    => '32',
			'sale'     => '24',
			'short'    => __( 'A two-litre steel can with a long spout that reaches the back of the border.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-08.webp',
			'content'  => $pixelgrid_p( __( 'Powder-coated steel with a top handle for carrying and a back handle for pouring. The rose comes off for a steady stream into pots.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( 'Holds 2 litres', 'pixelgrid' ), __( 'Removable brass rose', 'pixelgrid' ), __( 'Rust-resistant coating', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'day-backpack',
			'name'     => __( 'Day backpack', 'pixelgrid' ),
			'category' => 'outdoor',
			'price'    => '72',
			'short'    => __( 'Twenty litres of water-resistant canvas with a laptop sleeve and two bottle pockets.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-09.webp',
			'content'  => $pixelgrid_p( __( 'Waxed canvas with a roll top that keeps the rain out, and padded straps that do not dig in on a long walk. The sleeve fits most 15 inch laptops.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( '20 litres', 'pixelgrid' ), __( 'Padded laptop sleeve', 'pixelgrid' ), __( 'Two side pockets for bottles', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'over-ear-headphones',
			'name'     => __( 'Over-ear headphones', 'pixelgrid' ),
			'category' => 'tech',
			'price'    => '89',
			'sale'     => '69',
			'featured' => true,
			'short'    => __( 'Wired headphones with soft cushions and a flat-fold hinge. A cable and travel pouch are in the box.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-10.webp',
			'content'  => $pixelgrid_p( __( 'No battery and no pairing: plug them in and they play. The ear cushions and the cable both come off, so you can replace them instead of the headphones.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( 'Detachable 1.5 m cable with a 3.5 mm plug', 'pixelgrid' ), __( 'Replaceable ear cushions', 'pixelgrid' ), __( 'Folds flat into the pouch', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'simple-watch',
			'name'     => __( 'Simple watch', 'pixelgrid' ),
			'category' => 'tech',
			'price'    => '79',
			'short'    => __( 'A plain dial, a week-long battery and a strap you can swap without tools.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-11.webp',
			'content'  => $pixelgrid_p( __( 'Hours, minutes and steps, and nothing that buzzes. It charges in an hour on the included cable and lasts about a week on one charge.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( '38 mm case', 'pixelgrid' ), __( 'About seven days per charge', 'pixelgrid' ), __( 'Quick-release 20 mm strap', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'pocket-radio',
			'name'     => __( 'Pocket radio', 'pixelgrid' ),
			'category' => 'tech',
			'price'    => '49',
			'short'    => __( 'FM and DAB radio with a proper speaker, a telescopic aerial and a headphone socket.', 'pixelgrid' ),
			'image'    => 'assets/images/shop-12.webp',
			'content'  => $pixelgrid_p( __( 'Small enough for a coat pocket, loud enough for a kitchen. Five preset buttons on top remember your stations.', 'pixelgrid' ) )
				. $pixelgrid_list( array( __( 'FM and DAB', 'pixelgrid' ), __( 'Rechargeable battery, about 20 hours of listening', 'pixelgrid' ), __( 'Headphone socket and telescopic aerial', 'pixelgrid' ) ) ),
		),
	),
	'preview'            => 'assets/images/variants/shop.webp',
);
