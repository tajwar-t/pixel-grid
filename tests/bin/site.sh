#!/usr/bin/env bash
# Throwaway WordPress site for theme tests.
# Usage: tests/bin/site.sh up [--woo]|down
#   --woo  also installs WooCommerce from a local, already downloaded copy (never fetched here):
#          $PIXELGRID_WOO_CACHE, or the default path below, must hold the unpacked woocommerce/ folder.
set -u

SITE_DIR="E:/laragon/www/unique/fresh"
SITE_WIN='E:\laragon\www\unique\fresh'
CORE_SRC="E:/laragon/www/unique"
DB_NAME="pixelgrid_fresh"
SITE_URL="http://unique.test/fresh"
THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
THEME_LINK="$SITE_DIR/wp-content/themes/pixelgrid"
THEME_LINK_WIN="$SITE_WIN"'\wp-content\themes\pixelgrid'
CRED_FILE="$THEME_DIR/.superpowers/test-site.txt"
SNAPSHOT="$THEME_DIR/tests/.clean.sql"
WOO_CACHE="${PIXELGRID_WOO_CACHE:-C:/Users/BITBIR~1/AppData/Local/Temp/claude/15f55f0ab9153879/01673bcc-b1b8-42d1-8678-d2f2fd9a7330/scratchpad/woo-download/extract/woocommerce}"

wpq() { wp --path="$SITE_DIR" "$@" 2> >(grep -v Deprecated >&2) | grep -v Deprecated; return "${PIPESTATUS[0]}"; }

remove_junction() {
	if [ -e "$THEME_LINK" ] || [ -L "$THEME_LINK" ]; then
		cmd //c rmdir "$THEME_LINK_WIN" >/dev/null 2>&1
		if [ -e "$THEME_LINK" ]; then
			echo "ERROR: junction still present at $THEME_LINK; refusing to continue." >&2
			return 1
		fi
	fi
	return 0
}

up() {
	local woo="${1:-}"
	if [ -n "$woo" ] && [ "$woo" != "--woo" ]; then
		echo "Usage: $0 up [--woo]|down" >&2
		exit 2
	fi
	if [ -n "$woo" ] && [ ! -f "$WOO_CACHE/woocommerce.php" ]; then
		echo "ERROR: no WooCommerce at $WOO_CACHE (set PIXELGRID_WOO_CACHE)." >&2
		exit 1
	fi
	if [ -e "$SITE_DIR" ]; then
		echo "ERROR: $SITE_DIR already exists. Run 'site.sh down' first." >&2
		exit 1
	fi
	set -e
	mkdir -p "$SITE_DIR"
	cp -r "$CORE_SRC/wp-admin" "$CORE_SRC/wp-includes" "$SITE_DIR/"
	for f in "$CORE_SRC"/*.php; do
		[ "$(basename "$f")" = "wp-config.php" ] && continue
		cp "$f" "$SITE_DIR/"
	done
	mkdir -p "$SITE_DIR/wp-content/themes" "$SITE_DIR/wp-content/plugins" "$SITE_DIR/wp-content/uploads"
	echo "<?php // Silence is golden." > "$SITE_DIR/wp-content/index.php"

	mysql -uroot -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4;"

	local pass
	pass="$(head -c 24 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c 20)"
	mkdir -p "$(dirname "$CRED_FILE")"
	printf 'user=freshadmin\npass=%s\n' "$pass" > "$CRED_FILE"

	wpq config create --dbname="$DB_NAME" --dbuser=root --dbpass= --dbhost=localhost --skip-check
	wpq core install --url="$SITE_URL" --title="Pixelgrid Test" --admin_user=freshadmin \
		--admin_password="$pass" --admin_email=fresh@example.test --skip-email

	powershell -NoProfile -Command "New-Item -ItemType Junction -Path '$THEME_LINK_WIN' -Target '$(cygpath -w "$THEME_DIR")'" >/dev/null
	[ -e "$THEME_LINK/style.css" ] || { echo "ERROR: junction creation failed" >&2; exit 1; }

	wpq theme activate pixelgrid
	wpq option update fresh_site 1
	if [ -n "$woo" ]; then
		# A real copy, not a junction, so 'down' can delete the site folder safely.
		cp -r "$WOO_CACHE" "$SITE_DIR/wp-content/plugins/woocommerce" || { echo "ERROR: could not copy WooCommerce from $WOO_CACHE" >&2; exit 1; }
		wpq plugin activate woocommerce || { echo "ERROR: could not activate WooCommerce" >&2; exit 1; }
		# Skip the onboarding wizard and the task list.
		wpq option update woocommerce_onboarding_profile '{"skipped":true}' --format=json
		# WooCommerce maps this deprecated option to woocommerce_task_list_hidden_lists, so the
		# update reports a failure even when it worked: check the value instead.
		wpq option update woocommerce_task_list_hidden yes >/dev/null 2>&1 || true
		wpq option get woocommerce_task_list_hidden | grep -q '^yes' || { echo "ERROR: could not hide the WooCommerce task list" >&2; exit 1; }
		if ! wpq wc tool run install_pages --user=freshadmin >/dev/null; then
			wpq eval 'WC_Install::create_pages();' --user=freshadmin || { echo "ERROR: could not create the WooCommerce pages" >&2; exit 1; }
		fi
		wpq option get woocommerce_cart_page_id | grep -q '^[1-9]' || { echo "ERROR: WooCommerce pages are missing" >&2; exit 1; }
	fi
	# Drop the cached list of theme pattern files, so each test reads the patterns folder as it is now.
	# mysql directly: wp db query goes through cmd on Windows, which expands the % wildcards.
	mysql -uroot "$DB_NAME" -e "DELETE FROM $(wpq db prefix)options WHERE option_name LIKE '\_site\_transient\_%wp\_theme\_files\_patterns-%';"
	wpq db export "$SNAPSHOT" --quiet
	echo "Site up at $SITE_URL${woo:+ with WooCommerce} (snapshot: $SNAPSHOT)"
}

down() {
	remove_junction || exit 1
	mysql -uroot -e "DROP DATABASE IF EXISTS $DB_NAME;"
	if [ -e "$THEME_LINK" ]; then
		echo "ERROR: junction still exists; not deleting site folder." >&2
		exit 1
	fi
	rm -rf "$SITE_DIR"
	echo "Site removed."
}

case "${1:-}" in
	up) up "${2:-}" ;;
	down) down ;;
	*) echo "Usage: $0 up [--woo]|down" >&2; exit 2 ;;
esac
