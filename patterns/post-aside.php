<?php
/**
 * Title: Post sidebar
 * Slug: pixelgrid/post-aside
 * Inserter: no
 *
 * @package pixelgrid
 */

?>
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Categories', 'Sidebar heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:categories /-->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Recent notes', 'Sidebar heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:latest-posts {"postsToShow":4} /-->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html_x( 'Search notes', 'Sidebar heading', 'pixelgrid' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:pattern {"slug":"pixelgrid/search-form"} /-->
