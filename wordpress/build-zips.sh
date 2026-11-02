#!/usr/bin/env bash
# Build the installable ZIPs for the AK Studio theme and the AK Brand Core plugin into dist/.
set -euo pipefail
cd "$(dirname "$0")"
mkdir -p dist
rm -f dist/ak-studio.zip dist/ak-brand-core.zip
zip -qr -X dist/ak-studio.zip ak-studio -x '*/.DS_Store' '*/node_modules/*'
zip -qr -X dist/ak-brand-core.zip ak-brand-core -x '*/.DS_Store' '*/node_modules/*'
ls -lh dist/*.zip
