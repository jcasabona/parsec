<?php
/**
 * Template Name: Home
 *
 * @package Parsec
 */

get_header(); ?>

	<div id="primary" class="content-area one-col home">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					$podcatcher_slug = ( has_podcatcher() ) ? '-podcasts' : '';
					get_template_part( 'content', 'home' . $podcatcher_slug );
				?>

			<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
