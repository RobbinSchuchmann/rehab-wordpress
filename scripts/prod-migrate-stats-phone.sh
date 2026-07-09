#!/usr/bin/env bash
#
# Prod content migration — REH-87 (trust stats) + REH-88 (phone numbers).
#
# WHERE:  run ON the Cloudways app server, from the WordPress web root
#         (the directory containing wp-config.php), AFTER the code deploy
#         from the merges has landed.
# HOW:    safe by default — previews only (--dry-run). To actually write:
#           APPLY=1 ./prod-migrate-stats-phone.sh
#
# WHY it's this and not the oneshot: zz-oneshot.php and the aa-* builder
# mu-plugins are never deployed to prod, so `rebuild-treatment-v3-all`
# does not exist there. The stat-band/hero/cta blocks are self-closing
# DYNAMIC blocks that already render the new values from the deployed
# rehab-blocks block.json defaults — so this script only fixes the
# BAKED-STATIC content (pillar sentence, legacy prose pages,
# contact-methods / final-cta).
#
# SAFETY: the WhatsApp number (+66 96 582 3832 / wa.me/66965823832) is a
# different string and is never matched. The stat attribute replaces are
# key-scoped ("stat2Num":"4:1") so they can only hit block JSON, never
# prose or timestamps, and are harmless no-ops where the blocks are
# self-closing.

set -euo pipefail

WP="wp"
COMMON="--precise --skip-columns=guid --report-changed-only"

if [ "${APPLY:-0}" = "1" ]; then
  MODE=""
  echo ">>> APPLY MODE — writing to the database."
else
  MODE="--dry-run"
  echo ">>> DRY RUN — preview only. Re-run with 'APPLY=1 $0' to write."
fi

$WP core is-installed >/dev/null 2>&1 || {
  echo "ERROR: not a WordPress web root. cd into the prod site dir (with wp-config.php) first." >&2
  exit 1
}

echo
echo "### PHONE (REH-88): Thai landline +66 3 313 5303 -> AU +61 2 7908 2277"
$WP search-replace 'tel:+6633135303' 'tel:+61279082277' $COMMON $MODE
$WP search-replace '+66 3 313 5303'  '+61 2 7908 2277'  $COMMON $MODE

echo
echo "### STATS (REH-87): 4:1->2:1, 50+->35, 14+->12+"
# Baked-static pillar 'why residential' sentence (present on every treatment page):
$WP search-replace '4:1 staff-to-client ratio' '2:1 staff-to-client ratio' $COMMON $MODE
# No-op-safe fallbacks, in case any page baked the stat-band/hero attributes
# instead of using defaults (key-scoped, so zero risk to prose/timestamps):
$WP search-replace '"stat2Num":"4:1"' '"stat2Num":"2:1"' $COMMON $MODE
$WP search-replace '"stat3Num":"50+"' '"stat3Num":"35"'  $COMMON $MODE
$WP search-replace '"stat3Num":"14+"' '"stat3Num":"12+"' $COMMON $MODE

if [ "${APPLY:-0}" = "1" ]; then
  echo
  echo "### Purge cache"
  $WP breeze purge --cache=all || echo "(breeze purge unavailable — purge Varnish/Cloudflare manually)"

  echo
  echo "### Verify — each count should be 0"
  PREFIX="$($WP config get table_prefix)"
  for s in '+66 3 313 5303' 'tel:+6633135303' '4:1 staff-to-client ratio'; do
    cnt="$($WP db query "SELECT COUNT(*) FROM ${PREFIX}posts WHERE post_content LIKE '%${s}%'" --skip-column-names)"
    echo "  remaining \"${s}\": ${cnt}  (want 0)"
  done
  echo
  echo "### WhatsApp sanity — should still be > 0 (untouched)"
  wcnt="$($WP db query "SELECT COUNT(*) FROM ${PREFIX}posts WHERE post_content LIKE '%66965823832%'" --skip-column-names)"
  echo "  wa.me/66965823832 rows: ${wcnt}  (want > 0)"
fi

echo
echo "Done."
