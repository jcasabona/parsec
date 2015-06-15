<?php
/**
 * Template Name: Wedding Party Archive
 *
 * @package Parsec
 */

get_header();

?>

	<div id="primary" class="content-area one-col wedding-party">
		<main id="main" class="site-main" role="main">
			<?php
			if ( class_exists( 'Wedding_Party' ) ) :
			while ( have_posts() ) : the_post();
				$party_member = new Wedding_Party( $post );
				$classes = 'bio wedding-party clear';
				$classes .= ( $party_member->is_important_role() ) ? ' elevate' : '';
			?>

				<div class="<?php echo $classes; ?>">
					<div class="bio-photo alignleft">
							<?php echo $party_member->get_head_shot(); ?>
				</div>
				<div class="bio-info">
						<h3><?php the_title(); ?></h3>
						<h4><?php echo $party_member->get_role(); ?></h4>
						<h5><?php echo $party_member->get_relationship(); ?></h5>
				</div>
			<?php
				endwhile;
			endif;
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
