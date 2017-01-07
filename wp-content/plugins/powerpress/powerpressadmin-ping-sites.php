<?php
	// powerpressadmin-ping-sites.php
	
	function powerpressadmin_ping_sites_process()
	{
		$PingSites = false;
		if( isset($_POST['PingSite']) )
			$PingSites = $_POST['PingSite'];
		
		if( $PingSites && count($PingSites) > 0 )
		{
			$ping_site_data = get_option('ping_sites');
			
			while( list($null,$url) = each($PingSites) )
				$ping_site_data = trim($ping_site_data)."\r\n$url";
				
			update_option('ping_sites', $ping_site_data);
			powerpress_page_message_add_notice(  __('Update services added successfully.', 'powerpress') );
		}
		else
		{
			powerpress_page_message_add_notice(  __('No update services selected to add.', 'powerpress') );
		}
	}
	
	function powerpress_admin_ping_sites()
	{
		$ping_sites = get_option('ping_sites');
		$BlogSites = array('http://rpc.pingomatic.com/'=> __('Ping-o-Matic!', 'powerpress'),
			'http://blogsearch.google.com/ping/RPC2'=> __('Google Blog Search', 'powerpress'),
			'http://rssrpc.weblogs.com/RPC2'=> __('WebLogs', 'powerpress')  );
			
		$PodcastSites = array('http://audiorpc.weblogs.com/RPC2'=> __('WebLogs Audio', 'powerpress') );
?>


<input type="hidden" name="action" value="powerpress-ping-sites" />
<h2><?php echo __('Add Update services / Ping Sites', 'powerpress'); ?></h2>

<p style="margin-bottom: 0;"><?php echo __('Notify the following Update Services / Ping Sites when you create a new blog post / podcast episode.', 'powerpress'); ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Update Blog Searvices', 'powerpress'); ?></th> 
<td>
	<p><?php echo __('Select the blog service you would like to notify.', 'powerpress'); ?></p>
<?php
	while( list($url,$name) = each($BlogSites) )
	{
		if( stripos($ping_sites, $url) !== false )
		{
?>
	<p><input name="Ignore[]" type="checkbox" checked disabled value="1" /> <?php echo $name; ?></p>
<?php
		}
		else
		{
?>
	<p><input name="PingSite[]" type="checkbox" value="<?php echo esc_attr($url); ?>" /> <?php echo $name; ?></p>
<?php
		}
	}
?>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php echo __('Update Podcast Searvices', 'powerpress'); ?></th> 
<td>
	<p><?php echo __('Select the podcasting service you would like to notify.', 'powerpress'); ?></p>
<?php
	while( list($url,$name) = each($PodcastSites) )
	{
		if( stripos($ping_sites, $url) !== false )
		{
?>
	<p><input name="Ignore[]" type="checkbox" checked disabled value="1" /> <?php echo $name; ?></p>
<?php
		}
		else
		{
?>
	<p><input name="PingSite[]" type="checkbox" value="<?php echo esc_attr($url); ?>" /> <?php echo $name; ?></p>
<?php
		}
	}
?>
</td>
</tr>

</table>
<p>
	<?php echo __('You can manually add ping services by going to the to the "Update Services" section found in the <b>WordPress Settings</b> &gt; <b>Writing</b> page.', 'powerpress'); ?>
</p>
<p class="submit">
	<input type="submit" name="Submit" id="powerpress_save_button" class="button-primary button-blubrry" value="<?php echo __('Add Selected Update Services', 'powerpress'); ?>" />
</p>

	<!-- start footer -->
<?php
	}

?>