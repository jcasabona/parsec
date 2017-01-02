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

		<div class="site-info contain copyright">
			<?php printf( '&copy; Joe Casabona %1$s - %2$s, All rights reserved. Powered by <a href="%3$s">Parsec</a>', '2002', date('Y'), 'http://github.com/jcasabona/parsec' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
