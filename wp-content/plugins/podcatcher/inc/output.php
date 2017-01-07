<?php
/**
 *  Functions to control the output of sponsors & more.
 *
 * @package wp_podcatcher
 */

/**
 * Callback function to insert sponsors into content on episode pages.
 *
 * @param String $content from WordPress editor.
 */
function wpp_append_sponsors( $content ) {
	wpp_get_latest_episode();

	return $content . wpp_get_sponsors();
}

// Filter uses above function.
add_filter( 'the_content', 'wpp_append_sponsors' );
