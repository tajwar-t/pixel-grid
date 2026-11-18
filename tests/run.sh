#!/usr/bin/env bash
# Run theme tests on the throwaway site. Usage: tests/run.sh [test-file...]
set -u
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SITE_DIR="E:/laragon/www/unique/fresh"
SNAPSHOT="$ROOT/tests/.clean.sql"

if [ ! -f "$SNAPSHOT" ] || [ ! -d "$SITE_DIR" ]; then
	echo "Throwaway site is not up. Run: bash tests/bin/site.sh up" >&2
	exit 1
fi

if [ "$#" -gt 0 ]; then files=("$@"); else files=("$ROOT"/tests/test-*.php); fi

wpq() { wp --path="$SITE_DIR" "$@" 2>&1 | grep -v Deprecated; return "${PIPESTATUS[0]}"; }

status=0
for f in "${files[@]}"; do
	echo "== $f"
	wpq db import "$SNAPSHOT" >/dev/null || { echo "FAIL: could not reset database"; status=1; continue; }
	f_abs="$(cd "$(dirname "$f")" && pwd)/$(basename "$f")"
	wpq eval-file "$f_abs" --user=freshadmin --require="$ROOT/tests/bootstrap.php"
	[ "${PIPESTATUS[0]}" -eq 0 ] || status=1
done
exit $status
