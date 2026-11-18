<?php
/**
 * Smoke test: the throwaway site is up with the theme active.
 */
pg_assert( wp_get_theme()->get_stylesheet() === 'pixelgrid', 'theme pixelgrid is active' );
pg_assert( 1 == get_option( 'fresh_site' ), 'fresh_site option is set' );
pg_done();
