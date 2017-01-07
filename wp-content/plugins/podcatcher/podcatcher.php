<?php
/**
 * Plugin Name: WP Podcatcher
 * Plugin URI: http://howibuilt.it/
 * Description: This plugin allows you to properly power your Podcasting site
 * Author: Joe Casabona
 * Version: 0.5
 * Author URI: http://casabona.org/

 * @package wp-podcatcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/* Tell user if PowerPress is not installed, then kill it. */
function wpp_check_for_powerpress() {
	if ( ! defined( 'POWERPRESS_VERSION') ) {
		add_action( 'admin_notices', function() {
			?>
					<div class="notice notice-warning is-dismissible">
						<p>
							<?php esc_html_e( 'It looks like PowerPress is not installed. We strongly recommend you use that with this plugin.', 'wp-podcatcher' ); ?>
						</p>
					</div>
			<?php
		} );
	}
}

add_action( 'plugins_loaded', 'wpp_check_for_powerpress' );

define( 'WPP_VERSION', '0.5' );
define( 'WPP_URL', plugin_dir_url( __FILE__ ) );
define( 'WPP_ASSETS', 'WPP_URL' . '/assets/' );

/* Include the Goods */
include_once( 'fm/fieldmanager.php' );
include_once( 'inc/functions.php' );
include_once( 'inc/output.php' );
include_once( 'inc/widgets/widgets.php' );

/* Tell user if Field Manager faild to load */
if ( ! defined( 'FM_VERSION' ) ) {
	add_action( 'admin_notices', 'wppc_no_fm' );
}

// Create ad image sizes.
add_image_size( 'wpp-full-banner', 468, 60 );
add_image_size( 'wpp-leaderboard', 728, 90 );
add_image_size( 'wpp-big-square', 336, 280 );
add_image_size( 'wpp-small-square', 300, 250 );

/**
 * Function to enqueue any scripts and styles.
 */
function wpp_enqueue_assets() {
	wp_enqueue_style( 'wpp_style', WPP_ASSETS . 'style.css' );
}
