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
				$classes = ( $party_member->is_important_role() ) ? $party_member->get_classes( 'bio group elevate' ) : $party_member->get_classes( 'bio group' );
			?>

				<div class="<?php echo $classes; ?>">
					<div class="bio-photo">
							<?php echo $party_member->get_head_shot( 'wedding-party-photo' ); ?>
				</div>
				<div class="bio-info">
						<h3><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
						<h4><?php echo $party_member->get_role(); ?></h4>
						<h5><?php echo $party_member->get_relationship(); ?></h5>
				</div>
			</div>
			<?php
				endwhile;
			endif;
			?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
