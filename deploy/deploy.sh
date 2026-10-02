#!/usr/bin/env bash
# Deploys the app code to the Namecheap shared host (un-der.com).
#
#   bash deploy/deploy.sh
#
# Uploads app code and the freshly built Vite assets over SSH (tar stream),
# runs pending migrations and rebuilds the caches. It never touches the
# server's .env, storage/ or vendor/. Run `composer install` by hand and
# upload vendor/ separately only when dependencies change.
set -euo pipefail

cd "$(dirname "$0")/.."

SSH_KEY="${DEPLOY_KEY:-$HOME/.ssh/underground_deploy}"
SSH_TARGET="undejrjd@68.65.120.191"
SSH_PORT=21098
REMOTE_PHP=/opt/alt/php83/usr/bin/php
ssh_cmd=(ssh -i "$SSH_KEY" -p "$SSH_PORT" -o BatchMode=yes "$SSH_TARGET")

echo "==> Building assets"
npm run build >/dev/null

echo "==> Uploading application code"
tar -c app bootstrap/app.php bootstrap/providers.php config database/migrations database/seeders database/factories resources routes src composer.json composer.lock artisan 2>/dev/null \
    | "${ssh_cmd[@]}" 'tar -x -C ~/underground' 2>&1 | grep -v '^\*\*' || true

echo "==> Uploading public assets"
tar -c -C public build images favicon.ico robots.txt llms.txt manifest.webmanifest sw.js offline.html \
    | "${ssh_cmd[@]}" 'tar -x -C ~/public_html' 2>&1 | grep -v '^\*\*' || true

echo "==> Migrating and rebuilding caches"
"${ssh_cmd[@]}" "cd ~/underground && P=$REMOTE_PHP && \$P artisan migrate --force && \$P artisan optimize:clear >/dev/null && \$P artisan config:cache >/dev/null && \$P artisan route:cache >/dev/null && \$P artisan view:cache >/dev/null && echo done" 2>&1 | grep -v '^\*\*'
