<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Parsec
 */
?>

<section class="impact-area clear">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="featured-image">
			<?php the_post_thumbnail( 'large' ); ?>
		</div>
	<?php endif; ?>

	<div class="featured-content">
		<?php the_content(); ?>
	</div>
</section>


<section class="home-columns">
	<aside class="box-half">
		<?php dynamic_sidebar( 'home-left' ); ?>
	</aside>

	<aside class="box-half">
			<?php dynamic_sidebar( 'home-right' ); ?>
	</aside>
</section>
