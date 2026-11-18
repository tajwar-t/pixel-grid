<?php
/**
 * IT services variant: pages, categories and sample notes.
 *
 * Returns the variant definition. Block markup for the notes is built from
 * translatable strings.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pixelgrid_p = function ( $text ) {
	return "<!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
};
$pixelgrid_h = function ( $text, $level = 2 ) {
	$attrs = 2 === $level ? '' : ' {"level":' . (int) $level . '}';
	return '<!-- wp:heading' . $attrs . " -->\n<h" . (int) $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . (int) $level . ">\n<!-- /wp:heading -->\n\n";
};
$pixelgrid_ul = function ( $items ) {
	$out = "<!-- wp:list -->\n<ul class=\"wp-block-list\">";
	foreach ( $items as $item ) {
		$out .= "<!-- wp:list-item -->\n<li>" . esc_html( $item ) . "</li>\n<!-- /wp:list-item -->";
	}
	return $out . "</ul>\n<!-- /wp:list -->\n\n";
};
$pixelgrid_q = function ( $text, $cite ) {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>" . esc_html( $text ) . "</p>\n<!-- /wp:paragraph --><cite>" . esc_html( $cite ) . "</cite></blockquote>\n<!-- /wp:quote -->\n\n";
};

return array(
	'slug'        => 'it',
	'label'       => __( 'IT services', 'pixelgrid' ),
	'description' => __( 'A managed IT services business with services, pricing and a notes blog.', 'pixelgrid' ),
	'style'       => '',
	'parts'       => array(),
	'templates'   => array(),
	'pages'       => array(
		'home'     => array( __( 'Home', 'pixelgrid' ), 'pixelgrid/page-home', 'page-sections' ),
		'services' => array( __( 'Services', 'pixelgrid' ), 'pixelgrid/page-services', 'page-sections' ),
		'about'    => array( __( 'About', 'pixelgrid' ), 'pixelgrid/page-about', 'page-sections' ),
		'pricing'  => array( __( 'Pricing', 'pixelgrid' ), 'pixelgrid/page-pricing', 'page-sections' ),
		'contact'  => array( __( 'Contact', 'pixelgrid' ), 'pixelgrid/page-contact', 'page-sections' ),
		'notes'    => array( __( 'Notes', 'pixelgrid' ), '', '' ),
	),
	'front'       => 'home',
	'posts_page'  => 'notes',
	'menu'        => array( 'services', 'about', 'pricing', 'notes', 'contact' ),
	'categories'  => array(
		'security' => __( 'Security', 'pixelgrid' ),
		'cloud'    => __( 'Cloud', 'pixelgrid' ),
		'helpdesk' => __( 'Helpdesk', 'pixelgrid' ),
		'backups'  => __( 'Backups', 'pixelgrid' ),
	),
	'posts'       => array(
		array(
			'key'      => 'password-clean-up',
			'title'    => __( 'The five-minute password clean-up every office should do', 'pixelgrid' ),
			'excerpt'  => __( 'What to change today, and what can wait until next quarter.', 'pixelgrid' ),
			'category' => 'security',
			'tags'     => array( __( 'Passwords', 'pixelgrid' ), __( 'Checklists', 'pixelgrid' ) ),
			'image'    => 1,
			'content'  => $pixelgrid_p( __( 'Most break-ins we clean up after did not involve a clever hacker. They involved one password that was used in two places. Here is the clean-up we run for every new client, in the order we run it.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Start with the shared ones', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Every office has them: the Wi-Fi password on the whiteboard, the login for the courier site that six people know. List them first. They are the ones that never get changed when someone leaves.', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Change any password that a former employee knew', 'pixelgrid' ), __( 'Move shared logins into a password manager', 'pixelgrid' ), __( 'Take the Wi-Fi password off the wall', 'pixelgrid' ) ) )
				. $pixelgrid_q( __( 'If more than one person knows it, it is not a password. It is a rumour.', 'pixelgrid' ), __( 'Marta Figueira, head of security', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'Turn on the second step', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Sign-in protection, where a code on your phone confirms it is you, stops most stolen passwords from being useful. Turn it on for email first, then banking, then everything else.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'What can wait', 'pixelgrid' ), 3 )
				. $pixelgrid_p( __( 'Forcing everyone to change passwords every 90 days does more harm than good. People pick weaker ones. Leave that rule off unless an auditor insists.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'cloud-bill',
			'title'    => __( 'Why your cloud bill went up, and three switches to flip', 'pixelgrid' ),
			'excerpt'  => __( 'Most overspend hides in the same three places.', 'pixelgrid' ),
			'category' => 'cloud',
			'tags'     => array( __( 'Costs', 'pixelgrid' ) ),
			'image'    => 3,
			'content'  => $pixelgrid_p( __( 'Cloud bills creep. Nobody decides to spend more; servers just get left running and storage just keeps growing.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'The three usual suspects', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Test servers nobody switched off', 'pixelgrid' ), __( 'Backups kept forever instead of for 90 days', 'pixelgrid' ), __( 'Machines sized for a launch that ended two years ago', 'pixelgrid' ) ) )
				. $pixelgrid_p( __( 'Check those three and most teams save between a fifth and a third of their bill in an afternoon.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'new-starter',
			'title'    => __( 'Setting up a new starter in one day, start to finish', 'pixelgrid' ),
			'excerpt'  => __( 'The checklist we use, free to copy.', 'pixelgrid' ),
			'category' => 'helpdesk',
			'tags'     => array( __( 'Checklists', 'pixelgrid' ) ),
			'image'    => 2,
			'content'  => $pixelgrid_p( __( 'A new starter should spend their first morning meeting people, not waiting for a laptop. Here is how we get there.', 'pixelgrid' ) )
				. $pixelgrid_h( __( 'The week before', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Order the laptop and phone', 'pixelgrid' ), __( 'Create the accounts, switched off until day one', 'pixelgrid' ), __( 'Add them to the right groups and shared drives', 'pixelgrid' ) ) )
				. $pixelgrid_h( __( 'Day one', 'pixelgrid' ) )
				. $pixelgrid_p( __( 'Everything is switched on at 8 in the morning. A five-minute call covers sign-in protection and where to ask for help.', 'pixelgrid' ) ),
		),
		array(
			'key'      => 'restore-test',
			'title'    => __( 'A backup you have never restored is a guess', 'pixelgrid' ),
			'excerpt'  => __( 'How to test yours in under an hour.', 'pixelgrid' ),
			'category' => 'backups',
			'tags'     => array( __( 'Checklists', 'pixelgrid' ) ),
			'image'    => 5,
			'content'  => $pixelgrid_p( __( 'Backups fail quietly. The only way to know one works is to restore something from it.', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Pick one real file and one real mailbox', 'pixelgrid' ), __( 'Restore them somewhere safe', 'pixelgrid' ), __( 'Time how long it took and write it down', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'insurance-questions',
			'title'    => __( 'What cyber insurers ask now, in plain English', 'pixelgrid' ),
			'excerpt'  => __( 'The questions on every renewal form, and honest answers.', 'pixelgrid' ),
			'category' => 'security',
			'tags'     => array(),
			'image'    => 7,
			'content'  => $pixelgrid_p( __( 'Renewal forms have grown from one page to six. Most of the questions boil down to the same few things.', 'pixelgrid' ) )
				. $pixelgrid_ul( array( __( 'Do you use sign-in protection everywhere?', 'pixelgrid' ), __( 'Are updates applied within a set time?', 'pixelgrid' ), __( 'Are backups kept somewhere an attacker cannot delete them?', 'pixelgrid' ) ) ),
		),
		array(
			'key'      => 'replace-or-repair',
			'title'    => __( 'When to replace a laptop, and when to repair it', 'pixelgrid' ),
			'excerpt'  => __( 'A simple rule based on age, battery and what it is used for.', 'pixelgrid' ),
			'category' => 'helpdesk',
			'tags'     => array(),
			'image'    => 8,
			'content'  => $pixelgrid_p( __( 'Our rule of thumb: repair anything under three years old, replace anything over five, and decide in between based on the battery and the work it does.', 'pixelgrid' ) ),
		),
	),
);
