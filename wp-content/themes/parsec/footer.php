<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Parsec
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">

		<div class="footer-columns contain">
			<aside class="footer-widgets site-info contain">
				<?php dynamic_sidebar( 'footer-widgets' ); ?>
			</aside>

			<aside class="footer-widgets site-info contain">
				<?php dynamic_sidebar( 'footer-widgets-2' ); ?>
			</aside>

			<aside class="footer-widgets site-info contain">
				<?php dynamic_sidebar( 'footer-widgets-3' ); ?>
			</aside>
		</div>

		<div class="site-info contain">
			<?php printf( '&copy; Joe Casabona %s, All rights reserved.', date('Y') ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
