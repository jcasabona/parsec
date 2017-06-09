<?php
/**
 * @package Parsec
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clear episode-list' ); ?>>
	<header class="entry-header">
		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta posted-on">
			<?php parsec_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
		<div class="alignleft featured-image">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'thumbnail' );
			}
			?>
		</div>

		<?php the_title( sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
		<div class="episode-player">
			<?php
				$episode_data = powerpress_get_enclosure_data( get_the_id() );
				if ( ! empty( $episode_data['url'] ) ) {
					echo do_shortcode( '[audio src="'. esc_url( $episode_data['url'] ) .'"]' );
				}
			?>
		</div>
		<p class="alignright"><a href="<?php the_permalink(); ?>">View Show Notes</a></p>
	</header><!-- .entry-header -->
</article><!-- #post-## -->
