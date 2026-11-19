# Pixelgrid

A WordPress block theme for IT and technology companies. Every page sits on one pixel grid: bold condensed headlines, full-page colour sections that change as you scroll, and photos shown through pixel icons. Two starting points are included, IT services and Agency; more starting points come in later versions.

![Pixelgrid home page](screenshot.png)

## Requirements

- WordPress 6.6 or later (tested up to 7.1)
- PHP 7.4 or later

## Install

Copy this folder into `wp-content/themes/pixelgrid`, then activate **Pixelgrid** under Appearance, Themes.

## Starter content

On a new site the Dashboard shows a one-time notice. Click **Set up starter content**, or go to **Appearance, Pixelgrid setup** at any time, choose a starting point, tick what you want (pages, sample posts, menu, homepage setting, and optionally the starting point's design: styles, header, footer, templates) and click **Add starter content**. Each starting point is shown with a preview:

- **IT services**: Home, Services, About, Pricing, Contact and Notes pages, and six sample notes (posts). Its design is the theme default.
- **Agency**: Home, Work with six case studies, Services, Studio, Contact and Journal pages, and six sample journal posts. Its design changes the colours, fonts (Bricolage Grotesque display face), header, footer and blog template.

Nothing is added until you click, nothing is downloaded, and running it twice makes no duplicates. Applying a design backs up your current styles, header, footer and templates first. The IT services design is the theme default, so applying it does not change your Styles. Applying a design again only changes what you have not edited since setup. The same screen shows the starting point in use and can remove everything it added, restoring the backups and keeping edits you made after setup.

The setup follows the WordPress.org theme rules: core admin UI only, user consent before any content is created, no external calls, and a dismissible notice limited to the Dashboard and Themes screens.

## Build a page like the demo by hand

1. Create a page and pick the **Sections, no title** template.
2. In the block inserter, open Patterns, then **Pixelgrid pages**, and insert a layout (Home, About, Services, Pricing, Contact). Or build your own from **Pixelgrid sections**.
3. To use it as the home page: Settings, Reading, static front page.

## What is in the theme

| Area | Details |
|---|---|
| Section colours | Group block styles: Light, Bold, Bright, Dark, Soft, Alert. The page fades to the colour of the section in view. |
| Editable colours | Every section and the footer have four palette colours (background, text, hover, text on hover), changed in Appearance, Editor, Styles, Colours, Palette. Hover covers the cursor squares, button and link hover, and highlighted pixels. |
| Pixel mosaics | Image block styles that draw the photo through an 11 by 11 pixel icon: screen, cloud, padlock, speech bubble, chart, people, envelope, note, 404, or one that changes. Visitors can click pixels to redraw. |
| Other block styles | Lead and Big number paragraphs, Pixel bullets and Wordmarks lists, Pixel notch images, Price plan groups, Chips for categories and tags. |
| Templates | Blog, single post with sidebar, page, archive, search, 404, and Sections, no title. |
| Patterns | Sections and full pages in **Pixelgrid sections** and **Pixelgrid pages**, plus the Agency set in **Pixelgrid Agency**. All text is translatable. |
| Fonts | Instrument Sans, Instrument Serif, Sofia Sans Extra Condensed and Bricolage Grotesque, bundled locally (SIL Open Font License). |

The colour change, mosaics, background grid and section index need JavaScript. Without it, sections keep their own colour and pictures show as black and white images. The theme respects the visitor's reduced motion setting.

## Files

```
theme.json          colours, fonts, sizes, spacing, block styles
style.css           theme header, grid unit, tones, mosaic and layout rules
functions.php       asset loading, block styles, pattern categories
inc/                starter content setup page and the starting points (inc/variants/)
assets/js/          pixelgrid.js (grid, mosaics, tones, section index)
assets/fonts/       bundled woff2 files
assets/images/      original abstract images used by the patterns, setup previews
templates/  parts/  patterns/
```

## Release zip

The zip for WordPress.org contains the `pixelgrid` folder without `.git` and this README.

## License

GPLv2 or later. Font and image credits are in `readme.txt`.
