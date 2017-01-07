<?php
	// powerpressadmin-notifications.php
	
	// Notice message manager for PowerPress
	
	// Inspired by the notifications in Yoast's SEO
	// Also inspired from https://premium.wpmudev.org/blog/adding-admin-notices/
	// For the sake of keeping things simple, we will only include this class when we need to display notifications
	
class PowerPress_Notification {
	
	private $settings = array();
	
	public function __construct($content, $settings = array() )
	{
		$defaults = array();
		$defaults['content'] = '';
		$defaults['type'] = 'updated';
		$defaults['id'] = '';
		$this->settings = wp_parse_args( $settings, $defaults );
		if( !empty($content) )
			$this->settings['content'] = $content;
		if( empty($this->settings['content']) )
			$this->settings['id'] = crc32($this->settings['content']); // Not ideal, but with no ID we need some unique value
	}
	
	public function get_notification_as_string()
	{
		$html = '<div class="powerpress-notice notice is-dismissible ' . esc_attr( $this->settings['type'] ) . '" id="powerpress-notice-'. esc_attr( $this->settings['id'] ) .'">'; 
		$html .= wpautop( $this->settings['content'] );
			
		if( version_compare($GLOBALS['wp_version'], 4.2, '<' ) ) {
			$html .= '<p>&nbsp; <a style="float:right;" href="#" class="notice-dismiss-link">'. __('Dismiss', 'powerpress') .'</a></p>';
		}
		$html .= '</div>' . PHP_EOL;
		return $html;
	}
	
};

class PowerPress_Notification_Manager {

	private $notifications = array();
	private $dismissedNotifications = array();
	
	public function __construct()
	{
		$this->dismissedNotifications = get_option('powerpress_dismissed_notices');
		add_action( 'all_admin_notices', array( $this, 'all_admin_notices' ) );
		add_action('wp_ajax_powerpress_notice_dismiss', array($this, 'wp_ajax_powerpress_notice_dismiss') );
		add_action('admin_head', array($this, 'admin_head') );
	}
	
	public function all_admin_notices()
	{
		while( list($key, $notification) = each($this->notifications) )
		{
			echo $notification->get_notification_as_string();
		}
	}
	
	public function wp_ajax_powerpress_notice_dismiss()
	{
		$dismiss_notice_id = $_POST['dismiss_notice_id'];
		preg_match('/^powerpress-notice-(.*)$/i', $dismiss_notice_id, $match );
		if( empty($match[1]) )
			die('-1');
			
		$DismissedNotifications = get_option('powerpress_dismissed_notices');
		if( !is_array($DismissedNotifications) )
			$DismissedNotifications = array();
		$DismissedNotifications[ $match[1] ] = 1;
		update_option('powerpress_dismissed_notices',  $DismissedNotifications);
		die('1');
	}
	
	function admin_head()
	{
		if( count($this->notifications) > 0 ) // If there are notices to print, then lets also put in the ajax to clear them
		{
			if( version_compare($GLOBALS['wp_version'], 4.2, '>=' ) ) {
?>
<script type="text/javascript"><!--

jQuery(document).ready( function() {
	
	jQuery(document).on( 'click', '.powerpress-notice .notice-dismiss', function() {
	
		var dismissId = jQuery(this).closest('.powerpress-notice').attr('id');
		jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action:"powerpress_notice_dismiss", dismiss_notice_id: dismissId });
			});
});

--></script>
<?php
			}
			else
			{
?>
<script type="text/javascript"><!--

jQuery(document).ready( function() {
	
	jQuery(document).on( 'click', '.powerpress-notice .notice-dismiss-link', function(e) {
		e.preventDefault();
		var dismissId = jQuery(this).closest('.powerpress-notice').attr('id');
		jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action:"powerpress_notice_dismiss", dismiss_notice_id: dismissId });
		jQuery(this).closest('.powerpress-notice').hide(); // Hide the div now we dismissed it
	});
});

--></script>
<?php
			}
?>
<style>
.powerpress-notice a {
	text-decoration: underline;
}
</style>	
<?php
		}
	}
	
	public function dismissed_status($notification_id)
	{
		if( !empty($this->dismissedNotifications[ $notification_id ]) )
			return true;
		return false;
	}
	
	public function add($notification_id, $notification_content)
	{
		if( !$this->dismissed_status($notification_id) ) {
			$this->notifications[$notification_id] = new PowerPress_Notification($notification_content, array('id'=>$notification_id)  );
		}
	}

};

function powerpressadmin_notifications_appropriate()
{
	// Any powerpress page
	if( preg_match('/wp-admin\/.*powerpress/', $_SERVER['REQUEST_URI']) )
		return true;
		
	// Dashboard is acceptable:
	if( preg_match('/wp-admin\/(index\.php)?$/', $_SERVER['REQUEST_URI']) )
		return true;
	
	// edit posts (pages, post types, etc...)
	if( preg_match('/wp-admin\/edit\.php/', $_SERVER['REQUEST_URI']) )
		return true;
		
	// managing plugins
	if( preg_match('/wp-admin\/plugins\.php/', $_SERVER['REQUEST_URI']) )
		return true;
	
	// Required so we can dismiss notices
	if( preg_match('/wp-admin\/admin-ajax\.php/', $_SERVER['REQUEST_URI']) )
		return true;
		
	return false;
}

if( powerpressadmin_notifications_appropriate() ) {
	//$PowerPressNotificationManager = new PowerPress_Notification_Manager(); // Uncomment when we have notices to display

	// e.g. usage
	/*
	if( !$PowerPressNotificationManager->dismissed_status('apple-201602') ) {

		$message = '';
		$message .= '<h3 style="margin: 5px 0;">';
		$message .= __('iTunes Update, February, 2016 - Blubrry PowerPress', 'powerpress');
		$message .= '</h3>';
		$message .= __('Apple now recommends using a 3000 x 3000 pixel JPG or PNG in the RGB color space in a compressed format. Saving your image as a JPG with your favorite image editing software should automatically compress your image to optimize for mobile devices.', 'powerpress');
		$message .= ' &mdash; <a href="'.  admin_url("admin.php?page=powerpress/powerpressadmin_basic.php#tab7") .'">'. __('Update Now', 'powerpress') .'</a>';
		$message .= ' | <a href="http://www.powerpresspodcast.com/2016/02/19/new-itunes-podcast-directory-recommendations-february-2016/" target="_blank">'. __('Learn More', 'powerpress') .'</a>';
		$message .= "\n\n";
		$message .= __('Apple has changed the iTunes explicit setting. <strong>You <i>must</i> now select "clean" or "explicit"</strong> - the \'no\' option is no longer available', 'powerpress');
		$message .= ' &mdash; <a href="'.  admin_url("admin.php?page=powerpress/powerpressadmin_basic.php#tab5") .'">'. __('Update Now', 'powerpress') .'</a>';
		$message .= ' | <a href="http://www.powerpresspodcast.com/2016/02/19/new-itunes-podcast-directory-recommendations-february-2016/" target="_blank">'. __('Learn More', 'powerpress') .'</a>';
		$message .= "\n\n";
		$message .= __('Podcast submissions to the iTunes podcast directory are now managed by the new <a href="https://podcastsconnect.apple.com/" target="_blank">Podcast Connect</a> website. The new website allows you to submit new podcasts, as well as refresh, hide and delete your current podcast listings.', 'powerpress');
		$message .= ' &mdash; <a href="http://www.powerpresspodcast.com/2016/02/19/new-itunes-podcast-directory-recommendations-february-2016/" target="_blank">'. __('Learn More', 'powerpress') .'</a>';
		$message .= "\n\n";
		$message .= '<div style="text-align: center; margin: 5px 0; font-weight: bold;"><i>'.  powerpress_review_message() .'</i></div>';
		
		// http://www.apple.com/customer-letter/
		// if you love PowerPress, Please rate and review PowerPress here:
		
		$PowerPressNotificationManager->add('apple-201602', $message);
	}
	*/
}

// eof