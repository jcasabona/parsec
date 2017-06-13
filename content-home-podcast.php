<?php
/**
 * The template used for displaying page content in page-home.php, sans podcatcher plugin.
 *
 * @package Parsec
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="<?php echo get_permalink(); ?>" alt="<?php the_title(); ?>">
		<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'full' );
			}
		?>
	</a>
</article><!-- #post-## -->
