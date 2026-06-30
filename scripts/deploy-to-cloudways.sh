#!/usr/bin/env bash
# Deploy the code allowlist to a Cloudways server (REH-19, brand-parameterized REH-47).
#
# Principle: git = code, server = content. This ships ONLY the explicit
# allowlist below. The dangerous dev mu-plugins (zz-oneshot.php ungated
# endpoint, aa-dynamic-host.php host-header injection, aa-block-builders,
# aa-treatment-v3-specs, aa-acf-*) are NEVER deployed.
#
# One server = one brand. The shared engine (themes/rehab-parent +
# plugins/rehab-blocks) ships to every server; the brand's child theme is
# selected by BRAND (themes/${BRAND}-child). BRAND defaults to "diamond".
#
# Used by both CI (.github/workflows/deploy.yml) and humans:
#   BRAND=diamond \
#   DEPLOY_HOST=1.2.3.4 DEPLOY_USER=master_x \
#   DEPLOY_PATH=/home/master/applications/<app>/public_html \
#   ./scripts/deploy-to-cloudways.sh
#
# Optional: DEPLOY_SSH_KEY=/path/to/key   DRY_RUN=1 (preview, no changes)
set -euo pipefail

: "${DEPLOY_HOST:?set DEPLOY_HOST}"
: "${DEPLOY_USER:?set DEPLOY_USER}"
: "${DEPLOY_PATH:?set DEPLOY_PATH (the app public_html)}"

BRAND="${BRAND:-diamond}"

DRY="${DRY_RUN:+--dry-run}"
ssh_cmd="ssh -o StrictHostKeyChecking=accept-new -o ConnectTimeout=20"
[ -n "${DEPLOY_SSH_KEY:-}" ] && ssh_cmd="$ssh_cmd -i $DEPLOY_SSH_KEY -o IdentitiesOnly=yes"

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SRC="$ROOT/wp-content-dev"
DEST="$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/wp-content"

CHILD_THEME="themes/${BRAND}-child"
if [ ! -d "$SRC/$CHILD_THEME" ]; then
  echo "ERROR: no child theme for BRAND='$BRAND' (expected wp-content-dev/$CHILD_THEME)" >&2
  echo "Available brands:" >&2
  for d in "$SRC"/themes/*-child; do [ -d "$d" ] && echo "  - $(basename "$d" | sed 's/-child$//')" >&2; done
  exit 1
fi

echo "Deploying BRAND='$BRAND' -> $DEPLOY_HOST:$DEPLOY_PATH/wp-content ${DRY:+(DRY RUN)}"

# Self-contained dirs — fully git-managed, safe to mirror with --delete.
# The shared engine plus exactly one brand's child theme.
for d in themes/rehab-parent "$CHILD_THEME" plugins/rehab-blocks; do
  echo "  -> $d"
  rsync -az --delete $DRY -e "$ssh_cmd" "$SRC/$d/" "$DEST/$d/"
done

# mu-plugins: ONLY the production-safe files, and crucially NO --delete,
# so server-only files (e.g. zz-staging-noindex.php) are never removed and the
# dangerous dev mu-plugins are never introduced.
echo "  -> mu-plugins (zz-contact-form.php, zz-redirects.php, zz-mail-from.php)"
rsync -az $DRY -e "$ssh_cmd" \
  "$SRC/mu-plugins/zz-contact-form.php" \
  "$SRC/mu-plugins/zz-redirects.php" \
  "$SRC/mu-plugins/zz-mail-from.php" \
  "$DEST/mu-plugins/"

# Purge Breeze cache so the new code is visible to visitors. Breeze caches the
# page HTML + the combined CSS/JS bundle behind Varnish, so a deploy won't show
# until purged (the master user has no varnishadm/sudo, so the Breeze CLI is the
# only way — REH-56). Non-fatal: the code has already shipped by this point, so a
# purge failure warns rather than failing the deploy. Skipped on dry runs.
if [ -z "$DRY" ]; then
  echo "  -> purging Breeze cache (wp breeze purge --cache=all)"
  if $ssh_cmd "$DEPLOY_USER@$DEPLOY_HOST" "cd '$DEPLOY_PATH' && wp breeze purge --cache=all"; then
    echo "     cache purged"
  else
    echo "WARNING: cache purge failed — purge manually via 'wp breeze purge --cache=all' or the Cloudways panel (Purge Varnish)." >&2
  fi
else
  echo "  -> (dry run) skipping cache purge"
fi

echo "Done."
