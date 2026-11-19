<?php
/**
 * Title: News opinion column
 * Slug: pixelgrid/news-opinion
 * Categories: pixelgrid-news, posts
 * Keywords: news, opinion, column, comment, quote
 * Viewport Width: 1400
 * Description: The Opinion label, a pull quote and the two latest opinion pieces, beside a picture. Setup points the list at the Opinion category.
 *
 * @package pixelgrid
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-tone-fog pixelgrid-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-tone-fog pixelgrid-stack"><!-- wp:columns {"className":"pixelgrid-split"} -->
<div class="wp-block-columns pixelgrid-split"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-notch"} -->
<figure class="wp-block-image size-full is-style-notch"><img src="<?php echo esc_url( get_parent_theme_file_uri( 'assets/images/news-13.webp' ) ); ?>" alt="<?php echo esc_attr_x( 'An abstract grey picture for the opinion column', 'Image description', 'pixelgrid' ); ?>" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"className":"pixelgrid-news-label pixelgrid-news-rule"} -->
<h2 class="wp-block-heading pixelgrid-news-label pixelgrid-news-rule"><?php echo esc_html_x( 'Opinion', 'News section label', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:pullquote -->
<figure class="wp-block-pullquote"><blockquote><p><?php echo esc_html_x( '“A city paper earns its place one street at a time, by showing up when nobody else does.”', 'Sample opinion quote', 'pixelgrid' ); ?></p><cite><?php echo esc_html_x( 'From this week’s editor’s column', 'Sample quote source', 'pixelgrid' ); ?></cite></blockquote></figure>
<!-- /wp:pullquote -->

<!-- wp:query {"queryId":50,"query":{"perPage":2,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"className":"pixelgrid-news-opinion pixelgrid-cat-opinion"} -->
<div class="wp-block-query pixelgrid-news-opinion pixelgrid-cat-opinion"><!-- wp:post-template -->
<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":24} /-->

<!-- wp:post-author-name /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
