<?php $latest_episode_id = wpp_get_latest_episode(); ?>

<div class="latest-episode clear">
	<h5>Latest Episode</h5>
	<h3><?php echo esc_html( get_the_title( $latest_episode_id ) ); ?></h3>
	<div class="episode">
		<?php
			$episode_data = powerpress_get_enclosure_data( $latest_episode_id );
			echo do_shortcode( '[audio src="'. esc_url( $episode_data['url'] ) .'"]' );
		?>
	</div>
	<p class="alignleft"><a class="show-notes" href="<?php echo get_the_permalink( $latest_episode_id ); ?>">View Show Notes</a></p>
	<p class="alignright">Sponsored by: <?php echo parsec_sponsor_text_links( $latest_episode_id ); ?></p>
</div>
