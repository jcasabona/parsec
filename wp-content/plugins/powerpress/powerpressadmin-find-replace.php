<?php
	// powerpressadmin-find-replace.php
	$g_FindReplaceResults = array();
	
	// Returns an array of enclosures with key of array = meta_id
	function powerpressadmin_find_replace_get_enclosures($find_string)
	{
		$Episodes = array();
		global $wpdb;
		
		$query = "SELECT meta_id, post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE \"%enclosure\"";
		$results_data = $wpdb->get_results($query, ARRAY_A);
		
		while( list( $index, $row) = each($results_data) )
		{
			list($url) = @explode("\n", $row['meta_value'], 2 );
			$url = trim($url);
			if( $find_string == '' || strstr($url, $find_string) )
				$Episodes[ $row['meta_id'] ] = $row;
		}
		return $Episodes;
	}
	
	function powerpressadmin_find_replace_update_meta($meta_id, $meta_value)
	{
		global $wpdb;
		return $wpdb->update( $wpdb->postmeta, array('meta_value'=>$meta_value), array('meta_id'=>$meta_id) );
	}
	
	function powerpressadmin_find_replace_process()
	{
		$wp_remote_options = array();
		$wp_remote_options['user-agent'] = 'Blubrry PowerPress/'.POWERPRESS_VERSION;
		$wp_remote_options['httpversion'] = '1.1';
		
		global $g_FindReplaceResults;
		if( isset($_POST['FindReplace']) )
		{
			$FindReplace = $_POST['FindReplace'];
			$FindReplace['step'] = intval( $FindReplace['step'] );
			if( $FindReplace['step'] == 2 || $FindReplace['step'] == 3 )
			{
				$success_count = 0;
				$failed_count = 0;
				
				$FoundArray = powerpressadmin_find_replace_get_enclosures($FindReplace['find_string']);
				
				while( list($meta_id, $row) = each($FoundArray) )
				{
					// powerpress_get_post_meta
					$meta_value = get_post_meta($row['post_id'], $row['meta_key'], true);
					$parts = explode("\n", $meta_value, 2);
					$other_meta_data = false;
					if( count($parts) == 2 )
						list($old_url, $other_meta_data) = $parts;
					else
						$old_url = trim($meta_value);
					
					$old_url = trim($old_url);
					//echo  $old_url;
					$g_FindReplaceResults[ $meta_id ] = $row;
					$g_FindReplaceResults[ $meta_id ]['old_url'] = $old_url;
					$g_FindReplaceResults[ $meta_id ]['find_readable'] = str_replace($FindReplace['find_string'],
							sprintf('<span class="find_string strong">%s</span>', esc_attr($FindReplace['find_string'])), esc_attr($old_url) );
					$g_FindReplaceResults[ $meta_id ]['replace_readable'] = str_replace($FindReplace['find_string'],
							sprintf('<span class="replace_string strong">%s</span>', esc_attr($FindReplace['replace_string']) ), esc_attr($old_url) );
					$new_url = str_replace($FindReplace['find_string'],$FindReplace['replace_string'], $old_url);
					$g_FindReplaceResults[ $meta_id ]['new_url'] = $new_url;
					
					if( $FindReplace['step'] == 3 && $FindReplace['find_string'] != '' )
					{
						$good = true;
						if( !empty($FindReplace['verify']) )
						{
							$response = wp_remote_head( $new_url, $wp_remote_options );
							// Redirect 1
							if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
							{
								$headers = wp_remote_retrieve_headers( $response );
								$response = wp_remote_head( $headers['location'], $wp_remote_options );
							}
							// Redirect 2
							if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
							{
								$headers = wp_remote_retrieve_headers( $response );
								$response = wp_remote_head( $headers['location'], $wp_remote_options );
							}
							// Redirect 3
							if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
							{
								$headers = wp_remote_retrieve_headers( $response );
								$response = wp_remote_head( $headers['location'], $wp_remote_options );
							}
							// Redirect 4
							if( !is_wp_error( $response ) && ($response['response']['code'] == 301 || $response['response']['code'] == 302) )
							{
								$headers = wp_remote_retrieve_headers( $response );
								$response = wp_remote_head( $headers['location'], $wp_remote_options );
							}
							//$headers = wp_remote_retrieve_headers( $response );
				
							//$response = @wp_remote_head( $new_url, $wp_remote_options );
							if ( is_wp_error( $response ) )
							{
								$g_FindReplaceResults[ $meta_id ]['error'] = $response->get_error_message();
								$good = false;
							}
							
							if( $good && isset($response['response']['code']) && ($response['response']['code'] < 200 || $response['response']['code'] > 203) )
							{
								$g_FindReplaceResults[ $meta_id ]['error'] = 'Error, HTTP '.$response['response']['code'];
								$good = false;
							}
						}
						
						if( $good )
						{
							$DataUpdated = $new_url;
							if( $other_meta_data )
								$DataUpdated .= "\n". $other_meta_data;
							if( update_post_meta( $row['post_id'], $row['meta_key'], $DataUpdated) )
								$success_count++;
							else
								$good = false;
						}
						
						if( !$good )
						{
							$failed_count++;
						}
						$g_FindReplaceResults[ $meta_id ]['success'] = $good;
					}
					
				}
				
				if( $FindReplace['step'] == 3 )
				{
					if( $success_count > 0 )
						powerpress_page_message_add_notice(  sprintf(__('%d URLs updated successfully.', 'powerpress'), $success_count) );
					if( $failed_count > 0 )
						powerpress_page_message_add_error(  sprintf(__('%d URLs were not updated.', 'powerpress'), $failed_count) );
					else if( $FindReplace['find_string'] == '' )
						powerpress_page_message_add_notice(  __('Nothing specified to find.', 'powerpress') );
				}
			}
			
		
		}
		
		powerpress_page_message_add_notice(  __('WARNING: Please backup your database before proceeding. Blubrry PowerPress is not responsible for any lost or damaged data resulting from this Find and Replace tool.', 'powerpress') );
	}
	
	function powerpress_admin_find_replace()
	{
		$FindReplaceResults = array();
		
		if( isset($_POST['FindReplace']) )
		{
			$FindReplace = $_POST['FindReplace'];
			$FindReplace['step'] = intval( $FindReplace['step'] );
		}
		else
		{
			$FindReplace = array();
			$FindReplace['find_string'] = '';
			$FindReplace['replace_string'] = '';
			$FindReplace['step'] = 1;
		}
		
		if( $FindReplace['step'] == 2 )
		{
			$FindReplace['verify'] = true;
		}
		if( $FindReplace['step'] == 2 || $FindReplace['step'] == 3 )
		{
			$FindReplaceResults = powerpressadmin_find_replace_get_results();
		}
		
		//$FindReplace = powerpress_esc_html($FindReplace); // Prevent XSS
?>

<script type="text/javascript"><!--
function VerifyCheck(obj)
{
	if( !obj.checked && !confirm('<?php echo __('WARNING: Verification prevents changes if the URL entered is invalid.\n\nAre you sure you do not want to verify the URLs?', 'powerpress'); ?>') )
		obj.checked = true;
}

function ConfirmReplace()
{
	if( confirm('<?php echo __('WARNING: You are about to make permanent changes to your database.\n\nAre you sure you wish to continue?', 'powerpress'); ?>') )
	{
		jQuery('#replace_step').val('3');
		return true;
	}
	return false;
}
//-->
</script>
<style type="text/css">
.find_string {
	background-color: #CFE2F3; /* lt blue */
	padding: 1px;
}
.replace_string {
	background-color: #FCE5CD; /* orange */
	padding: 1px;
}
.strong {
	font-style:italic;
}

dd {
	margin: 2px 2px 2px 10px;
}
dt {
	margin: 2px 2px 2px 10px;
}
</style>

<input type="hidden" name="action" value="powerpress-find-replace" />
<input type="hidden" name="FindReplace[step]" value="<?php echo esc_attr($FindReplace['step']); ?>" id="replace_step" />

<h2><?php echo __("Find and Replace Episode URLs", 'powerpress'); ?></h2>

<p style="margin-bottom: 0;"><?php echo __('Find and replace complete or partial segments of media URLs. Useful if you move your media to a new web site or service.', 'powerpress'); ?></p>

<table class="form-table">
	<tr valign="top">
	<th scope="row"><?php echo __("Find in URL", 'powerpress'); ?></th> 
	<td>
			<input type="text" id="find_string" name="FindReplace[find_string]" style="width: 50%;" value="<?php echo esc_attr($FindReplace['find_string']); ?>" maxlength="255" <?php if( $FindReplace['step'] != 1 ) { echo ' readOnly'; } ?> />
			<?php if( $FindReplace['step'] != 1 ) { ?><a href="#" onclick="jQuery('#replace_step').val('1');jQuery('#replace_step').closest('form').submit();return false;"><?php echo __('Modify', 'powerpress'); ?></a><?php } ?>
			<p style="margin: 0; font-size: 90%;"><?php echo __('Example', 'powerpress'); ?>: http://www.oldsite.com/</p>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row"><?php echo __('Replace with', 'powerpress'); ?></th> 
	<td>
			<input type="text" id="replace_string" name="FindReplace[replace_string]" style="width: 50%;" value="<?php echo esc_attr($FindReplace['replace_string']); ?>" maxlength="255" <?php if( $FindReplace['step'] != 1 ) { echo ' readOnly'; } ?> />
			<?php if( $FindReplace['step'] != 1 ) { ?><a href="#" onclick="jQuery('#replace_step').val('1');jQuery('#replace_step').closest('form').submit();return false;"><?php echo __('Modify', 'powerpress'); ?></a><?php } ?>
			<p style="margin: 0; font-size: 90%;"><?php echo __('Example', 'powerpress'); ?>: http://www.newsite.com/</p>
	</td>
	</tr>
</table>




<?php
			if( $FindReplace['step'] == 2 || $FindReplace['step'] == 3 )
			{
?>
<h2><?php echo ($FindReplace['step'] == 2 ? __('Preview Changes', 'powerpress') : __('Change Results', 'powerpress') ); ?></h2>

<p><?php echo sprintf( __('Found %d results with "%s"', 'powerpress'), count($FindReplaceResults), "<span class=\"find_string strong\">". esc_attr($FindReplace['find_string']). "</span>" ); ?></p>

<ol>
<?php
				while( list($meta_id, $row) = each($FindReplaceResults) )
				{
					$post_view_link = '<a href="' . get_permalink($row['post_id']) . '" target="_blank">' . get_the_title($row['post_id']) . '</a>';
					$post_edit_link = '<a href="' . get_edit_post_link($row['post_id']) . '" target="_blank">' . __('Edit Post', 'powerpress') . '</a>';
?>
	<li>
<?php
					if( $FindReplace['step'] == 3 )
					{
						echo '<div>';
						powerpressadmin_find_replace_status($row['success']);
						echo ' &nbsp; ';
						if( !empty($row['error']) )
							echo $row['error'];
						echo '</div>';
					}
?>
		Post: <strong><?php echo $post_view_link; ?></strong>
		<span style="font-size: 90%;">(<?php echo $post_edit_link; ?>)</span>
		<dl>
			<dt>
			  <?php echo __('Found', 'powerpress') .': '. $row['find_readable']; ?>
			 </dt>
			 <dd>
			  <?php echo __('Replace', 'powerpress') .': '. $row['replace_readable']; ?>
			 (<a href="<?php echo esc_attr($row['new_url']); ?>" target="_blank"><?php echo __('test link', 'powerpress'); ?></a>)
			 </dd>
		</dl>
	</li>
<?php 
					}
?>
</ol>
<?php 	} ?>

<?php if( $FindReplace['step'] == 1 ) { ?>
<p class="submit">
	<input type="submit" name="Submit" id="powerpress_save_button" class="button-primary button-blubrry" value="<?php echo __('Find and Preview Changes', 'powerpress'); ?>" onclick="jQuery('#replace_step').val('2');" />
</p>
<?php } else if( $FindReplace['step'] == 2 && count($FindReplaceResults) > 0 ) { ?>
<p class="submit">
	<input type="submit" name="Submit" id="powerpress_save_button" class="button-primary button-blubrry" value="<?php echo __('Commit Changes', 'powerpress'); ?>" onclick="return ConfirmReplace()" />
	&nbsp;
	<input type="checkbox" name="FindReplace[verify]" value="1" <?php if( !empty($FindReplace['verify']) ) echo 'checked'; ?> onchange="return VerifyCheck(this)" />
	<strong><?php echo __('Verify URLs', 'powerpress'); ?></strong>
		(<?php echo __('Does not change URL if invalid', 'powerpress'); ?>)</p>
</p>
<?php } else if ( $FindReplace['step'] == 3 || ($FindReplace['step'] == 2 && count($FindReplaceResults) == 0) ) { ?>
<p class="submit">
	<strong><a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_tools.php'); ?>"><?php echo __('PowerPress Tools', 'powerpress'); ?></a></strong>
</p>
<?php 		} ?>

	<p style="margin-bottom: 40px; margin-top:0;"><?php echo sprintf( __('We recommend using the %s plugin to backup your database before using this Find and Replace tool.', 'powerpress'), '<a href="http://wordpress.org/extend/plugins/wp-db-backup/" target="_blank">'. __('WP-DB-Backup', 'powerpress') .'</a>' ); ?></p>
	<!-- start footer -->
<?php
	}
	
	function powerpressadmin_find_replace_status($success=true)
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
?>
	<img src="<?php echo admin_url(); ?>/images/<?php echo $img; ?>" style="vertical-align:text-top;" />
	<strong style="color:<?php echo $color; ?>;"><?php echo $text; ?></strong>
<?php
	}
	
	function powerpressadmin_find_replace_get_results()
	{
		global $g_FindReplaceResults;
		if( !is_array($g_FindReplaceResults) )
			return array();
		return $g_FindReplaceResults;
	}
?>