<?php
/**
 * The template for displaying all single posts.
 *
 * @package Parsec
 */

get_header(); ?>

	<div id="primary" class="content-area one-col">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php parsec_post_nav(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( ( comments_open() || get_comments_number() ) && is_singular( 'post' ) ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
