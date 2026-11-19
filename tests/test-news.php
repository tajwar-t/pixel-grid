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
for ( $i = 1; $i <= 15; $i++ ) {
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

pg_done();
