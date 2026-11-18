<?php
/**
 * Test helpers, loaded before every test file.
 */
if ( ! function_exists( 'pg_assert' ) ) {
	$GLOBALS['pg_failures'] = 0;

	function pg_assert( $cond, $label ) {
		if ( $cond ) {
			echo "PASS: $label\n";
		} else {
			echo "FAIL: $label\n";
			$GLOBALS['pg_failures']++;
		}
	}

	function pg_assert_same( $expected, $actual, $label ) {
		if ( $expected === $actual ) {
			echo "PASS: $label\n";
		} else {
			echo "FAIL: $label\n  expected: " . var_export( $expected, true ) . "\n  actual:   " . var_export( $actual, true ) . "\n";
			$GLOBALS['pg_failures']++;
		}
	}

	function pg_done() {
		exit( $GLOBALS['pg_failures'] > 0 ? 1 : 0 );
	}
}
