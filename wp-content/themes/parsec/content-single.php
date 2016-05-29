<?php
/**
 * @package Parsec
 */
?>
<?php if ( has_post_thumbnail() ) : ?>
	<div class="featured-image">
		<div class="contain">
			<?php the_post_thumbnail(); ?>
		</div>
	</div>
<?php endif; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry">
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'parsec' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->
	</div>
	<footer class="entry-footer group">
			<?php parsec_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
