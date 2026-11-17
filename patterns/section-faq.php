<?php
/**
 * Title: Questions and answers
 * Slug: pixelgrid/section-faq
 * Categories: pixelgrid-sections, text
 * Keywords: faq, questions, answers, accordion
 * Viewport Width: 1400
 * Description: A heading and four questions that open to show their answers.
 *
 * @package pixelgrid
 */

$pixelgrid_faq = array(
	array( __( 'Is there a minimum contract?', 'pixelgrid' ), __( 'No. It runs month to month and either side can end it with 30 days notice.', 'pixelgrid' ) ),
	array( __( 'What counts as a person?', 'pixelgrid' ), __( 'Anyone with a work account we support. Shared mailboxes and meeting rooms are free.', 'pixelgrid' ) ),
	array( __( 'Do you charge for onboarding?', 'pixelgrid' ), __( 'No. The first month costs the same as every other month.', 'pixelgrid' ) ),
	array( __( 'What is not included?', 'pixelgrid' ), __( 'New hardware, software licences and on-site visits are billed at cost, with your approval first.', 'pixelgrid' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Common questions', 'Section heading', 'pixelgrid' ); ?></h2>
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
