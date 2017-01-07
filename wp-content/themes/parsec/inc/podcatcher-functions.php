<?php
/**
 * Custom template tags for this theme + WP Podcatcher plugin.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Parsec
 */

/**
 * Function to check if WP Podcatcher is activated.
 *
 * @return boolean
 */
function has_podcatcher() {
	return defined( 'WPP_VERSION' );
}
