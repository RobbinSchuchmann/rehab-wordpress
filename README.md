# Rehab WordPress — block library + multi-tenant theme

A WordPress dev environment that serves as the design + content platform for
Diamond Rehab Thailand and (in time) six sister rehab clinics. One shared
parent theme, brand child themes, and a custom Gutenberg block plugin so
non-technical writers can author SEO long-form content directly in the
editor.

## What's in this repo

```
dev-stack/
  docker-compose.yml             WP 6.4 + MariaDB on localhost:8081
  tools/recover-demo.js                Playwright helper: canonicalize block markup
                                 via wp.data after bulk-edit migrations
  screenshot-*.js                Playwright helpers for visual QA
  wp-content-dev/
    themes/
      rehab-parent/              Shared parent theme — tokens, layout, header,
                                 footer, header.php, footer.php, single.php,
                                 template-treatment.php, template-article.php
                                 STYLE-GUIDE.md
      diamond-child/             Diamond brand: token overrides, logo, plus
                                 the bespoke page-homepage.php template +
                                 24 section partials in template-parts/homepage/
      anker-child/, cherrywood-child/, villa-consano-child/,
      imani-child/, afkick-child/, ankerhuis-fi-child/
                                 Six other brand themes (token-only overrides)
    plugins/
      rehab-blocks/              Native Gutenberg block library — 30 blocks:
                                 hero, prose, cards-grid/card, faq/faq-item,
                                 cta, comparison, tabs/tab, steps/step,
                                 features-list/feature, columns/column,
                                 team/team-member, testimonials/testimonial,
                                 gallery, accommodation, founder-bio, marquee,
                                 media-mentions, phone-cta, contact-form,
                                 programs-list/program, map.
                                 Built with @wordpress/scripts (webpack).
    mu-plugins/
      aa-block-builders.php      PHP helpers that emit canonical block markup
                                 (rehab_block_hero/prose/cards_grid/faq/cta).
                                 Used by migration tasks.
      zz-redirects.php           Mirror upstream live-site redirects.
      zz-oneshot.php             Maintenance task runner triggered by
                                 ?rehab_oneshot=<task> URL param.
                                 Tasks: rebuild-cocaine-page, rebuild-ice-page,
                                 rebuild-alltreats-page, media-mirror,
                                 set-homepage-template, find-block, etc.
    migrate-data/                JSON files extracted from live site, used as
                                 source content for migration scripts.
```

## Getting started

You need Docker and Node 18+.

```bash
# 1. Start the stack
cd dev-stack/
docker compose up -d

# 2. Build the blocks plugin (first time + after any block change)
cd wp-content-dev/plugins/rehab-blocks
npm install
npm run build           # one-shot
# or:
npm run start           # watch mode

# 3. Visit
# http://localhost:8081/             — public site
# http://localhost:8081/wp-admin     — admin (devadmin / dev123!)
```

The stack binds publicly to `0.0.0.0:8081`, so on a remote server it's also at
`http://<server-ip>:8081/`. WP_HOME and WP_SITEURL are computed dynamically
from `$_SERVER['HTTP_HOST']` so the URL adapts to whatever host you visit by.

## Architecture

**Parent → child split.** All shared CSS / templates / hooks live in
`themes/rehab-parent/`. Each brand has a child theme with token overrides
(in `style.css`) plus brand-specific assets (logo, favicon). To onboard a new
brand: copy `diamond-child/`, swap colors in `style.css`, swap the logo SVG.

**Block library is brand-agnostic.** All `rehab/*` blocks read from CSS
custom properties (`--rehab-sage`, `--rehab-tan`, etc) defined in
`themes/rehab-parent/assets/css/tokens.css`. Brand child themes override
these tokens. So one block library renders correctly across all 7 brands
without per-brand forks.

**Treatment pages are block-rendered.** Writers edit them in Gutenberg using
the `rehab/*` blocks. Migration tasks
(`?rehab_oneshot=rebuild-cocaine-page` etc) replay the canonical content
from `migrate-data/*.json` if the post needs to be reset.

**Homepage is PHP-rendered.** `themes/diamond-child/page-homepage.php`
orchestrates 24 section partials in `template-parts/homepage/`. This is the
designer-owned brand showcase — not writer-editable. Set the homepage page
to use the "Homepage Redesign" template (or call
`?rehab_oneshot=set-homepage-template`).

## After bulk DB changes

Block markup saved by PHP migration tasks may not be fully canonical
(e.g. attribute order, whitespace). Run the canonicalizer:

```bash
node tools/recover-demo.js <post_id>
```

This loads the post in Gutenberg via Playwright, lets `wp.data` re-render
each block to its canonical save markup, and saves. Avoids "Attempt block
recovery" warnings in the editor.

## CSS / design system

Design language source: the polished `drt-` namespace styles live in
`themes/diamond-child/assets/css/homepage/*.css` (ported from a separate
Diamond reference theme). Block styles in `plugins/rehab-blocks/src/*/style.scss`
mirror the same patterns under the `rehab-` namespace, so the
PHP-rendered homepage and block-rendered treatment pages share the same
visual vocabulary.

When adding a new block, source design patterns from
`homepage-components.css` first before inventing something new.

## Common tasks

| Need to... | Do this |
|---|---|
| Edit homepage layout | `themes/diamond-child/page-homepage.php` + section partials |
| Edit homepage CSS | `themes/diamond-child/assets/css/homepage/*.css` |
| Add a block | `plugins/rehab-blocks/src/<name>/` (block.json + index.js + style.scss). Run `npm run build`. |
| Add a brand | Copy `themes/diamond-child/`, edit `style.css` tokens + logo |
| Bulk-edit posts | Add a case to `mu-plugins/zz-oneshot.php`, hit `?rehab_oneshot=<task>` |
| Migrate live content | Save JSON to `migrate-data/`, write a one-shot task that builds blocks via `aa-block-builders.php` helpers |

## Live site reference

The currently-live Diamond site is `https://diamondrehabthailand.com/`. URL
parity is intentional — every page slug on live exists on this dev stack.
The `migrate-data/*.json` files capture live's content for each rebuilt page.
