# Pixelgrid variants: design

Date: 2026-11-18
Status: approved by the owner (short design); builder check done, owner answered product pictures and release cadence
Theme: `wp-content/themes/pixelgrid` (repo `tajwar-t/pixel-grid`), currently 1.2.0

## 1. Intent

Pixelgrid is today a single-purpose block theme for an IT company. The owner wants one theme that serves four kinds of site, each chosen at setup with its own demo content:

1. **IT company**: the current design and content, kept as is.
2. **Agency**: a digital or creative agency.
3. **E-commerce**: an online shop on WooCommerce.
4. **News portal**: a magazine or news site.

Why: one product that reaches more buyers, where a site owner picks the variant closest to their business and starts from finished pages instead of a blank site.

Each variant has its own pages, colours, fonts, header, footer and sample content, but all four keep the Pixelgrid identity: the 24-cell pixel grid, pixel mosaics, colour sections that the page fades through on scroll, condensed uppercase headings, and the pixel-notch image treatment.

## 2. Goals, non-goals, anti-goals

**Goals**
- A site owner picks a variant on Appearance > Pixelgrid setup and gets that variant's look and pages in one step, with consent for each part.
- A site owner can change the look later without the setup page: Appearance > Editor > Styles > Browse styles lists the four variant looks.
- Each variant's pages and sections are also available as patterns, so they can be mixed (for example, a news row on an agency site).
- The theme still meets the WordPress.org Theme Directory requirements (see section 5).
- The shop variant works with WooCommerce's block templates: shop, product, product category and tag, product search, cart, checkout and order confirmation.

**Non-goals**
- Payment, shipping, tax or store settings. WooCommerce's own setup handles those.
- Real product, article or company data. All sample content is neutral placeholder content.
- Newsletter sign-up, contact forms, comments beyond core, ad slots, view counts, social sharing. These are plugin territory.
- A page builder, a settings framework or a theme options panel.

**Anti-goals** (would count as failure even if it works)
- Four themes glued together: variants that no longer share the grid, mosaics and section tones.
- Anything the theme does without the user asking: no content, style or plugin changes on activation.
- Any content or file fetched from outside the theme or WordPress (no remote demo files, no image downloads).
- Breaking existing sites: anyone on 1.2.0 who updates must keep the same look and content.

## 3. Constraints

- Block theme, `theme.json` version 3, WordPress 6.6 or later (tested up to 7.1), PHP 7.4 or later.
- All user-facing text translatable with the `pixelgrid` text domain; text lives in PHP patterns, not `.html` templates.
- Fonts and images bundled in the theme, under GPL-compatible licences, credited in `readme.txt`.
- No hardcoded post, term, menu or media IDs in theme files.
- The existing palette slugs (`paper-*`, `blue-*`, `sun-*`, `night-*`, `fog-*`, `red-*`, `footer-*`) and block style names (`is-style-tone-*`, `is-style-mosaic-*`, and so on) must keep working, because saved content uses them.
- Release zip excludes `docs/`, `README.md` and `.git` (add a `.gitattributes` with `export-ignore`).

## 4. Approaches considered

| Approach | Summary | Trade-off | Decision |
|---|---|---|---|
| A. One theme with a variant picker | Style variations for colours and fonts, variant header, footer and patterns, and a variant step in the existing setup page | One download and codebase; setup page grows | **Chosen** |
| B. One child theme per variant | Separate `pixelgrid-agency`, `pixelgrid-shop`, `pixelgrid-news` child themes | Clean separation, each listable on WordPress.org, but 4 downloads, no "pick one when importing", fixes repeated | Rejected: does not match "one theme, choose a demo" |
| C. Completely different design per variant | Each variant its own visual system | Much bigger, harder to keep coherent and directory-clean | Rejected by the owner, who chose a shared identity |

## 5. WordPress.org rules this design must respect

From the current Themes Team "Required" list (checked 2026-11-18):
- No demo imports as part of an admin page or onboarding. Creating posts and pages with `wp_insert_post()` is allowed **with user consent**. The setup page therefore calls it "starter content", does nothing until the user ticks options and clicks, makes no external calls and imports no files.
- Admin notices: through `admin_notices`, one-time or permanently dismissible, no popups on activation, must not leak onto other admin pages.
- Recommending plugins: only plugins hosted on WordPress.org, installed only by user action. WooCommerce qualifies. The theme links to the core plugin install screen; it never installs or activates a plugin itself.
- Plugin territory stays out: no custom post types, taxonomies, shortcodes, custom blocks, forms or analytics. WooCommerce products are WooCommerce's post type; the theme only creates sample products through WooCommerce's own API when WooCommerce is active and the user asks.
- Admin page uses core WordPress UI only.

## 6. What gets built

### 6.1 Variant registry

A PHP registry describes each variant, so the setup page, patterns and removal all read one source. One file per variant in `inc/variants/` returning an array:

| Key | Meaning |
|---|---|
| `slug`, `label`, `description` | `it`, `agency`, `shop`, `news`; translatable label and one-line description |
| `style` | The style variation file in `styles/` that holds its colours and fonts |
| `header`, `footer` | Pattern slugs for the variant's header and footer |
| `pages` | Page slug => title, pattern slug, page template |
| `front`, `posts_page` | Which page becomes the homepage and the posts page |
| `menu` | Ordered page slugs for the menu |
| `categories` | Category slug => name |
| `posts` | Sample posts (same shape as `inc/starter-posts.php` today) |
| `products` | Shop only: sample products (name, price, sale price, category, image, short text) |
| `requires` | Shop only: `woocommerce` |
| `preview` | Card image in `assets/images/variants/` |

The current IT content moves into `inc/variants/it.php` unchanged.

### 6.2 Look of each variant

All four share the grid, tones, mosaics and type rules. Each style variation overrides the palette (same 28 slugs, new colours and names) and may swap the heading or body font among bundled families.

| Variant | Palette direction | Type | Header and footer |
|---|---|---|---|
| IT company | Current (white, electric blue, yellow, black, fog, red) | Sofia Sans Extra Condensed headings, Instrument Sans body, Instrument Serif leads | Current |
| Agency | Off-white and ink, violet and hot coral accents, a lime "Bright" tone | New bundled grotesque display face for headings (OFL), Instrument Sans body | Large wordmark, minimal menu, "Start a project" button; footer with big closing line |
| E-commerce | Warm paper, deep green, tangerine, cream | Instrument Sans for headings in bold, condensed face for prices and labels | Two rows: announcement strip, then logo, product search, account, mini-cart; footer with shop links and payment note |
| News portal | Newsprint white, black, one red accent, grey | New bundled serif for headlines and body (OFL), condensed face for section labels and the masthead date line | Masthead with date and category menu; footer with category columns |

Exact colours are the builder's call within these directions, checked for 4.5:1 text contrast in every tone (section 9).

Tone labels become neutral so they make sense in every palette. Slugs do not change:

| Slug | New label |
|---|---|
| paper | Light |
| blue | Bold |
| sun | Bright |
| night | Dark |
| fog | Soft |
| red | Alert |

Palette names follow the new labels, for example "Bold section: hover".

### 6.3 Pages and patterns per variant

Every page is a pattern (category "Pixelgrid pages") built from section patterns (category per variant, for example "Pixelgrid Agency"). Shared sections (call to action, FAQ, steps, numbers) are reused with variant-specific text where needed.

**IT company** (existing): Home, Services, About, Pricing, Contact, Notes; 6 notes.

**Agency**
- Home: hero with changing mosaic, selected work, capabilities, client wordmarks, process steps, latest journal rows, call to action.
- Work: grid of 6 case study cards. Each case study is a **page** (child of Work) built from a "case study" page pattern; no custom post type.
- Services, Studio (team and values), Contact, Journal (posts page).
- Sample content: 6 case study child pages, 6 journal posts.

**E-commerce** (needs WooCommerce)
- Shop home: hero offer, featured products, shop by category tiles, promo split, best sellers, value strip (delivery, returns, support), call to action.
- Theme templates for WooCommerce, styled on the grid: `archive-product`, `single-product`, `taxonomy-product_cat`, `taxonomy-product_tag`, `product-search-results`, `page-cart`, `page-checkout`, `order-confirmation`, plus a `mini-cart` part.
- About, Contact, Journal pages.
- Sample content: 12 products in 4 product categories, with regular and some sale prices, short descriptions and images; 3 journal posts.
- Patterns that use WooCommerce blocks are registered only when WooCommerce is active (registered in PHP on `init`, not in the `patterns/` folder), so the editor never shows broken blocks.

**News portal**
- Home: lead story with large image, top stories grid, one row per category (5 categories), "Most read" list (the 5 latest posts in a chosen category; the theme does not count views), opinion column, call to action for an about or tips page.
- Category archive template styled as a section front; single post with byline, standfirst, related row.
- About, Contact, Tips (how to send a story) pages.
- Sample content: 15 articles across World, Business, Technology, Culture and Sport, with excerpts, tags and images.
- Query blocks that need a category get its real term ID filled in when the setup creates the categories; the inserter versions of those patterns show the latest posts with no category filter.

### 6.4 Setup page (Appearance > Pixelgrid setup)

Extends the existing page:

1. **Choose a variant**: four cards (preview image, label, description) as radio buttons. The shop card says "Needs WooCommerce" and, when it is not active, links to `plugin-install.php?s=woocommerce&tab=search&type=term` instead of enabling.
2. **Choose what to add** (all ticked by default):
   - Apply this variant's design (style variation, header and footer)
   - Pages
   - Sample posts (and products, for shop)
   - Menu
   - Homepage and posts page setting
3. **Add starter content** button.
4. **Starter content on this site**: list grouped by type with edit links, and the variant in use.
5. **Remove starter content**: confirmation tick, then removes content and undoes the design change.

Rules:
- Variant-specific templates (section 6.7) are applied the same way as the header and footer: setup saves site-level customisations of those templates, marked as starter content. Theme template files stay neutral and unchanged, so IT sites and updated 1.2.0 sites keep their look.
- If the header, footer or any template that setup would customise is **already customised** in the Site Editor, setup saves a copy of the existing customisation first and removal puts that copy back. Nothing the user made is deleted.
- One variant's starter content at a time. If starter content exists, the other cards are disabled with the note "Remove the current starter content to switch". The design alone can still be changed in Styles.
- Applying the design writes the variant's style variation into the site's user global styles (what Browse styles does), saves the previous user global styles, and saves a copy of exactly what setup wrote.
- On removal, styles are restored only if the user global styles still match what setup wrote. If the user changed styles after setup, removal leaves the styles alone and says so on screen, so later edits are never lost.
- Header and footer: templates keep referencing the `header` and `footer` parts. Applying a variant creates site-specific (database) customisations of those two parts containing the variant's header or footer pattern, marked as starter content. Removal deletes those customisations, which falls back to the theme's default header and footer.
- Every created item carries `_pixelgrid_starter` = `{variant}:{key}`, so running again creates no duplicates and removal only touches what setup created.
- Existing 1.2.0 sites: starter content marked with the old key format (`page-home` and so on, no variant prefix) is treated as the IT variant.

### 6.5 Templates that differ by variant

Theme files keep today's templates. Each variant can carry template overrides that setup applies as site-level customisations (and removal deletes or restores, per 6.4):

| Template | IT | Agency | Shop | News |
|---|---|---|---|---|
| `home` (posts page) heading | "Notes" (today) | "Journal" | "Journal" | none: posts page is the front of the site's "Latest" |
| `single` | today | today | today | byline, standfirst, related row |
| `category` | `archive` (today) | `archive` | `archive` | section front: lead story plus grid |

The posts-page heading text comes from a per-variant pattern (`pixelgrid/blog-heading-{variant}`), so it stays translatable. The theme's own `home.html` keeps `pixelgrid/blog-heading` (IT wording) as the default.

### 6.6 Notice

Unchanged behaviour: one-time, permanently dismissible, Dashboard and Themes screens only, fresh sites only. The wording mentions that there are four starting points.

### 6.7 Images and fonts

- More original abstract images generated with code for the theme (about 30 in total, so agency and news do not repeat the same pictures). Same greyscale style, own work under GPL.
- **Shop product pictures are pixel-art product illustrations** (owner's choice): simple objects drawn on the pixel grid with code (for example mug, lamp, chair, headphones, bag, plant, watch, backpack), in the shop palette, one per sample product. Own work under GPL.
- Up to two new OFL font families (Agency display, News serif), latin and latin-ext, credited in `readme.txt`.
- Product and article images get the pixel-notch treatment; mosaics stay for heroes and feature sections.

## 7. Decisions made with the owner

- Approach A, one theme with a variant picker.
- Shared Pixelgrid identity, not separate visual systems.
- E-commerce built on WooCommerce.
- IT company kept as a fourth variant, unchanged.
- WordPress.org requirements remain binding.
- Build order: variant system and setup picker, then Agency, then News, then Shop.
- **Each phase is its own public release** (owner's choice), with version number, changelog, readme credits and release zip:
  - 1.3.0: variant system, neutral tone labels, setup picker with the IT variant only (other cards hidden until they exist)
  - 1.4.0: Agency
  - 1.5.0: News portal
  - 1.6.0: E-commerce
- Each release must work on its own and stay compatible with the next. The `_pixelgrid_starter` key format (`{variant}:{key}`) and the saved backups (styles, parts, templates) are fixed in 1.3.0, so a site that set up a variant on any of these releases removes cleanly on later ones.
- Product pictures: pixel-art product illustrations.

## 8. Calls made by the designer (check these)

- Variant templates go through site-level customisations at setup, not theme file changes (6.4, 6.5).
- Existing header, footer and template customisations are backed up and restored, never deleted (6.4).
- Removal keeps style changes the user made after setup (6.4).
- Images are generated with code, so they are own work under GPL; no AI image generation and no stock photos (6.7).

## 9. Left to the builder

- Exact colours per variant within the directions above, subject to contrast checks.
- Which OFL families to bundle for Agency display and News serif.
- Copy for all sample content, in the same plain, specific voice as the IT variant.
- Exact section lineup inside each page pattern, as long as the sections listed above exist.
- Preview card images: captured from the real variant home pages.
- Whether the theme `screenshot.png` stays IT or becomes a 2 by 2 of the variants (a real render either way).

## 10. Done means

- Each variant set up on a fresh install from the setup page matches its description, with no PHP notices under `WP_DEBUG` and no console errors from the theme.
- Every pattern, template and part opens in the editor with no invalid blocks.
- Every tone of every variant meets 4.5:1 text contrast.
- Remove leaves the site as it was before setup: content, reading settings, header, footer and templates (restoring earlier customisations), and styles unless the user changed them after setup.
- A site that set up a variant on one release removes cleanly after updating to the next.
- A 1.2.0 site updated to the new version looks and behaves the same until the owner chooses otherwise.
- The WordPress.org checker shows no errors outside `.git`, and any warnings are reviewed.
- Shop: browsing, adding to cart, cart and checkout pages render styled with WooCommerce active. With WooCommerce inactive, nothing breaks and no shop patterns appear.
