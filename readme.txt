=== Pixelgrid ===
Contributors: bitbirdssolutions
Requires at least: 6.6
Tested up to: 7.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A block theme for IT companies, design studios, news sites and shops, built on one pixel grid.

== Description ==

Pixelgrid is a block theme for IT companies, design studios, news sites and shops. Every page sits on one pixel grid: bold headlines, full-page colour sections that change as you scroll, and photos shown through pixel icons.

Four starting points are included: IT services, which uses the theme's default design; Agency, a design studio with its own colours, the Bricolage Grotesque display face, header, footer and journal template; News, a city newspaper with the Newsreader serif, a masthead with today's date, section fronts and related articles; and Shop, a small shop for everyday things with sample products, which needs the WooCommerce plugin. Each adds its own pages and sample posts, only when you choose.

Features:

* Six section colours (Light, Bold, Bright, Dark, Soft, Alert), applied as Group block styles. As a visitor scrolls, the whole page fades to the colour of the section in view.
* Pixel mosaics: give an Image block one of the "Pixel" styles and the photo is drawn through an 11 by 11 pixel icon (screen, cloud, padlock, speech bubble, chart, people, envelope, heart, note, 404, or one that changes). Visitors can click pixels to redraw the picture.
* A faint background grid that lights up under the cursor.
* A small section index on the right edge, built from the headings of the colour sections on the page.
* Ready-made section and page patterns: hero, service sections, recent work, client quote, numbers, steps, principles, team, pricing plans, questions and answers, contact and call to action.
* Templates for the blog, single posts with a sidebar, pages, archives, search and 404, plus a "Sections, no title" page template for landing pages.
* WooCommerce templates on the same grid: shop, product categories and tags, single product, product search, cart, checkout, order confirmation and a mini-cart. They work with every starting point, whenever WooCommerce is active.

= Starter content =

On a new site, a notice on the Dashboard offers to set up the pages shown in the theme preview. You can also go to Appearance, Pixelgrid setup at any time. Choose a starting point (IT services, Agency, News or Shop), tick what you want and click "Add starter content". Each starting point is shown with a preview.

IT services adds:

* Pages: Home, Services, About, Pricing, Contact and Notes, built from the theme's patterns
* Six sample notes (posts) with categories, tags and images

Agency adds:

* Pages: Home, Work, Services, Studio, Contact and Journal, plus six case studies under Work, built from the theme's patterns
* Six sample journal posts with categories, tags and images

News adds:

* Pages: Home, Latest, About, Contact and Tips. Home is a front page with a lead story, top stories, a row for each section, most read and opinion, filled with the new categories.
* 15 sample articles in World, Business, Technology, Culture and Sport, with standfirsts, tags and images. An article can be in more than one category.
* The design adds a masthead with today's date, section fronts (category pages) and a row of related articles under each article.

"Most read" is a category you fill yourself: the theme does not count page views. Add an article to the Most read category to list it there; a few sample articles start in it. The category is not shown in the category labels on articles.

Shop adds:

* Pages: Home, About, Contact and Journal, plus a Shop link in the menu. The shop, cart, checkout and account pages are WooCommerce's own pages, shown with the theme's grid templates.
* 12 sample products in four categories (Kitchen, Home, Outdoor and Tech), each with a pixel-art picture. Four are on sale and four are featured on the Home page.
* 3 sample journal posts with images.
* The design changes the colours, header (with product search, account link and a mini-cart), footer and the posts page template.

Needs WooCommerce: the Shop starting point can only be chosen while the WooCommerce plugin is active. The theme does not install WooCommerce; the setup screen links to the plugin installer instead. Prices are plain numbers: the currency, payments and shipping are WooCommerce settings.

All starting points also add:

* A menu linking the new pages (for News: the five sections, About and Tips; for Shop: Shop, About, Journal and Contact)
* Optionally, the new Home page as the homepage and the blog page (Notes, Journal or Latest) as the posts page
* Optionally, the starting point's design: its styles, header, footer and templates

Nothing is added until you click the button, and nothing is downloaded: the content is built from the theme's own patterns and images. Clicking again does not create duplicates. When you apply a design, your current styles, header, footer and templates are backed up first. The IT services starting point uses the theme's default design, so applying its design does not change your Styles. The Agency design changes the colours, fonts, header, footer and blog template. The News design changes the colours and fonts (the Newsreader serif for headlines and articles), the header, the footer, and the templates for the posts page, single articles and category pages. Applying a design again only changes the styles, header, footer and templates you have not edited since setup. The Shop design changes the colours, the header, the footer and the template for the posts page; the WooCommerce pages keep the theme's own grid templates. The same screen shows the starting point in use, lists what was added and has a "Remove starter content" button, which moves the starter pages, posts, products and menu to the trash, deletes the starter images, removes the starter categories, product categories and tags that are no longer used (including by drafts), puts your homepage setting back and restores the backed-up design. Edits you made to the design after setup are kept; if your styles are kept, the styles you had before setup can still be found in Styles, Revisions.

= Setting up a page like the demo by hand =

1. Create a page and choose the "Sections, no title" template.
2. Open the block inserter, go to Patterns, then "Pixelgrid pages", and insert a page layout. Or build the page from the "Pixelgrid sections" category.
3. To set it as the home page, go to Settings, Reading, and choose it as the static front page.

= Design notes and limitations =

* The colour change on scroll, the pixel mosaics, the background grid and the section index need JavaScript. Without it, sections keep their own background colour and pictures show as plain black and white images.
* Pictures in Pixel mosaics and in card images are shown in black and white by design. Product pictures keep their colours.
* The theme respects the "reduce motion" setting of the visitor's device.
* Placeholder text in the patterns, such as the email address, phone number and client names, is meant to be replaced.

== Frequently Asked Questions ==

= Does this theme support any plugins? =

Pixelgrid works with plugins that follow WordPress standards. For a contact form, add a form plugin and place its block in the contact page.

= Does the theme work with WooCommerce? =

Yes. With WooCommerce active, the shop, product, cart, checkout and account pages are styled on the theme's grid, whichever starting point you use, or none. The Shop starting point needs WooCommerce, and the theme does not install it for you. Currency, payments, shipping and taxes are WooCommerce settings, under WooCommerce, Settings.

= How do I change a section's colour? =

To switch a section to another colour set, select the Group block that wraps it, open the Styles panel and pick one of the section styles.

= How do I change the colours themselves? =

Go to Appearance, Editor, Styles, Colours, Palette. Open the menu next to Theme and choose "Show details" to see each colour by name. Every section (Light, Bold, Bright, Dark, Soft, Alert) and the footer has four colours:

* background
* text
* hover: the squares that light up under the cursor, button and link hover, and the highlighted pixels in pictures
* text on hover: text shown on top of the hover colour, such as a hovered button label

Changes apply to the whole site. Keep enough contrast between each background and its text, and between each hover colour and its text on hover.

== Changelog ==

= 1.6.0 =
* Released: November 22, 2026

New Shop starting point for a small shop of everyday things: Home, About, Contact and Journal pages, 12 products in four categories with pixel-art pictures, and 3 journal posts, with its own design: colours, a header with a mini-cart, footer and posts page template. It needs WooCommerce, which the theme does not install; without it the Shop is shown on the setup screen but cannot be chosen, with a link to the plugin installer. WooCommerce's shop, product, cart, checkout and order pages are now styled on the grid for every starting point, with new Pixelgrid Shop patterns and a Pixel heart mosaic. Starter content can now include products, and removing it moves them to the trash and deletes the product categories it created once nothing else uses them. Menus can link to the WooCommerce shop page, and the link is only added while that page is published. Picture mosaics now fit narrow screens, so phones no longer scroll sideways. Running setup again no longer resets a category you picked for a row on the starter Home page, and removal keeps categories and tags that drafts or scheduled posts still use. The Dashboard notice now says there are four starting points and when the Shop needs WooCommerce. The screenshot now shows the home pages of all four starting points.

= 1.5.0 =
* Released: November 21, 2026

New News starting point for a city newspaper: Home, Latest, About, Contact and Tips pages and 15 articles in five sections, with its own design: colours, the Newsreader serif, a masthead with today's date, footer, and templates for the posts page, articles and category pages. Starter posts can now be in several categories, and menus can link to categories. The rows on the News Home page are filled with the real categories when you set it up. A new "Today's date" block binding shows the current date, and articles end with a row of related articles that share a category. The "Most read" category is hidden from category labels, since it is a list you keep yourself. Removing the starter content now also removes the tags that setup created, for all starting points, when no other post uses them. Starter posts are now dated from today, so the newest one is the lead story.

= 1.4.0 =
* Released: November 19, 2026

New Agency starting point for a small design studio: 12 pages (Home, Work with six case studies, Services, Studio, Contact and Journal) and 6 journal posts, with its own design: colours, the Bricolage Grotesque display face for headings, header, footer and journal template. The setup screen now shows a preview of each starting point and says "posts" instead of "notes", since not every starting point has a notes blog. Applying a design again no longer brings back a header, footer or template you deleted or trashed after setup. Templates added by a design get their proper names, such as "Blog Home". When you remove the starter content, the notice lists the parts you edited and kept by name (Header, Footer, Blog Home), and only points to Styles, Revisions when there are revisions. A missing or damaged style file now shows an error and changes nothing. A design's styles now take effect even when the site's styles record was created without a signed-in user.

= 1.3.0 =
* Released: November 18, 2026

Setup now asks for a starting point. IT services is the first; more come in later versions. You can also apply a starting point's design (styles, header, footer and templates). Your own versions are backed up first and restored when you remove the starter content, while edits you make after setup are kept, also when you apply the design again. Applying the IT services design does not change your Styles. The section colours are renamed Light, Bold, Bright, Dark, Soft and Alert (the colours are the same).

= 1.2.0 =
* Released: November 18, 2026

Added Appearance, Pixelgrid setup: optional starter pages, notes and menu, added only when you choose, with a one-time notice on new sites. Pattern links now work with every permalink setting.

= 1.1.0 =
* Released: November 17, 2026

Every section colour, including its hover colour, and the footer colours can now be changed in Styles, Colours, Palette.

= 1.0.0 =
* Released: November 17, 2026

Initial release

== Copyright ==

Pixelgrid WordPress Theme, (C) 2026 bitBirds Solutions
Pixelgrid is distributed under the terms of the GNU GPL.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

== Resources ==

Instrument Sans
Copyright 2022 The Instrument Sans Project Authors (https://github.com/Instrument/instrument-sans)
License: SIL Open Font License, Version 1.1
Source: https://fonts.google.com/specimen/Instrument+Sans

Instrument Serif
Copyright 2022 The Instrument Serif Project Authors (https://github.com/Instrument/instrument-serif)
License: SIL Open Font License, Version 1.1
Source: https://fonts.google.com/specimen/Instrument+Serif

Sofia Sans Extra Condensed
Copyright 2019 The Sofia Sans Project Authors (https://github.com/lettersoupfonts/sofiasans)
License: SIL Open Font License, Version 1.1
Source: https://fonts.google.com/specimen/Sofia+Sans+Extra+Condensed

Bricolage Grotesque
Copyright 2022 The Bricolage Grotesque Project Authors (https://github.com/ateliertriay/bricolage)
License: SIL Open Font License, Version 1.1
Source: https://fonts.google.com/specimen/Bricolage+Grotesque

Newsreader
Copyright 2020 The Newsreader Project Authors (http://github.com/productiontype/Newsreader)
License: SIL Open Font License, Version 1.1
Source: https://fonts.google.com/specimen/Newsreader

Images in assets/images (plate-1.webp to plate-8.webp, agency-01.webp to agency-12.webp, news-01.webp to news-18.webp, shop-13.webp to shop-15.webp)
Copyright 2026 bitBirds Solutions. Original abstract images made for this theme, generated by code (tools/make-plates.php in the source repository).
License: GPLv2 or later

Product pictures in assets/images (shop-01.webp to shop-12.webp)
Copyright 2026 bitBirds Solutions. Original pixel-art product illustrations made for this theme, drawn on a 16 by 16 grid and generated by code (tools/make-products.php in the source repository).
License: GPLv2 or later

Setup screen previews in assets/images/variants (it.webp, agency.webp, news.webp, shop.webp)
Copyright 2026 bitBirds Solutions. Captures of the theme's own starting points using its bundled images.
License: GPLv2 or later

Screenshot
Copyright 2026 bitBirds Solutions. A capture of the home pages of the theme's four starting points (IT services, Agency, News and Shop), using only the theme's own bundled images.
License: GPLv2 or later
