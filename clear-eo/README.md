# CLEAR-EO WordPress theme

WordPress version of the static CLEAR-EO redesign (`no-wp/clear-eo`). It has the same design and features, and editors use the normal WordPress admin instead of Decap CMS.

## Install

1. Zip the `clear-eo` folder (or use `clear-eo.zip`), then go to **Appearance → Themes → Add New → Upload Theme** and activate it.
2. Go to **Tools → CLEAR-EO content → Import CLEAR-EO content**. This adds the applications, partners, newsletter items, events and webinars from the static site, with their pictures, and moves WordPress's sample "Hello world!" post to the bin. Items imported before are skipped, so running it again doesn't duplicate anything.
3. Under **Settings → Permalinks**, pick any setting except "Plain" so posts get readable addresses like `/events/ditto-conference/`.

The home page is always the one-page layout (`front-page.php`), whatever **Settings → Reading** says.

## Where each part is edited

| Part of the site | Where |
|---|---|
| Top banner, key facts, About, Virtual Observatory, Consortium texts, women box, collaborations, What's new heading, default header images, footer | **Appearance → Customize → CLEAR-EO page sections** (live preview) |
| Application tabs and their own pages | **Applications**: excerpt = panel summary, featured image = background, editor = full page, *Application panel* box = tab label, colour, location, list/tag sections. *Order* sets the tab order. |
| Partner and affiliated-entity cards | **Partners**: featured image = logo, *Partner card* box = country, website, role, hover description, "affiliated entity". *Order* sets the card order. |
| Newsletter items | **Newsletter** (WordPress Posts): excerpt = card summary, featured image = article header, *What's new card* box = card image and original-post link |
| Events | **Events**: start/end date, location, event website, card image |
| Webinars | **Webinars**: date, time (`14:00–15:30 CET`), registration, recording, speakers, card image |
| Menus | **Appearance → Menus**: *Top menu* and *Footer: Explore*. Links like `#about` go to that home-page section from every page. Without a menu, the home-page sections are listed. |
| Logo | **Customize → Site Identity** (defaults to the CLEAR-EO logo). The part of the site title after the first hyphen is highlighted, as in CLEAR**-EO**. |

In short text fields, `**double asterisks**` make text bold and `[text](https://link)` makes a link, as in the static site.

In the block editor, the extra fields of each type (Event details, Webinar details, Application panel, Partner card, What's new card) are a panel in the sidebar's post settings, and are saved together with the post. The classic editor shows them in a box under the text.

## Features carried over

- Light and dark themes that follow the system setting, with a toggle the browser remembers.
- Application tabs with keyboard navigation, and a page for each application.
- Partner cards whose role and description slide up on hover (shown in the card on touch screens).
- What's new merges newsletters, events and webinars, newest first, with type filters. Events and webinars that haven't ended get an **Upcoming** badge, and ended ones a **Past** badge. One with no start date gets neither.
- Cards open a reader window. Its address `#news/<date>-<title>` can be shared, and old links from the static site still work. Each item also has its own page for search engines and for sharing.
- Upcoming events and webinars get **Add to Outlook / Teams calendar** and **Download .ics** links (`?clear_eo_ics=<id>`). A webinar time like `14:00–15:30 CET` is added with its hours (Brussels time, or UTC); anything else is added as all-day.
- Cards with no page link to the registration, recording or website, or to the default link set in the Customizer.
- Scroll reveal, scroll-spy menu, reduced-motion support, and an EU funding footer.

WordPress handles what the static site's admin add-ons used to do: preview, revisions, the media library, pasting from Word in the block editor, and publish/delete.

## Files

```
style.css            design (the static site's CSS + a WordPress layer at the end)
functions.php        loads inc/
inc/options.php      Customizer fields; defaults come from import/content/*.json
inc/post-types.php   Events, Webinars, Applications, Partners; Posts relabelled Newsletter
inc/meta-boxes.php   extra fields under the editor
inc/content.php      What's new logic, cards, reader, calendar links, .ics
inc/importer.php     Tools → CLEAR-EO content
inc/markdown.php     the static site's Markdown (md / mdBlock), ported to PHP
front-page.php       one-page home
single*.php, page.php, index.php, 404.php
assets/js/main.js    theme toggle, menu, tabs, filters, reader window, reveal
import/              content and pictures of the static site, for the importer
```
