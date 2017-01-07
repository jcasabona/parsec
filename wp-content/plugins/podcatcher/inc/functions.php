<?php
/**
 * A general functions file
 *
 * @package wp-podcatcher
 */

/**
 * Display a notification if Fieldmanager can't be found.
 */
function wppc_no_fm() {
?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php esc_html_e( 'Fieldmanager should be here. Something has gone wrong. Contact joe@wpinonemonth.com', 'wp-podcatcher' ); ?>
			</p>
		</div>
<?php
}

// Start Your Engines.
require_once( 'post-types/parent-class.php' );

/**
 * Generate HTML for displaying sponsors associated with episode.
 *
 * @return HTML string if there are sponsors, false if there are not.
 */
function wpp_get_sponsors( $episode_id = null ) {
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  get_the_id();
	$sponsor_ids = get_post_meta( $episode_id, 'wpp_episode_sponsor', true );

	if ( empty( $sponsor_ids ) ) {
		return false;
	}

	$sponsor_output = '<div class="wpp-episode-sponsors">';
	/**
	 * 1: Sponsor URL
	 * 2: Post Title (the_title)
	 * 3: Logo if available, Title if no Logo
	 * 4: Description (the_content)
	 */
	$format = '<div class="wpp-sponsor"><a href="%1$s" title="%2$s" target="_blank">%3$s</a> <p>%4$s</p></div>';
	$sponsors = new WP_Query( array( 'post_type' => 'sponsor', 'post__in' => $sponsor_ids ) );

	if ( $sponsors->have_posts() ) {
		while ( $sponsors->have_posts() ) {
			$sponsors->the_post();

			$sponsor_link = get_post_meta( get_the_id(), 'wpp_sponsor_link', true ); // @TODO: Check for link.

			$sponsor_link_content = ( has_post_thumbnail() ) ? get_the_post_thumbnail( get_the_id(), 'large' ) : get_the_title();

			$sponsor_output .= sprintf( $format,
				esc_url( $sponsor_link ),
				esc_attr( get_the_title() ),
				$sponsor_link_content,
				get_the_content()
			);
		}
		wp_reset_postdata();
	} else {
		$sponsor_output .= '<h4 class="wpp-no-sponsors">No sponsors this week. Interested?</h4>';
	}

	return $sponsor_output . '</div>'; // Close the div we opened on L53.
}

/**
 * Print results of wpp_get_sponsors()
 *
 * @param $episode_id int ID of post to get sponsors.
 */
function wpp_print_sponsors( $episode_id = null ) {
	$episode_id  = ( ! empty( $episode_id ) ) ? $episode_id :  get_the_id();
	$sponsors = wpp_get_sponsors( $episode_id );
	if ( ! empty( $sponsors ) ) {
		echo $sponsors;
	}
}

/**
 * Get the most recent episode's ID
 */
function wpp_get_latest_episode() {
	$args = array(
		'posts_per_page' => 1,
		'orderby' => 'post_date',
		'order' => 'DESC',
		'meta_key' => 'enclosure', // This is the meta key used by PowerPress.
	);

	$latest_episode = new WP_Query( $args );
	$post_ids = wp_list_pluck( $latest_episode->posts, 'ID' );
	return $post_ids[0];
}

/**
 * Get the next scheduled posts
 *
 * @param $posts_per_page int # of posts to display.
 */
function wpp_get_upcoming_episodes( $posts_per_page = 1 ) {
	$args = array(
		'posts_per_page' => $posts_per_page,
		'post_status' => 'future',
		'orderby' => 'post_date',
		'order' => 'ASC',
		'meta_key' => 'enclosure', // This is the meta key used by PowerPress.
	);

	$next_episodes = new WP_Query( $args );

	$episode_output = '<div class="wpp-upcoming-episodes">';
	/**
	 * 1: Episode Title
	 * 2: Time Stamp
	 * 3: Human Readable Date
	 */
	$format = '<div class="wpp-upcoming-episode"><h4>%1$s</h4><time datetime="%2$s">%3$s</time></div>';

	if ( $next_episodes->have_posts() ) {
		while ( $next_episodes->have_posts() ) {
			$next_episodes->the_post();

			$episode_output .= sprintf( $format,
				esc_attr( get_the_title() ),
				esc_attr( get_the_date( 'c' ) ),
				get_the_date()
			);
		}
		wp_reset_postdata();
	} else {
		$episode_output .= '<h4 class="wpp-no-schedule">There are no scheduled episodes right now.</h4>';
	}

	return $episode_output . '</div>'; // Close the div we opened on L113.
}

/**
 * Print the next scheduled posts
 *
 * @param $posts_per_page int # of posts to display.
 */
function wpp_print_upcoming_episodes( $posts_per_page = 1 ) {
	echo wpp_get_upcoming_episodes();
}

/**
 * Shortcode for next scheduled posts
 *
 * @param $atts Array
 * [wpp_schedule number="1"]
 */
function wpp_schedule_shortcode( $atts ) {
	$a = shortcode_atts( array(
		'number' => -1,
	), $atts );

	return wpp_get_upcoming_episodes( $a['number'] );
}

add_shortcode( 'wpp_schedule', 'wpp_schedule_shortcode' );
