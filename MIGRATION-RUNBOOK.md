# Migration runbook — dev stack → fresh Cloudways server

*Repeatable, per-site. Each rehab website lives on **its own** Cloudways server, built from the **same git codebase** (parent theme + per-brand child theme) with **its own** content/DB. This runbook is the template — for site #2, #3 … just re-run it with different placeholders.*

> First written 15 Jun 2026 for **Diamond Rehab**. Read alongside `PROJECT-STATUS.md` ("Migration plugin gaps", "Go-live SEO toggles").

## Placeholders used below

| Token | Diamond Rehab value | Meaning |
|---|---|---|
| `<SITE>` | `diamond` | site/brand slug |
| `<CHILD_THEME>` | `diamond-child` | brand child theme dir |
| `<OLD_DOMAIN>` | `diamondrehabthailand.com` | current live domain |
| `<NEW_DOMAIN>` | `diamondrehabthailand.com` *(unchanged)* | production domain on the new server |
| `<APP_PATH>` | — | Cloudways app path: `applications/<app>/public_html` |

> For Diamond the domain is unchanged, so the domain search-replace is a no-op — you only fix dev-host stragglers. For a **new brand**, `<OLD_DOMAIN>` ≠ `<NEW_DOMAIN>` and the domain replace matters.

---

## Core principles (don't skip)

1. **git = code, server = content.** Code flows here → server via git, forever. Content lives in the DB + uploads; git never carries it.
2. **Seed from THIS dev box's DB, NOT a live export.** The live DB is the *old* design; your v3 rebuild (446 audited pages) lives only in this box's Docker `dbdata` volume. Seeding from live throws the rebuild away.
3. **One-way DB seed.** After editors start on the new server, the **server DB is the single source of truth** — you can never push a dev DB over it again (a DB import is a full overwrite, not a merge).
4. **Build up from clean parts; never disk-clone.** That's how you shed the old server's trash (backup files, dead plugins, caches).

---

## Phase 0 — Pre-flight decisions

- [ ] Confirm seed source = **dev box DB** (this runbook assumes it).
- [ ] Confirm **article drift scope** — which learning articles were edited on live since the last dump (Diamond: learning articles, re-synced once in Phase 8).
- [ ] Confirm `<NEW_DOMAIN>` and the **lead-notification recipient** (REH-16).
- [ ] **Freeze meaningful edits on the old live site** *except* the learning articles you'll re-sync once. After Phase 8, all new content happens on the new server.

---

## Phase 1 — Provision the Cloudways server

- [ ] New WordPress app. **PHP 8.3+**, recent **MariaDB/MySQL** (match the dev stack).
- [ ] Enable **Let's Encrypt SSL** immediately (fixes editors logging in over HTTP).
- [ ] Turn on **automated backups** day one.
- [ ] Note SSH/SFTP creds, DB creds, and `<APP_PATH>`. Cloudways gives you native **WP-CLI over SSH** — used throughout below.
- [ ] (Optional) Create the **1-click staging** environment for a dry run before pointing DNS.

---

## Phase 2 — Deploy code from git (clean — no old plugins)

- [ ] **Add the server's deploy secrets** (`CLOUDWAYS_HOST/USER/PATH/SSH_KEY`) so the GitHub Actions workflow (`.github/workflows/deploy.yml` → `scripts/deploy-to-cloudways.sh`) rsyncs the allowlist over SSH on merge to `main` (per-brand routing via `BRAND`, REH-47). It ships only the repo's `wp-content` payload:
  - `themes/rehab-parent` + `themes/<CHILD_THEME>`
  - `plugins/rehab-blocks` (the `build/` dir is tracked — no npm step needed unless you change source)
  - mu-plugins — **triaged**, see below
- [ ] **Plugin allowlist** (install only these): RankMath (free + Pro), Akismet, + SMTP plugin (REH-16). **Do NOT** bring Breeze / UpdraftPlus / Smush / wp-migrate-db / wpmudev / content-views / Forminator / IHAF etc.

### 🔴 mu-plugin triage (security-critical)

| mu-plugin | Production? | Why |
|---|---|---|
| `zz-contact-form.php` | ✅ keep | runtime — the form REST endpoint + lead CPT |
| `zz-redirects.php` | ✅ keep | runtime — preserves live redirect behaviour |
| `aa-dynamic-host.php` | ❌ **remove** | filters `home`/`siteurl` to the request Host header — host-header injection + breaks canonical/schema on a fixed domain |
| `zz-oneshot.php` | ❌ **remove** | **UNGATED** `?rehab_oneshot=` endpoint (no auth/nonce) → anyone could trigger destructive rebuild/rollback tasks |
| `aa-treatment-v3-specs.php` | ❌ remove | build-time spec data for the oneshots |
| `aa-block-builders.php` | ❌ remove | build-time function library, no runtime hooks (DB renders fine without it) |
| `aa-acf-*` (chrome/mapper/migrator/reader) | ❌ remove | one-time ACF→blocks migration dev tools |

> If you ever need to run builders/oneshots **on** the new server, deploy `zz-oneshot.php` + the builder/spec files **temporarily**, run, then delete them again. Never leave the ungated oneshot endpoint live.

---

## Phase 3 — Export the dev box DB

Run on the **dev box host** (needs Docker access; container `rehab-wp-db`, DB `diamond`):

```bash
docker exec rehab-wp-db mysqldump -uroot -prootpass \
  --single-transaction --default-character-set=utf8mb4 diamond \
  | gzip > ~/<SITE>-dev-$(date +%F).sql.gz
```

---

## Phase 4 — Import to Cloudways

Upload the dump via SFTP, then over SSH from `<APP_PATH>`:

```bash
gunzip < <SITE>-dev-YYYY-MM-DD.sql.gz | wp db import -
```

---

## Phase 5 — Clean the DB (WP-CLI on Cloudways)

The dev DB inherited trash from the original dump. Shed it:

```bash
# post revisions
wp post delete $(wp post list --post_type=revision --format=ids) --force 2>/dev/null
# transients + spam
wp transient delete --all
wp comment delete $(wp comment list --status=spam --format=ids) --force 2>/dev/null
# orphaned plugin tables — LIST first, then drop what you confirm is dead
wp db query "SHOW TABLES LIKE 'wp_frmt%';"      # old Forminator entry tables
wp db query "SHOW TABLES LIKE 'wp_breeze%';"
# e.g.: wp db query "DROP TABLE IF EXISTS wp_frmt_form_entry, wp_frmt_form_entry_meta, wp_frmt_form_views;"
# dead plugin options (inspect, then delete)
wp option list --search='breeze*' --fields=option_name
wp option list --search='updraft*' --fields=option_name
```

---

## Phase 6 — Fix hosts + set site state

```bash
# dev-host stragglers (the schema @id/logo etc. that froze to the dev IP)
wp search-replace 'http://5.223.87.211:8081' 'https://<NEW_DOMAIN>' --all-tables --precise --recurse-objects --report-changed-only
wp search-replace 'http://localhost:8081'    'https://<NEW_DOMAIN>' --all-tables --precise --recurse-objects --report-changed-only

# NEW BRAND ONLY (domain changed): rewrite the old domain too
# wp search-replace '<OLD_DOMAIN>' '<NEW_DOMAIN>' --all-tables --precise --recurse-objects --skip-columns=guid

wp option update home    'https://<NEW_DOMAIN>'
wp option update siteurl 'https://<NEW_DOMAIN>'
wp option update blog_public 0      # stay noindex while editors work; flip to 1 at launch
```

> This replaces the dev-only `aa-dynamic-host.php` (removed in Phase 2) — `home`/`siteurl` are now real, fixed values.

---

## Phase 7 — Uploads (real media only)

```bash
rsync -av \
  --exclude 'ai1wm-backups' --exclude 'updraft' --exclude 'cache' --exclude 'smush-webp' \
  /path/to/old/wp-content/uploads/  user@cloudways:<APP_PATH>/wp-content/uploads/
```

The excluded dirs are where the "gigabytes of unnecessary" backup/cache files live.

---

## Phase 8 — Re-sync drifted learning articles from live (one-time)

Articles are interim content-on-a-template (REH-3 redesign not done yet), so refreshing their `post_content` from a fresh live export is safe and doesn't touch the v3 block rebuilds on treatment/core/team pages.

```bash
# 1. Get a FRESH export of the live site DB → upload → load into a scratch schema
wp db query "CREATE DATABASE IF NOT EXISTS live_scratch;"
mysql live_scratch < live-fresh-YYYY-MM-DD.sql

# 2. Identify the article pages (same IDs both sides — shared lineage)
wp post list --post_type=page --meta_key=_wp_page_template \
  --meta_value=template-article.php --format=ids        # ~355 ids

# 3. Refresh ONLY content fields for those IDs from the scratch copy
wp db query "UPDATE wp_posts p
             JOIN live_scratch.wp_posts l ON p.ID = l.ID
             SET p.post_content = l.post_content,
                 p.post_title   = l.post_title,
                 p.post_excerpt = l.post_excerpt,
                 p.post_modified = l.post_modified
             WHERE p.post_type='page'
               AND p.ID IN (<ARTICLE_IDS>);"

wp db query "DROP DATABASE live_scratch;"
```

⚠️ Double-check the 6 reclassified therapy-method pages (CBT/DBT/Mindfulness/EMDR/Stages-of-change/sniffing-coke) aren't unintentionally clobbered — confirm they should take live content too.

### 8b — Uploads delta-sync (mandatory after ANY content re-sync)

A content re-sync imports HTML that may reference media uploaded to live **after** the Phase 7 rsync — those files 404 until fetched (REH-115: 47 images across 15 pages, mostly a `2026/06/` month folder that postdated the seed). Content re-syncs can also **clobber earlier content-fix oneshots** on the same posts (REH-116: the REH-6 canada-dry fix) — re-verify any content migration that predates the re-sync.

```bash
# 1. Find referenced-but-missing media: crawl every public URL, collect
#    <img>/<source>/<a> targets under /wp-content/uploads/, HEAD-check each.
#    (tools/ has the pieces; the pre-launch audit link sweep does exactly this.)

# 2. No SSH to the old host is needed — the files are public. Fetch by path:
while read -r rel; do
  mkdir -p "uploads/$(dirname "$rel")"
  curl -sf -o "uploads/$rel" "https://<OLD_DOMAIN>/wp-content/uploads/$rel"
done < missing-uploads.txt

# 3. Push the delta to the server (never --delete):
rsync -az --files-from=missing-uploads.txt uploads/ <ssh>:<APP_PATH>/wp-content/uploads/

# 4. Re-run the link sweep: expect 0 missing.
```

---

## Phase 9 — Production hardening

- [ ] **mu-plugin triage applied** (Phase 2) — confirm `zz-oneshot.php` + `aa-dynamic-host.php` are absent: `curl -s 'https://<NEW_DOMAIN>/?rehab_oneshot=check-search-engine'` should do **nothing**.
- [ ] **Schema URLs** use `<NEW_DOMAIN>` (Diamond: run the replace-host oneshot if any stragglers remain).
- [ ] **Forms (REH-16):** SMTP provider configured; `rehab_form_recipient` set to the client inbox; SPF/DKIM/DMARC in Cloudways DNS; smoke-test one live submission (`emailed:true` + arrives).
- [ ] **Analytics (REH-15):** host-gated tracking mu-plugin (GTM/GA4/Clarity/lead pixel/WhatsApp/GSC meta) live.

---

## Phase 10 — Verify (use the existing audit scripts)

Point the scripts at `https://<NEW_DOMAIN>` and confirm parity with the dev box:

- [ ] `tools/audit-seo.sh` — schema + meta on every page, 0 gaps
- [ ] `tools/validate-sweep.js` — 0 invalid-block recoveries
- [ ] `tools/audit-responsive.js` — 0 overflow / overlap / zero-area buttons

---

## Phase 11 — Cutover at launch

- [ ] Final article re-sync (Phase 8) + **hard-freeze** old-site editing.
- [ ] `wp option update blog_public 1` → indexing + canonicals return automatically.
- [ ] Point **DNS** to the Cloudways server.
- [ ] Post-launch smoke test: form submission, a few page loads, analytics firing, sitemap, robots.

---

## Per-site reuse (sites #2, #3 …)

- Same repo/codebase via git; each site = **its own Cloudways server + its own DB/content**.
- Differences per site: `<CHILD_THEME>`, `<OLD_DOMAIN>`/`<NEW_DOMAIN>`, the content seed, and the domain search-replace in Phase 6 (now a real replace, not a no-op).
- Everything else (clean build, mu-plugin triage, hardening, verify) is identical. Keep the **dev stack as the factory** — build/iterate code there, deploy to each site's server via git.

---

## Appendix — Incremental changes (after a site is live)

*The phases above are the **one-time** migration. Once a server is running, every ongoing change follows this much shorter loop. The golden rule: **code deploys itself on merge; DB/template/content changes are applied on the server by hand.***

> **How code actually reaches the server (corrected — GitHub Actions, not Cloudways git).** The Cloudways app is **not** a git checkout — it's a standard WP install (normal `wp-content`, no `.git`). Code lands via a **GitHub Actions allowlist rsync** (REH-19/47): merging to `main` triggers `.github/workflows/deploy.yml`, which runs `scripts/deploy-to-cloudways.sh` to `rsync` only the allowlisted dirs over SSH (stamped at deploy time). There is **no `git pull` step** and no Cloudways git integration — don't go looking for a repo on the server. What the deploy *can't* do is touch the database, so anything DB-shaped is a manual WP-CLI step.

### The deploy loop

1. **Build + verify on the dev stack** (localhost:8081), exactly as you did for the original build.
2. **Land it in git the normal way:** issue → `reh-NN-…` branch → PR → merge to `main`. (Linear status rides the git events.) **The merge triggers the deploy** — the GitHub Actions workflow rsyncs the allowlist to the brand's server (`BRAND` selects the child theme, REH-47).
3. **Confirm the code arrived** over SSH (`ssh -i ~/.ssh/cw_deploy_key master_<…>@<server-ip>`), then from `<APP_PATH>`:

   ```bash
   # a changed theme file should already be present, stamped at deploy time:
   wp eval 'echo file_exists(get_template_directory()."/<changed-file>.php") ? "OK\n" : "MISSING — deploy not through yet\n";'
   ```

4. **Apply any DB/template/content step by hand** — the deploy moves *code only*. Anything that lives in the DB (page-template assignment, an option, content) must be run with WP-CLI on the server, because **`zz-oneshot.php` is not deployed to prod** (Phase 2 triage). Example — repointing a page at a new template:

   ```bash
   ID=$(wp post list --post_type=page --name=<slug> --field=ID)
   wp post meta update "$ID" _wp_page_template <template-file>.php
   wp post meta get "$ID" _wp_page_template   # verify
   ```

   > If a dev change relied on a oneshot/builder task, port that task's effect into an explicit WP-CLI command here. Never deploy `zz-oneshot.php` to prod to "just run it" — if you must, deploy it **temporarily**, run, then delete (Phase 2 note).

5. **Purge caches** — Breeze caches the page HTML + the combined CSS/JS bundle behind Varnish, so a change (even a template switch) won't show to normal visitors until you purge:

   ```bash
   wp breeze purge --cache=all      # NOT `wp breeze purge varnish` — that errors. Flag is --cache=<all|varnish|local>
   # the master user has no varnishadm/sudo, so Breeze CLI (or the Cloudways panel -> Purge Varnish) is the only way
   ```

6. **Verify on the live URL.** Mind two cache gotchas: a `?nocache=$RANDOM` query string bypasses Varnish (tests the backend), and **Breeze minifies the HTML onto few lines** so `grep -c` (counts lines) lies — use `grep -o <class> | wc -l` (counts occurrences). Then eyeball desktop + mobile.

   ```bash
   curl -s -A Mozilla "https://<host>/<path>/" | grep -o <expected-class> | wc -l
   ```

### Rollback

- **Code:** `git revert` the commit → merge → the deploy workflow ships the revert → purge.
- **A DB/template switch:** just set the meta back — it's reversible and content is untouched. E.g. `wp post meta update "$ID" _wp_page_template <old-template>.php`, then `wp breeze purge --cache=all`.

### Worked example — REH-24 "All Treatments hub" (17 Jun 2026)

`template-treatments-hub.php` + `treatments-hub.css` + a `functions.php` enqueue line. Merging to `main` auto-deployed all three to the server; the page stayed broken until the **manual** DB step: `wp post meta update 1219 _wp_page_template template-treatments-hub.php` → `wp breeze purge --cache=all` → verified 5 `rehab-tx-section` / 33 `rehab-tx-link` through Varnish. The page's old plain-list content was left in the DB as a one-command rollback (`… template-treatment.php`).

### Worked example — REH-66 "Brand images → Media Library" (4 Jul 2026)

9 treatment-page brand assets (press/partner logos, ministry badge, hero, founder photo) were baked into `themes/diamond-child/assets/img/treatment/` where the editorial team couldn't manage them. Fix: relocate to the Media Library at the **stable** path `uploads/brand/<file>` (not a month folder — keeps URLs identical dev↔prod). The dev builders + baked `post_content` were repointed on the dev stack via two oneshots (`import-treatment-brand-media`, then `repoint-brand-image-urls&apply=1`). **Prod is DB + media, so it does NOT use those oneshots** — port the effect to WP-CLI from `<APP_PATH>`:

```bash
# 1. Upload the 9 files (uploads are not code-deployed — scp/rsync them in):
#    scp wp-content-dev/themes/diamond-child/assets/img/treatment/{business-insider.png,yahoo-finance.png,\
#    well-good.png,psych-central.png,recovery-com.webp,bangkok-hospital.png,\
#    ministry-public-health-badge.webp,hero-pool-pavilion.avif,founder-theo.avif} \
#    <ssh>:<APP_PATH>/wp-content/uploads/brand/
# 2. Register each as a Media Library attachment in place (no copy, no month folder):
for f in <APP_PATH>/wp-content/uploads/brand/*; do wp media import "$f" --skip-copy; done
# 3. Repoint the baked-in page markup (the builders emit static HTML into post_content).
#    Run BOTH forms: freshly-baked block attrs carry JSON-escaped slashes (\/wp-content\/…)
#    until an editor save normalises them, and a plain replace misses those rows:
wp search-replace '/wp-content/themes/diamond-child/assets/img/treatment/' '/wp-content/uploads/brand/' \
  --precise --skip-columns=guid --report-changed-only
wp search-replace '\/wp-content\/themes\/diamond-child\/assets\/img\/treatment\/' '\/wp-content\/uploads\/brand\/' \
  --precise --skip-columns=guid --report-changed-only
wp breeze purge --cache=all
```

Rollback: `wp search-replace` the two paths in reverse (the theme files are left in place, so the old URL still resolves).
