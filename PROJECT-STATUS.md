# Project status & handoff

*Last updated: 23 June 2026. Read this first after a restart — session task lists and chat context do not survive, but this file does.*

---

## 1. Linear + git workflow — setup state

The full protocol is in `CLAUDE.md` (same folder). Team **Rehab Websites / `REH`**, every issue assigned to **Robbin Schuchmann**, no worktrees (single tmux).

**Done (on this server):**
- `dev-stack/.mcp.json` — Linear MCP (`linear-server`, hosted HTTP, OAuth on first use).
- `dev-stack/CLAUDE.md` — the Linear Workflow Protocol.
- `dev-stack/.mcp.env` — **gitignored**, contains the real `GH_TOKEN` (a GitHub PAT). `.mcp.env.example` is the tracked template.
- `gh` CLI 2.94.0 installed at `~/.local/bin`; verified `push=true, admin=true` on the repo.
- `~/.bashrc`: sources `.mcp.env` *before* the interactivity guard (line ~7) so subprocesses inherit `GH_TOKEN`; adds `tm()` + `ta/tn/tl/tk` tmux helpers and `~/.local/bin` on PATH.
- Termius startup command: `cd /home/robbin/projects/rehab-wordpress/dev-stack && bash -ic 'tm rehab'`

**Done:** MCP is live; the **Website v3 rollout** project and issues **REH-1…REH-8** are created (section 2). Status automation drives: first push → In Progress, PR opened → In Review, PR merged → Done. Never set status by hand.

**Verify in the Linear UI (only you can — needs a browser):** confirm the **GitHub integration** is connected to `RobbinSchuchmann/rehab-wordpress`, branch format is **`identifier-title`**, and **PR opened → In Review** is mapped. (Manual; the MCP can't configure teams/integrations.)

---

## 2. Linear issues (created — project "Website v3 rollout")

Project **"Website v3 rollout"**, team `REH`, assignee Robbin. Code work only. Created 12 Jun 2026:

| ID | Issue (imperative title) | Type | Priority | Note |
|---|---|---|---|---|
| REH-1 | Roll team-profile template across ~36 member pages | Feature | Medium | ✅ **Done** — rolled across the full 21-member roster (the real roster is 21, not 36) |
| REH-2 | Close treatment-template leftovers (3 conversions + 6 article reclassifications) | Improvement | Medium | ✅ **Done** — 3 pages converted to v3, 6 reclassified to articles |
| REH-3 | Design + implement article/blog template (355 pages) | Feature | High | blocked on design bundle |
| REH-4 | Design + implement treatment hub (/all-treatments/) | Feature | Medium | blocked on design bundle |
| REH-5 | Rebuild remaining core pages (Hua Hin, Testimonials, Programs, Careers, Superannuation, policies) | Feature | Medium | ✅ **Done** (per decisions) — Careers polished, policies on utility template, Hua Hin/Programs/Super accepted; Testimonials deferred (content-gated) |
| REH-6 | Fix canada-dry article's broken image-compare widget | Bug | Low | renders `src="state.currentImage.currentSrc"` |
| REH-7 | Restructure repo to clean wp-content layout before deploy | Improvement | Low | deferred to end of build |
| REH-8 | Commit workflow config files (.mcp.json, CLAUDE.md, PROJECT-STATUS.md) | Improvement | Medium | first end-to-end automation-loop test |
| REH-9 | Fix inconsistent section column width (benefits-numbered full-bleed) | Bug | Medium | ✅ **Done** — merged to main via PR #2 (`7aafd66`) |
| REH-10 | Fix mobile/responsive horizontal overflow (hero, treatment-phases, article body) | Bug | Medium | ✅ **Done** — merged to main via PR #8 (`3365825`); full-site sweep 446 pages clean |
| REH-11 | Recover latent invalid-block warnings across 28 pages (pre-go-live sweep) | Bug | Medium | ✅ **Done** — merged via PR #9 (`fb69f5b`); 91 pages swept, 28 recovered to 0 |
| REH-13 | Fix header nav overlap (mid-widths) + CTA-band button rendering | Bug | Medium | ✅ **Done** — merged via PR #10 (`7ba5edb`); tiered nav collapse + button padding |
| REH-14 | Extend audit (overlaps + zero-area buttons); fix clipped article-sidebar CTA button | Improvement | Medium | ✅ **Done** — merged via PR #11 (`c181d11`); audit now catches collisions + padding-less buttons |
| REH-15 | Restore analytics & marketing tracking dropped with IHAF (GA4/GTM/Clarity, lead pixel, WhatsApp widget) | Feature | High | **go-live blocker** — host-gated mu-plugin; see "Migration plugin gaps" below |
| REH-16 | Harden contact/intake forms for production (recipient, SMTP, deploy smoke test) | Improvement | High | **go-live blocker** — forms work but would silently lose leads (wrong recipient + no SMTP); see "Migration plugin gaps" below |
| REH-17 | Migrate Diamond Rehab to a fresh Cloudways server (per-site runbook) | Improvement | High | step-by-step in `MIGRATION-RUNBOOK.md`; reusable template — each site gets its own server from the same git codebase |

**Excluded from Linear on purpose:** "Editor review gates" — content/business sign-off, not code. Per the `CLAUDE.md` rule (Linear = code work only) it stays out. Tracked in section 4 + the parent `SITE-PAGES-PLAN.md`.

---

## 3. Build state (what's already done)

Detailed build notes live in auto-memory: `memory/treatment-design-v3.md` (loads automatically each session). Summary:

- **35 treatment pages** rebuilt to the v3 design (the original 32 + the 3 REH-2 conversions: couples 8340, substance-abuse hub 4611, traumatic reenactment 4456). Driven by `aa-treatment-v3-specs.php` + oneshots in `zz-oneshot.php`. Group 2 leftovers are now closed except the `/all-treatments/` hub (1219 → REH-4, design-blocked).
- **6 therapy-method pages reclassified** off the treatment template to `template-article.php` (REH-2): CBT 1323, DBT 1327, Mindfulness 1334, EMDR 1339, Stages of change 2853, sniffing-coke 1568. Interim look until the article-template redesign (REH-3) lands.
- **Core pages done:** Cost (834), Contact (1189), Why Us (825), Team (722, real 21-person roster), FAQ (1197).
- **Team-member profiles (REH-1): all 21 roster members** built to the approved hi-fi profile (role · portrait · pulled quote · bio + sticky enquiry form), via the `rollout-team-profiles` oneshot. The roster is 21, not the ~36 the issue estimated. All 21 editor-validated (0 recovery warnings).
- **Standard nav header** replaced the off-canvas-only header.
- **Site-wide link check done:** 67 broken → 2 (the remaining 2 are the canada-dry widget, issue above). Fixes included 18 wrong article slugs hardcoded in the homepage content-grid theme partial — *these are also live-site bugs*.
- **Remaining core pages (REH-5):** Hua Hin (8177), Programs (857), Superannuation (8973) accepted as already-built; Careers (9015) polished (orphan SEO tail wrapped in prose containers via `polish-careers`); policy pages (Privacy 3, Confidentiality 4197, Policies & procedures 1546) put on a new `template-utility.php` (readable prose column, no content change). **Testimonials deferred** — page doesn't exist and needs confirmed review/video content (content-gated, like task 15). Intake forms (5440, 9557) left as-is.
- **Dev stack is host-agnostic** (`aa-dynamic-host.php`): browse via `localhost:8081` or `5.223.87.211:8081`.

**Git state:** the v3 rollout (`9a4783a`) **has been merged to `main`** — `origin/main` is now at the PR-#2 merge commit (`7aafd66`), which also includes **REH-9** (benefits-numbered full-bleed fix). The `v3-design-rollout` branch is deleted on origin (a stale local tracking ref may remain; `git remote prune origin`). The workflow config files (`.mcp.json`, `CLAUDE.md`, `.mcp.env.example`, `PROJECT-STATUS.md`, `.gitignore`) are still **uncommitted** — that's **REH-8**.

**Rebuild/verify pattern:** edit specs/builders → `?rehab_oneshot=<task>` → `node tools/recover-demo.js <id>` → `node tools/check-editor.js <id>` (expect 0 "Attempt recovery") → screenshot.

**🚦 Pre-go-live gate (15 Jun 2026) — PASSED.** Full-site responsive audit (446 pages × 360/1024/1280/1440 = 1,784 checks) and full block-validation sweep (all 448 pages) both ran clean: **0 overflow, 0 overlaps, 0 zero-area buttons, 0 invalid-block warnings, 0 errors.** Re-run via the two commands below after any further content/CSS changes.

**Responsive audit (REH-10 / extended REH-14):** `tools/audit-responsive.js` scans any URL list across widths (default 360/390/768/1024/1440/2560; override with `WIDTHS=360,1024`) and flags three bug classes: (1) horizontal overflow, (2) element overlap (in-flow interactive/heading/brand collisions, scoped to positioning context — catches nav-under-logo without false-positiving fixed bars), (3) zero-area buttons (btn-class elements with a fill but no padding/height). `INJECT_CSS='<css>'` env hook reproduces regressions. Last full-site run: **446 pages × {360,1024} = 0 overflow, 0 errors**; broad 84-page × 6-width run: 0 overlaps, 0 bad buttons. Fixes landed: `hero` grid (fractional fr columns), `treatment-phases` panel (`minmax(0,1fr)` + `overflow-wrap`), `.rehab-article__body` (`overflow-wrap` for long chemical names, protects ~355 article pages), header nav tiered collapse (REH-13), CTA-band button padding (REH-13), article sidebar `flex-shrink:0` (REH-14, un-clips the "Ask a question" button).

**Block-validation sweep (REH-11):** `?rehab_oneshot=list-rehab-pages` lists every page with `wp:rehab/*` blocks; `tools/validate-sweep.js <ids-file>` opens each in the editor, recovers + re-saves any with invalid-block ("Attempt recovery") warnings, re-verifies. Runs: rehab-block pages **91 → 28 recovered** (incl. homepage, hua-hin, programme, superannuation, CBT/DBT/EMDR/mindfulness, living-sober); article-template pages **353 → 1 recovered** (3403 ketamine-addiction); team profiles (21) + remaining core/policy pages (privacy 3, all-articles 1218, policies 1546, confidentiality 4197) **→ all clean**. **Every published page (448) is now validation-checked; 29 recovered total, all re-verified clean.** Re-run before go-live or after bulk content edits. Recovered content lives in the DB (outside the repo), so it's not a git diff.

**RankMath / schema sweep (15 Jun 2026) — VERIFIED clean.** `tools/audit-seo.sh` (curl-based, reads the sitemap → `/tmp/seo-audit.csv`) confirmed **all 446 pages** emit title, meta description, OG tags, and a `rank-math-schema-pro` JSON-LD block — 0 gaps. The previous version's **custom schemas are intact and rendering on every page** (MedicalClinic, MedicalOrganization, LocalBusiness, WebPage/MedicalWebPage + WebSite/Person/Service graph). They survived all rebuilds because the oneshots only call `wp_update_post([ID, post_content])` and never touch `rank_math_*` postmeta. RankMath free + Pro both active.

**🚦 Go-live SEO toggles (NOT yet applied — deploy-time, intentionally still in dev state):**
1. **Set `blog_public = 1`** (live dev DB is `0`; the dump had `1`). Dev is `noindex,nofollow` sitewide as hygiene because the box is publicly reachable at `5.223.87.211:8081`. This also removes the canonical tag — RankMath strips `rel="canonical"` on any noindex page by design (`seo-by-rank-math/includes/frontend/class-head.php:237`). Setting `blog_public = 1` restores **both** indexing and canonicals automatically. Check current value via `?rehab_oneshot=check-search-engine`.
2. **Run the replace-host oneshot** so schema `@id`/`logo`/`url` (and all baked URLs) use `diamondrehabthailand.com` instead of the dev IP. The dev IP in schema is a cosmetic artifact of `aa-dynamic-host.php` filtering `option_home`/`option_siteurl` to the request host — fine for dev, must be corrected for production.

Re-run `tools/audit-seo.sh` after toggle #1 to confirm `rel="canonical"` appears on all pages.

**Migration plugin gaps (audited 15 Jun 2026).** The v3 migration dropped **19 of the old site's 23 plugins** in favour of custom blocks/theme. Most user-facing functions were cleanly replaced; a few were **not** and need attention before go-live. Verified against the old dump + live dev site.

- **Forms — ✅ replaced & working.** Old **Forminator** (14 forms, `[forminator_form id=…]` shortcodes, `wp_frmt_*` entry tables) → custom `rehab/final-cta` block + REST endpoint `POST /wp-json/rehab/v1/contact` (live, validating) + `rehab_submission` CPT storage with admin email (CPT = fallback if `wp_mail()` drops). **0 leaked shortcodes across all 446 pages.** Contact (1189) + Intake Form (5440) carry working forms. Page **9557** (`intake-form-halfway-house`) is an old-theme page **intentionally 301-redirected to home** by `zz-redirects.php` (mirrors live-site behaviour) — verified, not a broken/missing form. ⚠️ Production hardening → **REH-16** (go-live blocker): notification recipient defaults to `admin_email` (= a dev/agency Gmail in the dump, not the client's intake inbox), and `wp_mail()` needs real SMTP (dev returns `emailed:false`). Verified end-to-end live (valid→stored, honeypot→dropped, bad input→400); endpoint is nonce-free so it's cache-safe, and the CPT stores every lead before email so leads are never lost regardless. **Cloudflare can't send mail** (Email Routing is inbound-only) — use a transactional provider + Cloudflare DNS for SPF/DKIM/DMARC.
- **Analytics / marketing tracking — 🔴 missing → REH-15 (go-live blocker).** All injected via the dropped **Insert Headers & Footers** plugin; nothing restores it automatically. Missing: **GTM** (`GTM-…`), **GA4** (`G-…`) + legacy **UA-…**, **Microsoft Clarity**, **lead pixel** (`leads.internetdominators.app`, PxGrabber account `76381674`), **Elfsight WhatsApp chat widget**, and the **GSC verification meta** (`google-site-verification`, bundled in the same IHAF header field — dropping it loses Search Console access unless GSC is verified via DNS). Plan: host-gated mu-plugin (mirror `aa-dynamic-host.php`) firing only on the production domain.
- **Article table of contents — 🟡 gap.** Old **Easy-TOC**; new articles have no TOC. UX/SEO nicety on long articles. (Out of REH-15 scope — separate item if wanted.)
- **CallTrackingMetrics — 🟡 dropped.** Dynamic call-tracking phone numbers gone; numbers now static (lose call attribution). Business decision.
- **Google Reviews widget — 🟡 dropped.** Tied to the deferred **Testimonials** page (content-gated).
- **✅ Cleanly replaced (no action):** accordion-blocks → `rehab/faq` block (native `<details>`, also feeds FAQ schema); top-bar → custom utility-bar; interactive-geo-maps → Google Maps iframe (contact page); Breeze/Smush/Flying-Scripts → production-infra concern, not content.

### 🚀 Cloudways INITIAL SEED — executed 16 Jun 2026 (REH-17)

Diamond's Cloudways server is **seeded and serving** (initial seed only — **no DNS cutover**; editors work on the staging URL until launch). Done from the dev box over SSH (dev box ed25519 key authorized).

- **Server:** `138.197.93.177`, user `master_pjydkfpusf`, app **`afsjjjgrnm`**, app path `/home/master/applications/afsjjjgrnm/public_html`. PHP 8.2.31 (runbook wanted 8.3+ — bump in panel). **Staging URL:** `https://wordpress-1636937-6489349.cloudwaysapps.com`.
- **Done:** Phase 2 code (themes + `rehab-blocks` active; mu-plugin triage clean — only `zz-contact-form` + `zz-redirects`). Phase 3 export (dev DB via `mariadb-dump`, MariaDB 11). Phase 4 import (**448 pages**, `diamond-child` active, `blog_public=0`). Phase 5 cleanup (revisions/transients dropped; 18 orphan tables dropped — **Forminator lead tables `wp_frmt_form_entry`/`_meta` KEPT** as historical archive). Phase 6 hosts (8,499 replacements → staging URL). Phase 7 uploads (**22,428 files / 6.6 GB** rsynced; only `smush-webp-test.png` junk skipped).
- **RankMath gap CLOSED:** prior session never installed RankMath. Copied `seo-by-rank-math` 1.0.269 + `seo-by-rank-math-pro` 3.0.112 from dev → activated. Meta now renders.
- **Staging noindex guard:** `wp-content/mu-plugins/zz-staging-noindex.php` forces `noindex` (meta + `X-Robots-Tag`) on any non-prod host. **DELETE at cutover.** Verified via WP-CLI.
- **Cache drop-ins parked:** `object-cache.php` + `advanced-cache.php` → `.migbak` (so the foreign DB wouldn't fatal OCP/Breeze). Re-enable Breeze + Object Cache Pro via the Cloudways panel near cutover.
- **Rollback:** stock pre-seed DB at `/home/master/pre-seed-backup-2026-06-16.sql`. Dev dump at `~/diamond-dev-2026-06-16.sql.gz` (both boxes).

**🔑 Pending MANUAL steps (can't do from CLI):**
1. **Purge Varnish** (Cloudways panel → app → Purge Varnish). *Critical:* all HTTP responses are served from a stale cache predating RankMath+noindex — until purged the live HTML wrongly shows `robots=index` / no RankMath meta though the backend is already correct. (Master user has no `sudo`/`varnishadm` access.)
2. **Register RankMath Pro** for this domain (wp-admin → RankMath → connect account). Pro license is domain-locked → `rank-math-schema` not rendering yet. Re-connect again for `diamondrehabthailand.com` at cutover.

**Still open for go-live (per scope, after editors start):** REH-15 analytics mu-plugin, REH-16 forms SMTP/recipient, Phase 8 article re-sync, Phase 10 audit against staging, Phase 11 DNS cutover (+ delete the noindex guard, flip `blog_public=1`, re-replace staging URL → `diamondrehabthailand.com`).

### 🏠 Homepage → editable Gutenberg blocks (REH-37…41) — LIVE on prod (22–23 Jun 2026)

The homepage was hardcoded in `diamond-child/page-homepage.php` (~22 static `drt-` section template-parts). It is now **fully editable in the block editor while pixel-identical** to that design. Details + rollout/revert steps in auto-memory `memory/homepage-editable-blocks.md`.

- **REH-37 (PR #32):** 23 new dynamic blocks `rehab/home-*` (one per section) in the `rehab-blocks` plugin, emitting the same `drt-` markup and reusing the existing `drt-` CSS/JS so the look is unchanged. New template `page-homepage-blocks.php` renders `the_content()` inside the required `.drt-homepage` wrapper (global/nav/footer CSS keys off `body:has(.drt-homepage)`). `diamond-child/functions.php` loads the `drt-` bundle wherever `home-*` blocks render. Interactive sections keep their JS hooks byte-identical (Swiper, Fancybox, tab/accordion engines, mobile sticky bar). Seed markup: `mu-plugins/homepage-seed.html` (26 block comments in page order; `conversion-bridge` ×3 + `separator` ×2).
- **REH-38 (PR #33/#34):** blocks were unstyled in the editor canvas — fixed by loading the `drt-` CSS via `enqueue_block_assets` (injected into the editor **iframe**), not `enqueue_block_editor_assets` (outer doc only). Added `assets/css/homepage/homepage-editor.css` to re-scope the `.drt-homepage` heading typography to `.editor-styles-wrapper`.
- **REH-39 (PR #35):** heading font was `Ivymode, Georgia, serif`, but **Ivymode is loaded nowhere** (no @font-face/Typekit) → headings rendered in Georgia. Switched the homepage stack to `"Playfair Display", Georgia, serif` (already loaded) across the drt CSS + templates + editor; also enqueue Playfair in the editor. Scoped to diamond-child homepage; parent `--rehab-font-display` untouched.
- **REH-40 (PR #36) / REH-41 (PR #37):** block-library editability audit. `signs-grid` could add list items but not remove → added per-item remove buttons. Audit found no other add-but-no-remove gaps, but `article-row` `listItems` and `team-profile` `trustItems` were rendered with **no** editor UI → added "one per line" textarea controls. `save()`/`render.php` output unchanged on all three (no content migration).

**Applied on prod (page 6 = front page):** content set to the block seed + template switched to `page-homepage-blocks.php`, Breeze purged. Done by **direct SSH + wp-cli** (`ssh -i ~/.ssh/id_ed25519 master_pjydkfpusf@138.197.93.177`) because the ungated `zz-oneshot.php` is deliberately **not** deployed to prod. Pre-switch content backed up on the server at `~/home-6-backup-20260622-230513.html`; revert helper `/home/robbin/switch-homepage-prod.sh revert` (local, not in repo). Verified live (desktop + mobile pixel-parity vs the old hardcoded homepage; editor verified in prod wp-admin). Still on the staging URL — no DNS cutover.

**⚠️ Gotcha:** literal `<br>`/`&` inside block-attribute JSON breaks WP's block parser (the comment renders as escaped text and the block fails). `homepage-seed.html` keeps them `<`/`&`-escaped — preserve that if regenerating.

---

## 4. Full task list (persisted — session tasks don't survive restart)

| # | Task | Status |
|---|---|---|
| 8 | Site-wide link check on dev stack | ✅ done |
| 9 | Build team-profile template + pilot (Eugene) | ✅ done |
| 10 | Roll profile template across ~36 team member pages | ⏳ pending → REH issue |
| 11 | Close treatment-template leftovers | ⏳ pending → REH issue |
| 12 | Design + implement article/blog template (355 pages) | ⏳ pending (needs design) → REH issue |
| 13 | Design + implement treatment hub (/all-treatments/) | ⏳ pending (needs design) → REH issue |
| 14 | Remaining core pages (Hua Hin, Testimonials, Programs, Careers, Superannuation, policies) | ⏳ pending → REH issue |
| 15 | Editor review gates before go-live | ⏳ pending (content — NOT Linear) |
| 16 | Fix canada-dry article widget | ⏳ pending → REH issue |
| 17 | Restructure repo to clean wp-content layout before deploy | ⏳ deferred → REH issue |

---

## 5. Immediate next actions

1. Verify the Linear↔GitHub integration in the Linear UI (section 1).
2. Resume the build — **REH-1** (team-profile rollout, task #10) is queued and ready. Issue exists, so: `git checkout -b reh-1-roll-team-profile-template-across-36-member-pages`, then work → push → PR → merge.
