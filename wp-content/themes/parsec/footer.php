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

	<section class="panels">
			<div class="our-story">
					<a class="panel" href="<?php echo parsec_panel_link( 'wedding_story_page' ); ?>">Our Story</a>
			</div>

			<div class="rsvp">
				<a class="panel" href="<?php echo parsec_panel_link( 'wedding_rsvp_page' ); ?>">RSVP</a>
			</div>

			<div class="registry">
				<a class="panel" href="<?php echo parsec_panel_link( 'wedding_gifts_page' ); ?>">Gifts</a>
			</div>
	</section>

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
