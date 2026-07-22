#!/usr/bin/env bash
#
# Construit les zips de release consommés par la mise à jour de flotte :
#   dist/maji-framework.zip  (thème)
#   dist/maji-core.zip       (plugin, vendor de prod inclus)
#
# Utilisé par la CI (release.yml) au tag vX.Y.Z.

set -euo pipefail

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DIST="$REPO_DIR/dist"
STAGE="$(mktemp -d)"

trap 'rm -rf "$STAGE"' EXIT

mkdir -p "$DIST"
rm -f "$DIST/maji-framework.zip" "$DIST/maji-core.zip"

echo "→ Thème maji-framework…"
cp -R "$REPO_DIR/themes/maji-framework" "$STAGE/maji-framework"
( cd "$STAGE" && zip -rq "$DIST/maji-framework.zip" maji-framework -x "*/node_modules/*" -x "*/.DS_Store" )

echo "→ Plugin maji-core…"
cp -R "$REPO_DIR/plugins/maji-core" "$STAGE/maji-core"
( cd "$STAGE/maji-core" && composer install --no-dev --no-interaction --optimize-autoloader --quiet )
( cd "$STAGE" && zip -rq "$DIST/maji-core.zip" maji-core \
	-x "maji-core/node_modules/*" -x "maji-core/tests/*" -x "*/.DS_Store" )

echo "✔ Zips construits dans $DIST :"
ls -lh "$DIST"
