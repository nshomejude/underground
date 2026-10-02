#!/usr/bin/env bash
# Runs `npm run build` while holding a directory lock so parallel workers never
# build at the same time (vite empties public/build). Usage: bash deploy/build-locked.sh
set -euo pipefail
cd "$(dirname "$0")/.."
LOCK="${TMPDIR:-/tmp}/ug-build.lock"
for i in $(seq 1 120); do
  if mkdir "$LOCK" 2>/dev/null; then
    trap 'rmdir "$LOCK" 2>/dev/null || true' EXIT
    npm run build 2>&1 | grep -E "built|rror" || true
    exit 0
  fi
  sleep 2
done
echo "could not get the build lock after 4 minutes" >&2
exit 1
