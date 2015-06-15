<?php
/**
 * Template Name: One Column
 *
 * @package Parsec
 */

get_header();

global $post;

?>

	<div id="primary" class="content-area one-col">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post();

				if ( class_exists( 'Wedding_Party' ) ) {
					$party_member = new Wedding_Party( $post );


				}

			?>



			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
