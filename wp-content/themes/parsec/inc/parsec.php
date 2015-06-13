<?php
/*
 * Functions and such specifically added for parsec.
*/

/** Responsive Images **/
//@TODO: Make these functions into a class

function parsec_get_featured_image( $html, $aid = null ){

  //@TODO: get these dynamically
  $sizes= array( 'full' => 800,
            'large' => 600,
            'medium' => 300,
            'thumbnail' => 50 );

	$img = '<picture>';
	$ct = 0;
	$aid = ( is_null( $aid ) ) ? get_post_thumbnail_id() : $aid;
  $format = '<source srcset="%1$s" media="(min-width: %2$spx)">';

	foreach ( $sizes as $size => $width ){
		$url = wp_get_attachment_image_src( $aid, $size );

    if ( 'medium' == $size ) {
      $default_img = sprintf( '<img srcset="%1$s" alt="%2$s">',
          esc_attr( $url[0] ),
          get_the_title()
      );
    }

		/* @TODO: Revisit this math
		* $width = ( $ct < sizeof( $sizes ) - 1 ) ? ( $url[1] * 0.66 ) : ( $width/0.66 ) + 25;
    */

    $img .= sprintf( $format, $url[0], $width );

		$ct++;
	}
  $img .= $default_img;
  $img .= '</picture>';

	return $img;
}

add_filter( 'post_thumbnail_html', 'parsec_get_featured_image' );

//@TODO: Revisit this function
function parsec_responsive_image(  $atts, $content=null ){

	extract( shortcode_atts( array(
		'src' => false
	), $atts ) );

	if ( ! $src ) {
		return '';
	} else {
		$aid = parsec_get_attachment_id_from_src( $src );
		$img = parsec_get_featured_image( '', $aid );
	}

	return $img;
}

add_shortcode('parsec_image', 'parsec_responsive_image');

function parsec_get_attachment_id_from_src($url) {
  global $wpdb;

  $prefix = $wpdb->prefix;
  $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $url ) );

  return $attachment[0];
}

//@TODO: Optimize / update
function parsec_replace_post_images($content){
	global $post;

	preg_match('#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#', $content, $matches);
	foreach($matches as $match){
		print $match;
		preg_replace( $match, parsec_get_featured_image(mf_get_attachment_id_from_src ($match)), $content );
	}
	 return $content;
}
//add_filter('the_content', 'parsec_replace_post_images');
