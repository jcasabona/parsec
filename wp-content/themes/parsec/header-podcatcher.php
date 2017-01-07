<?php $latest_episode_id = wpp_get_latest_episode(); ?>

<div class="latest-episode">
	<h5>Latest Episode</h5>
	<div class="alignleft featured-image">
		<?php
		if ( has_post_thumbnail( $latest_episode_id ) ) {
			echo get_the_post_thumbnail( $latest_episode_id, 'thumbnail' );
		}
		?>
	</div>
	<h3><?php echo esc_html( get_the_title( $latest_episode_id ) ); ?></h3>
	<div class="episode">
		<?php
			$episode_data = powerpress_get_enclosure_data( $latest_episode_id );
			echo do_shortcode( '[audio src="'. esc_url( $episode_data['url'] ) .'"]' );
		?>
	</div>

	<h5>Sponsored by:</h5>
	<?php wpp_print_sponsors( $latest_episode_id ); ?>
	<p><a class="show-notes" href="<?php echo get_the_permalink( $latest_episode_id ); ?>">View Show Notes</a></p>
</div>
