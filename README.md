# Pixelgrid

A WordPress block theme for IT and technology companies. Every page sits on one pixel grid: bold condensed headlines, full-page colour sections that change as you scroll, and photos shown through pixel icons.

![Pixelgrid home page](screenshot.png)

## Requirements

- WordPress 6.6 or later (tested up to 7.1)
- PHP 7.4 or later

## Install

Copy this folder into `wp-content/themes/pixelgrid`, then activate **Pixelgrid** under Appearance, Themes.

## Starter content

On a new site the Dashboard shows a one-time notice. Click **Set up starter content**, or go to **Appearance, Pixelgrid setup** at any time, tick what you want (pages, sample notes, menu, homepage setting) and click **Add starter content**. Nothing is added until you click, nothing is downloaded, and running it twice makes no duplicates. The same screen can remove everything it added.

The setup follows the WordPress.org theme rules: core admin UI only, user consent before any content is created, no external calls, and a dismissible notice limited to the Dashboard and Themes screens.

## Build a page like the demo by hand

1. Create a page and pick the **Sections, no title** template.
2. In the block inserter, open Patterns, then **Pixelgrid pages**, and insert a layout (Home, About, Services, Pricing, Contact). Or build your own from **Pixelgrid sections**.
3. To use it as the home page: Settings, Reading, static front page.

## What is in the theme

| Area | Details |
|---|---|
| Section colours | Group block styles: Paper, Blue, Sun, Night, Fog, Red. The page fades to the colour of the section in view. |
| Editable colours | Every section and the footer have four palette colours (background, text, hover, text on hover), changed in Appearance, Editor, Styles, Colours, Palette. Hover covers the cursor squares, button and link hover, and highlighted pixels. |
| Pixel mosaics | Image block styles that draw the photo through an 11 by 11 pixel icon: screen, cloud, padlock, speech bubble, chart, people, envelope, note, 404, or one that changes. Visitors can click pixels to redraw. |
| Other block styles | Lead and Big number paragraphs, Pixel bullets and Wordmarks lists, Pixel notch images, Price plan groups, Chips for categories and tags. |
| Templates | Blog, single post with sidebar, page, archive, search, 404, and Sections, no title. |
| Patterns | 18 sections and 5 full pages. All text is translatable. |
| Fonts | Instrument Sans, Instrument Serif and Sofia Sans Extra Condensed, bundled locally (SIL Open Font License). |

The colour change, mosaics, background grid and section index need JavaScript. Without it, sections keep their own colour and pictures show as black and white images. The theme respects the visitor's reduced motion setting.

## Files

```
theme.json          colours, fonts, sizes, spacing, block styles
style.css           theme header, grid unit, tones, mosaic and layout rules
functions.php       asset loading, block styles, pattern categories
inc/                starter content setup page and sample notes
assets/js/          pixelgrid.js (grid, mosaics, tones, section index)
assets/fonts/       bundled woff2 files
assets/images/      original abstract images used by the patterns
templates/  parts/  patterns/
```

## Release zip

The zip for WordPress.org contains the `pixelgrid` folder without `.git` and this README.

## License

GPLv2 or later. Font and image credits are in `readme.txt`.
