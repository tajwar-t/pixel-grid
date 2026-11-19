<?php
/**
 * Title: Agency questions and answers
 * Slug: pixelgrid/agency-faq
 * Categories: pixelgrid-agency, text
 * Keywords: faq, questions, answers, accordion
 * Viewport Width: 1400
 * Description: A heading and four questions about starting a project that open to show their answers.
 *
 * @package pixelgrid
 */

$pixelgrid_faq = array(
	array( __( 'What should I send first?', 'pixelgrid' ), __( 'A few lines on what you need, your deadline and a rough budget. A full brief can come later.', 'pixelgrid' ) ),
	array( __( 'Do you work with small organisations?', 'pixelgrid' ), __( 'Yes. Most of our clients have fewer than fifty people. A workshop day is a good place to start.', 'pixelgrid' ) ),
	array( __( 'Who owns the work?', 'pixelgrid' ), __( 'You do. Once the final invoice is paid, every file and font licence we bought for you is yours.', 'pixelgrid' ) ),
	array( __( 'Can you work with our own developers?', 'pixelgrid' ), __( 'Often. We hand over designs and a working prototype, and stay on call while your team builds.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Before you write', 'Section heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:group {"layout":{"type":"default"}} -->
<div class="wp-block-group">
<?php foreach ( $pixelgrid_faq as $pixelgrid_qa ) : ?>
<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $pixelgrid_qa[0] ); ?></summary><!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_qa[1] ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<?php endforeach; ?>
</div>
<!-- /wp:group --></section>
<!-- /wp:group -->
