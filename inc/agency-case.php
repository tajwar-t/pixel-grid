<?php
/**
 * Shared block markup for the Agency case study patterns.
 *
 * The patterns agency-case-study and agency-case-1 to agency-case-6 each pass their own
 * text to pixelgrid_agency_case(), so the sections are written once.
 *
 * @package pixelgrid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pixelgrid_agency_case' ) ) :
	/**
	 * Prints a case study page: lead, results, challenge, approach and outcome, a pull quote
	 * and a link to the next case study.
	 *
	 * @param array $c {
	 *     Case study text. Strings are plain text and are escaped here.
	 *
	 *     @type string $label      Client and discipline line.
	 *     @type string $title      Page heading.
	 *     @type string $lead       Introduction.
	 *     @type string $image      Theme-relative image path.
	 *     @type string $alt        Image description.
	 *     @type array  $stats      Three array( number, label ) pairs.
	 *     @type string $challenge  Challenge text.
	 *     @type string $approach   Approach text.
	 *     @type string $outcome    Outcome text.
	 *     @type string $quote      Client quote.
	 *     @type string $cite       Quote source.
	 *     @type string $next_path  pagename of the next page, for example work/tidewater-museum.
	 *     @type string $next_title Heading of the next page.
	 * }
	 */
	function pixelgrid_agency_case( array $c ) {
		$next_url = add_query_arg( 'pagename', $c['next_path'], home_url( '/' ) );
		$prose    = array(
			array( _x( 'The challenge', 'Case study heading', 'pixelgrid' ), $c['challenge'] ),
			array( _x( 'Our approach', 'Case study heading', 'pixelgrid' ), $c['approach'] ),
			array( _x( 'The outcome', 'Case study heading', 'pixelgrid' ), $c['outcome'] ),
		);
		?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-tall","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-tall"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html( $c['label'] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html( $c['title'] ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html( $c['lead'] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( get_parent_theme_file_uri( $c['image'] ) ); ?>" alt="<?php echo esc_attr( $c['alt'] ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-sun pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-sun pixelgrid-stack"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Results', 'Case study heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"className":"pixelgrid-stats"} -->
<div class="wp-block-columns pixelgrid-stats">
		<?php foreach ( $c['stats'] as $pixelgrid_stat ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-stat"} -->
<p class="is-style-stat"><?php echo esc_html( $pixelgrid_stat[0] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_stat[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
		<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:group {"className":"pixelgrid-prose","layout":{"type":"default"}} -->
<div class="wp-block-group pixelgrid-prose">
		<?php foreach ( $prose as $pixelgrid_part ) : ?>
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html( $pixelgrid_part[0] ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html( $pixelgrid_part[1] ); ?></p>
<!-- /wp:paragraph -->
		<?php endforeach; ?>
</div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-blue pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-blue pixelgrid-stack"><!-- wp:pullquote -->
<figure class="wp-block-pullquote"><blockquote><p><?php echo esc_html( $c['quote'] ); ?></p><cite><?php echo esc_html( $c['cite'] ); ?></cite></blockquote></figure>
<!-- /wp:pullquote --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-night pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-night pixelgrid-stack"><!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php echo esc_html_x( 'Next case study', 'Case study label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-xx-large-font-size"><a href="<?php echo esc_url( $next_url ); ?>"><?php echo esc_html( $c['next_title'] ); ?></a></h2>
<!-- /wp:heading -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $next_url ); ?>"><?php echo esc_html_x( 'Read the next case study', 'Case study button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'work', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'All work', 'Case study button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
		<?php
	}
endif;
