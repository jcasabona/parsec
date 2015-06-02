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
			<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'parsec' ) ); ?>"><?php printf( __( 'Proudly powered by %s', 'parsec' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'parsec' ), 'Parsec', '<a href="http://casabona.org" rel="designer">Joe Casabona</a>' ); ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
