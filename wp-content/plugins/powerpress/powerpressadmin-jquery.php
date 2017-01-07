<?php
	// jQuery specific functions and code go here..
	
function powerpress_add_blubrry_redirect($program_keyword)
{
	$Settings = powerpress_get_settings('powerpress_general');
	$RedirectURL = 'http://media.blubrry.com/'.$program_keyword;
	$NewSettings = array();
	
	// redirect1
	// redirect2
	// redirect3
	for( $x = 1; $x <= 3; $x++ )
	{
		$field = sprintf('redirect%d', $x);
		if( !empty($Settings[$field]) && stripos($Settings[$field], 'podtrac.com') === false )
			$NewSettings[$field] = '';
	}
	$NewSettings['redirect1'] = $RedirectURL.'/';
	
	if( count($NewSettings) > 0 )
		powerpress_save_settings($NewSettings);
}

function powerpress_strip_redirect_urls($url)
{
	$Settings = powerpress_get_settings('powerpress_general');
	for( $x = 1; $x <= 3; $x++ )
	{
		$field = sprintf('redirect%d', $x);
		if( !empty($Settings[$field]) )
		{
			$redirect_no_http = str_replace('http://', '', $Settings[$field]);
			if( substr($redirect_no_http, -1, 1) != '/' )
				$redirect_no_http .= '/';
			$url = str_replace($redirect_no_http, '', $url);
		}
	}
	
	return $url;
}

function powerpress_admin_jquery_init()
{
	$Settings = false; // Important, never remove this
	$Settings = get_option('powerpress_general');
	
	$Error = false;

	$Programs = false;
	$Step = 1;
	
	$action = (isset($_GET['action'])?$_GET['action']: (isset($_POST['action'])?$_POST['action']:false) );
	if( !$action )
		return;
	
	$DeleteFile = false;
	switch($action)
	{
		case 'powerpress-jquery-stats': {
		
			// Make sure users have permission to access this
			if( !empty($Settings['use_caps']) && !current_user_can('view_podcast_stats') )
			{
				powerpress_admin_jquery_header( __('Blubrry Media Statistics', 'powerpress') );
?>
<h2><?php echo __('Blubrry Media Statistics', 'powerpress'); ?></h2>
<p><?php echo __('You do not have sufficient permission to manage options.', 'powerpress'); ?></p>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
				powerpress_admin_jquery_footer();
				exit;
			}
			else if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header( __('Blubrry Media Statistics', 'powerpress') );
				powerpress_page_message_add_notice( __('You do not have sufficient permission to view media statistics.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
				
			$StatsCached = get_option('powerpress_stats');
			
			powerpress_admin_jquery_header( __('Blubrry Media Statistics', 'powerpress') );
?>
<h2><?php echo __('Blubrry Media Statistics', 'powerpress'); ?></h2>
<?php
			echo $StatsCached['content'];
			powerpress_admin_jquery_footer();
			exit;
		}; break;
		case 'powerpress-jquery-media-disable': {
			
			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header('Uploader');
				powerpress_page_message_add_notice( __('You do not have sufficient permission to disable this option.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			
			check_admin_referer('powerpress-jquery-media-disable');
			
			$DisableSetting = array();
			$DisableSetting['no_media_url_folder'] = 1;
			powerpress_save_settings($DisableSetting);
			
			powerpress_admin_jquery_header( __('Select Media', 'powerpress') );
?>
<h2><?php echo __('Select Media', 'powerpress'); ?></h2>
<p><?php echo __('Blubrry Media Hosting icon will no longer be displayed when editing posts and pages.', 'powerpress'); ?></p>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
			powerpress_admin_jquery_footer();
			exit;
			
		}; // No break here, let this fall thru..
		case 'powerpress-jquery-media-delete': {
			
			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header('Uploader');
				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			
			check_admin_referer('powerpress-jquery-media-delete');
			$DeleteFile = $_GET['delete'];
			
		}; // No break here, let this fall thru..
		
		case 'powerpress-jquery-hosting': {
		
				powerpress_admin_jquery_header( __('Blubrry Podcast Media Hosting', 'powerpress') );
				
				// Congratulations you aleady have hosting!
?>
<div style="line-height: 32px; height: 32px;">&nbsp;</div>
<iframe src="//www.blubrry.com/pp/" frameborder="0" style="overflow:hidden; overflow-y: hidden;" width="100%" height="480" scrolling="no" seamless="seamless"></iframe>
<p><?php echo __('Already have a blubrry hosting account?', 'powerpress'); ?> 
	<strong><a class="button-primary button-blubrry thickbox" title="<?php echo esc_attr(__('Blubrry Services Integration', 'powerpress')); ?>" href="<?php echo wp_nonce_url(admin_url('admin.php?action=powerpress-jquery-account'), 'powerpress-jquery-account'); ?>"><?php echo __('Click here to link Blubrry account', 'powerpress'); ?></a></strong>
</p>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
				powerpress_admin_jquery_footer();
				exit;
				
		}; break;
		
		case 'powerpress-jquery-media': {
			
			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header( __('Select Media', 'powerpress') );
?>
<h2><?php echo __('Select Media', 'powerpress'); ?></h2>
<p><?php echo __('You do not have sufficient permission to manage options.', 'powerpress'); ?></p>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
				powerpress_admin_jquery_footer();
				exit;
			}
			
			if( empty($Settings['blubrry_auth']) || empty($Settings['blubrry_hosting']) || $Settings['blubrry_hosting'] === 'false' )
			{
				powerpress_admin_jquery_header( __('Select Media', 'powerpress') );
?>
<h2><?php echo __('Select Media', 'powerpress'); ?></h2>
<p><?php echo __('Wait a sec! This feature is only available to Blubrry Media Podcast Hosting customers.', 'powerpress'); ?></p>
<iframe src="//www.blubrry.com/pp/" frameborder="0" style="overflow:hidden; overflow-y: hidden;" width="100%" height="480" scrolling="no" seamless="seamless"></iframe>
<p><?php echo __('Already have a blubrry hosting account?', 'powerpress'); ?> 
	<strong><a class="button-primary button-blubrry thickbox" title="<?php echo esc_attr(__('Blubrry Services Integration', 'powerpress')); ?>" href="<?php echo wp_nonce_url(admin_url('admin.php?action=powerpress-jquery-account'), 'powerpress-jquery-account'); ?>"><?php echo __('Click here to link Blubrry account', 'powerpress'); ?></a></strong>
</p>
<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
<?php
				powerpress_admin_jquery_footer();
				exit;
			}
			
			$Msg = false;
			if( $DeleteFile )
			{
				$json_data = false;
				$api_url_array = powerpress_get_api_array();
				while( list($index,$api_url) = each($api_url_array) )
				{
					$req_url = sprintf('%s/media/%s/%s?format=json', rtrim($api_url, '/'), $Settings['blubrry_program_keyword'], $DeleteFile );
					$req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'&'. POWERPRESS_BLUBRRY_API_QSA:'');
					$json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 10, 'DELETE');
					if( !$json_data && $api_url == 'https://api.blubrry.com/' ) { // Lets force cURL and see if that helps...
						$json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 10, 'DELETE', true); // Only give this 2 seconds to return results
					}
					if( $json_data != false )
						break;
				}
				$results =  powerpress_json_decode($json_data);
				
				if( isset($results['text']) )
					$Msg = $results['text'];
				else if( isset($results['error']) )
					$Msg = $results['error'];
				else
					$Msg = __('An unknown error occurred deleting media file.', 'powerpress');
			}
			
			$json_data = false;
			$api_url_array = powerpress_get_api_array();
			while( list($index,$api_url) = each($api_url_array) )
			{
				$req_url = sprintf('%s/media/%s/index.json?quota=true&published=true', rtrim($api_url, '/'), $Settings['blubrry_program_keyword'] );
				$req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'&'. POWERPRESS_BLUBRRY_API_QSA:'');
				$json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth']);
				if( !$json_data && $api_url == 'https://api.blubrry.com/' ) { // Lets force cURL and see if that helps...
					$json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 15, false, true);
				}
				if( $json_data != false )
					break;
			}
			
			$results =  powerpress_json_decode($json_data);
				
			$FeedSlug = sanitize_title($_GET['podcast-feed']);
			powerpress_admin_jquery_header( __('Select Media', 'powerpress'), true );
?>
<style>
.error {
	padding:  6px 10px;
	border 1px solid #ffc599;
	background-color: #ff9800;
	color: #fff;
	font-weight: bold;
}
</style>
<script language="JavaScript" type="text/javascript"><!--

function SelectMedia(File)
{
	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').value=File;
	self.parent.document.getElementById('powerpress_hosting_<?php echo $FeedSlug; ?>').value='1';
	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').readOnly=true;
	self.parent.document.getElementById('powerpress_hosting_note_<?php echo $FeedSlug; ?>').style.display='block';
	if( self.parent.powerpress_update_for_video )
		self.parent.powerpress_update_for_video(File, '<?php echo $FeedSlug; ?>');
	self.parent.tb_remove();
}
function SelectURL(url)
{
	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').value=url;
	self.parent.document.getElementById('powerpress_hosting_<?php echo $FeedSlug; ?>').value='0';
	self.parent.document.getElementById('powerpress_url_<?php echo $FeedSlug; ?>').readOnly=false;
	self.parent.document.getElementById('powerpress_hosting_note_<?php echo $FeedSlug; ?>').style.display='none';
	if( self.parent.powerpress_update_for_video )
		self.parent.powerpress_update_for_video(url, '<?php echo $FeedSlug; ?>');
	self.parent.tb_remove();
}
function DeleteMedia(File)
{
	return confirm('<?php echo __('Delete', 'powerpress'); ?>: '+File+'\n\n<?php echo __('Are you sure you want to delete this media file?', 'powerpress'); ?>');
}
//-->
</script>
		<div id="media-header">
			<h2><?php echo __('Select Media', 'powerpress'); ?></h2>
			<?php
			
				if(  !empty($results['quota']['expires'] ) )
				{
					$message = '';
					if( !empty($results['quota']['expires']['expired']) )
					{
						$message = '<p>'. sprintf( __('Media hosting service expired on %s.', 'powerpress'), esc_attr($results['quota']['expires']['readable_date'])) . '</p>';
					}
					else
					{
						$message = '<p>'. sprintf( __('Media hosting service will expire on %s.', 'powerpress'), esc_attr($results['quota']['expires']['readable_date'])) . '</p>';
					}
					
					$message .= '<p style="text-align: center;"><strong><a href="'. $results['quota']['expires']['renew_link'] .'" target="_blank" style="text-decoration: underline;">'. __('Renew Media Hosting Service', 'powerpress') . '</a></strong></p>';
					powerpress_page_message_add_notice( $message, 'inline', false );
					powerpress_page_message_print();
				}
				else if( empty($results) )
				{
					// Handle the error here.
					$message = '<h3>'.__('Error', 'powerpress') . '</h3>';
					global $g_powerpress_remote_error, $g_powerpress_remote_errorno;
					if( !empty($g_powerpress_remote_errorno) && $g_powerpress_remote_errorno == 401 )
						$message .= '<p>'. __('Incorrect sign-in email address or password.', 'powerpress').'</p><p>'.__('Verify your account entered under Services and Statistics settings then try again.', 'powerpress') .'</p>';
					else if( !empty($g_powerpress_remote_error) )
						$message .= '<p>'.$g_powerpress_remote_error.'</p>';
					else
						$message .= '<p>'.__('Unable to connect to service.','powerpress').'</p>';
			
					// Print an erro here
					powerpress_page_message_add_notice( $message, 'inline', false );
					powerpress_page_message_print();
				}
				
				if( $Msg )
				echo '<p>'. $Msg . '</p>';
			?>
			<div class="media-upload-link"><a title="<?php echo esc_attr(__('Blubrry Podcast Hosting', 'powerpress')); ?>"  href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-upload", 'powerpress-jquery-upload'); ?>&podcast-feed=<?php echo $FeedSlug; ?>&keepThis=true&TB_iframe=true&height=350&width=530&modal=false" class="thickbox"><?php echo __('Upload Media File', 'powerpress'); ?></a></div>
			<p><?php echo __('Select from media files uploaded to blubrry.com', 'powerpress'); ?>:</p>
		</div>
	<div id="media-items-container">
		<div id="media-items">
<?php
		$QuotaData = false;
		if( isset($results['error']) )
		{
			echo '<p class="error">'.$results['error'].'</p>';
			echo '<p><a href="https://publish.blubrry.com/services/" target="_blank">'. __('Manage your blubrry.com Account', 'powerpress') .'</a></p>';
		}
		else if( is_array($results) )
		{
			$PublishedList = false;
			while( list($index,$data) = each($results) )
			{
				if( $index === 'quota' )
				{
					$QuotaData = $data;
					continue;
				}
				
				if( $PublishedList == false && !empty($data['published']) )
				{
?>
<div id="media-published-title">
	<?php echo __('Last 20 Published media files', 'powerpress'); ?>:
</div>
<?php
					$PublishedList = true;
				}

?>
<div class="media-item <?php echo (empty($data['published'])?'media-unpublished':'media-published'); ?>">
	<strong class="media-name"><?php echo htmlspecialchars($data['name']); ?></strong>
	<cite><?php echo powerpress_byte_size($data['length']); ?></cite>
	<?php if( !empty($data['published']) ) { ?>
	<div class="media-published-date">&middot; <?php echo __('Published on', 'powerpress'); ?> <?php echo date(get_option('date_format'), $data['last_modified']); ?></div>
	<?php } ?>
	<div class="media-item-links">
		<?php if( !empty($data['published']) && !empty($data['url']) ) { ?>
			<a href="#" onclick="SelectURL('<?php echo $data['url']; ?>'); return false;"><?php echo __('Select', 'powerpress'); ?></a>
		<?php } else { ?>
			<?php if (function_exists('curl_init')) { ?>
				<a href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-media-delete", 'powerpress-jquery-media-delete'); ?>&amp;podcast-feed=<?php echo $FeedSlug; ?>&amp;delete=<?php echo urlencode($data['name']); ?>" onclick="return DeleteMedia('<?php echo $data['name']; ?>');"><?php echo __('Delete', 'powerpress'); ?></a> | 
			<?php } ?>
			<a href="#" onclick="SelectMedia('<?php echo $data['name']; ?>'); return false;"><?php echo __('Select', 'powerpress'); ?></a>
		<?php } ?>
	</div> 
</div>
<?php				
			}
		}
?>
		</div><!-- end media-items -->
	</div><!-- end media-items-container -->
	<div id="media-footer">
		<div class="media-upload-link"><a title="<?php echo esc_attr(__('Blubrry Podcast Hosting', 'powerpress')); ?>" href="<?php echo admin_url() . wp_nonce_url("admin.php?action=powerpress-jquery-upload", 'powerpress-jquery-upload'); ?>&podcast-feed=<?php echo $FeedSlug; ?>&keepThis=true&TB_iframe=true&height=350&width=530&modal=false" class="thickbox"><?php echo __('Upload Media File', 'powerpress'); ?></a></div>
		<?php
		if( $QuotaData ) {
					
			$NextDate = strtotime( $QuotaData['published']['next_date']);
		?>
			<?php
			if( $QuotaData['unpublished']['available'] != $QuotaData['unpublished']['total'] )
			{
				//echo '<p>';
				//echo sprintf( __('You have uploaded %s (%s available) of your %s upload limit.', 'powerpress'),
				//	'<em>'. powerpress_byte_size($QuotaData['unpublished']['used']) .'</em>',
				//	'<em>'. powerpress_byte_size($QuotaData['unpublished']['available']) .'</em>',
				//	'<em>'. powerpress_byte_size($QuotaData['unpublished']['total']) .'</em>' );
				//echo '</p>';
			}
			
			if( $QuotaData['published']['status'] == 'OK' )
			{
			?>
			<p><?php
			
			if( $QuotaData['published']['available'] > 0 ) // != $QuotaData['published']['total'] )
			{
				echo sprintf( __('Publishing space available: %s of (%s %%) of %s/month quota.', 'powerpress'),
					'<em>'. powerpress_byte_size($QuotaData['published']['available']) .'</em>',
					//'<em>'. powerpress_byte_size($QuotaData['published']['total']-$QuotaData['published']['available']) .'</em>',
					'<em>'. round( ($QuotaData['published']['available']/$QuotaData['published']['total'])*100 ) .'</em>',
					'<em>'. str_replace('.0', '', powerpress_byte_size($QuotaData['published']['total'])) .'</em>' );
				//echo sprintf( __('You have %s available (%s published in the last 30 days) of your %s publish limit.', 'powerpress'),
				//	'<em>'. powerpress_byte_size($QuotaData['published']['available']) .'</em>',
				//	'<em>'. powerpress_byte_size($QuotaData['published']['total']-$QuotaData['published']['available']) .'</em>',
				//	'<em>'. powerpress_byte_size($QuotaData['published']['total']) .'</em>' );
			}
			else if( $QuotaData['published']['available'] == 0 ) // Hosting account frozen
			{
			
			}
			else
			{
				echo sprintf( __('You have %s publish space available.', 'powerpress'),
					'<em>'. powerpress_byte_size($QuotaData['published']['total']) .'</em>' );
			}
			?>
			</p>
			<p>
			<?php
				echo sprintf( __('No-Fault Hosting: You may upload a media file up to %s in size and be within the No-Fault maximum (25%% more than your %s quota).', 'powerpress'),
					 powerpress_byte_size($QuotaData['published']['no_fault_maximum']),
					str_replace('.0', '', powerpress_byte_size($QuotaData['published']['total'])));
				echo ' ';
				echo '<a href="http://create.blubrry.com/resources/podcast-media-hosting/no-fault/" target="_blank">'. __('Learn More', 'powerpress') .'</a>';
			?>
			</p>
			<p><?php
			if( $QuotaData['published']['available'] != $QuotaData['published']['total'] )
			{
			echo sprintf( __('Your quota will reset on %s.', 'powerpress'),
				date('m/d/Y', $NextDate)  );
			}
			?>
			</p>
			<?php
				}
				else if( $QuotaData['published']['status'] == 'UNLIMITED' )
				{
					echo '<p>';
					echo __('Publishing Space Available: Unlimited (Professional Hosting)', 'powerpress');
					echo '<p>';
				}
				else
				{
					echo '<p>';
					echo __('Publishing Space Available: Account has expired', 'powerpress');
					echo '<p>';
				}
			?>
		<?php } ?>
		<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
	</div>
	
<?php	
			powerpress_admin_jquery_footer(true);
			exit;
		}; break;
		case 'powerpress-jquery-account-save': {
		
			if( !current_user_can(POWERPRESS_CAPABILITY_MANAGE_OPTIONS) )
			{
				powerpress_admin_jquery_header('Blubrry Services', 'powerpress');
				powerpress_page_message_add_notice( __('You do not have sufficient permission to manage options.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			
			check_admin_referer('powerpress-jquery-account');
			
			$Password = $_POST['Password'];
			$SaveSettings = $_POST['Settings'];
			$Password = powerpress_stripslashes($Password);
			$SaveSettings = powerpress_stripslashes($SaveSettings);
			
			$Save = false;
			$Close = false;
		
			
			if( !empty($_POST['Remove']) )
			{
				$SaveSettings['blubrry_username'] = '';
				$SaveSettings['blubrry_auth'] = '';
				$SaveSettings['blubrry_program_keyword'] = '';
				$SaveSettings['blubrry_hosting'] = false;
				$Close = true;
				$Save = true;
			}
			else
			{
				$Programs = array();
				$ProgramHosting = array();
					
					// Anytime we change the password we need to test it...
				$auth = base64_encode( $SaveSettings['blubrry_username'] . ':' . $Password );
				$json_data = false;
				$api_url_array = powerpress_get_api_array();
				while( list($index,$api_url) = each($api_url_array) )
				{
					$req_url = sprintf('%s/service/index.json', rtrim($api_url, '/') );
					$req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
					$json_data = powerpress_remote_fopen($req_url, $auth);
					if( !$json_data && $api_url == 'https://api.blubrry.com/' ) { // Lets force cURL and see if that helps...
						$json_data = powerpress_remote_fopen($req_url, $auth, array(), 15, false, true);
					}
					if( $json_data != false )
						break;
				}
				
				if( $json_data )
				{
					$results =  powerpress_json_decode($json_data);
					
					if( isset($results['error']) )
					{
						$Error = $results['error'];
						if( strstr($Error, __('currently not available', 'powerpress') ) )
						{
							$Error = __('Unable to find podcasts for this account.', 'powerpress');
							$Error .= '<br /><span style="font-weight: normal; font-size: 12px;">';
							$Error .= 'Verify that the email address you enter here matches the email address you used when you listed your podcast on blubrry.com.</span>';
						}
						else if( preg_match('/No programs found.*media hosting/i', $results['error']) )
						{
							$Error .= '<br/><span style="font-weight: normal; font-size: 12px;">';
							$Error .= 'Service may take a few minutes to activate.</span>';
						}
					}
					else if( !is_array($results) )
					{
						$Error = $json_data;
					}
					else
					{
						// Get all the programs for this user...
						while( list($null,$row) = each($results) )
						{
							$Programs[ $row['program_keyword'] ] = $row['program_title'];
							if( $row['hosting'] === true || $row['hosting'] == 'true' )
								$ProgramHosting[ $row['program_keyword'] ] = true;
							else
								$ProgramHosting[ $row['program_keyword'] ] = false;
						}
						
						if( count($Programs) > 0 )
						{
							$SaveSettings['blubrry_auth'] = $auth;
							
							if( !empty($SaveSettings['blubrry_program_keyword']) )
							{
								powerpress_add_blubrry_redirect($SaveSettings['blubrry_program_keyword']);
								$SaveSettings['blubrry_hosting'] = $ProgramHosting[ $SaveSettings['blubrry_program_keyword'] ];
								if( !is_bool($SaveSettings['blubrry_hosting']) )
								{
									if( $SaveSettings['blubrry_hosting'] === 'false' || empty($SaveSettings['blubrry_hosting']) )
										$SaveSettings['blubrry_hosting'] = false;
								}
									
								$Save = true;
								$Close = true;
							}
							else if( isset($SaveSettings['blubrry_program_keyword']) ) // Present but empty
							{
								$Error = __('You must select a program to continue.', 'powerpress');
							}
							else if( count($Programs) == 1 )
							{
								list($keyword, $title) = each($Programs);
								$SaveSettings['blubrry_program_keyword'] = $keyword;
								$SaveSettings['blubrry_hosting'] = $ProgramHosting[ $keyword ];
								if( !is_bool($SaveSettings['blubrry_hosting']) )
								{
									if( $SaveSettings['blubrry_hosting'] === 'false' || empty($SaveSettings['blubrry_hosting']) )
										$SaveSettings['blubrry_hosting'] = false;
								}
								powerpress_add_blubrry_redirect($keyword);
								$Close = true;
								$Save = true;
							}
							else
							{
								$Error = __('Please select your podcast program to continue.', 'powerpress');
								$Step = 2;
								$Settings['blubrry_username'] = $SaveSettings['blubrry_username'];
							}
						}
						else
						{
							$Error = __('No podcasts for this account are listed on blubrry.com.', 'powerpress');
						}
					}
				}
				else
				{
					global $g_powerpress_remote_error, $g_powerpress_remote_errorno;
					//$Error = '<h3>'. __('Error', 'powerpress') .'</h3>';
					if( !empty($g_powerpress_remote_errorno) && $g_powerpress_remote_errorno == 401 )
						$Error .= '<p>'. __('Incorrect sign-in email address or password.', 'powerpress') .'</p><p>'. __('Verify your account settings then try again.', 'powerpress') .'</p>';
					else if( !empty($g_powerpress_remote_error) )
						$Error .= '<p>'.$g_powerpress_remote_error .'</p>';
					else
						$Error .= '<p>'.__('Authentication failed.', 'powerpress') .'</p>';
				}
				
				if( $Error )
				{
					$Error .= '<p style="text-align: center;"><a href="http://create.blubrry.com/resources/powerpress/powerpress-settings/services-stats/" target="_blank">'. __('Click Here For Help','powerpress') .'</a></p>';
				}
			}
			
			if( $Save )
				powerpress_save_settings($SaveSettings);
			
			// Clear cached statistics
			delete_option('powerpress_stats');
			
			if( $Error )
				powerpress_page_message_add_notice( $Error, 'inline', false );
				
			if( $Close )
			{
				powerpress_admin_jquery_header( __('Blubrry Services', 'powerpress') );
				powerpress_page_message_print();
?>
<p style="display: none; text-align: right; position: absolute; top: 5px; right: 5px; margin: 0; padding:0;"><a href="#" onclick="self.parent.tb_remove(); return false;" title="<?php echo __('Close', 'powerpress'); ?>"><img src="<?php echo admin_url(); ?>/images/no.png" alt="<?php echo __('Close', 'powerpress'); ?>" /></a></p>
<h2><?php echo __('Blubrry Services', 'powerpress'); ?></h2>
<p style="text-align: center;"><strong><?php echo __('Settings Saved Successfully!', 'powerpress'); ?></strong></p>
<p style="text-align: center;">
	<a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_basic.php"); ?>" onclick="self.parent.tb_remove(); return false;" target="_top"><?php echo __('Close', 'powerpress'); ?></a>
</p>
<script type="text/javascript"><!--

jQuery(document).ready(function($) {
	// Upload loading, check the parent window for #blubrry_stats_settings div
	if( jQuery('#blubrry_stats_settings',parent.document).length )
	{
		jQuery('#blubrry_stats_settings',parent.document).html('');
	}
});

// --></script>
<?php
				powerpress_admin_jquery_footer();
				exit;
			}
			
			
		} // no break here, let the next case catch it...
		case 'powerpress-jquery-account':
		{
			if( !current_user_can(POWERPRESS_CAPABILITY_MANAGE_OPTIONS) )
			{
				powerpress_admin_jquery_header( __('Blubrry Services', 'powerpress') );
				powerpress_page_message_add_notice( __('You do not have sufficient permission to manage options.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			
			if( !ini_get( 'allow_url_fopen' ) && !function_exists( 'curl_init' ) )
			{
				powerpress_admin_jquery_header( __('Blubrry Services', 'powerpress') );
				powerpress_page_message_add_notice( __('Your server must either have the php.ini setting \'allow_url_fopen\' enabled or have the PHP cURL library installed in order to continue.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			
			check_admin_referer('powerpress-jquery-account');
			
			if( !$Settings )
				$Settings = get_option('powerpress_general');
			
			if( empty($Settings['blubrry_username']) )
				$Settings['blubrry_username'] = '';
			if( empty($Settings['blubrry_hosting']) || $Settings['blubrry_hosting'] === 'false' )
				$Settings['blubrry_hosting'] = false;
			if( empty($Settings['blubrry_program_keyword']) )
				$Settings['blubrry_program_keyword'] = '';
				
			if( $Programs == false )
				$Programs = array();
			
			powerpress_admin_jquery_header( __('Blubrry Services', 'powerpress') );
			powerpress_page_message_print();	
?>
<form action="<?php echo admin_url('admin.php'); ?>" enctype="multipart/form-data" method="post">
<?php wp_nonce_field('powerpress-jquery-account'); ?>
<input type="hidden" name="action" value="powerpress-jquery-account-save" />
<div id="accountinfo">
	<h2><?php echo __('Blubrry Services', 'powerpress'); ?></h2>
<?php if( $Step == 1 ) { ?>
	<p>
		<label for="blubrry_username"><?php echo __('Blubrry User Name (Email)', 'powerpress'); ?></label>
		<input type="text" id="blubrry_username" name="Settings[blubrry_username]" value="<?php echo esc_attr($Settings['blubrry_username']); ?>" />
	</p>
	<p id="password_row">
		<label for="password_password"><?php echo __('Blubrry Password', 'powerpress'); ?></label>
		<input type="password" id="password_password" name="Password" value="" />
	</p>
<?php } else { ?>
	<input type="hidden" name="Settings[blubrry_username]" value="<?php echo esc_attr($Settings['blubrry_username']); ?>" />
	<input type="hidden" name="Password" value="<?php echo esc_attr($Password); ?>" />
	<!-- <input type="hidden" name="Settings[blubrry_hosting]" value="<?php echo $Settings['blubrry_hosting']; ?>" /> -->
	<p>
		<label for="blubrry_program_keyword"><?php echo __('Select Blubrry Program', 'powerpress'); ?></label>
<select id="blubrry_program_keyword" name="Settings[blubrry_program_keyword]">
<option value=""><?php echo __('Select Program', 'powerpress'); ?></option>
<?php
while( list($value,$desc) = each($Programs) )
	echo "\t<option value=\"$value\"". ($Settings['blubrry_program_keyword']==$value?' selected':''). ">$desc</option>\n";
?>
</select>
	</p>
<?php } ?>
	<p>
		<input type="submit" name="Save" value="<?php echo __('Save', 'powerpress'); ?>" />
		<input type="button" name="Cancel" value="<?php echo __('Cancel', 'powerpress'); ?>" onclick="self.parent.tb_remove();" />
		<input type="submit" name="Remove" value="Remove" style="float: right;" onclick="return confirm('<?php echo __('Remove Blubrry Services Integration, are you sure?', 'powerpress'); ?>');" />
	</p>
</div>
</form>
<?php
			powerpress_admin_jquery_footer();
			exit;
		}; break;
		case 'powerpress-jquery-upload': {
			
			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header( __('Uploader', 'powerpress') );
				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.','powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			
			check_admin_referer('powerpress-jquery-upload');
			
			$RedirectURL = false;
			$Error = false;
			
			if( !$Settings )
				$Settings = get_option('powerpress_general');
			
			if( empty($Settings['blubrry_hosting']) || $Settings['blubrry_hosting'] === 'false' )
				$Settings['blubrry_hosting'] = false;
			if( empty($Settings['blubrry_program_keyword']) )
				$Settings['blubrry_program_keyword'] = '';
			if( empty($Settings['blubrry_auth']) )
				$Settings['blubrry_auth'] = '';	
				
			if( empty($Settings['blubrry_hosting']) )
			{
				$Error = __('This feature is available to Blubrry Hosting users only.','powerpress');
			}
			
			if( $Error == false )
			{
				$json_data = false;
				$api_url_array = powerpress_get_api_array();
				while( list($index,$api_url) = each($api_url_array) )
				{
					$req_url = sprintf('%s/media/%s/upload_session.json', rtrim($api_url, '/'), $Settings['blubrry_program_keyword'] );
					$req_url .= (defined('POWERPRESS_BLUBRRY_API_QSA')?'?'. POWERPRESS_BLUBRRY_API_QSA:'');
					$json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth']);
					if( !$json_data && $api_url == 'https://api.blubrry.com/' ) { // Lets force cURL and see if that helps...
						$json_data = powerpress_remote_fopen($req_url, $Settings['blubrry_auth'], array(), 15, false, true);
					}
					if( $json_data != false )
						break;
				}
				
				
				$results =  powerpress_json_decode($json_data);
				
				// We need to obtain an upload session for this user...
				if( isset($results['error']) && strlen($results['error']) > 1 )
				{
					$Error = $results['error'];
					if( strstr($Error, 'currently not available') )
						$Error = __('Unable to find podcasts for this account.','powerpress');
				}
				else if( $results === $json_data )
				{
					$Error = $json_data;
				}
				else if( !is_array($results) || $results == false )
				{
					$Error = $json_data;
				}
				else
				{
					if( isset($results['url']) && !empty($results['url']) )
						$RedirectURL = $results['url'];
				}
			}
			
			if( $Error == false && $RedirectURL )
			{
				$RedirectURL .= '&ReturnURL=';
				$RedirectURL .= urlencode( admin_url("admin.php?action=powerpress-jquery-upload-complete") );
				header("Location: $RedirectURL");
				exit;
			}
			else if( $Error == false )
			{
				global $g_powerpress_remote_error, $g_powerpress_remote_errorno;
				if( !empty($g_powerpress_remote_errorno) && $g_powerpress_remote_errorno == 401 )
					$Error = '<p>'. __('Incorrect sign-in email address or password.', 'powerpress').'</p><p>'.__('Verify your account entered under Services and Statistics settings then try again.', 'powerpress') .'</p>';
				else if( !empty($g_powerpress_remote_error) )
					$Error = '<p>'.$g_powerpress_remote_error .'</p>';
				else
					$Error = '<p>'.__('Unable to obtain upload session.','powerpress') .'</p>';
			}
			
			powerpress_admin_jquery_header( __('Uploader','powerpress') );
			echo '<h2>'. __('Uploader','powerpress') .'</h2>';
			echo '<p>';
			echo $Error;
			echo '</p>';
			?>
			<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
			<?php
			powerpress_admin_jquery_footer();
			exit;
		}; break;
		case 'powerpress-jquery-upload-complete': {
		
			if( !current_user_can('edit_posts') )
			{
				powerpress_admin_jquery_header('Uploader');
				powerpress_page_message_add_notice( __('You do not have sufficient permission to upload media.', 'powerpress') );
				powerpress_page_message_print();
				powerpress_admin_jquery_footer();
				exit;
			}
			// sanitize_title esc_attr esc_html powerpress_esc_html
			$File = (isset($_GET['File'])? htmlspecialchars($_GET['File']):false);
			$Message = (isset($_GET['Message'])? htmlspecialchars($_GET['Message']):'');
			
			powerpress_admin_jquery_header( __('Upload Complete', 'powerpress') );
			echo '<h2>'. __('Uploader', 'powerpress') .'</h2>';
			echo '<p>';
			if( $File )
			{
				echo __('File', 'powerpress')  .': ';
				echo $File;
				echo ' - ';
			}
			echo $Message;
			echo '</p>';
			?>
			<p style="text-align: center;"><a href="#" onclick="self.parent.tb_remove();"><?php echo __('Close', 'powerpress'); ?></a></p>
			<?php
			
			if( empty($Message) )
			{
?>
<script language="JavaScript" type="text/javascript"><!--
<?php if( $File != '' ) { ?>
self.parent.SelectMedia('<?php echo $File ; ?>'); <?php } ?>
self.parent.tb_remove();
//-->
</script>
<?php
			}
			powerpress_admin_jquery_footer();
			exit;
		}; break;
	}
	
}

function powerpress_admin_jquery_header($title, $jquery = false)
{
	$other = false;
	if( $jquery )
		add_thickbox(); // we use the thckbox for some settings
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo $title; ?> &#8212; <?php echo __('WordPress', 'powerpress'); ?></title>
<?php

// In case these functions haven't been included yet...
require_once(ABSPATH . 'wp-admin/includes/admin.php');

wp_admin_css( 'css/global' );
wp_admin_css();
if( $jquery )
	wp_enqueue_script('utils');

do_action('admin_print_styles');
do_action('admin_print_scripts');
do_action('admin_head');

echo '<!-- done adding extra stuff -->';

?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/jquery.css" type="text/css" media="screen" />
<?php if( $other ) echo $other; ?>
</head>
<body>
<div id="container">
<p style="display: none; text-align: right; position: absolute; top: 5px; right: 5px; margin: 0; padding: 0;"><a href="#" onclick="self.parent.tb_remove();" title="<?php echo __('Cancel', 'powerpress'); ?>"><img src="<?php echo admin_url(); ?>/images/no.png" /></a></p>
<?php
}


function powerpress_admin_jquery_footer($jquery = false)
{
	if( $jquery )
		do_action('admin_print_footer_scripts');
	
?>
</div><!-- end container -->
</body>
</html>
<?php
}

?>