<?php
/**
 * Template Name: One Column
 *
 * @package Parsec
 */

get_header();

global $post;

?>

	<div id="primary" class="content-area one-col wedding-party">
		<main id="main" class="site-main clear" role="main">

			<?php while ( have_posts() ) : the_post();

			get_template_part( 'content', 'single-wedding-party' );

			endwhile; // end of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
