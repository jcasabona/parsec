<?php
	// powerpressadmin-ping-sites.php
	
	function powerpressadmin_diagnostics_process()
	{
		global $powerpress_diags;
		$powerpress_diags = array();
		
		// First, see if the user has cURL and/or allow_url_fopen enabled...
		$powerpress_diags['detecting_media'] = array();
		$powerpress_diags['detecting_media']['success'] = true;
		$powerpress_diags['detecting_media']['warning'] = false;
		$powerpress_diags['detecting_media']['allow_url_fopen'] = (ini_get( 'allow_url_fopen' ) != false); // fopen
		$powerpress_diags['detecting_media']['curl'] = function_exists( 'curl_init' ); // cURL
		$powerpress_diags['detecting_media']['message2'] = ''; // if ( !ini_get('safe_mode') && !ini_get('open_basedir') )
		$powerpress_diags['detecting_media']['message3'] = ''; // ssl checks
		
		// Testing:
		//$powerpress_diags['detecting_media']['allow_url_fopen'] = false;
		//$powerpress_diags['detecting_media']['curl'] = false;
		
		if( $powerpress_diags['detecting_media']['curl'] )
		{
			$powerpress_diags['detecting_media']['message'] = __('Your web server supports the PHP cURL library.', 'powerpress');
			if( $powerpress_diags['detecting_media']['allow_url_fopen'] )
				$powerpress_diags['detecting_media']['message'] .= ' '. __('Your web server is also configured with the php.ini setting \'allow_url_fopen\' enabled, but the cURL library takes precedence.', 'powerpress');
			
			if( ini_get('safe_mode') && ini_get('open_basedir') )
			{
				$powerpress_diags['detecting_media']['warning'] = true;
				$powerpress_diags['detecting_media']['message2'] = __('Warning: Both php.ini settings \'safe_mode\' and \'open_basedir\' will prevent the cURL library from following redirects in URLs.', 'powerpress');
			}
			else if( ini_get('safe_mode') )
			{
				$powerpress_diags['detecting_media']['warning'] = true;
				$powerpress_diags['detecting_media']['message2'] = __('Warning: The php.ini setting \'safe_mode\' will prevent the cURL library from following redirects in URLs.', 'powerpress');
			}
			else if( ini_get('open_basedir') )
			{
				$powerpress_diags['detecting_media']['warning'] = true;
				$powerpress_diags['detecting_media']['message2'] = __('Warning: The php.ini setting \'open_basedir\' will prevent the cURL library from following redirects in URLs.', 'powerpress');
			}
		}
		else if( $powerpress_diags['detecting_media']['allow_url_fopen'] )
		{
			$powerpress_diags['detecting_media']['message'] = __('Your web server is configured with the php.ini setting \'allow_url_fopen\' enabled.', 'powerpress');
		}
		else
		{
			$powerpress_diags['detecting_media']['success'] = false;
			$powerpress_diags['detecting_media']['message'] = __('Your server must either have the php.ini setting \'allow_url_fopen\' enabled or have the PHP cURL library installed in order to detect media information.', 'powerpress');
		}
		
		// OpenSSL or curl SSL is required
		$powerpress_diags['detecting_media']['openssl'] = extension_loaded('openssl');
		$powerpress_diags['detecting_media']['curl_ssl'] = false;
		if( function_exists('curl_version') )
		{
			$curl_info = curl_version();
			$powerpress_diags['detecting_media']['curl_ssl'] = ($curl_info['features'] & CURL_VERSION_SSL );
		}
		
		if( $powerpress_diags['detecting_media']['openssl'] == false && $powerpress_diags['detecting_media']['curl_ssl'] == false ) {
			$powerpress_diags['detecting_media']['warning'] = true;
			$powerpress_diags['detecting_media']['message3'] = __('WARNING: Your server should support SSL either openssl or curl_ssl.', 'powerpress');
		}
		
		// testing:
		//$powerpress_diags['pinging_itunes']['openssl'] = false;
		//$powerpress_diags['pinging_itunes']['curl_ssl'] = false;
		
		
		// Third, see if the uploads/powerpress folder is writable
		$UploadArray = wp_upload_dir();
		$powerpress_diags['uploading_artwork'] = array();
		$powerpress_diags['uploading_artwork']['success'] = false;
		$powerpress_diags['uploading_artwork']['file_uploads'] = ini_get( 'file_uploads' );
		$powerpress_diags['uploading_artwork']['writable'] = false;
		$powerpress_diags['uploading_artwork']['upload_path'] = '';
		$powerpress_diags['uploading_artwork']['message'] = '';
		
		// Testing:
		//$UploadArray['error'] = 'WordPres broke';
		//$powerpress_diags['uploading_artwork']['file_uploads'] = false;
		//$UploadArray['error'] = true;
		
		if( $powerpress_diags['uploading_artwork']['file_uploads'] == false )
		{
			$powerpress_diags['uploading_artwork']['message'] = __('Your server requires the php.ini setting \'file_uploads\' enabled in order to upload podcast artwork.', 'powerpress');
		}
		else if( $UploadArray['error'] === false )
		{
			$powerpress_diags['uploading_artwork']['upload_path'] = $UploadArray['basedir'] . '/powerpress/';
			
			if ( !is_dir($powerpress_diags['uploading_artwork']['upload_path']) && ! wp_mkdir_p( rtrim($powerpress_diags['uploading_artwork']['upload_path'], '/') ) )
			{
				$powerpress_diags['uploading_artwork']['message'] = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?', 'powerpress'), rtrim($powerpress_diags['uploading_artwork']['upload_path'], '/') );
			}
			else
			{
				$powerpress_diags['uploading_artwork']['writable'] = powerpressadmin_diagnostics_is_writable($powerpress_diags['uploading_artwork']['upload_path']);
				if( $powerpress_diags['uploading_artwork']['writable'] == false )
				{
					$powerpress_diags['uploading_artwork']['message'] = sprintf(__('PowerPress is unable to write to the %s directory.', 'powerpress'), $powerpress_diags['uploading_artwork']['upload_path']);
				}
				else
				{
					$powerpress_diags['uploading_artwork']['success'] = true;
					$powerpress_diags['uploading_artwork']['message'] = __('You are able to upload and save artwork images for your podcasts.', 'powerpress');
				}
			}
		}
		else
		{
			if( strlen($UploadArray['error']) > 2 )
				$powerpress_diags['uploading_artwork']['message'] = $UploadArray['error'];
			else
				$powerpress_diags['uploading_artwork']['message'] = __('An error occurred obtaining the uploads directory from WordPress.', 'powerpress');
		}
		
		// Fourth, see if we have enough memory and we're running an appropriate version of PHP
		$powerpress_diags['system_info'] = array();
		$powerpress_diags['system_info']['warning'] = false;
		$powerpress_diags['system_info']['success'] = true;
		$powerpress_diags['system_info']['php_version'] = phpversion();
		$powerpress_diags['system_info']['php_cgi'] = (function_exists('php_sapi_name') && preg_match('/cgi/i', php_sapi_name())? true : false );
		$powerpress_diags['system_info']['memory_limit'] = (int) ini_get('memory_limit');
		$powerpress_diags['system_info']['temp_directory'] = get_temp_dir(); // Function available since WP2.5+
		
		// testing:
		//$powerpress_diags['system_info']['memory_limit'] = -1;
		//$powerpress_diags['system_info']['memory_limit'] = 0;
		//$powerpress_diags['system_info']['memory_limit'] = 16;
		
		if( $powerpress_diags['system_info']['memory_limit'] == 0 )
		{
			if( version_compare($powerpress_diags['system_info']['php_version'], '5.2') > 0 )
				$powerpress_diags['system_info']['memory_limit'] = 128;
			else if( version_compare($powerpress_diags['system_info']['php_version'], '5.2') == 0 )
				$powerpress_diags['system_info']['memory_limit'] = 16;
			else
				$powerpress_diags['system_info']['memory_limit'] = 8;
		}
		$powerpress_diags['system_info']['memory_used'] = 0;
		
		if( version_compare($powerpress_diags['system_info']['php_version'], '5.4') > -1 )
		{
			$powerpress_diags['system_info']['message'] = sprintf( __('Your version of PHP (%s) is OK!', 'powerpress'), $powerpress_diags['system_info']['php_version'] );
		}
		else if( version_compare($powerpress_diags['system_info']['php_version'], '5.3') > -1 )
		{
			$powerpress_diags['system_info']['message'] = sprintf( __('Your version of PHP (%s) is OK, though PHP 5.4 or newer is recommended.', 'powerpress'), $powerpress_diags['system_info']['php_version'] );
		}
		else
		{
			$powerpress_diags['system_info']['message'] = sprintf( __('Your version of PHP (%s) will work, but PHP 5.4 or newer is recommended.', 'powerpress'), $powerpress_diags['system_info']['php_version'] );
		}
		
		$used = 0;
		$total = $powerpress_diags['system_info']['memory_limit'];
		
		if( $total == -1 )
		{
			$powerpress_diags['system_info']['message2'] = __('Your scripts have no limit to the amount of memory they can use.', 'powerpress');
			$used = (function_exists('memory_get_peak_usage')? memory_get_peak_usage() : ( function_exists('memory_get_usage') ? memory_get_usage() : 0 ) );
			if( $used )
				$powerpress_diags['system_info']['memory_used'] = round($used / 1024 / 1024, 2);
		}
		else if( function_exists('memory_get_peak_usage') )
		{
			$used = round(memory_get_peak_usage() / 1024 / 1024, 2);
			$powerpress_diags['system_info']['memory_used'] = $used;
			$percent = ($used/$total)*100;
			$powerpress_diags['system_info']['message2'] = sprintf(__('You are using %d%% (%.01fM of %.01dM) of available memory.', 'powerpress'), $percent, $used, $total);
		}
		else if( function_exists('memory_get_usage') )
		{
			$used = round(memory_get_usage() / 1024 / 1024, 2);
			$powerpress_diags['system_info']['memory_used'] = $used;
			$percent = ($used/$total)*100;
			$powerpress_diags['system_info']['message2'] = sprintf(__('You are using %d%% (%.01fM of %dM) of available memory. Versions of PHP 5.2 or newer will give you a more accurate total of memory usage.', 'powerpress'), $percent, $used, $total);
		}
		else
		{
			$powerpress_diags['system_info']['message2'] = sprintf(__('Your scripts have a total of %dM.', 'powerpress'), $total );
		}
		
		if( $total > 0 && ($used + 4) > $total )
		{
			$powerpress_diags['system_info']['warning'] = true;
			$powerpress_diags['system_info']['message2'] = __('Warning:', 'powerpress') .' '. $powerpress_diags['system_info']['message2'];
			$powerpress_diags['system_info']['message2'] .= ' ';
			$powerpress_diags['system_info']['message2'] .= sprintf(__('We recommend that you have at least %dM (4M more that what is currently used) or more memory to accomodate all of your installed plugins.', 'powerpress'), ceil($used)+4 );
		}
		
		if( empty($powerpress_diags['system_info']['temp_directory']) )
		{
			$powerpress_diags['system_info']['success'] = false;
			$powerpress_diags['system_info']['message3'] =  __('Error:', 'powerpress') .' '. __('No temporary directory available.', 'powerpress');
		}
		else if( is_dir($powerpress_diags['system_info']['temp_directory']) && is_writable($powerpress_diags['system_info']['temp_directory']) )
		{
			$powerpress_diags['system_info']['message3'] = sprintf(__('Temporary directory %s is writable.', 'powerpress'), $powerpress_diags['system_info']['temp_directory']);
		}
		else
		{
			$powerpress_diags['system_info']['success'] = false;
			$powerpress_diags['system_info']['message3'] = __('Error:', 'powerpress') .' '. sprintf(__('Temporary directory %s is not writable.', 'powerpress'), $powerpress_diags['system_info']['temp_directory']);
		}
		
		if( empty($powerpress_diags['system_info']['php_cgi']) )
		{
			$powerpress_diags['system_info']['message4'] = '';
		}
		else
		{
			$powerpress_diags['system_info']['message4'] = __('Warning:', 'powerpress') .' '. __('PHP running in CGI mode.', 'powerpress');
		}
		
		if( isset($_GET['Email']) && strlen($_GET['Email']) > 4 )
		{
			check_admin_referer('powerpress-diagnostics');
			$email = $_GET['Email'];
			powerpressadmin_diagnostics_email($email);
			powerpress_page_message_add_notice(  sprintf(__('Diagnostic results sent to %s.', 'powerpress'), $email) );
		}
	}
	
	function powerpressadmin_diagnostics_email($email)
	{
		global $powerpress_diags, $wpmu_version, $wp_version, $powerpress_diag_message;
		$SettingsGeneral = get_option('powerpress_general');
		
		// First we need some basic information about the blog...
		$message = __('Blog Title:', 'powerpress') .' '. get_bloginfo('name') . "<br />\n";
		$message .= __('Blog URL:', 'powerpress') .' '. get_bloginfo('url') . "<br />\n";
		$message .= __('WordPress Version:', 'powerpress') .' '. $wp_version . "<br />\n";
		if( !empty($wpmu_version) )
				$message .= __('WordPress MU Version:', 'powerpress') .' '. $wpmu_version . "<br />\n";
		$message .= __('System:', 'powerpress') .' '. $_SERVER['SERVER_SOFTWARE'] . "<br />\n";
		$message .= __('Safe node:', 'powerpress') .' '. ( ini_get('safe_mode')?'true':'false') ."<br />\n";
		$message .= __('Open basedir:', 'powerpress') .' '. ini_get('open_basedir') ."<br />\n";
		
		// Crutial PowerPress Settings
		$message .= "<br />\n";
		$message .= '<strong>'. __('Important PowerPress Settings', 'powerpress') ."</strong><br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('PowerPress version:', 'powerpress') .' '. POWERPRESS_VERSION ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('episode box file size/duration fields:', 'powerpress') .' '. ( empty($SettingsGeneral['episode_box_mode']) ?__('yes', 'powerpress'): ($SettingsGeneral['episode_box_mode']==1?__('no', 'powerpress'):__('yes', 'powerpress')) ) ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('Podcasting capability:', 'powerpress') .' '. ( empty($SettingsGeneral['use_caps'])?__('Disabled (default)', 'powerpress'): __('Enabled', 'powerpress')) ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('Feed capability:', 'powerpress') .' '. ( empty($SettingsGeneral['feed_caps'])?__('Disabled (default)', 'powerpress'): __('Enabled', 'powerpress')) ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('Category Podcasting:', 'powerpress') .' '. ( empty($SettingsGeneral['cat_casting']) ?__('Disabled (default)', 'powerpress'): __('Enabled', 'powerpress')) ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('Podcast Channels:', 'powerpress') .' '. ( empty($SettingsGeneral['channels']) ?__('Disabled (default)', 'powerpress'): __('Enabled', 'powerpress')) ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('Additional Player Options:', 'powerpress') .' '. ( empty($SettingsGeneral['player_options'])?__('Disabled (default)', 'powerpress'): __('Enabled', 'powerpress')) ."<br />\n";
		
		// Detecting Media Information
		$message .= "<br />\n";
		$message .= '<strong>'.__('Detecting Media Information', 'powerpress') ."</strong><br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('success:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['success']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('warning:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['warning']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('allow_url_fopen:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['allow_url_fopen']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('curl:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['curl']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('curl_ssl:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['curl_ssl']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('openssl:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['openssl']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('message:', 'powerpress') .' '. $powerpress_diags['detecting_media']['message'] ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('message 2:', 'powerpress') .' '. $powerpress_diags['detecting_media']['message2'] ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('message 3:', 'powerpress') .' '. $powerpress_diags['detecting_media']['message3'] ."<br />\n";
		
		// Pinging iTunes
		//$message .= "<br />\n";
		//$message .= '<strong>'.__('Pinging iTunes', 'powerpress') ."</strong><br />\n";
		//$message .= " &nbsp; \t &nbsp; ". __('success:', 'powerpress') .' '. ($powerpress_diags['pinging_itunes']['success']?'true':'false') ."<br />\n";
		//$message .= " &nbsp; \t &nbsp; ". __('curl_ssl:', 'powerpress') .' '. ($powerpress_diags['pinging_itunes']['curl_ssl']?'true':'false') ."<br />\n";
		//$message .= " &nbsp; \t &nbsp; ". __('openssl:', 'powerpress') .' '. ($powerpress_diags['pinging_itunes']['openssl']?'true':'false') ."<br />\n";
		//$message .= " &nbsp; \t &nbsp; ". __('message:', 'powerpress') .' '. $powerpress_diags['pinging_itunes']['message'] ."<br />\n";
		
		// Uploading Artwork
		$message .= "<br />\n";
		$message .= '<strong>'.__('Uploading Artwork', 'powerpress') ."</strong><br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('success:', 'powerpress') .' '. ($powerpress_diags['uploading_artwork']['success']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('file_uploads:', 'powerpress') .' '. ($powerpress_diags['uploading_artwork']['file_uploads']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('writable:', 'powerpress') .' '. ($powerpress_diags['uploading_artwork']['writable']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('upload_path:', 'powerpress') .' '. $powerpress_diags['uploading_artwork']['upload_path'] ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('message:', 'powerpress') .' '. $powerpress_diags['uploading_artwork']['message'] ."<br />\n";
		
		// System Information
		$message .= "<br />\n";
		$message .= '<strong>'.__('System Information', 'powerpress') ."</strong><br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('success:', 'powerpress') .' '. ($powerpress_diags['system_info']['success']?'true':'false') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('warning:', 'powerpress') .' '. ($powerpress_diags['system_info']['warning']?'yes':'no') ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('php_version:', 'powerpress') .' '. $powerpress_diags['system_info']['php_version'] ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('memory_limit:', 'powerpress') .' '. $powerpress_diags['system_info']['memory_limit'] ."M\n";
		$message .= " &nbsp; \t &nbsp; ". __('memory_used:', 'powerpress') .' '. sprintf('%.01fM',$powerpress_diags['system_info']['memory_used']) ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('temp directory:', 'powerpress') .' '. $powerpress_diags['system_info']['temp_directory'] ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('message:', 'powerpress') .' '. $powerpress_diags['system_info']['message'] ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('message 2:', 'powerpress') .' '. $powerpress_diags['system_info']['message2'] ."<br />\n";
		$message .= " &nbsp; \t &nbsp; ". __('message 3:', 'powerpress') .' '. $powerpress_diags['system_info']['message3'] ."<br />\n";
		if( !empty($powerpress_diags['system_info']['message4']) )
			$message .= " &nbsp; \t &nbsp; ". __('message 4:', 'powerpress') .' '. $powerpress_diags['system_info']['message4'] ."<br />\n";

		if( !empty($_GET['ap']) )
		{
			$current_plugins = get_option('active_plugins');
			$message .= "<br />\n";
			$message .= '<strong>'.__('Active Plugins', 'powerpress') ."</strong><br />\n";
			while( list($null,$plugin_path) = each($current_plugins) )
			{
				$plugin_data = get_plugin_data( rtrim(WP_PLUGIN_DIR, '/\\'). '/'. rtrim($plugin_path, '\\/'), false, false ); //Do not apply markup/translate as it'll be cached.
				
				$message .= " &nbsp; \t &nbsp; " . __('Title:', 'powerpress') .' '. $plugin_data['Title']. "<br />\n";
				$message .= " &nbsp; \t &nbsp; " . __('Relative Path:', 'powerpress') .' '. $plugin_path. "<br />\n";
				$message .= " &nbsp; \t &nbsp; " . __('Version:', 'powerpress') .' '. $plugin_data['Version']. "<br />\n";
				$message .= " &nbsp; \t &nbsp; " . __('Web Site:', 'powerpress') .' '. $plugin_data['PluginURI']. "<br />\n";
				//$message .= " &nbsp; \t &nbsp; " . __('Description:', 'powerpress') .' '. $plugin_data['Description']. "<br />\n";
				//$message .= " &nbsp; \t &nbsp; " . __('Author Name:', 'powerpress') .' '. $plugin_data['Author']. "<br />\n";
				//$message .= " &nbsp; \t &nbsp; " . __('Author Web Site:', 'powerpress') .' '. $plugin_data['AuthorURI']. "<br />\n";
				//print_r($plugin_data);
				$message .= "<br />\n";
			}
		}
		//$message .= " &nbsp; \t &nbsp; ". __('success:', 'powerpress') .' '. ($powerpress_diags['system_info']['success']?'true':'false') ."<br />\n";
		
		// Now lets loop through each section of diagnostics
		$user_info = wp_get_current_user();
		$from_email = $user_info->user_email;
		$from_name = $user_info->user_nicename;
		$headers = 'From: "'.$from_name.'" <'.$from_email.'>'."\n"
			.'Reply-To: "'.$from_name.'" <'.$from_email.'>'."\n"
			.'Return-Path: "'.$from_name.'" <'.$from_email.'>'."\n";
		if( !empty($_GET['CC']) )
			$headers .= 'CC: "'.$from_name.'" <'.$from_email.'>'."\n";
		$headers .= "Content-Type: text/html\n";
		
		@wp_mail($email, sprintf(__('Blubrry PowerPress diagnostic results for %s', 'powerpress'), get_bloginfo('name')), $message, $headers);
		$powerpress_diag_message = $message;
	}
	
	function powerpressadmin_diagnostics_is_writable($dir)
	{
		// Make sure we can create a file in the specified directory...
		if( is_dir($dir) )
		{
			return is_writable($dir);
		}
		return false;
	}
	
	function powerpressadmin_diagnostics_status($success=true, $warning=false)
	{
		$img = 'yes.png';
		$color = '#458045';
		$text = __('Success', 'powerpress');
		if( $success == false ) // Failed takes precedence over warning
		{
			$img = 'no.png';
			$color = '#CC0000';
			$text = __('Failed', 'powerpress');
		}
		else if( $warning )
		{
			$img = '../../../wp-includes/images/smilies/icon_exclaim.gif';
			$color = '#D98500';
			$text = __('Warning', 'powerpress');
		}
?>
	<img src="<?php echo admin_url(); ?>/images/<?php echo $img; ?>" style="vertical-align:text-top;" />
	<strong style="color:<?php echo $color; ?>;"><?php echo $text; ?></strong>
<?php
	}
	
	function powerpressadmin_diagnostics()
	{
		global $powerpress_diags, $powerpress_diag_message;
		$GeneralSettings = get_option('powerpress_general');
		
		if( empty($powerpress_diags) )
		{
			powerpressadmin_diagnostics_process();
			powerpress_page_message_print();
		}
?>

<h2><?php echo __('Blubrry PowerPress Diagnostics', 'powerpress'); ?></h2>
<p>
	<?php echo __('The Diagnostics page checks to see if your server is configured to support all of the available features in Blubrry PowerPress.', 'powerpress'); ?>
</p>

<?php
	if( !empty($powerpress_diag_message) )
	{
?>
<h3 style="margin-bottom: 2px;"><?php echo __('Diagnostics Email Message', 'powerpress'); ?></h3>
<div style="border: 2px inset #000000; padding: 10px; margin-right: 20px; font-size: 85%;">
<?php echo $powerpress_diag_message; ?>
</div>
<?php } ?>

<h3 style="margin-bottom: 0;"><?php echo __('Detecting Media Information', 'powerpress'); ?></h3>
<p style="margin: 0;">
	<?php echo __('The following test checks to see if your web server can make connections with other web servers to obtain file size and media duration information. The test checks to see if either the PHP cURL library is installed or the php.ini setting \'allow_url_fopen\' enabled.', 'powerpress'); ?>
</p>
<table class="form-table">
<tr valign="top">
<th scope="row">
	<?php powerpressadmin_diagnostics_status($powerpress_diags['detecting_media']['success'],$powerpress_diags['detecting_media']['warning']); ?>
</th> 
<td>
	<p><?php echo htmlspecialchars($powerpress_diags['detecting_media']['message']); ?></p>
<?php if( $powerpress_diags['detecting_media']['message2'] ) { ?>
	<p><?php echo htmlspecialchars($powerpress_diags['detecting_media']['message2']); ?></p><?php } ?>
<?php if( $powerpress_diags['detecting_media']['message3'] ) { ?>
	<p><?php echo htmlspecialchars($powerpress_diags['detecting_media']['message3']); ?></p><?php } ?>
<?php if( $powerpress_diags['detecting_media']['success'] ) { ?>
	<p><?php echo __('If you are still having problems detecting media information, check with your web hosting provider if there is a firewall blocking your server.', 'powerpress'); ?></p>
<?php } else { ?>
	<p><?php echo __('Contact your web hosting provider with the information above.', 'powerpress'); ?></p>
<?php } ?>
	<ul><li><ul>
		<li><?php echo __('allow_url_fopen:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['allow_url_fopen']?'true':'false'); ?></li>
		<li><?php echo __('curl:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['curl']?'true':'false'); ?></li>
		<li><?php echo __('curl_ssl:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['curl_ssl']?'true':'false'); ?></li>
		<li><?php echo __('openssl:', 'powerpress') .' '. ($powerpress_diags['detecting_media']['openssl']?'true':'false'); ?></li>
	</ul></li></ul>
</td>
</tr>
</table>

<h3 style="margin-bottom: 0;"><?php echo __('Uploading Artwork', 'powerpress'); ?></h3>
<p style="margin: 0;"><?php echo __('The following test checks to see that you can upload and store files on your web server.', 'powerpress'); ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row">
	<?php powerpressadmin_diagnostics_status($powerpress_diags['uploading_artwork']['success']); ?>
</th> 
<td>
	<p><?php echo htmlspecialchars($powerpress_diags['uploading_artwork']['message']); ?></p>
</td>
</tr>
</table>

<h3 style="margin-bottom: 0;"><?php echo __('System Information', 'powerpress'); ?></h3>
<p style="margin: 0;"><?php echo __('The following test checks your version of PHP, memory usage and temporary directory access.', 'powerpress'); ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row">
	<?php powerpressadmin_diagnostics_status($powerpress_diags['system_info']['success'], ($powerpress_diags['system_info']['warning'] || $powerpress_diags['system_info']['php_cgi']) ); ?>
</th> 
<td>
	<p><?php echo htmlspecialchars( sprintf(__('WordPress Version %s'), $GLOBALS['wp_version']) ); ?></p>
	<p><?php echo htmlspecialchars($powerpress_diags['system_info']['message']); ?></p>
	<p><?php echo htmlspecialchars($powerpress_diags['system_info']['message2']); ?></p>
	<p><?php echo htmlspecialchars($powerpress_diags['system_info']['message3']); ?></p>
<?php if( !empty($powerpress_diags['system_info']['php_cgi']) ) { ?>
	<p><?php echo __('Warning:', 'powerpress') .' '. __('PHP running in CGI mode.', 'powerpress'); ?></p>
<?php } if( $powerpress_diags['system_info']['warning'] ) { ?>
	<p><?php echo __('Contact your web hosting provider to inquire how to increase the PHP memory limit on your web server.', 'powerpress'); ?></p>
<?php } ?>

</td>
</tr>
</table>

<form enctype="multipart/form-data" method="get" action="<?php echo admin_url('admin.php'); ?>">
<input type="hidden" name="action" value="powerpress-diagnostics" />
<input type="hidden" name="page" value="powerpress/powerpressadmin_tools.php" />
<?php
	// Print nonce
	wp_nonce_field('powerpress-diagnostics');
?>

<h3 style="margin-bottom: 0;"><?php echo __('Email Results', 'powerpress'); ?></h3>
<p style="margin: 0;"><?php echo __('Send the results above to the specified Email address.', 'powerpress'); ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row">
	<?php echo __('Email', 'powerpress'); ?>
</th> 
<td>
	<div style="margin-top: 5px;">
		<input type="text" name="Email" value="" style="width: 50%;" />
		<input type="submit" name="Submit" id="powerpress_save_button" class="button-primary button-blubrry" value="Send Results" />
	</div>
	<div>
		<input type="checkbox" name="CC" value="1" style="vertical-align: text-top;" checked /> CC: <?php $user_info = wp_get_current_user(); echo "&quot;{$user_info->user_nicename}&quot; &lt;{$user_info->user_email}&gt;"; ?>
	</div>
	<div>
		<input type="checkbox" name="ap" value="1" style="vertical-align: text-top;" checked /> <?php echo __('Include list of active plugins in diagnostics results.', 'powerpress') ?>
	</div>
</td>
</tr>
</table>
</form>

<p>&nbsp;</p>

	<!-- start footer -->
<?php
	}

?>