<?php
/***Shortcodes**/

function cse_show_events($atts, $content=null){
	extract( shortcode_atts( array(
		'num' => -1,
		'previous' => false,
		'short' => false
	), $atts ) );

	$events = ($short) ? cse_short_events($num, $previous) : cse_print_events($num, $previous);
	return $events;

}

add_shortcode('cse_events', 'cse_show_events');


function cse_load_scripts(){
    wp_enqueue_script('timepicker', CSEPATH.'scripts/jquery.timepicker.js', array('jquery'));
    wp_enqueue_script('cseadmin', CSEPATH.'scripts/cse-scripts.js', array('timepicker'));
    wp_register_style( 'timepicker-style', CSEPATH.'scripts/cse-styles.css');
    wp_enqueue_style( 'timepicker-style' );
}

add_action( 'admin_enqueue_scripts', 'cse_load_scripts' );



function cse_print_events( $numPosts=-1, $previous=false ) {

global $post;

$compare= ( $previous ) ? '<' : '>=';
$order= ( $previous ) ? 'DESC' : 'ASC';

$args = array( 'post_type' => 'speaking-events',
				'orderby' => 'meta_value',
				'meta_key' => 'unixdate',
				'meta_value' => date('Y-m-d'),
				'meta_compare' => $compare,
				'order' => $order,
				'posts_per_page' => $numPosts
		);

$events = get_posts( $args );

$output = '<dl class="cse-events">';

if ( ! empty( $events ) ) {

	$format = '<div>
			<dt class="cse-title">%1$s: %2$s</dt>
			<dd class="cse-location">%3$s at <a href="https://www.google.com/maps/preview#!q=%4$s">%4$s</a></dd>
			<dd class="cse-content">%5$s</dd>
			%6$s
		</div>';

	foreach( $events as $post ) {
		setup_postdata($post);
		$title= apply_filters( 'the_title', wp_kses_post( get_the_title() ) );
		$desc=  apply_filters( 'the_content', wp_kses_post( get_the_content() ) );
		$event_name= get_post_custom_values('eventname');
		$event_name= apply_filters( 'the_title', $event_name[0] );
		$loc= get_post_custom_values('location');
		$loc= $loc[0];
		$date= get_post_custom_values('eventdate');
		$date= $date[0];
		$pres= get_post_custom_values('preslink');
		$pres= ($pres[0] != "") ? '<dd class="cse-slides"><a href="'.esc_url( $pres[0] ).'">Slides and Resources</a></dd>' : "";

		$output .= sprintf( $format,
			esc_html( $date ),
			$title,
			esc_html( $event_name ),
			esc_attr( $loc ),
			$desc,
			$pres
		);
	}
	wp_reset_postdata();

} else {
	$output .= '<div class="cse-no-events">
		<dd>Nothing scheduled for right now, but check back soon!</dd>
		</div>';
}

$output .= '</dl>';

return $output;

}


function cse_short_events($numPosts=-1, $previous=false){

global $post;
$events= "<table class=\"sessions\">\n";

$compare= ($previous) ? '<' : '>=';
$order= ($previous) ? 'DESC' : 'ASC';

$args = array( 'post_type' => 'speaking-events',
				'orderby' => 'meta_value',
				'meta_key' => 'unixdate',
				'meta_value' => date('Y-m-d'),
				'meta_compare' => $compare,
				'order' => $order,
				'posts_per_page' => $numPosts
		);

$myposts = get_posts( $args );

foreach( $myposts as $post ) : setup_postdata($post); ?>

		<?php
			$title= str_ireplace('"', '', trim(get_the_title()));
			$desc= str_ireplace('"', '', trim(get_the_content()));
			$eventName= get_post_custom_values('eventname');
			$eventName= $eventName[0];
			$loc= get_post_custom_values('location');
			$loc= $loc[0];
			$date= get_post_custom_values('eventdate');
			$date= $date[0];
			$events.="<tr>";
			$events.= "<td> $title</td>
						<td>$date</td>
						<td>$loc</td>";
			$events.="</tr>";


endforeach;

$events.="</table>";


return $events;

}


add_shortcode( 'cse_events', 'cse_show_events' );

function cse_single_loop(){

	if (have_posts()) : while (have_posts()) : the_post();

			$author= get_post_custom_values('author');
			$amzn= get_post_custom_values('amazonLink');
			$status= get_post_custom_values('bookStatus');
			$r= get_post_custom_values('rating');
			if($status[0] == 1) $status[1]= "Reading";
			else if($status[0] == -1) $status[1]= "Finished Reading";
			else $status[1]= "Want to Read";

			$rating= ($status[0] == -1 && $r[0] != "") ? readlist_build_rating($r[0]) : "--";
		?>

			<h1 class="entry-title full-title"><em><?php the_title(); ?></em> by: <?php print $author[0]; ?></h1>
			<p class="status">Status: <?php print $status[1]; ?> <?php if($status[0] == -1){ ?> | Rating: <?php print $rating; ?> <?php } ?></p>
			<p><?php if($amzn[0] != "") print '<p><a href="'.$amzn[0].'">Get <em>'. get_the_title() .'</em> on Amazon</a></p>'; ?></p>

			<?php the_content(); ?>


	<?php endwhile; endif;
}


?>
