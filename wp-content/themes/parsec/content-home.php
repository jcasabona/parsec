<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Parsec
 */

 $has_img = '';
?>

<section class="impact-area clear">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="featured-image">
			<?php
				the_post_thumbnail( 'large' );
				$has_img = ' with-featured-img';
			?>
		</div>
	<?php endif; ?>

	<div class="featured-content<?php echo $has_img; ?>">
		<?php the_content(); ?>
	</div>
</section>


<section class="latest-post">
	<?php
		$latest_post = get_posts( array( 'numberposts' => 3, 'post_status' => 'publish' ) );
		global $post;
		foreach( $latest_post as $post ) {
			setup_postdata( $post );
			get_template_part( 'content', 'home-post' );
		}
		wp_reset_postdata();
	?>
</section>


<section class="home-widgets">
	<?php dynamic_sidebar( 'home-right' ); ?>
</section>
