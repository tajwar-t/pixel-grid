<?php
/**
 * Title: Pricing plans
 * Slug: pixelgrid/section-plans
 * Categories: pixelgrid-sections, pricing
 * Keywords: pricing, plans, prices
 * Viewport Width: 1400
 * Description: Page heading and three price plans, the middle one highlighted.
 *
 * @package pixelgrid
 */

$pixelgrid_plans = array(
	array(
		'style' => 'plan',
		'name'  => __( 'Helpdesk', 'pixelgrid' ),
		'price' => __( '$38', 'pixelgrid' ),
		'text'  => __( 'For teams that mainly need someone to call.', 'pixelgrid' ),
		'items' => array( __( '24 hour helpdesk', 'pixelgrid' ), __( 'Device and account setup', 'pixelgrid' ), __( 'Monthly report', 'pixelgrid' ) ),
		'cta'   => __( 'Choose Helpdesk', 'pixelgrid' ),
	),
	array(
		'style' => 'plan-featured',
		'name'  => __( 'Complete', 'pixelgrid' ),
		'price' => __( '$74', 'pixelgrid' ),
		'text'  => __( 'What most clients pick. Helpdesk, cloud and security together.', 'pixelgrid' ),
		'items' => array( __( 'Everything in Helpdesk', 'pixelgrid' ), __( 'Cloud monitoring and bill reviews', 'pixelgrid' ), __( 'Security updates within 72 hours', 'pixelgrid' ), __( 'Backups tested monthly', 'pixelgrid' ) ),
		'cta'   => __( 'Choose Complete', 'pixelgrid' ),
	),
	array(
		'style' => 'plan',
		'name'  => __( 'Regulated', 'pixelgrid' ),
		'price' => __( '$96', 'pixelgrid' ),
		'text'  => __( 'For healthcare, legal and finance teams with audits to pass.', 'pixelgrid' ),
		'items' => array( __( 'Everything in Complete', 'pixelgrid' ), __( 'Audit preparation', 'pixelgrid' ), __( 'Quarterly security review', 'pixelgrid' ), __( 'Named compliance engineer', 'pixelgrid' ) ),
		'cta'   => __( 'Choose Regulated', 'pixelgrid' ),
	),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php echo esc_html_x( 'Pricing', 'Pricing page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'One price per person, per month. No setup fee, no charge per ticket, cancel with 30 days notice.', 'Pricing introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:columns {"className":"pixelgrid-plans"} -->
<div class="wp-block-columns pixelgrid-plans">
<?php foreach ( $pixelgrid_plans as $pixelgrid_plan ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"is-style-<?php echo esc_attr( $pixelgrid_plan['style'] ); ?>","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-<?php echo esc_attr( $pixelgrid_plan['style'] ); ?>"><!-- wp:heading {"level":2,"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size"><?php echo esc_html( $pixelgrid_plan['name'] ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-stat"} -->
<p class="is-style-stat"><?php echo esc_html( $pixelgrid_plan['price'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html_x( 'per person a month', 'Price unit', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_plan['text'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel">
	<?php foreach ( $pixelgrid_plan['items'] as $pixelgrid_plan_item ) : ?>
<!-- wp:list-item -->
<li><?php echo esc_html( $pixelgrid_plan_item ); ?></li>
<!-- /wp:list-item -->
	<?php endforeach; ?>
</ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'contact', home_url( '/' ) ) ); ?>"><?php echo esc_html( $pixelgrid_plan['cta'] ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html_x( 'Prices in US dollars for teams of 10 to 100. Larger teams pay less per person.', 'Pricing footnote', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->
