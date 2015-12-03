<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Parsec
 */
?>

<div class="impact-area">
	<?php the_content(); ?>
</div>

<aside class="box-half">
	<?php dynamic_sidebar( 'home-left' ); ?>
</aside>

<aside class="box-half">
	<?php dynamic_sidebar( 'home-right' ); ?>
</aside>
