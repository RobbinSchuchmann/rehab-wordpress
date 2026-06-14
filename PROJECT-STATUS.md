# Project status & handoff

*Last updated: 12 June 2026. Read this first after a restart — session task lists and chat context do not survive, but this file does.*

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

**Rebuild/verify pattern:** edit specs/builders → `?rehab_oneshot=<task>` → `node recover-demo.js <id>` → `node check-editor.js <id>` (expect 0 "Attempt recovery") → screenshot.

**Responsive audit (REH-10 / extended REH-14):** `audit-responsive.js` scans any URL list across widths (default 360/390/768/1024/1440/2560; override with `WIDTHS=360,1024`) and flags three bug classes: (1) horizontal overflow, (2) element overlap (in-flow interactive/heading/brand collisions, scoped to positioning context — catches nav-under-logo without false-positiving fixed bars), (3) zero-area buttons (btn-class elements with a fill but no padding/height). `INJECT_CSS='<css>'` env hook reproduces regressions. Last full-site run: **446 pages × {360,1024} = 0 overflow, 0 errors**; broad 84-page × 6-width run: 0 overlaps, 0 bad buttons. Fixes landed: `hero` grid (fractional fr columns), `treatment-phases` panel (`minmax(0,1fr)` + `overflow-wrap`), `.rehab-article__body` (`overflow-wrap` for long chemical names, protects ~355 article pages), header nav tiered collapse (REH-13), CTA-band button padding (REH-13), article sidebar `flex-shrink:0` (REH-14, un-clips the "Ask a question" button).

**Block-validation sweep (REH-11):** `?rehab_oneshot=list-rehab-pages` lists every page with `wp:rehab/*` blocks; `validate-sweep.js <ids-file>` opens each in the editor, recovers + re-saves any with invalid-block ("Attempt recovery") warnings, re-verifies. Last run: **91 pages → 28 recovered to 0 warnings** (incl. homepage, hua-hin, programme, superannuation, CBT/DBT/EMDR/mindfulness, living-sober). Re-run before go-live or after bulk content edits. The recovered content lives in the DB (outside the repo), so it's not a git diff.

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
