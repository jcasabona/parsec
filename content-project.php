<?php
/**
 * @package Parsec
 */

	$banner = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium', true );
?>


<article id="post-<?php the_ID(); ?>" <?php post_class( 'project' ); ?> style="background-image: url('<?php echo $banner[0]; ?>');">
	<h3><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
</article>
