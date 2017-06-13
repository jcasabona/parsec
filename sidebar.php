<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Parsec
 */

$sidebar_slug = ( is_front_page() ) ? 'sidebar-home' : 'sidebar-1';

if ( ! is_active_sidebar( $sidebar_slug ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( $sidebar_slug ); ?>
</aside><!-- #secondary -->
