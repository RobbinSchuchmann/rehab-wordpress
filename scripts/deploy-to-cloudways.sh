#!/usr/bin/env bash
# Deploy the code allowlist to a Cloudways server (REH-19).
#
# Principle: git = code, server = content. This ships ONLY the explicit
# allowlist below. The dangerous dev mu-plugins (zz-oneshot.php ungated
# endpoint, aa-dynamic-host.php host-header injection, aa-block-builders,
# aa-treatment-v3-specs, aa-acf-*) are NEVER deployed.
#
# Used by both CI (.github/workflows/deploy.yml) and humans:
#   DEPLOY_HOST=1.2.3.4 DEPLOY_USER=master_x \
#   DEPLOY_PATH=/home/master/applications/<app>/public_html \
#   ./scripts/deploy-to-cloudways.sh
#
# Optional: DEPLOY_SSH_KEY=/path/to/key   DRY_RUN=1 (preview, no changes)
set -euo pipefail

: "${DEPLOY_HOST:?set DEPLOY_HOST}"
: "${DEPLOY_USER:?set DEPLOY_USER}"
: "${DEPLOY_PATH:?set DEPLOY_PATH (the app public_html)}"

DRY="${DRY_RUN:+--dry-run}"
ssh_cmd="ssh -o StrictHostKeyChecking=accept-new -o ConnectTimeout=20"
[ -n "${DEPLOY_SSH_KEY:-}" ] && ssh_cmd="$ssh_cmd -i $DEPLOY_SSH_KEY -o IdentitiesOnly=yes"

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SRC="$ROOT/wp-content-dev"
DEST="$DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH/wp-content"

echo "Deploying allowlist -> $DEPLOY_HOST:$DEPLOY_PATH/wp-content ${DRY:+(DRY RUN)}"

# Self-contained dirs — fully git-managed, safe to mirror with --delete.
for d in themes/rehab-parent themes/diamond-child plugins/rehab-blocks; do
  echo "  -> $d"
  rsync -az --delete $DRY -e "$ssh_cmd" "$SRC/$d/" "$DEST/$d/"
done

# mu-plugins: ONLY the two production-safe files, and crucially NO --delete,
# so server-only files (e.g. zz-staging-noindex.php) are never removed and the
# dangerous dev mu-plugins are never introduced.
echo "  -> mu-plugins (zz-contact-form.php, zz-redirects.php)"
rsync -az $DRY -e "$ssh_cmd" \
  "$SRC/mu-plugins/zz-contact-form.php" \
  "$SRC/mu-plugins/zz-redirects.php" \
  "$DEST/mu-plugins/"

echo "Done."
