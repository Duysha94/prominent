#!/usr/bin/env bash
# Rebuild wordpress/ak-zeyna-child.zip from the wordpress/ak-zeyna-child/ tree.
# Run from the repo root. The zip root is the ak-zeyna-child/ directory itself,
# which is what WordPress's theme uploader expects.
set -euo pipefail
cd "$(dirname "$0")/../wordpress"
rm -f ak-zeyna-child.zip
zip -r -X ak-zeyna-child.zip ak-zeyna-child \
  -x 'ak-zeyna-child/.DS_Store' -x '*/.DS_Store'
unzip -l ak-zeyna-child.zip | tail -3

# Regenerate the update manifest the theme's self-updater polls.
VERSION=$(grep -m1 '^Version:' ak-zeyna-child/style.css | awk '{print $2}')
mkdir -p update
cat > update/update.json <<JSON
{
  "version": "$VERSION",
  "package": "https://duysha94.github.io/prominent/theme/ak-zeyna-child.zip",
  "details": "https://duysha94.github.io/prominent/"
}
JSON
echo "manifest: version $VERSION"
