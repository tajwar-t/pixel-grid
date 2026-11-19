<?php
/**
 * Title: News about page
 * Slug: pixelgrid/news-page-about
 * Categories: pixelgrid-pages
 * Keywords: page, layout, news, about, newsroom
 * Block Types: core/post-content
 * Post Types: page
 * Viewport Width: 1400
 * Description: About the paper: who it is for, what it covers and how the newsroom works, beside a picture, then a call to send a tip.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-paper pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-paper pixelgrid-stack"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"pixelgrid-news-label"} -->
<p class="pixelgrid-news-label"><?php echo esc_html_x( 'About', 'News section label', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html_x( 'An independent paper for the whole city', 'News about page heading', 'pixelgrid' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-lead"} -->
<p class="is-style-lead"><?php echo esc_html_x( 'The Grid Ledger reports on the city as the people who live here see it: the council and the courts, the businesses and the bus routes, the clubs and the stages.', 'News about page introduction', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/news-18.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture for the about page', 'Image description', 'pixelgrid' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:group {"className":"pixelgrid-prose","layout":{"type":"default"}} -->
<div class="wp-block-group pixelgrid-prose"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'What we cover', 'News about page heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'Five sections run every day: World, Business, Technology, Culture and Sport. Each one starts from the same question: what does this mean for someone who lives here?', 'News about page text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'How we work', 'News about page heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-pixel"} -->
<ul class="wp-block-list is-style-pixel"><!-- wp:list-item -->
<li><?php echo esc_html_x( 'Every story is checked by a second editor before it is published.', 'News about page list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'We correct mistakes in the article itself and say what changed.', 'News about page list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html_x( 'Opinion pieces are labelled as opinion and kept apart from the news.', 'News about page list item', 'pixelgrid' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Who pays for it', 'News about page heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html_x( 'The paper is funded by its readers and by local advertising. Advertisers never see a story before it runs and have no say in what we cover.', 'News about page text', 'pixelgrid' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"pixelgrid/news-tips-cta"} /-->
