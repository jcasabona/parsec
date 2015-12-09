<?php
/*
 * Functions and such specifically added for parsec.
*/

/**
 * Apply styles to the visual editor
 */
add_filter( 'mce_css', 'parsec_mcekit_editor_style');

function parsec_mcekit_editor_style( $url ) {

    if ( ! empty( $url ) ) {
        $url .= ',';
    }

    $url .= trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/editor-styles.css';

    return $url;
}
