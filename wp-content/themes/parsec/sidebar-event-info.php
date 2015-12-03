<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Parsec
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<section class="event-info" role="complementary">
	<div class="contain">
		<?php dynamic_sidebar( 'event-info' ); ?>
	</div>
</section>
