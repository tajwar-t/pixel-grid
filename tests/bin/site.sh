#!/usr/bin/env bash
# Throwaway WordPress site for theme tests.
# Usage: tests/bin/site.sh up|down
set -u

SITE_DIR="E:/laragon/www/unique/fresh"
SITE_WIN='E:\laragon\www\unique\fresh'
CORE_SRC="E:/laragon/www/unique"
DB_NAME="pixelgrid_fresh"
SITE_URL="http://unique.test/fresh"
THEME_DIR="$(git rev-parse --show-toplevel)"
THEME_LINK="$SITE_DIR/wp-content/themes/pixelgrid"
THEME_LINK_WIN="$SITE_WIN"'\wp-content\themes\pixelgrid'
CRED_FILE="$THEME_DIR/.superpowers/test-site.txt"
SNAPSHOT="$THEME_DIR/tests/.clean.sql"

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
	wpq db export "$SNAPSHOT" --quiet
	echo "Site up at $SITE_URL (snapshot: $SNAPSHOT)"
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
	up) up ;;
	down) down ;;
	*) echo "Usage: $0 up|down" >&2; exit 2 ;;
esac
