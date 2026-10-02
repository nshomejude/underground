#!/usr/bin/env bash
# Uploads the production Composer dependencies (vendor/) to the shared host.
# Run this whenever composer.json / composer.lock change. vendor/ is built
# in a throwaway staging folder with `--no-dev`, so your local dev vendor/
# is untouched. deploy.sh deliberately never uploads vendor/.
#
#   bash deploy/vendor.sh
set -euo pipefail

cd "$(dirname "$0")/.."

SSH_KEY="${DEPLOY_KEY:-$HOME/.ssh/underground_deploy}"
SSH_TARGET="undejrjd@68.65.120.191"
SSH_PORT=21098
COMPOSER="${COMPOSER:-php /c/laragon/bin/composer/composer.phar}"
ssh_cmd=(ssh -i "$SSH_KEY" -p "$SSH_PORT" -o BatchMode=yes "$SSH_TARGET")

STAGE="$(mktemp -d)"
trap 'cd /; rm -rf "$STAGE"' EXIT

cp composer.json composer.lock "$STAGE/"
mkdir -p "$STAGE/app" "$STAGE/src" "$STAGE/database" "$STAGE/bootstrap"
cp -r app src "$STAGE/" 2>/dev/null || true
cp -r database/factories database/seeders "$STAGE/database/" 2>/dev/null || true

echo "==> Installing production dependencies"
(cd "$STAGE" && $COMPOSER install --no-dev --no-scripts --optimize-autoloader --no-interaction --quiet)

echo "==> Uploading vendor/"
tar -c -C "$STAGE" vendor | "${ssh_cmd[@]}" 'tar -x -C ~/underground' 2>&1 | grep -v '^\*\*' || true

echo "==> Clearing compiled caches"
"${ssh_cmd[@]}" 'cd ~/underground && /opt/alt/php83/usr/bin/php artisan optimize:clear >/dev/null && echo done' 2>&1 | grep -v '^\*\*'
