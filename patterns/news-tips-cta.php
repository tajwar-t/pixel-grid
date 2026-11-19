<?php
/**
 * Title: News tips call to action
 * Slug: pixelgrid/news-tips-cta
 * Categories: pixelgrid-news, call-to-action
 * Keywords: news, tips, story, contact, call to action
 * Viewport Width: 1400
 * Description: A dark section asking readers to send a story, with a button to the Tips page, beside a picture.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-night pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-night pixelgrid-stack"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"pixelgrid-news-label"} -->
<p class="pixelgrid-news-label"><?php echo esc_html_x( 'Tips', 'News section label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html_x( 'Seen something we should know about?', 'News call to action heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'The best stories start with a reader. Tell us what is happening on your street, at your work or in your club, and a reporter will read every message.', 'News call to action text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( add_query_arg( 'pagename', 'tips', home_url( '/' ) ) ); ?>"><?php echo esc_html_x( 'Send us a story', 'News call to action button', 'pixelgrid' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/news-14.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture for the tips call to action', 'Image description', 'pixelgrid' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
