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

		<aside class="footer-widgets site-info contain">
			<?php dynamic_sidebar( 'footer-widgets' ); ?>
		</aside>

		<div class="site-info contain">
			<?php echo '<a href="http://casabona.org" title="by Joe Casabona" rel="designer">Scranton Made</a>'; ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
