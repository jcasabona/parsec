<?php $latest_episode = wpp_get_latest_episode(); ?>

<div class="latest-episode">
	<?php
	if ( has_post_thumbnail( $latest_episode['ID'] ) ) {
		echo get_the_post_thumbnail( $latest_episode['ID'] );
	}
	?>
	<h2><?php esc_html( $latest_episode['title'] ); ?></h2>
	<?php printf( '[audio src="%s"]', esc_url( $latest_episode['audio_file'] ) ); ?>
	<p><a href="<?php echo esc_url( $latest_episode['permalink'] ); ?>">View Show Notes</a></p>
</div>
