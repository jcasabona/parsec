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

/**
 * Function that returns text links of sponsors.
 *
 * @param $post_id int Post's ID.
 * @return String.
 */
function parsec_sponsor_text_links( $post_id = null ) {
	$post_id = ( ! empty( $post_id ) ) ? $post_id : get_the_id();
	$sponsor_ids = get_post_meta( $post_id, 'wpp_episode_sponsor', true );
	if ( ! $sponsor_ids ) {
		return;
	}

	$sponsor_text_links = '';
	$format = '<a href="%1$s" title="%2$s">%2$s</a> | ';

	foreach ( $sponsor_ids as $id ) {
		$sponsor_text_links .= sprintf( $format,
			esc_url( get_the_permalink( $id ) ),
			esc_attr( get_the_title( $id ) )
		);
	}

	return substr( $sponsor_text_links, 0, strlen( $sponsor_text_links ) - 3 );
}
