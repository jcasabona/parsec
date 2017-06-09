<?php
/**
 * Template Name: Homepage
 *
 * @package Parsec
 */

get_header(); ?>

	<div id="primary" class="content-area one-col">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'home' ); ?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
