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
