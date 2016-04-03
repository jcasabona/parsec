<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: Archives
*/
?>

<?php get_header(); ?>


<h2>Archives by Year/Month</h2>
<ul class="compact-archives">
	<?php compact_archive('block'); ?>
</ul>

<h2>Tag Cloud</h2>
<p class="tags"><?php wp_tag_cloud('smallest=0.9&largest=2.2&unit=em'); ?> </p>


<?php get_footer(); ?>
