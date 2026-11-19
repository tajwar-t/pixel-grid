<?php
/**
 * News look tests: style variation, contrast and the News serif.
 */
require_once __DIR__ . '/lib/contrast.php';

$theme_json = json_decode( file_get_contents( get_parent_theme_file_path( 'theme.json' ) ), true );
$agency     = json_decode( file_get_contents( get_parent_theme_file_path( 'styles/agency.json' ) ), true );
$news_path  = get_parent_theme_file_path( 'styles/news-portal.json' );
$news_raw   = file_exists( $news_path ) ? file_get_contents( $news_path ) : false;
$news       = false === $news_raw ? null : json_decode( $news_raw, true );

pg_assert( is_array( $news ), 'styles/news-portal.json exists and decodes' );
$news     = is_array( $news ) ? $news : array();
$news_raw = (string) $news_raw;
pg_assert_same( 3, $news['version'] ?? null, 'news is version 3' );
pg_assert_same( 'News', $news['title'] ?? null, 'news is titled News' );

$news_palette = $news['settings']['color']['palette'] ?? array();
$names_of     = function ( $palette ) {
	$out = array();
	foreach ( $palette as $entry ) {
		$out[ $entry['slug'] ] = $entry['name'];
	}
	ksort( $out );
	return $out;
};
pg_assert_same( 28, count( $news_palette ), 'news has 28 palette entries' );
pg_assert_same( $names_of( $agency['settings']['color']['palette'] ), $names_of( $news_palette ), 'news has the agency.json slugs with the same names and no others' );
foreach ( $news_palette as $entry ) {
	pg_assert( (bool) preg_match( '/^#[0-9A-Fa-f]{6}$/', $entry['color'] ?? '' ), "news {$entry['slug']} is a hex colour" );
}

$pg_min = INF;
foreach ( pixelgrid_test_palette_pairs( $news_palette ) as $pair ) {
	list( $tone, $text_ratio, $hover_ratio ) = $pair;
	$pg_min = min( $pg_min, $text_ratio, $hover_ratio );
	printf( "INFO: News %s text/bg %.2f on-hover/hover %.2f\n", $tone, $text_ratio, $hover_ratio );
	pg_assert( $text_ratio >= 4.5, "News $tone text on background reaches 4.5:1" );
	pg_assert( $hover_ratio >= 4.5, "News $tone text on hover reaches 4.5:1" );
}
printf( "INFO: News minimum contrast %.2f\n", $pg_min );

// The light tone is newsprint and ink, with the red accent on hover.
$news_colors = array();
foreach ( $news_palette as $entry ) {
	$news_colors[ $entry['slug'] ] = strtoupper( $entry['color'] );
}
pg_assert_same( '#F7F5F0', $news_colors['paper-background'] ?? null, 'news light background is newsprint' );
pg_assert_same( '#141414', $news_colors['paper-text'] ?? null, 'news light text is ink' );
pg_assert_same( '#C8102E', $news_colors['paper-hover'] ?? null, 'news light hover is the red accent' );
pg_assert_same( '#E6E3DC', $news_colors['fog-background'] ?? null, 'news soft background is grey' );

// Typography: the serif for body, headings and the site title; sentence-case headings.
$serif   = 'var:preset|font-family|news-serif';
$heading = $news['styles']['elements']['heading']['typography'] ?? array();
pg_assert_same( $serif, $news['styles']['typography']['fontFamily'] ?? null, 'news body uses news-serif' );
pg_assert_same( $serif, $heading['fontFamily'] ?? null, 'news headings use news-serif' );
pg_assert_same( '700', $heading['fontWeight'] ?? null, 'news headings are weight 700' );
pg_assert_same( 'none', $heading['textTransform'] ?? null, 'news headings are sentence case' );
pg_assert_same( '-0.01em', $heading['letterSpacing'] ?? null, 'news headings letter spacing is -0.01em' );
$title = $news['styles']['blocks']['core/site-title']['typography'] ?? array();
pg_assert_same( $serif, $title['fontFamily'] ?? null, 'news site title uses news-serif' );
pg_assert_same( '800', $title['fontWeight'] ?? null, 'news site title is weight 800' );
pg_assert_same( 'none', $title['textTransform'] ?? null, 'news site title is not uppercased' );
pg_assert( false === strpos( $news_raw, 'fontFace' ), 'news variation declares no fontFace' );

// Headline sizes: Newsreader 700 is about 0.55em per letter, and a phone column at 390px is
// about 11 grid units wide. h1 and h2 stay small enough that a 14-letter word fits on one line.
foreach ( array( 'h1' => 1.4, 'h2' => 1.2 ) as $pg_level => $pg_max ) {
	$pg_size = $news['styles']['elements'][ $pg_level ]['typography']['fontSize'] ?? '';
	$pg_line = $news['styles']['elements'][ $pg_level ]['typography']['lineHeight'] ?? '';
	pg_assert( $pg_size !== ( $theme_json['styles']['elements'][ $pg_level ]['typography']['fontSize'] ?? '' ), "news $pg_level font size differs from theme.json" );
	pg_assert( '' !== $pg_line && $pg_line !== ( $theme_json['styles']['elements'][ $pg_level ]['typography']['lineHeight'] ?? '' ), "news $pg_level line height is its own" );
	pg_assert( (bool) preg_match( '/^calc\(var\(--pixelgrid-u\) \* ([0-9.]+)\)$/', $pg_size, $pg_m ) && (float) $pg_m[1] <= $pg_max, "news $pg_level size is at most $pg_max grid units" );
}
$pg_sizes = array();
foreach ( (array) ( $news['settings']['typography']['fontSizes'] ?? array() ) as $pg_entry ) {
	$pg_sizes[ $pg_entry['slug'] ] = $pg_entry['size'];
}
pg_assert_same( $news['styles']['elements']['h2']['typography']['fontSize'] ?? null, $pg_sizes['xx-large'] ?? '', 'news Headline preset matches its h2 size' );
pg_assert( false !== strpos( $pg_sizes['huge'] ?? '', 'vw' ), 'news Huge preset is capped by the window width for phones' );

// The news-serif family is registered in base theme.json with its bundled faces.
$families = wp_get_global_settings( array( 'typography', 'fontFamilies', 'theme' ) );
$by_slug  = array();
foreach ( (array) $families as $family ) {
	$by_slug[ $family['slug'] ?? '' ] = $family;
}
$news_serif = $by_slug['news-serif'] ?? null;
pg_assert( is_array( $news_serif ), 'theme font families include news-serif' );
pg_assert_same( 'News serif', $news_serif['name'] ?? null, 'news-serif family is named News serif' );
pg_assert_same( 'Newsreader, Georgia, "Times New Roman", serif', $news_serif['fontFamily'] ?? null, 'news-serif falls back to Georgia' );
$faces = $news_serif['fontFace'] ?? array();
pg_assert_same( 2, count( $faces ), 'news-serif has latin and latin-ext faces' );
$pg_files = array();
foreach ( $faces as $face ) {
	pg_assert_same( '400 800', $face['fontWeight'] ?? null, 'news-serif face covers weights 400 to 800' );
	foreach ( (array) $face['src'] as $src ) {
		$rel = 0 === strpos( $src, 'file:./' ) ? substr( $src, 7 ) : null;
		if ( null === $rel ) {
			$theme_uri = trailingslashit( get_template_directory_uri() );
			$rel       = 0 === strpos( $src, $theme_uri ) ? substr( $src, strlen( $theme_uri ) ) : $src;
		}
		$pg_files[] = $rel;
	}
}
sort( $pg_files );
pg_assert_same( array( 'assets/fonts/newsreader-400-800-latin-ext.woff2', 'assets/fonts/newsreader-400-800-latin.woff2' ), $pg_files, 'news-serif uses the two Newsreader files' );
foreach ( $pg_files as $rel ) {
	pg_assert( file_exists( get_parent_theme_file_path( $rel ) ) && filesize( get_parent_theme_file_path( $rel ) ) > 10000, "font file $rel exists" );
}

// Review Focus 1: picking News in Browse styles without setup still finds every font it uses.
preg_match_all( '/var:preset\|font-family\|([a-z0-9-]+)|--wp--preset--font-family--([a-z0-9-]+)/', $news_raw, $pg_uses );
$pg_used = array_unique( array_filter( array_merge( $pg_uses[1], $pg_uses[2] ) ) );
pg_assert( in_array( 'news-serif', $pg_used, true ), 'news uses news-serif' );
foreach ( $pg_used as $pg_slug ) {
	pg_assert( isset( $by_slug[ $pg_slug ] ), "news font family $pg_slug is registered in theme.json" );
}

$titles = array();
foreach ( WP_Theme_JSON_Resolver::get_style_variations() as $variation ) {
	$titles[] = $variation['title'] ?? '';
}
pg_assert( in_array( 'News', $titles, true ), 'style variations list News' );

// Rendered with the News variation, the stylesheet carries the serif and the faces.
$pg_css = wp_get_global_stylesheet();
pg_assert( false !== strpos( $pg_css, '--wp--preset--font-family--news-serif' ), 'global stylesheet defines the news-serif preset' );

pg_assert( isset( $news['styles']['css'] ) && false !== strpos( $news['styles']['css'], 'p.is-style-lead' ) && false !== strpos( $news['styles']['css'], 'news-serif' ), 'News lead paragraphs use the News serif' );

// News images: generated plates exist, are valid 1200x1200 WebP, stay small and keep mid-tone variation.
for ( $i = 1; $i <= 18; $i++ ) {
	$img_rel  = sprintf( 'assets/images/news-%02d.webp', $i );
	$img_path = get_parent_theme_file_path( $img_rel );
	pg_assert( file_exists( $img_path ), "$img_rel exists" );
	$img_info = file_exists( $img_path ) ? getimagesize( $img_path ) : false;
	pg_assert( is_array( $img_info ) && 'image/webp' === $img_info['mime'], "$img_rel is a valid WebP" );
	pg_assert( is_array( $img_info ) && 1200 === $img_info[0] && 1200 === $img_info[1], "$img_rel is 1200x1200" );
	pg_assert( file_exists( $img_path ) && filesize( $img_path ) <= 150 * 1024, "$img_rel is at most 150 KB" );
	$src = file_exists( $img_path ) ? imagecreatefromwebp( $img_path ) : false;
	$sd  = 0;
	if ( $src ) {
		$small = imagecreatetruecolor( 100, 100 );
		imagecopyresampled( $small, $src, 0, 0, 0, 0, 100, 100, 1200, 1200 );
		$vals = array();
		for ( $py = 0; $py < 100; $py++ ) {
			for ( $px = 0; $px < 100; $px++ ) {
				$vals[] = imagecolorat( $small, $px, $py ) & 0xFF;
			}
		}
		$mean = array_sum( $vals ) / count( $vals );
		$var  = 0;
		foreach ( $vals as $val ) {
			$var += ( $val - $mean ) ** 2;
		}
		$sd = sqrt( $var / count( $vals ) );
	}
	printf( "INFO: %s stddev %.1f size %d
", $img_rel, $sd, file_exists( $img_path ) ? filesize( $img_path ) : 0 );
	pg_assert( $sd >= 35, sprintf( '%s luminance stddev is at least 35 (got %.1f)', $img_rel, $sd ) );
}

// News patterns: registered, expandable, valid markup, real images, no database ids or taxQuery.
$pg_news_cats  = array( 'world', 'business', 'technology', 'culture', 'sport' );
$pg_news_slugs = array(
	'news-header',
	'news-footer',
	'news-lead',
	'news-top-stories',
	'news-category-row',
	'news-most-read',
	'news-opinion',
	'news-tips-cta',
	'news-page-home',
	'news-page-about',
	'news-page-contact',
	'news-page-tips',
	'news-template-home',
	'news-template-single',
	'news-template-category',
);
foreach ( $pg_news_cats as $pg_cat ) {
	$pg_news_slugs[] = 'news-row-' . $pg_cat;
}
$pg_registry  = WP_Block_Patterns_Registry::get_instance();
$pg_theme_uri = trailingslashit( get_template_directory_uri() );
$pg_news      = array();
foreach ( $pg_news_slugs as $pg_slug ) {
	$pg_pattern = $pg_registry->get_registered( 'pixelgrid/' . $pg_slug );
	pg_assert( is_array( $pg_pattern ), "pixelgrid/$pg_slug is registered" );
	$pg_content          = is_array( $pg_pattern ) ? pixelgrid_starter_expand( $pg_pattern['content'] ) : '';
	$pg_news[ $pg_slug ] = $pg_content;
	pg_assert( '' !== trim( $pg_content ), "pixelgrid/$pg_slug has expanded content" );
	pg_assert( false === strpos( $pg_content, '<!-- wp:pattern' ), "pixelgrid/$pg_slug has no unexpanded pattern refs" );
	pg_assert( ! preg_match( '/"(id|ref)":\s*\d/', $pg_content ), "pixelgrid/$pg_slug has no numeric id or ref attribute" );
	pg_assert( false === strpos( $pg_content, 'taxQuery' ), "pixelgrid/$pg_slug has no taxQuery" );
	$pg_source = (string) file_get_contents( get_parent_theme_file_path( 'patterns/' . $pg_slug . '.php' ) );
	pg_assert( false === strpos( $pg_source, '"theme":' ), "patterns/$pg_slug.php sets no theme attribute" );

	$pg_junk = array();
	$pg_walk = function ( $blocks ) use ( &$pg_walk, &$pg_junk ) {
		foreach ( $blocks as $block ) {
			if ( null === $block['blockName'] && '' !== trim( $block['innerHTML'] ) ) {
				$pg_junk[] = trim( $block['innerHTML'] );
			}
			$pg_walk( $block['innerBlocks'] );
		}
	};
	$pg_walk( parse_blocks( $pg_content ) );
	pg_assert( array() === $pg_junk, "pixelgrid/$pg_slug parses with no freeform text" . ( $pg_junk ? ': ' . substr( $pg_junk[0], 0, 80 ) : '' ) );

	preg_match_all( '/<img[^>]+src="([^"]+)"/', $pg_content, $pg_imgs );
	foreach ( $pg_imgs[1] as $pg_src ) {
		$pg_rel = 0 === strpos( $pg_src, $pg_theme_uri ) ? substr( $pg_src, strlen( $pg_theme_uri ) ) : $pg_src;
		pg_assert( 0 === strpos( $pg_rel, 'assets/images/news-' ) && file_exists( get_parent_theme_file_path( $pg_rel ) ), "pixelgrid/$pg_slug image $pg_rel is a News theme file" );
	}

	// Rendering the pattern raises no PHP notice or warning.
	$pg_errors = array();
	set_error_handler(
		function ( $errno, $errstr, $errfile, $errline ) use ( &$pg_errors ) {
			if ( in_array( $errno, array( E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE ), true ) ) {
				$pg_errors[] = "$errstr ($errfile:$errline)";
			}
			return true;
		}
	);
	$pg_html = '' !== $pg_content ? do_blocks( $pg_content ) : '';
	restore_error_handler();
	pg_assert( array() === $pg_errors, "pixelgrid/$pg_slug renders with no PHP notices" . ( $pg_errors ? ': ' . $pg_errors[0] : '' ) );
	pg_assert( false === strpos( $pg_html, 'Warning:' ) && false === strpos( $pg_html, 'Notice:' ), "pixelgrid/$pg_slug output has no PHP messages" );
}

$pg_news_cat = WP_Block_Pattern_Categories_Registry::get_instance()->get_registered( 'pixelgrid-news' );
pg_assert_same( 'Pixelgrid News', $pg_news_cat['label'] ?? null, 'pattern category pixelgrid-news is labelled Pixelgrid News' );

$pg_has = function ( $slug, $needle ) use ( $pg_news ) {
	return false !== strpos( $pg_news[ $slug ] ?? '', $needle );
};
foreach ( $pg_news_cats as $pg_cat ) {
	$pg_cat_url = esc_url( add_query_arg( 'category_name', $pg_cat, home_url( '/' ) ) );
	pg_assert( $pg_has( 'news-row-' . $pg_cat, 'pixelgrid-cat-' . $pg_cat ), "news-row-$pg_cat has the pixelgrid-cat-$pg_cat marker" );
	pg_assert( $pg_has( 'news-row-' . $pg_cat, $pg_cat_url ), "news-row-$pg_cat heading links to the category" );
	pg_assert( $pg_has( 'news-footer', $pg_cat_url ), "news footer links to $pg_cat" );
	pg_assert( $pg_has( 'news-page-home', 'pixelgrid-cat-' . $pg_cat ), "news home page has the $pg_cat row" );
}
$pg_most_read = $pg_registry->get_registered( 'pixelgrid/news-most-read' );
pg_assert( $pg_has( 'news-most-read', 'pixelgrid-cat-most-read' ), 'news-most-read has the pixelgrid-cat-most-read marker' );
pg_assert( $pg_has( 'news-most-read', '"perPage":5' ), 'news-most-read lists 5 posts' );
pg_assert( false !== stripos( is_array( $pg_most_read ) ? $pg_most_read['description'] : '', 'does not count views' ), 'news-most-read says the theme does not count views' );
pg_assert( $pg_has( 'news-opinion', 'pixelgrid-cat-opinion' ) && $pg_has( 'news-opinion', '"perPage":2' ), 'news-opinion lists 2 opinion posts' );
pg_assert( $pg_has( 'news-opinion', 'wp:pullquote' ), 'news-opinion has a pull quote' );
pg_assert( ! $pg_has( 'news-category-row', 'pixelgrid-cat-' ) && $pg_has( 'news-category-row', '"perPage":4' ), 'news-category-row lists 4 posts with no category marker' );
pg_assert( $pg_has( 'news-lead', '"perPage":1' ), 'news-lead lists 1 post' );
pg_assert( $pg_has( 'news-top-stories', '"perPage":3' ) && $pg_has( 'news-top-stories', '"offset":1' ), 'news-top-stories lists posts 2 to 4' );
pg_assert( $pg_has( 'news-tips-cta', esc_url( add_query_arg( 'pagename', 'tips', home_url( '/' ) ) ) ), 'news-tips-cta links to the tips page' );
foreach ( array( 'pixelgrid-cat-most-read', 'pixelgrid-cat-opinion', '"offset":1', 'pagename=tips' ) as $pg_needle ) {
	pg_assert( $pg_has( 'news-page-home', $pg_needle ), "news home page includes $pg_needle" );
}
pg_assert( $pg_has( 'news-header', '"source":"pixelgrid/today"' ), 'news-header has the pixelgrid/today binding' );
pg_assert( $pg_has( 'news-header', 'wp:navigation' ) && $pg_has( 'news-header', 'wp:search' ), 'news-header has the menu and search' );
pg_assert( $pg_has( 'news-template-single', 'pixelgrid-related' ) && $pg_has( 'news-template-single', 'wp:post-excerpt' ), 'news-template-single has the standfirst and related row' );
pg_assert( (bool) preg_match( '/wp:query \{[^\n]*"perPage":3[^\n]*"inherit":false[^\n]*pixelgrid-related/', $pg_news['news-template-single'] ?? '' ), 'news related row is 3 posts, not inherited' );
pg_assert( $pg_has( 'news-template-home', '"inherit":true' ) && $pg_has( 'news-template-home', 'wp:query-pagination' ), 'news-template-home is an inherited river with pagination' );
pg_assert( $pg_has( 'news-template-category', 'wp:query-title' ) && $pg_has( 'news-template-category', '"inherit":true' ) && $pg_has( 'news-template-category', 'wp:query-pagination' ), 'news-template-category is a section front' );
foreach ( array( 'news-template-home', 'news-template-single', 'news-template-category' ) as $pg_slug ) {
	$pg_source = (string) file_get_contents( get_parent_theme_file_path( 'patterns/' . $pg_slug . '.php' ) );
	pg_assert( false !== strpos( $pg_source, '<!-- wp:template-part {"slug":"header","tagName":"header"} /-->' ) && false !== strpos( $pg_source, '<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->' ), "$pg_slug has header and footer parts" );
	pg_assert( $pg_has( $pg_slug, '"tagName":"main"' ), "$pg_slug has a main" );
}
foreach ( array( 'news-page-home', 'news-page-about', 'news-page-contact', 'news-page-tips' ) as $pg_slug ) {
	$pg_pattern = $pg_registry->get_registered( 'pixelgrid/' . $pg_slug );
	pg_assert( in_array( 'pixelgrid-pages', $pg_pattern['categories'] ?? array(), true ), "pixelgrid/$pg_slug is in Pixelgrid pages" );
	pg_assert( in_array( 'core/post-content', $pg_pattern['blockTypes'] ?? array(), true ), "pixelgrid/$pg_slug is a post-content pattern" );
	pg_assert_same( array( 'page' ), $pg_pattern['postTypes'] ?? null, "pixelgrid/$pg_slug is for pages" );
}
foreach ( array( 'news-row-world', 'news-row-sport', 'news-template-home', 'news-template-single', 'news-template-category' ) as $pg_slug ) {
	$pg_pattern = $pg_registry->get_registered( 'pixelgrid/' . $pg_slug );
	pg_assert( isset( $pg_pattern['inserter'] ) && false === $pg_pattern['inserter'], "pixelgrid/$pg_slug is hidden from the inserter" );
}
foreach ( array( 'news-lead', 'news-top-stories', 'news-category-row', 'news-most-read', 'news-opinion', 'news-tips-cta' ) as $pg_slug ) {
	$pg_pattern = $pg_registry->get_registered( 'pixelgrid/' . $pg_slug );
	pg_assert( in_array( 'pixelgrid-news', $pg_pattern['categories'] ?? array(), true ), "pixelgrid/$pg_slug is in Pixelgrid News" );
}
$pg_header = $pg_registry->get_registered( 'pixelgrid/news-header' );
$pg_footer = $pg_registry->get_registered( 'pixelgrid/news-footer' );
pg_assert( in_array( 'core/template-part/header', $pg_header['blockTypes'] ?? array(), true ), 'news-header is a header part pattern' );
pg_assert( in_array( 'core/template-part/footer', $pg_footer['blockTypes'] ?? array(), true ), 'news-footer is a footer part pattern' );
pg_assert( function_exists( 'pixelgrid_news_row' ), 'pixelgrid_news_row helper is loaded by the row patterns' );

// Static pictures have their own files, never an article image (news-01 to news-15).
foreach ( array(
	'news-opinion'    => 'news-16',
	'news-tips-cta'   => 'news-17',
	'news-page-about' => 'news-18',
) as $pg_slug => $pg_img ) {
	pg_assert( $pg_has( $pg_slug, 'assets/images/' . $pg_img . '.webp' ), "$pg_slug uses $pg_img" );
}
preg_match_all( '/assets\/images\/(news-\d\d)\.webp/', $pg_news['news-page-home'] ?? '', $pg_home_imgs );
pg_assert_same( count( $pg_home_imgs[1] ), count( array_unique( $pg_home_imgs[1] ) ), 'no static image appears twice on the News home page' );

// Page titles are titles, not small labels.
pg_assert( (bool) preg_match( '/wp:query-title \{[^}]*"fontSize":"xx-large"/', $pg_news['news-template-category'] ?? '' ), 'section front title uses the Headline size' );
pg_assert( (bool) preg_match( '/wp:heading \{"level":1[^}]*"fontSize":"xx-large"/', $pg_news['news-template-home'] ?? '' ), 'Latest page title uses the Headline size' );
$pg_style = (string) file_get_contents( get_parent_theme_file_path( 'style.css' ) );
pg_assert( (bool) preg_match( '/@media \(max-width: 781px\) \{\s*\.pixelgrid-news-row \.wp-block-post-template\.is-layout-grid \{\s*grid-template-columns: repeat\(2, minmax\(0, 1fr\)\);/', $pg_style ), 'News rows drop to 2 columns below 782px' );

// News variant: definition, end-to-end setup and removal.
$pg_def = pixelgrid_variant( 'news' );
pg_assert( is_array( $pg_def ), 'news variant exists' );
pg_assert( is_array( $pg_def ) && pixelgrid_variant_available( $pg_def ), 'news variant is available' );
$pg_def = is_array( $pg_def ) ? $pg_def : pixelgrid_variant_defaults( array() );
pg_assert_same( 'News', $pg_def['label'], 'news is labelled News' );
pg_assert( '' !== $pg_def['description'], 'news has a description' );
pg_assert_same( 'styles/news-portal.json', $pg_def['style'], 'news uses styles/news-portal.json' );
pg_assert_same(
	array(
		'header' => 'pixelgrid/news-header',
		'footer' => 'pixelgrid/news-footer',
	),
	$pg_def['parts'],
	'news parts are its header and footer'
);
pg_assert_same(
	array(
		'home'     => 'pixelgrid/news-template-home',
		'single'   => 'pixelgrid/news-template-single',
		'category' => 'pixelgrid/news-template-category',
	),
	$pg_def['templates'],
	'news templates are home, single and category'
);
foreach ( array( 'home', 'about', 'contact', 'tips' ) as $pg_slug ) {
	$pg_page = $pg_def['pages'][ $pg_slug ] ?? array();
	pg_assert_same( 'pixelgrid/news-page-' . $pg_slug, $pg_page[1] ?? null, "news page $pg_slug uses news-page-$pg_slug" );
	pg_assert_same( 'page-sections', $pg_page[2] ?? null, "news page $pg_slug uses page-sections" );
}
pg_assert_same( '', $pg_def['pages']['latest'][1] ?? null, 'latest page is empty' );
pg_assert_same( '', $pg_def['pages']['latest'][2] ?? null, 'latest page has no template' );
pg_assert_same( 5, count( $pg_def['pages'] ), 'news has 5 pages' );
pg_assert_same( 'home', $pg_def['front'], 'news front page is home' );
pg_assert_same( 'latest', $pg_def['posts_page'], 'news posts page is latest' );
pg_assert_same( array( 'cat:world', 'cat:business', 'cat:technology', 'cat:culture', 'cat:sport', 'about', 'tips' ), $pg_def['menu'], 'news menu order' );
pg_assert_same(
	array(
		'world'      => 'World',
		'business'   => 'Business',
		'technology' => 'Technology',
		'culture'    => 'Culture',
		'sport'      => 'Sport',
		'opinion'    => 'Opinion',
		'most-read'  => 'Most read',
	),
	$pg_def['categories'],
	'news categories'
);
pg_assert_same( '', $pg_def['preview'], 'news has no preview image yet' );
foreach ( array( 'pages', 'posts', 'reading' ) as $pg_key ) {
	pg_assert( ! empty( $pg_def['content_labels'][ $pg_key ] ), "news has content label $pg_key" );
}
pg_assert( '' !== $pg_def['design_note'], 'news has a design note' );

pg_assert_same( 15, count( $pg_def['posts'] ), 'news has 15 articles' );
$pg_per_cat   = array_fill_keys( $pg_news_cats, array() );
$pg_most_keys = array();
$pg_op_keys   = array();
$pg_images    = array();
foreach ( $pg_def['posts'] as $pg_post ) {
	$pg_cats   = (array) $pg_post['category'];
	$pg_main   = array_values( array_intersect( $pg_cats, $pg_news_cats ) );
	$pg_images[] = $pg_post['image'];
	pg_assert( 1 === count( $pg_main ) && $pg_main[0] === $pg_cats[0], "article {$pg_post['key']} has one main category, listed first" );
	pg_assert( array() === array_diff( $pg_cats, array_keys( $pg_def['categories'] ) ), "article {$pg_post['key']} uses only News categories" );
	if ( $pg_main ) {
		$pg_per_cat[ $pg_main[0] ][] = $pg_post;
	}
	if ( in_array( 'most-read', $pg_cats, true ) ) {
		$pg_most_keys[] = $pg_post['key'];
	}
	if ( in_array( 'opinion', $pg_cats, true ) ) {
		$pg_op_keys[] = $pg_post['key'];
	}
	pg_assert( '' !== trim( $pg_post['excerpt'] ) && 1 === preg_match_all( '/[.!?](\s|$)/', trim( $pg_post['excerpt'] ) ), "article {$pg_post['key']} has a one-sentence standfirst" );
	pg_assert( count( $pg_post['tags'] ) >= 2 && count( $pg_post['tags'] ) <= 3, "article {$pg_post['key']} has 2 or 3 tags" );
	$pg_blocks = parse_blocks( $pg_post['content'] );
	$pg_junk   = array_filter(
		$pg_blocks,
		function ( $block ) {
			return null === $block['blockName'] && '' !== trim( $block['innerHTML'] );
		}
	);
	pg_assert( array() === $pg_junk, "article {$pg_post['key']} is clean block markup" );
	$pg_names = array_count_values( array_filter( wp_list_pluck( $pg_blocks, 'blockName' ) ) );
	pg_assert( ( $pg_names['core/paragraph'] ?? 0 ) >= 4 && ( $pg_names['core/paragraph'] ?? 0 ) <= 6, "article {$pg_post['key']} has 4 to 6 paragraphs" );
	pg_assert_same( 1, $pg_names['core/heading'] ?? 0, "article {$pg_post['key']} has one subheading" );
}
foreach ( $pg_per_cat as $pg_cat => $pg_list ) {
	pg_assert_same( 3, count( $pg_list ), "3 articles in $pg_cat" );
}
pg_assert_same( 5, count( $pg_most_keys ), '5 articles are most read' );
pg_assert_same( 2, count( $pg_op_keys ), '2 articles are opinion' );
$pg_expected_images = array();
for ( $pg_i = 1; $pg_i <= 15; $pg_i++ ) {
	$pg_expected_images[] = sprintf( 'assets/images/news-%02d.webp', $pg_i );
}
pg_assert_same( $pg_expected_images, $pg_images, 'articles use news-01 to news-15 in order' );

// Helpers for rendered pages.
$pg_title_html = function ( $title ) {
	return wptexturize( $title );
};
$pg_query_html = function ( $html, $marker ) {
	if ( ! preg_match( '/<div[^>]*class="[^"]*\b' . preg_quote( $marker, '/' ) . '\b[^"]*"[^>]*>/', $html, $m, PREG_OFFSET_CAPTURE ) ) {
		return '';
	}
	$at  = $m[0][1];
	// The query's own list ends at the first </ul>; a query with no posts has none, so stop at
	// the end of its section and never read into the next row.
	$end     = strpos( $html, '</ul>', $at );
	$section = strpos( $html, '</section>', $at );
	if ( false === $end || ( false !== $section && $section < $end ) ) {
		return '';
	}
	return substr( $html, $at, $end - $at );
};
$pg_titles_in = function ( $html ) {
	preg_match_all( '/<h[1-6][^>]*wp-block-post-title[^>]*><a[^>]*>(.*?)<\/a>/s', $html, $m );
	return array_map( 'trim', $m[1] );
};
$pg_get = function ( $url ) {
	$res = wp_remote_get( $url, array( 'timeout' => 30 ) );
	return array( (int) wp_remote_retrieve_response_code( $res ), (string) wp_remote_retrieve_body( $res ) );
};

// The HTTP requests below run with WP_DEBUG and WP_DEBUG_LOG on, so notices land in the log.
$pg_config_path = ABSPATH . 'wp-config.php';
$pg_config_orig = (string) file_get_contents( $pg_config_path );
$pg_debug_log   = WP_CONTENT_DIR . '/debug.log';
$pg_probe_path  = WP_CONTENT_DIR . '/mu-plugins/pixelgrid-test-probe.php';
$pg_restore     = function () use ( $pg_config_path, $pg_config_orig, $pg_probe_path ) {
	file_put_contents( $pg_config_path, $pg_config_orig );
	if ( file_exists( $pg_probe_path ) ) {
		unlink( $pg_probe_path );
	}
};
register_shutdown_function( $pg_restore );
$pg_debug_defs = "if ( ! defined( 'WP_DEBUG' ) ) { define( 'WP_DEBUG', true ); }\nif ( ! defined( 'WP_DEBUG_LOG' ) ) { define( 'WP_DEBUG_LOG', true ); }\nif ( ! defined( 'WP_DEBUG_DISPLAY' ) ) { define( 'WP_DEBUG_DISPLAY', false ); }\n";
$pg_config_new = preg_replace( "/define\(\s*'WP_DEBUG',\s*false\s*\);\s*/", '', $pg_config_orig );
$pg_config_new = preg_replace( '/^<\?php\s*/', "<?php\n" . $pg_debug_defs, $pg_config_new, 1 );
file_put_contents( $pg_config_path, $pg_config_new );
$pg_log_lines = function () use ( $pg_debug_log ) {
	if ( ! file_exists( $pg_debug_log ) ) {
		return array();
	}
	clearstatcache();
	$out = array();
	foreach ( preg_split( '/\R/', (string) file_get_contents( $pg_debug_log ) ) as $line ) {
		if ( '' !== trim( $line ) && false === strpos( $line, 'Deprecated' ) ) {
			$out[] = $line;
		}
	}
	return $out;
};

// The log check proves itself: a probe notice raised over HTTP lands in the log.
if ( ! is_dir( dirname( $pg_probe_path ) ) ) {
	mkdir( dirname( $pg_probe_path ) );
}
file_put_contents( $pg_probe_path, "<?php\nif ( isset( \$_GET['pixelgrid_probe'] ) ) { trigger_error( 'pixelgrid test probe', E_USER_NOTICE ); }\n" );
$pg_probe_start = count( $pg_log_lines() );
$pg_get( add_query_arg( 'pixelgrid_probe', '1', home_url( '/' ) ) );
$pg_probe_new = array_slice( $pg_log_lines(), $pg_probe_start );
pg_assert( 1 === count( $pg_probe_new ) && false !== strpos( $pg_probe_new[0], 'pixelgrid test probe' ), 'the debug log catches a notice raised over HTTP' );
unlink( $pg_probe_path );

$pg_theme        = get_stylesheet();
$pg_header_block = '<!-- wp:template-part {"slug":"header","theme":"' . $pg_theme . '"} /-->';
$pg_styles_id    = (int) WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles( wp_get_theme(), true )['ID'];
$pg_styles_prev  = get_post( $pg_styles_id )->post_content;
$pg_all          = array(
	'design'  => true,
	'pages'   => true,
	'posts'   => true,
	'menu'    => true,
	'reading' => true,
);
$pg_log_start    = count( $pg_log_lines() );

$pg_made = pixelgrid_starter_create( 'news', $pg_all );
pg_assert_same(
	array(
		'pages' => 5,
		'posts' => 15,
		'menu'  => 1,
	),
	$pg_made,
	'news create makes 5 pages, 15 posts and 1 menu'
);
$pg_term_ids = array();
foreach ( array_keys( $pg_def['categories'] ) as $pg_slug ) {
	$pg_term                 = get_term_by( 'slug', $pg_slug, 'category' );
	$pg_term_ids[ $pg_slug ] = $pg_term ? (int) $pg_term->term_id : 0;
	pg_assert( $pg_term_ids[ $pg_slug ] > 0, "category $pg_slug exists after setup" );
}
$pg_post_ids = array();
foreach ( $pg_def['posts'] as $pg_post ) {
	$pg_post_ids[ $pg_post['key'] ] = pixelgrid_starter_find( 'post', 'news:post-' . $pg_post['key'] );
}

// Menu: the five sections by real term ID, then About and Tips.
$pg_menu_id = pixelgrid_starter_find( 'wp_navigation', 'news:menu' );
$pg_links   = array();
foreach ( parse_blocks( (string) get_post_field( 'post_content', $pg_menu_id ) ) as $pg_block ) {
	if ( 'core/navigation-link' === $pg_block['blockName'] ) {
		$pg_links[] = $pg_block['attrs'];
	}
}
$pg_cat_links = array_values(
	array_filter(
		$pg_links,
		function ( $attrs ) {
			return 'taxonomy' === ( $attrs['kind'] ?? '' ) && 'category' === ( $attrs['type'] ?? '' );
		}
	)
);
pg_assert_same(
	array( $pg_term_ids['world'], $pg_term_ids['business'], $pg_term_ids['technology'], $pg_term_ids['culture'], $pg_term_ids['sport'] ),
	array_map( 'intval', wp_list_pluck( $pg_cat_links, 'id' ) ),
	'menu has the 5 section links with the real term IDs'
);
pg_assert_same( array( 'About', 'Tips' ), array_slice( wp_list_pluck( $pg_links, 'label' ), 5 ), 'menu ends with About and Tips' );
pg_assert_same( 7, count( $pg_links ), 'menu has 7 links' );

// Home: every marked query points at its real category.
$pg_home_id = pixelgrid_starter_find( 'page', 'news:page-home' );
$pg_queries = array();
$pg_find_q  = function ( $blocks ) use ( &$pg_find_q, &$pg_queries ) {
	foreach ( $blocks as $block ) {
		if ( 'core/query' === $block['blockName'] && preg_match( '/pixelgrid-cat-([a-z-]+)/', $block['attrs']['className'] ?? '', $m ) ) {
			$pg_queries[ $m[1] ] = $block['attrs']['query']['taxQuery'] ?? null;
		}
		$pg_find_q( $block['innerBlocks'] );
	}
};
$pg_find_q( parse_blocks( (string) get_post_field( 'post_content', $pg_home_id ) ) );
foreach ( array( 'world', 'business', 'technology', 'culture', 'sport', 'most-read', 'opinion' ) as $pg_slug ) {
	pg_assert_same( array( 'category' => array( $pg_term_ids[ $pg_slug ] ) ), $pg_queries[ $pg_slug ] ?? null, "Home $pg_slug query has the real term ID" );
}

pg_assert_same( $pg_home_id, (int) get_option( 'page_on_front' ), 'front page is Home' );
pg_assert_same( pixelgrid_starter_find( 'page', 'news:page-latest' ), (int) get_option( 'page_for_posts' ), 'posts page is Latest' );
pg_assert( false !== strpos( do_blocks( $pg_header_block ), esc_html( wp_date( get_option( 'date_format' ) ) ) ), "header renders today's date" );

// Home rendered over HTTP.
list( $pg_code, $pg_home_html ) = $pg_get( home_url( '/' ) );
pg_assert_same( 200, $pg_code, 'Home returns 200' );
$pg_world_titles = array();
foreach ( $pg_per_cat['world'] as $pg_post ) {
	$pg_world_titles[] = $pg_title_html( $pg_post['title'] );
}
$pg_row = $pg_titles_in( $pg_query_html( $pg_home_html, 'pixelgrid-cat-world' ) );
sort( $pg_row );
$pg_want = $pg_world_titles;
sort( $pg_want );
pg_assert_same( $pg_want, $pg_row, 'Home World row lists exactly the 3 World articles' );
$pg_world_row = $pg_query_html( $pg_home_html, 'pixelgrid-cat-world' );
foreach ( $pg_per_cat['sport'] as $pg_post ) {
	pg_assert( false === strpos( $pg_world_row, $pg_title_html( $pg_post['title'] ) ), "Home World row has no Sport title ({$pg_post['key']})" );
}
$pg_most = $pg_titles_in( $pg_query_html( $pg_home_html, 'pixelgrid-cat-most-read' ) );
$pg_want = array();
foreach ( $pg_def['posts'] as $pg_post ) {
	if ( in_array( $pg_post['key'], $pg_most_keys, true ) ) {
		$pg_want[] = $pg_title_html( $pg_post['title'] );
	}
}
sort( $pg_most );
sort( $pg_want );
pg_assert_same( $pg_want, $pg_most, 'Home most read lists exactly the 5 most-read articles' );
preg_match_all( '/<div[^>]*class="[^"]*wp-block-post-terms[^"]*"[^>]*>(.*?)<\/div>/s', $pg_home_html, $pg_terms_out );
pg_assert( count( $pg_terms_out[1] ) > 0, 'Home renders post-terms labels' );
$pg_with_most = array_filter(
	$pg_terms_out[1],
	function ( $out ) {
		return false !== strpos( $out, 'Most read' );
	}
);
pg_assert_same( array(), array_values( $pg_with_most ), 'no post-terms label on Home says Most read' );

// The first World article: standfirst and a related row of the other World articles.
$pg_first = $pg_per_cat['world'][0];
list( $pg_code, $pg_single ) = $pg_get( get_permalink( $pg_post_ids[ $pg_first['key'] ] ) );
pg_assert_same( 200, $pg_code, 'first World article returns 200' );
pg_assert( false !== strpos( $pg_single, wptexturize( $pg_first['excerpt'] ) ), 'article shows its standfirst' );
$pg_related = $pg_titles_in( $pg_query_html( $pg_single, 'pixelgrid-related' ) );
sort( $pg_related );
$pg_want = array( $pg_title_html( $pg_per_cat['world'][1]['title'] ), $pg_title_html( $pg_per_cat['world'][2]['title'] ) );
sort( $pg_want );
pg_assert_same( $pg_want, $pg_related, 'related row lists the other 2 World articles and not the article itself' );

// Section front.
list( $pg_code, $pg_front ) = $pg_get( add_query_arg( 'cat', $pg_term_ids['world'], home_url( '/' ) ) );
pg_assert_same( 200, $pg_code, 'World section front returns 200' );
pg_assert( false !== strpos( $pg_front, 'pixelgrid-news-front' ), 'World section front uses the section-front template' );
pg_assert( false !== strpos( $pg_front, $pg_world_titles[0] ), 'World section front lists a World article' );

// User styles hold the News palette.
$pg_user_styles = json_decode( get_post( $pg_styles_id )->post_content, true );
$pg_user_colors = array();
foreach ( (array) ( $pg_user_styles['settings']['color']['palette'] ?? array() ) as $pg_entry ) {
	$pg_user_colors[ $pg_entry['slug'] ?? '' ] = strtoupper( $pg_entry['color'] ?? '' );
}
pg_assert_same( $news_colors, $pg_user_colors, 'user styles hold the News palette' );

foreach ( array( 'home', 'single', 'category' ) as $pg_slug ) {
	$pg_tpl = get_block_template( $pg_theme . '//' . $pg_slug, 'wp_template' );
	pg_assert( $pg_tpl && 'custom' === $pg_tpl->source, "setup customised the $pg_slug template" );
}
pg_assert_same( array(), array_slice( $pg_log_lines(), $pg_log_start ), 'no PHP notices logged during News setup and page views' );

$pg_removed = pixelgrid_starter_remove();
pg_assert( $pg_removed['removed'] >= 25, 'news remove removes at least 25 items (got ' . $pg_removed['removed'] . ')' );
pg_assert_same( array(), $pg_removed['kept'], 'news remove keeps nothing' );
pg_assert( false !== strpos( do_blocks( $pg_header_block ), 'Get a quote' ), 'header renders the theme default after removal' );
foreach ( array( 'single', 'archive', 'home' ) as $pg_slug ) {
	$pg_tpl = get_block_template( $pg_theme . '//' . $pg_slug, 'wp_template' );
	pg_assert( $pg_tpl && 'theme' === $pg_tpl->source, "the theme $pg_slug template is back after removal" );
}
$pg_cat_tpl = get_block_template( $pg_theme . '//category', 'wp_template' );
pg_assert( ! $pg_cat_tpl || 'custom' !== $pg_cat_tpl->source, 'no category template customisation is left' );
pg_assert_same( $pg_styles_prev, get_post( $pg_styles_id )->post_content, 'user styles are back to the pre-setup content' );
$pg_published = array();
foreach ( get_posts(
	array(
		'post_type'      => array( 'page', 'post' ),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
) as $pg_id ) {
	if ( 0 === strpos( (string) get_post_meta( $pg_id, '_pixelgrid_starter', true ), 'news:' ) ) {
		$pg_published[] = $pg_id;
	}
}
pg_assert_same( array(), $pg_published, 'no News page or article is published after removal' );
foreach ( array_keys( $pg_def['categories'] ) as $pg_slug ) {
	pg_assert( false === get_term_by( 'slug', $pg_slug, 'category' ), "empty starter category $pg_slug is gone after removal" );
}

// Review Focus 2: the World category is deleted after setup, then Home is viewed.
pg_assert( is_array( pixelgrid_starter_create( 'news', $pg_all ) ), 'news created again' );
$pg_world = get_term_by( 'slug', 'world', 'category' );
pg_assert( $pg_world && true === wp_delete_term( (int) $pg_world->term_id, 'category' ), 'World category deleted' );
$pg_log_before              = count( $pg_log_lines() );
list( $pg_code, $pg_after ) = $pg_get( home_url( '/' ) );
pg_assert_same( 200, $pg_code, 'Home returns 200 after the World category is deleted' );
pg_assert( false === strpos( $pg_after, 'Warning:' ) && false === strpos( $pg_after, 'Notice:' ) && false === strpos( $pg_after, 'Fatal error' ), 'Home shows no PHP message after the World category is deleted' );
pg_assert_same( array(), array_slice( $pg_log_lines(), $pg_log_before ), 'no new PHP log entries after the World category is deleted' );
pg_assert_same( array(), $pg_titles_in( $pg_query_html( $pg_after, 'pixelgrid-cat-world' ) ), 'the World row lists no posts once its category is gone' );
pg_assert( false !== strpos( $pg_after, 'pixelgrid-cat-business' ) && array() !== $pg_titles_in( $pg_query_html( $pg_after, 'pixelgrid-cat-business' ) ), 'the other rows still list their posts' );
$pg_removed = pixelgrid_starter_remove();
pg_assert( $pg_removed['removed'] >= 25, 'remove still runs after the World category is deleted (got ' . $pg_removed['removed'] . ')' );
pg_assert_same( array(), $pg_removed['kept'], 'remove after the World category is deleted keeps nothing' );

$pg_restore();

// One starting point at a time.
pg_assert( is_array( pixelgrid_starter_create( 'agency', array( 'pages' => true ) ) ), 'agency created' );
$pg_busy = pixelgrid_starter_create( 'news', $pg_all );
pg_assert( is_wp_error( $pg_busy ) && 'pixelgrid_variant_busy' === $pg_busy->get_error_code(), 'news while agency is active returns pixelgrid_variant_busy' );
pixelgrid_starter_remove();

pg_done();
