<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Parsec
 */
?>

<aside class="box-half">
	<div class="widget date-time">
		<h2>Date &amp; Time</h2>

		<h3 class="date"><?php echo date( 'F d<\s\u\p>S</\s\u\p> Y', strtotime( esc_attr( get_option( 'wedding_date' ) ) ) ); ?></h3>

		<h4 class="time">@ <?php echo date( 'g:ia', strtotime( esc_attr( get_option( 'wedding_time' ) ) ) ); ?></h4>

		<?php if ( get_option( 'wedding_reception_time' ) ) : ?>
			<h5>Reception: <?php echo date( 'g:ia', strtotime( esc_attr( get_option( 'wedding_reception_time' ) ) ) ); ?></h5>
		<?php endif; ?>
	</div>

	<div class="widget share">
		<h2>Share it!</h2>

		<h3>#<?php echo esc_html( get_option( 'wedding_hashtag' ) ); ?></h3>

		<p><strong>...then see it at <a href="http://erinandjoeswedding.rocks/">ErinAndJoesWedding.Rocks</a></strong></p>
	</div>
</aside>

<aside class="box-half">
	<div class="widget location">
		<h2>Location</h2>

		<?php echo parsec_google_map( get_option( 'wedding_local_address' ) ); ?>
		<p><b>The Wedding:</b> <?php echo esc_html( get_option( 'wedding_local' ) ); ?></p>

		<?php echo parsec_google_map( get_option( 'wedding_reception_local_address' ) ); ?>
		<p><b>The Reception:</b> <?php echo esc_html( get_option( 'wedding_reception_local' ) ); ?></p>
	</div>
</aside>
