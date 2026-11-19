<?php
/**
 * Title: News tips page
 * Slug: pixelgrid/news-page-tips
 * Categories: pixelgrid-pages
 * Keywords: page, layout, news, tips, story, sources
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1400
 * Description: How to send a story: what to include, how sources are protected and how to reach the newsroom, beside a pixel padlock.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"pixelgrid-news-label"} -->
<p class="pixelgrid-news-label"><?php echo esc_html_x( 'Tips', 'News section label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html_x( 'Send us a story', 'News tips page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'Many of our best stories began with a short message from a reader. If you know something the city should know, tell us.', 'News tips page introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"is-style-mosaic-lock"} -->
<figure class="wp-block-image size-full is-style-mosaic-lock"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/news-11.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture shown through a pixel padlock', 'Image description', 'pixelgrid' ); ?>"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:group {"className":"pixelgrid-prose","layout":{"type":"default"}} -->
<div class="wp-block-group pixelgrid-prose"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'What to include', 'News tips page heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'What happened, where and when, in a few plain sentences.', 'News tips page list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'How you know: whether you saw it yourself or heard it from someone else.', 'News tips page list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Any documents, photos or dates that help us check it.', 'News tips page list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'The best way and time to reach you.', 'News tips page list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'How we protect sources', 'News tips page heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Only the reporter and one editor know who you are. We never publish your name unless you agree, and we do not share it with anyone outside the newsroom.', 'News tips page text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'If you are worried about being identified, say so in your first message and send it from a personal device and account, not one your employer manages.', 'News tips page text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'How to reach us', 'News tips page heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Email tips@example.com or call +1 800 555 0142. A reporter reads the tips inbox every day.', 'News tips page contact line', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
