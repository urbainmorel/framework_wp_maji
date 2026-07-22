#!/usr/bin/env bash
#
# Installation complète d'un site MAJI à partir d'une base vide.
#
# Usage :
#   ./scripts/install.sh --url=https://client.example --title="Hôtel Atlantique" \
#     --admin-user=maji --admin-email=tech@maji.digital --admin-password=CHANGEZ-MOI \
#     --dna=demo-content/hotel-business/dna.json [--with-woo]
#
# Prérequis : wp-cli, PHP >= 8.1, base MySQL accessible, wp-config.php présent
# (ou variables DB_* exportées pour `wp config create`).

set -euo pipefail

REPO_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

URL=""
TITLE="Site MAJI"
ADMIN_USER="maji"
ADMIN_EMAIL="tech@example.com"
ADMIN_PASSWORD=""
DNA=""
WITH_WOO=""

for arg in "$@"; do
	case "$arg" in
		--url=*) URL="${arg#*=}" ;;
		--title=*) TITLE="${arg#*=}" ;;
		--admin-user=*) ADMIN_USER="${arg#*=}" ;;
		--admin-email=*) ADMIN_EMAIL="${arg#*=}" ;;
		--admin-password=*) ADMIN_PASSWORD="${arg#*=}" ;;
		--dna=*) DNA="${arg#*=}" ;;
		--with-woo) WITH_WOO="--with-woo" ;;
		*) echo "Option inconnue : $arg" >&2; exit 1 ;;
	esac
done

if [[ -z "$URL" || -z "$DNA" || -z "$ADMIN_PASSWORD" ]]; then
	echo "Options obligatoires : --url, --dna, --admin-password" >&2
	exit 1
fi

echo "→ Téléchargement de WordPress (fr_FR)…"
if [[ ! -f wp-load.php ]]; then
	wp core download --locale=fr_FR --skip-content
fi

if ! wp core is-installed 2>/dev/null; then
	echo "→ Installation de WordPress…"
	wp core install \
		--url="$URL" \
		--title="$TITLE" \
		--admin_user="$ADMIN_USER" \
		--admin_password="$ADMIN_PASSWORD" \
		--admin_email="$ADMIN_EMAIL" \
		--locale=fr_FR
fi

echo "→ Installation du thème et du plugin MAJI…"
mkdir -p wp-content/themes wp-content/plugins
if [[ -f "$REPO_DIR/dist/maji-framework.zip" ]]; then
	wp theme install "$REPO_DIR/dist/maji-framework.zip" --force
	wp plugin install "$REPO_DIR/dist/maji-core.zip" --force --activate
else
	cp -R "$REPO_DIR/themes/maji-framework" wp-content/themes/
	cp -R "$REPO_DIR/plugins/maji-core" wp-content/plugins/
	( cd wp-content/plugins/maji-core && composer install --no-dev --no-interaction --quiet ) || true
	wp plugin activate maji-core
fi
wp theme activate maji-framework

echo "→ Réglages de base…"
wp option update timezone_string "Africa/Porto-Novo"
wp option update permalink_structure "/%postname%/"
wp rewrite flush

echo "→ Provision MAJI…"
wp maji provision --dna="$DNA" $WITH_WOO

echo "✔ Site provisionné : $URL"
echo "  Pensez à : wp maji dna register $DNA --registry=$REPO_DIR/registry/registry.json"
