<?php
/**
 * @package Parsec
 */

if ( class_exists( 'Wedding_Party' ) ) {
	$party_member = new Wedding_Party( $post );

?>

<h2 class="big-header">For the <?php echo $party_member->get_party(); ?></h2>

<article id="post-<?php the_ID(); ?>" <?php post_class( $party_member->get_classes() ); ?>>
	<header class="entry-header">
		<?php if ( has_post_thumbnail() ) : ?>
				<div class="alignleft"><?php the_post_thumbnail( 'medium' ); ?></div>
		<?php endif; ?>

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<h3>Role: <?php echo $party_member->get_role(); ?></h3>
			<p><b>Relation:</b> <?php echo $party_member->get_relationship(); ?></p>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<p><a href="<?php echo get_post_type_archive_link( 'wedding-party' ); ?>">Back to the Wedding Party</a></p>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->

<?php }
