<?php
/**
 * @package Parsec
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="featured-image center">
		<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'full' );
			}
		?>
	</div>
	<header class="entry-header">
		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta posted-on">
			<?php parsec_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>

		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			/* translators: %s: Name of current post */
			the_content( sprintf(
				__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'parsec' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
		?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'parsec' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php parsec_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
