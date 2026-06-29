# New-brand runbook — stand up a rehab site for a new brand

*How to take the shared rehab codebase and launch a **new brand** site. Covers the brand-specific code (child theme + identity config + assets) and content, then hands off to `MIGRATION-RUNBOOK.md` for the server/DB/go-live mechanics (those phases are identical for every site).*

> **Scope split.** This file = the *new* parts (child theme, brand config, content). `MIGRATION-RUNBOOK.md` = provisioning a Cloudways server, DB export/import, uploads, hardening, cutover. Don't duplicate; cross-reference.

---

## Mental model

- **One shared engine, many thin children.** `themes/rehab-parent` + `plugins/rehab-blocks` are brand-agnostic and shared by every brand (REH-42/43/46). A brand is a **child theme** that only: (1) overrides `--rehab-*` tokens in `style.css`, (2) sets brand identity via `theme_mod` filters in `functions.php`, (3) supplies its own logo/images/fonts.
- **One server = one brand.** Each brand gets its own Cloudways server + its own DB/content. The deploy ships the shared engine + that brand's child theme, selected by `BRAND` (REH-47).
- **The dev stack is the factory.** Build and iterate every brand here (`localhost:8081`), then deploy code to each brand's server and seed its content.

### Starting point — the stub children already exist
`afkick-child`, `anker-child`, `ankerhuis-fi-child`, `cherrywood-child`, `imani-child`, `villa-consano-child` are scaffolded (each has `style.css` with a token palette + `functions.php` with the stylesheet enqueue, logo filter, and `has_custom_logo`). So **Steps 1–2 below are largely done for these six**; you mainly add the identity map (Step 3), assets (Step 4), and content (Step 6).

---

## Step 1 — The child theme (skip if a stub exists)

To scaffold a brand new one, copy `diamond-child`'s shape (not its content):

```
themes/<brand>-child/
  style.css            # header (Template: rehab-parent) + :root token overrides
  functions.php        # enqueue + logo + has_custom_logo + BRAND IDENTITY MAP (Step 3)
  assets/img/logo.svg  # brand logo (Step 4)
  assets/...           # brand images/fonts as needed
```

`style.css` header must read `Template: rehab-parent`. Enqueue depends on the parent handles: `[ 'rehab-tokens', 'rehab-typography', 'rehab-layout', 'rehab-buttons', 'rehab-utilities' ]`.

## Step 2 — Brand tokens (`style.css`)

Redefine **only** `--rehab-*` tokens — no new selectors, no layout, no block CSS. The full token set lives in `themes/rehab-parent/assets/css/tokens.css`; override the brand-distinctive ones (surfaces, sage→accent, charcoal, tan, mist, gold). See `anker-child/style.css` for a worked navy/teal example.

> **Also override the `-rgb` triplet tokens.** Translucent borders/overlays use `--rehab-*-rgb` (e.g. `--rehab-sage-rgb: 79, 109, 122;`) via `rgba(var(--rehab-sage-rgb), .x)` (REH-50). Override each alongside its hex counterpart, or the semi-transparent variants stay the previous brand's colour.

## Step 3 — Brand identity (`theme_mod` filters) — **the REH-46 map**

The parent reads **27 `theme_mod`s** for all brand identity (phone, socials, contact, footer, nav, schema). Set them in the child via the brand-filter pattern so they ship in code (the deploy is git-based — values set only in the DB wouldn't travel). Copy this into the child `functions.php` and fill in the brand's values:

```php
/**
 * Brand contact + identity. Overrides the parent's neutral theme_mod defaults
 * via the theme_mod_{name} filter so the values ship with the code (REH-46).
 * The filter always wins over the Customizer — edit here to change them.
 */
function <brand>_child_brand_contact( array $map ): void {
	foreach ( $map as $mod => $value ) {
		add_filter( "theme_mod_{$mod}", static function () use ( $value ) {
			return $value;
		} );
	}
}
<brand>_child_brand_contact( [
	// Phone / contact
	'rehab_phone_display'    => '+__ _ ____ ____',
	'rehab_phone_number'     => '+________',          // digits + country code
	'rehab_whatsapp_number'  => '',                   // digits only, optional
	'rehab_contact_email'    => 'info@<brand>.com',
	// Socials (only set the ones the brand has)
	'rehab_social_facebook'  => '',
	'rehab_social_instagram' => '',
	'rehab_social_x'         => '',
	'rehab_social_linkedin'  => '',
	'rehab_social_youtube'   => '',
	'rehab_social_pinterest' => '',
	'rehab_social_threads'   => '',
	// Footer
	'rehab_footer_address'   => "Street\nCity, Region\nCountry POSTCODE",
	'rehab_footer_intl_phones' => '',                 // "Label|+number" per line, optional
	// Nav / menu
	'rehab_menu_pitch_title' => '',                   // mega-menu pitch
	'rehab_nav_cta_url'      => '/contact-us/',
	'rehab_assessment_url'   => '/contact-us/',        // mobile "Free assessment" target
	// SEO / LocalBusiness schema (fallback JSON-LD; RankMath emits the live schema)
	'rehab_default_description' => '',
	'rehab_addr_street'      => '',
	'rehab_addr_locality'    => '',
	'rehab_addr_region'      => '',
	'rehab_addr_postal'      => '',
	'rehab_addr_country'     => '',                    // ISO code, e.g. NL
	'rehab_geo_lat'          => '',
	'rehab_geo_lng'          => '',
] );
```

See `diamond-child/functions.php` (`diamond_child_brand_contact()`) for a filled-in reference. Full field table in **Appendix B**. (The live SEO schema is RankMath's, set per-server — see go-live; this filter only feeds the theme's fallback schema.)

## Step 4 — Logo, images, fonts

- `assets/img/logo.svg` — the child logo filter renders it inline (falls back to a text wordmark if absent).
- Homepage/section images go under `assets/images/...`; point the block attributes / homepage seed at the brand's paths.
- If the brand uses a different display/body font than Diamond's Playfair/Inter, enqueue it in the child and override `--rehab-font-*` tokens.

## Step 5 — Homepage assets (only if using the block homepage)

If the brand ships the editable homepage (the `rehab/home-*` blocks, REH-37), copy the `drt_homepage_*` enqueue block from `diamond-child/functions.php` into the child (it loads the shared homepage CSS from the parent + the brand's JS/images), and produce a brand `homepage-seed.html` for its content. Otherwise skip.

**Page templates that pull brand data via filters.** Some shared page templates are data-driven by the child. If the brand uses the **Treatments Hub** (`template-treatments-hub.php`, the `/all-treatments/` directory), provide its programs via the `rehab_treatments_hub_categories` filter in the child — see `diamond_child_treatments_hub_categories()` for the shape (REH-49). Without it the hub renders its hero + concierge band but no program list.

## Step 6 — Content build (decision)

The Diamond content tooling (`mu-plugins/zz-oneshot.php`, `aa-block-builders.php`, `aa-treatment-v3-specs.php`) is **Diamond-bespoke and never deployed to prod** — it's dev-only scaffolding. Content is **per-site (DB)**, built on the dev factory and seeded to the server. Two approaches:

- **A — Clone-and-rewrite (recommended for speed).** Start from Diamond's page set on the dev stack, then rewrite copy/images per brand in the block editor (the homepage + pages are editable Gutenberg blocks). Export/import the brand DB to its server via `MIGRATION-RUNBOOK.md` Phases 3–4.
- **B — Build fresh.** Author each page from the block library in the editor. Slower; use when the brand's IA diverges a lot from Diamond.

If several brands end up sharing structure, generalize the builders/specs to be brand-parameterized (own ticket) rather than copy-pasting.

## Step 7 — Verify on the dev stack

Browse `localhost:8081` with the brand child active. Re-use the existing audit scripts (`audit-responsive.js`, `validate-sweep.js`, `audit-seo.sh`) and confirm the footer/header/schema show the brand's identity (not Diamond's).

## Step 8 — Deploy the code (REH-47)

The deploy is the **GitHub Actions allowlist rsync** (`.github/workflows/deploy.yml` → `scripts/deploy-to-cloudways.sh`), not Cloudways git auto-deploy. It ships `rehab-parent` + `rehab-blocks` + `themes/${BRAND}-child` + the 3 safe mu-plugins.

1. **Add the brand's server secrets** in GitHub (one server per brand). Today the workflow uses a single set (`CLOUDWAYS_HOST/USER/PATH/SSH_KEY`). For a second brand, store its target as a `DEPLOY_TARGET_<BRAND>` JSON secret + its SSH key, and convert the deploy step to a matrix (the secrets context can't be indexed by a dynamic name — see the comment in `deploy.yml`).
2. **Deploy:** manual run via **Actions → Deploy to Cloudways → Run workflow → brand: `<brand>`** (the `workflow_dispatch` `brand` input), or set the `DEPLOY_BRAND` repo variable for auto-deploy on push. Use the **Dry run** toggle first.
3. Locally you can dry-run any brand: `BRAND=<brand> DRY_RUN=1 DEPLOY_HOST=… DEPLOY_USER=… DEPLOY_PATH=… DEPLOY_SSH_KEY=~/.ssh/key ./scripts/deploy-to-cloudways.sh`.

## Step 9 — Server, DB, go-live → MIGRATION-RUNBOOK

Provisioning the Cloudways server, importing the brand DB, syncing uploads, hardening, and cutover are identical to Diamond. Follow `MIGRATION-RUNBOOK.md` **Phases 1–11** (note its "Per-site reuse" section). Brand-specific go-live items: register RankMath Pro for the brand domain, set the live schema/contact in RankMath, flip `blog_public=1`, delete the staging `zz-staging-noindex.php` guard, and run the domain search-replace (old→new domain) in Phase 6.

---

## Appendix A — new-brand checklist

- [ ] Child theme exists (`Template: rehab-parent`) — stub or scaffolded
- [ ] `style.css` token palette set (Step 2)
- [ ] Brand identity map in `functions.php` (Step 3 — all required mods filled)
- [ ] `logo.svg` + brand images/fonts (Step 4)
- [ ] Homepage enqueue + seed, if using block homepage (Step 5)
- [ ] Content built/seeded on the dev factory (Step 6)
- [ ] Verified on `localhost:8081` (Step 7)
- [ ] Server secrets added; code deployed via the workflow (Step 8)
- [ ] Server/DB/uploads/go-live per MIGRATION-RUNBOOK (Step 9)

## Appendix B — `theme_mod` reference (the brand config surface)

| theme_mod | Group | Required? | Notes |
|---|---|---|---|
| `rehab_phone_display` | Phone | **yes** | Header + prominent footer number |
| `rehab_phone_number` | Phone | **yes** | `tel:` digits + country code |
| `rehab_whatsapp_number` | Phone | no | Utility-bar WhatsApp link |
| `rehab_contact_email` | Contact | **yes** | Contact-form fallback (default: admin_email) |
| `rehab_social_facebook` | Social | if exists | Footer icon + schema `sameAs` |
| `rehab_social_instagram` | Social | if exists | " |
| `rehab_social_x` | Social | if exists | " |
| `rehab_social_linkedin` | Social | if exists | " |
| `rehab_social_youtube` | Social | if exists | " |
| `rehab_social_pinterest` | Social | if exists | " |
| `rehab_social_threads` | Social | if exists | " |
| `rehab_footer_address` | Footer | **yes** | Multiline |
| `rehab_footer_copyright` | Footer | no | Defaults to `© YEAR <site name>` |
| `rehab_footer_intl_phones` | Footer | no | `Label\|+number` per line |
| `rehab_menu_pitch_title` | Nav | no | Mega-menu pitch (neutral default) |
| `rehab_menu_pitch_body` | Nav | no | " |
| `rehab_nav_cta_text` | Nav | no | Default "Talk with admissions" |
| `rehab_nav_cta_url` | Nav | no | Default `/contact-us/` |
| `rehab_assessment_url` | Nav | no | Mobile "Free assessment" (default `/contact-us/`) |
| `rehab_default_description` | SEO | **yes** | Default meta description (falls back to tagline) |
| `rehab_addr_street` | Schema | rec. | LocalBusiness fallback JSON-LD |
| `rehab_addr_locality` | Schema | rec. | " |
| `rehab_addr_region` | Schema | rec. | " |
| `rehab_addr_postal` | Schema | rec. | " |
| `rehab_addr_country` | Schema | rec. | ISO code |
| `rehab_geo_lat` | Schema | rec. | " |
| `rehab_geo_lng` | Schema | rec. | " |
