<?php
// powerpressadmin-metabox.php

if( !empty($Powerpress) && !empty($Powerpress['metamarks']) )
	require_once(POWERPRESS_ABSPATH .'/powerpressadmin-metamarks.php');

function powerpress_meta_box($object, $box)
{
	$FeedSlug = esc_attr( str_replace('powerpress-', '', $box['id']) );
	
	$DurationHH = '';
	$DurationMM = '';
	$DurationSS = '';
	$EnclosureURL = '';
	$EnclosureLength = '';
	$Embed = '';
	$CoverImage = '';
	$iTunesDuration = false;
	$iTunesKeywords = '';
	$iTunesSubtitle = '';
	$iTunesSummary = '';
	$GooglePlayDesc = '';
	$GooglePlayExplicit = '';
	$GooglePlayBlock = '';
	$iTunesAuthor = '';
	$iTunesExplicit = '';
	$iTunesCC = false;
	$iTunesOrder = false;
	$FeedAlways = false;
	$iTunesBlock = false;
	$NoPlayer = false;
	$NoLinks = false;
	$IsHD = false;
	$IsVideo = false;
	$Width = false;
	$Height = false;
	$WebMSrc = false;
	$FeedTitle = '';
	$PodcastCategory = '';
	$GeneralSettings = get_option('powerpress_general');
	if( !isset($GeneralSettings['set_size']) )
		$GeneralSettings['set_size'] = 0;
	if( !isset($GeneralSettings['set_duration']) )
		$GeneralSettings['set_duration'] = 0;
	if( !isset($GeneralSettings['episode_box_embed']) )
		$GeneralSettings['episode_box_embed'] = 0;
	if( !empty($GeneralSettings['blubrry_hosting']) && $GeneralSettings['blubrry_hosting'] === 'false' )
		$GeneralSettings['blubrry_hosting'] = false;
	$ExtraData = array();
	
	if( $object->ID )
	{
		
		if( $FeedSlug == 'podcast' )
			$enclosureArray = get_post_meta($object->ID, 'enclosure', true);
		else
			$enclosureArray = get_post_meta($object->ID, '_'.$FeedSlug.':enclosure', true);
		
		$EnclosureURL = '';
		$EnclosureLength = '';
		$EnclosureType = '';
		$EnclosureSerialized = false;
		if( $enclosureArray )
		{
			// list($EnclosureURL, $EnclosureLength, $EnclosureType, $EnclosureSerialized) =  explode("\n", $enclosureArray, 4);
			$MetaParts = explode("\n", $enclosureArray, 4);
			if( count($MetaParts) > 0 )
				$EnclosureURL = $MetaParts[0];
			if( count($MetaParts) > 1 )
				$EnclosureLength = $MetaParts[1];
			if( count($MetaParts) > 2 )
				$EnclosureType = $MetaParts[2];
			if( count($MetaParts) > 3 )
				$EnclosureSerialized = $MetaParts[3];
		}
		$EnclosureURL = trim($EnclosureURL);
		$EnclosureLength = trim($EnclosureLength);
		$EnclosureType = trim($EnclosureType);
		
		if( $EnclosureSerialized )
		{
			$ExtraData = @unserialize($EnclosureSerialized);
			if( $ExtraData )
			{
				if( isset($ExtraData['duration']) )
					$iTunesDuration = $ExtraData['duration'];
				else if( isset($ExtraData['length']) ) // Podcasting plugin support
					$iTunesDuration = $ExtraData['length'];
				if( isset($ExtraData['embed']) )
					$Embed = $ExtraData['embed'];
				if( isset($ExtraData['keywords']) )
					$iTunesKeywords = $ExtraData['keywords'];
				if( isset($ExtraData['subtitle']) )
					$iTunesSubtitle = $ExtraData['subtitle'];
				if( isset($ExtraData['summary']) )
					$iTunesSummary = $ExtraData['summary'];
				if( isset($ExtraData['gp_desc']) )
					$GooglePlayDesc = $ExtraData['gp_desc'];
				if( isset($ExtraData['gp_explicit']) )
					$GooglePlayExplicit = $ExtraData['gp_explicit'];	
				if( isset($ExtraData['gp_block']) )
					$GooglePlayBlock = $ExtraData['gp_block'];
				if( isset($ExtraData['author']) )
					$iTunesAuthor = $ExtraData['author'];
				if( isset($ExtraData['no_player']) )
					$NoPlayer = $ExtraData['no_player'];
				if( isset($ExtraData['no_links']) )
					$NoLinks = $ExtraData['no_links'];	
				if( isset($ExtraData['explicit']) )	
					$iTunesExplicit = $ExtraData['explicit'];
				if( isset($ExtraData['cc']) )		
					$iTunesCC = $ExtraData['cc'];
				if( isset($ExtraData['order']) )		
					$iTunesOrder = $ExtraData['order'];
				if( isset($ExtraData['always']) )		
					$FeedAlways = $ExtraData['always'];
				if( isset($ExtraData['block']) )		
					$iTunesBlock = $ExtraData['block'];	
				if( isset($ExtraData['image']) )	
					$CoverImage = $ExtraData['image'];
				if( isset($ExtraData['ishd']) )	
					$IsHD = $ExtraData['ishd'];
				if( isset($ExtraData['height']) )	
					$Height = $ExtraData['height'];
				if( isset($ExtraData['width']) )	
					$Width = $ExtraData['width'];
				if( isset($ExtraData['webm_src']) )	
					$WebMSrc = $ExtraData['webm_src'];
				if( isset($ExtraData['feed_title']) )	
					$FeedTitle = $ExtraData['feed_title'];
			}
		}
		
		if( defined('POWERPRESS_AUTO_DETECT_ONCE') && POWERPRESS_AUTO_DETECT_ONCE != false )
		{
			if( $EnclosureLength )
				$GeneralSettings['set_size'] = 1; // specify
			if( $iTunesDuration )
				$GeneralSettings['set_duration'] = 1; // specify
		}
		
		if( $FeedSlug == 'podcast' && !$iTunesDuration ) // Get the iTunes duration the old way (very old way)
			$iTunesDuration = get_post_meta($object->ID, 'itunes:duration', true);
			
		if( $iTunesDuration )
		{
			$iTunesDuration = powerpress_readable_duration($iTunesDuration, true);
			list($DurationHH, $DurationMM, $DurationSS) = explode(':', $iTunesDuration);
			if( ltrim($DurationHH, '0') == 0 )
				$DurationHH = '';
			if( $DurationHH == '' && ltrim($DurationMM, '0') == 0 )
				$DurationMM = '';
			if( $DurationHH == '' && $DurationMM == '' && ltrim($DurationSS, '0') == 0 )
				$DurationSS = '';
		}
		
		// Check for HD Video formats
		if( preg_match('/\.(mp4|m4v|webm|ogg|ogv)$/i', $EnclosureURL ) )
		{
			$IsVideo = true;
		}
	}
	
	if( $EnclosureURL )
	{
?>
<div>
	<input type="checkbox" name="Powerpress[<?php echo $FeedSlug; ?>][change_podcast]" id="powerpress_change" value="1"  onchange="javascript:document.getElementById('powerpress_podcast_box_<?php echo $FeedSlug; ?>').style.display=(this.checked?'block':'none');" />
	<?php echo __('Modify existing podcast episode', 'powerpress'); ?>
</div>
<?php 
	}
	else
	{
		echo '<input type="hidden" name="Powerpress['. $FeedSlug .'][new_podcast]" value="1" />'.PHP_EOL;
	}
?>

<div class="powerpress_podcast_box" id="powerpress_podcast_box_<?php echo $FeedSlug; ?>"<?php if( $EnclosureURL ) echo ' style="display:none;"'; ?>>
<?php
	if( $EnclosureURL )
	{
?>
	<div class="powerpress_row">
		<label><?php echo __('Remove', 'powerpress'); ?></label>
		<div class="powerpress_row_content">
			<input type="checkbox" name="Powerpress[<?php echo $FeedSlug; ?>][remove_podcast]" id="powerpress_remove" value="1"  onchange="javascript:document.getElementById('powerpress_podcast_edit_<?php echo $FeedSlug; ?>').style.display=(this.checked?'none':'block');" />
			<?php echo __('Podcast episode will be removed from this post upon save', 'powerpress'); ?>
		</div>
	</div>
<?php
	}
?>
	<div id="powerpress_podcast_edit_<?php echo $FeedSlug; ?>">
		<div class="warning error below-h2" id="powerpress_warning_<?php echo $FeedSlug; ?>" style="display:none;"></div>
		<div class="success below-h2" id="powerpress_success_<?php echo $FeedSlug; ?>" style="display:none;"></div>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][url]"><?php echo __('Media URL', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_url_<?php echo $FeedSlug; ?>" class="powerpress-url" name="Powerpress[<?php echo $FeedSlug; ?>][url]" value="<?php echo esc_attr($EnclosureURL); ?>" <?php echo ( !empty($ExtraData['hosting']) ?'readOnly':''); ?> style="width: 70%;" />
				<?php if( true ) { // NOW ALWAYS SHOW FOLDER, PREVIOUS: if( !empty($GeneralSettings['blubrry_hosting']) && $GeneralSettings['blubrry_hosting']!=='false'  && !empty($GeneralSettings['timestamp']) && $GeneralSettings['timestamp'] <	1414627200 ) { // display the folder icon for folks before october 30, 2014 ?>
					<a title="<?php echo esc_attr(__('Blubrry Podcast Hosting', 'powerpress')); ?>" href="<?php echo admin_url('admin.php'); ?>?action=powerpress-jquery-media&podcast-feed=<?php echo $FeedSlug; ?>&KeepThis=true&TB_iframe=true&modal=false" title="<?php echo __('Browse Media File', 'powerpress'); ?>" class="thickbox"><img src="<?php echo powerpress_get_root_url(); ?>/images/blubrry_folder.png" alt="<?php echo __('Browse Media Files', 'powerpress'); ?>" /></a>
				<?php } ?>
				<input type="button" id="powerpress_check_<?php echo $FeedSlug; ?>_button" name="powerpress_check_<?php echo $FeedSlug; ?>_button" value="<?php echo __('Verify URL', 'powerpress'); ?>" onclick="powerpress_get_media_info('<?php echo $FeedSlug; ?>');" alt="<?php echo __('Verify Media', 'powerpress'); ?>" class="button" />
				<img id="powerpress_check_<?php echo $FeedSlug; ?>" src="<?php echo admin_url(); ?>images/loading.gif" style="vertical-align:text-top; display: none;" alt="<?php echo __('Checking Media', 'powerpress'); ?>" />
				
				<input type="hidden" id="powerpress_hosting_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][hosting]" value="<?php echo ( !empty($ExtraData['hosting'])?'1':'0'); ?>" />
				 <div id="powerpress_hosting_note_<?php echo $FeedSlug; ?>" style="margin-left: 2px; padding-bottom: 2px; padding-top: 2px; display: <?php echo ( !empty($ExtraData['hosting'])?'block':'none'); ?>"><em><?php echo __('Media file hosted by blubrry.com.', 'powerpress'); ?> 
					(<a href="#" title="<?php echo __('Remove Blubrry.com hosted media file', 'powerpress'); ?>" onclick="powerpress_remove_hosting('<?php echo $FeedSlug; ?>');return false;"><?php echo __('remove', 'powerpress'); ?></a>)
				</em></div>
				
				<div class="powerpress-hosting-buttons">
					<a class="powerpress-hosting-button powerpress-button thickbox" href="<?php echo admin_url('admin.php'); ?>?action=powerpress-jquery-media&podcast-feed=<?php echo $FeedSlug; ?>&KeepThis=true&TB_iframe=true&modal=false" title="<?php echo esc_attr(__('Blubrry Podcast Hosting', 'powerpress')); ?>" class="thickbox">
					<img src="<?php echo powerpress_get_root_url(); ?>images/button_icon_blubrry.png" class="powerpress-button-icon" alt="" />
					<?php echo __('Link to Media hosted on Blubrry.com', 'powerpress'); ?></a> 
					<!--  <a href="<?php echo admin_url('admin.php'); ?>?action=powerpress-jquery-media&podcast-feed=<?php echo $FeedSlug; ?>&KeepThis=true&TB_iframe=true&modal=false" title="<?php echo __('Upload Media File to your Blubrry.com account', 'powerpress'); ?>" class="thickbox"><?php echo __('Upload Media File', 'powerpress'); ?></a> -->
					<?php if( empty($GeneralSettings['blubrry_hosting']) || $GeneralSettings['blubrry_hosting']==='false' ) { ?>
						&nbsp; <?php echo __('Don\'t have Blubrry Podcast Media Hosting?', 'powerpress'); ?>	<a href="http://create.blubrry.com/resources/podcast-media-hosting/" target="_blank"><?php echo __('Learn More', 'powerpress'); ?></a>
					<?php } ?>

				</div>
				
				<div style="padding-bottom: 2px; padding-top: 2px;">
					<span id="powerpress_ishd_<?php echo $FeedSlug; ?>_span" style="margin-left: 20px; display: <?php echo ($IsVideo?'inline':'none'); ?>; "><input id="powerpress_ishd_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][ishd]" value="1" type="checkbox" <?php echo ($IsHD==1?'checked':''); ?> /> <?php echo __('Video is HD (720p/1080i/1080p)', 'powerpress'); ?></span>
<?php

	if( !empty($GeneralSettings['episode_box_no_player']) || !empty($GeneralSettings['episode_box_no_links']) || !empty($GeneralSettings['episode_box_no_player_and_links']) )
	{
		if( !empty($GeneralSettings['episode_box_no_player_and_links']) )
		{
		?>
		<span style="margin-left: 20px;"><input id="powerpress_no_player_and_links_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][no_player_and_links]" value="1" type="checkbox" <?php echo ($NoPlayer==1&&$NoLinks==1?'checked':''); ?> /> <?php echo __('Do not display player and media links', 'powerpress'); ?></span>
		<?php
		}
		if( !empty($GeneralSettings['episode_box_no_player'])  )
		{
		?>
		<span style="margin-left: 20px;"><input id="powerpress_no_player_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][no_player]" value="1" type="checkbox" <?php echo ($NoPlayer==1?'checked':''); ?> /> <?php echo __('Do not display player', 'powerpress'); ?></span>
		<?php
		}
		if( !empty($GeneralSettings['episode_box_no_links'])  )
		{
		?>
		<span style="margin-left: 20px;"><input id="powerpress_no_links_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][no_links]" value="1" type="checkbox" <?php echo ($NoLinks==1?'checked':''); ?> /> <?php echo __('Do not display media links', 'powerpress'); ?></span>
		<?php
		}
	}
?>
				</div>
			</div><!-- end powerpress_row_content -->
		</div><!-- end powerpress_row -->
		<div class="powerpress_row" id="powerpress_webm_<?php echo $FeedSlug; ?>" style="display: <?php echo ($WebMSrc != '' || (preg_match('/\.(mp4|m4v)$/i', $EnclosureURL) ) ?'block':'none'); ?>;">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][webm_src]"><?php echo __('Alt WebM URL', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_webm_src_<?php echo $FeedSlug; ?>" class="powerpress-webm-src" name="Powerpress[<?php echo $FeedSlug; ?>][webm_src]" value="<?php echo esc_attr($WebMSrc); ?>" style="width: 70%; " />
			</div>
			<div class="powerpress_row_content">
				<em><?php echo __('For HTML5 Video fallback, enter an alternative WebM media URL above. (optional)', 'powerpress'); ?></em>
			</div>
		</div>
<?php 
	if( !empty($GeneralSettings['seo_feed_title']) )
	{
?>	
		<div class="powerpress_row">
			<label for="powerpress_feed_title_<?php echo $FeedSlug; ?>"><?php echo __('Episode Title', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_feed_title_<?php echo $FeedSlug; ?>" class="powerpress-feed_title" name="Powerpress[<?php echo $FeedSlug; ?>][feed_title]" value="<?php echo esc_attr($FeedTitle); ?>" placeholder="<?php echo __('Custom episode title for feed', 'powerpress'); ?>" style="width: 70%; " />
			</div>
			<?php if( !empty($GeneralSettings['seo_itunes']) ) { ?>
			<div class="powerpress_row_content">
				<em><?php echo __('Podcasting SEO Suggestion: Use the blog post title for search engine optimization and use this title for iTunes search.', 'powerpress'); ?></em>
			</div>
			<?php } ?>
		</div>
<?php
	}
	
	if( empty($GeneralSettings['episode_box_mode']) || $GeneralSettings['episode_box_mode'] != 1 ) // If not simple mode
	{
?>
		<div class="powerpress_row">
			<label><?php echo __('File Size', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<div style="margin-bottom: 4px;">
					<input id="powerpress_set_size_0_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][set_size]" value="0" type="radio" <?php echo ($GeneralSettings['set_size']==0?'checked':''); ?> /> 
					<?php echo __('Auto detect file size', 'powerpress'); ?>
				</div>
				<div>
					<input id="powerpress_set_size_1_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][set_size]" value="1" type="radio" <?php echo ($GeneralSettings['set_size']==1?'checked':''); ?> />
					<?php echo __('Specify', 'powerpress').': '; ?>
					<input type="text" id="powerpress_size_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][size]" value="<?php echo esc_attr($EnclosureLength); ?>" style="width: 110px;" onchange="javascript:jQuery('#powerpress_set_size_1_<?php echo $FeedSlug; ?>').attr('checked', true);"  />
					<?php echo __('in bytes', 'powerpress'); ?>
				</div>
			</div>
		</div>
		<div class="powerpress_row">
			<label><?php echo __('Duration', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<div style="margin-bottom: 4px;">
					<input id="powerpress_set_duration_0_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][set_duration]" value="0" type="radio" <?php echo ($GeneralSettings['set_duration']==0?'checked':''); ?> />
					<?php echo __('Auto detect duration (mp3\'s only)', 'powerpress'); ?>
				</div>
				<div style="margin-bottom: 4px;">
					<input id="powerpress_set_duration_1_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][set_duration]" value="1" type="radio" <?php echo ($GeneralSettings['set_duration']==1?'checked':''); ?> />
					<?php echo __('Specify', 'powerpress').': '; ?>
					<input type="text" id="powerpress_duration_hh_<?php echo $FeedSlug; ?>" class="powerpress-duration-hh" placeholder="HH" name="Powerpress[<?php echo $FeedSlug; ?>][duration_hh]" maxlength="2" value="<?php echo esc_attr($DurationHH); ?>" style="width: 36px; text-align: right;" onchange="javascript:jQuery('#powerpress_set_duration_1_<?php echo $FeedSlug; ?>').attr('checked', true);" /><strong>:</strong> 
					<input type="text" id="powerpress_duration_mm_<?php echo $FeedSlug; ?>" class="powerpress-duration-mm" placeholder="MM" name="Powerpress[<?php echo $FeedSlug; ?>][duration_mm]" maxlength="2" value="<?php echo esc_attr($DurationMM); ?>" style="width: 36px; text-align: right;" onchange="javascript:jQuery('#powerpress_set_duration_1_<?php echo $FeedSlug; ?>').attr('checked', true);" /><strong>:</strong> 
					<input type="text" id="powerpress_duration_ss_<?php echo $FeedSlug; ?>" class="powerpress-duration-ss" placeholder="SS" name="Powerpress[<?php echo $FeedSlug; ?>][duration_ss]" maxlength="10" value="<?php echo esc_attr($DurationSS); ?>" style="width: 36px; text-align: right;" onchange="javascript:jQuery('#powerpress_set_duration_1_<?php echo $FeedSlug; ?>').attr('checked', true);" />
				</div>
				<div>
					<input id="powerpress_set_duration_2_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][set_duration]" value="-1" type="radio" <?php echo ($GeneralSettings['set_duration']==-1?'checked':''); ?> />
					<?php echo __('Not specified', 'powerpress'); ?>
				</div>
			</div>
		</div>
<?php
	}
	else
	{
?>
<input id="powerpress_set_size_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][set_size]" value="0" type="hidden" />
<input id="powerpress_set_duration_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][set_duration]" value="0" type="hidden" />
<?php
	}

		// Video Coverart Image (Poster)
		if( !empty($GeneralSettings['episode_box_cover_image']) || $CoverImage )
		{
			$form_action_url = admin_url("media-upload.php?type=powerpress_image&tab=type&post_id={$object->ID}&powerpress_feed={$FeedSlug}&TB_iframe=true&width=450&height=200");
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][image]"><?php echo __('Poster Image', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_image_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][image]" value="<?php echo esc_attr($CoverImage); ?>" placeholder="<?php echo htmlspecialchars(__('e.g. http://example.com/path/to/image.jpg', 'powerpress')); ?>" style="width: 70%; font-size: 90%;" size="250" />
				<a href="<?php echo $form_action_url; ?>" class="thickbox powerpress-image-browser" id="powerpress_image_browser_<?php echo $FeedSlug; ?>" title="<?php echo __('Select Poster Image', 'powerpress'); ?>"><img src="images/media-button-image.gif" /></a>
			</div>
			<div class="powerpress_row_content">
				<em><?php echo __('Poster image for video (m4v, mp4, ogv, webm, etc..)', 'powerpress'); ?></em>
			</div>
		</div>
<?php
		}

		// Player width/height
		if( !empty($GeneralSettings['episode_box_player_size']) || $Width || $Height )
		{
?>
		<div class="powerpress_row">
			<label><?php echo __('Player Size', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_player_width_<?php echo $FeedSlug; ?>" class="powerpress-player-width" placeholder="<?php echo htmlspecialchars(__('Width', 'powerpress')); ?>" name="Powerpress[<?php echo $FeedSlug; ?>][width]" value="<?php echo esc_attr($Width); ?>" style="width: 50px; font-size: 90%;" size="5" />
				x
				<input type="text" id="powerpress_player_height_<?php echo $FeedSlug; ?>" class="powerpress-player-height" placeholder="<?php echo htmlspecialchars(__('Height', 'powerpress')); ?>" name="Powerpress[<?php echo $FeedSlug; ?>][height]" value="<?php echo esc_attr($Height); ?>" style="width: 50px; font-size: 90%;" size="5" />
			</div>
		</div>
<?php
		}
		
		// Embed option, enter your own embed code provided by sites such as YouTube
		if( !empty($GeneralSettings['episode_box_embed']) || $Embed )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][embed]"><?php echo __('Media Embed', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<textarea class="powerpress-embed" id="powerpress_embed_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][embed]" style="width: 90%; height: 80px; font-size: 90%;" onfocus="this.select();"><?php echo esc_textarea($Embed); ?></textarea>
			</div>
		</div>
<?php
		}
		
		if( $iTunesKeywords )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][keywords]"><?php echo __('iTunes Keywords', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_keywords_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][keywords]" value="<?php echo esc_attr($iTunesKeywords); ?>" style="width: 90%; font-size: 90%;" size="250" />
			</div>
			<div class="powerpress_row_content">
				<em><?php echo __('Feature Deprecated by Apple. Keywords above are for your reference only.', 'powerpress'); ?></em>
			</div>
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_subtitle']) || !empty($GeneralSettings['seo_itunes']) || $iTunesSubtitle )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][subtitle]"><?php echo __('iTunes Subtitle', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_subtitle_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][subtitle]" value="<?php echo esc_attr($iTunesSubtitle); ?>" style="width: 90%; font-size: 90%;" size="250" />
			</div>
			<div class="powerpress_row_content">
				<em><?php echo __('Your subtitle may not contain HTML and cannot exceed 250 characters in length. Leave blank to use the first 250 characters of your excerpt, or blog post if no excerpt is set.', 'powerpress'); ?></em>
			</div>
			<?php if( !empty($GeneralSettings['seo_itunes']) ) { ?>
			<div class="powerpress_row_content">
				<em><?php echo __('Podcasting SEO Suggestion: Write something concise and compelling that includes keywords not mentioned in the episode title.', 'powerpress'); ?></em>
			</div>
			<?php } ?>
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_summary']) || $iTunesSummary )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][summary]"><?php echo __('iTunes Summary', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<textarea id="powerpress_summary_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][summary]" style="width: 90%; height: 80px; font-size: 90%;"><?php echo esc_textarea($iTunesSummary); ?></textarea>
			</div>	
			<div class="powerpress_row_content">
				<em><?php echo __('Your summary cannot exceed 4,000 characters in length and should not include HTML, except for hyperlinks. Leave blank to use your blog post.', 'powerpress'); ?></em>
			</div>
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_gp_desc']) || $GooglePlayDesc )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][gp_desc]"><?php echo __('Google Play Description', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<textarea id="powerpress_gp_desc_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][gp_desc]" style="width: 90%; height: 80px; font-size: 90%;"><?php echo esc_textarea($GooglePlayDesc); ?></textarea>
			</div>	
			<div class="powerpress_row_content">
				<em><?php echo __('Your summary cannot exceed 4,000 characters in length. Leave blank to use your blog post.', 'powerpress'); ?></em>
			</div>
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_author']) || !empty($GeneralSettings['seo_itunes']) || $iTunesAuthor )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][author]"><?php echo __('iTunes Author', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_author_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][author]" value="<?php echo esc_attr($iTunesAuthor); ?>" style="width: 60%; font-size: 90%;" size="250" />
			</div>
			<div class="powerpress_row_content">
				<em><?php echo __('Leave blank to use post author name.', 'powerpress'); ?></em>
			</div>
			<?php if( !empty($GeneralSettings['seo_itunes']) ) { ?>
			<div class="powerpress_row_content">
				<em><?php echo __('Podcasting SEO Suggestion: Include talent names and nicknames not mentioned in the episode title.', 'powerpress'); ?></em>
			</div><?php } ?>
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_explicit']) || $iTunesExplicit )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][explicit]"><?php echo __('iTunes Explicit', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<select id="powerpress_explicit_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][explicit]" style="width: 220px;">
<?php
$explicit_array = array(''=>__('Use feed\'s explicit setting', 'powerpress'), 0=>__('no - display nothing', 'powerpress'), 1=>__('yes - explicit content', 'powerpress'), 2=>__('clean - no explicit content', 'powerpress') );

while( list($value,$desc) = each($explicit_array) )
	echo "\t<option value=\"$value\"". ($iTunesExplicit==$value?' selected':''). ">$desc</option>\n";

?>
					</select>
			</div>	
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_gp_explicit']) || $GooglePlayExplicit )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][gp_explicit]"><?php echo __('Google Play Explicit', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<select id="powerpress_explicit_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][gp_explicit]" style="width: 220px;">
<?php
$explicit_array = array(''=>__('Use feed\'s explicit setting', 'powerpress'), 0=>__('no - display nothing', 'powerpress'), 1=>__('yes - explicit content', 'powerpress') );

while( list($value,$desc) = each($explicit_array) )
	echo "\t<option value=\"$value\"". ($GooglePlayExplicit==$value?' selected':''). ">$desc</option>\n";

?>
					</select>
			</div>	
		</div>
<?php
		}
		
		
		if( !empty($GeneralSettings['episode_box_closed_captioned']) || $iTunesCC )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][cc]"><?php echo __('iTunes CC', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<select id="powerpress_cc_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][cc]" style="width: 220px;">
<?php
$cc_array = array(''=>__('No Closed Captioning', 'powerpress'), 1=>__('Yes, Closed Captioned media', 'powerpress') );

while( list($value,$desc) = each($cc_array) )
	echo "\t<option value=\"$value\"". ($iTunesCC==$value?' selected':''). ">$desc</option>\n";
unset($cc_array);
?>
					</select>
			</div>	
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_order']) || $iTunesOrder )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][order]"><?php echo __('iTunes Order', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_order_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][order]" value="<?php echo esc_attr($iTunesOrder); ?>" style="width: 60px; font-size: 90%;" size="250" />
			</div>	
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_feature_in_itunes']) )
		{
			$iTunesFeatured = get_option('powerpress_itunes_featured');
			$FeaturedChecked = false;
			if( !empty($object->ID) && !empty($iTunesFeatured[ $FeedSlug ]) && $iTunesFeatured[ $FeedSlug ] == $object->ID ) {
				$FeaturedChecked = true; }
?>
		<div class="powerpress_row">
			<label for="PowerpressFeature[<?php echo $FeedSlug; ?>]"><?php echo __('Feature Episode', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
			<?php if( $FeaturedChecked ) { ?>
				<input type="hidden" name="PowerpressFeature[<?php echo $FeedSlug; ?>]" value="0" /><?php } ?>
				<input type="checkbox" id="powerpress_feature_<?php echo $FeedSlug; ?>" name="PowerpressFeature[<?php echo $FeedSlug; ?>]" value="1" <?php echo ($FeaturedChecked?'checked':''); ?> />
				<?php echo __('Episode will appear at the top of your episode list in the iTunes directory.', 'powerpress'); ?>
			</div>	
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_block']) || $iTunesBlock )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][block]"><?php echo __('iTunes Block', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<select id="powerpress_block_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][block]" style="width: 220px;">
<?php
$block_array = array(''=>__('No', 'powerpress'), 1=>__('Yes, Block episode from iTunes', 'powerpress') );

while( list($value,$desc) = each($block_array) )
	echo "\t<option value=\"$value\"". ($iTunesBlock==$value?' selected':''). ">$desc</option>\n";
unset($block_array);
?>
					</select>
			</div>	
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_gp_block']) || $GooglePlayBlock )
		{
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][gp_block]"><?php echo __('Google Play Block', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<select id="powerpress_block_<?php echo $FeedSlug; ?>" name="Powerpress[<?php echo $FeedSlug; ?>][gp_block]" style="width: 220px;">
<?php
$block_array = array(''=>__('No', 'powerpress'), 1=>__('Yes, Block episode from Google Play Music', 'powerpress') );

while( list($value,$desc) = each($block_array) )
	echo "\t<option value=\"$value\"". ($GooglePlayBlock==$value?' selected':''). ">$desc</option>\n";
unset($block_array);
?>
					</select>
			</div>	
		</div>
<?php
		}
		
		if( !empty($GeneralSettings['episode_box_itunes_image']) || !empty($ExtraData['itunes_image']) )
		{
			if( empty($ExtraData['itunes_image']) )
				$ExtraData['itunes_image'] = '';
			
			$form_action_url = admin_url("media-upload.php?type=powerpress_image&tab=type&post_id={$object->ID}&powerpress_feed={$FeedSlug}&TB_iframe=true&width=450&height=200");
?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][itunes_image]"><?php echo __('iTunes Image', 'powerpress'); ?></label>
			<div class="powerpress_row_content">
				<input type="text" id="powerpress_itunes_image_<?php echo $FeedSlug; ?>" placeholder="<?php echo htmlspecialchars(__('e.g. http://example.com/path/to/image.jpg', 'powerpress')); ?>" name="Powerpress[<?php echo $FeedSlug; ?>][itunes_image]" value="<?php echo esc_attr($ExtraData['itunes_image']); ?>" style="width: 70%; font-size: 90%;" size="250" />
				<a href="<?php echo $form_action_url; ?>" class="thickbox powerpress-itunes-image-browser" id="powerpress_itunes_image_browser_<?php echo $FeedSlug; ?>" title="<?php echo __('Select iTunes Image', 'powerpress'); ?>"><img src="images/media-button-image.gif" /></a>
			</div>
		</div>
<?php
		}

		if( !empty($GeneralSettings['cat_casting_strict']) && !empty($GeneralSettings['custom_cat_feeds']) )
		{
			// Get Podcast Categories...
			$cur_cat_id = intval(!empty($ExtraData['category'])?$ExtraData['category']:0);
			if( count($GeneralSettings['custom_cat_feeds']) == 1 ) // Lets auto select the category
			{
				
				list($null, $cur_cat_id) = each($GeneralSettings['custom_cat_feeds']);
				reset($GeneralSettings['custom_cat_feeds']);
			}
			
		?>
		<div class="powerpress_row">
			<label for="Powerpress[<?php echo $FeedSlug; ?>][category]"><?php echo __('Category', 'powerpress'); ?></label>
			<div class="powerpress_row_content"><?php
				echo '<select id="powerpress_category_'. $FeedSlug . '" name="Powerpress['. $FeedSlug .'][category]" style="width: 70%;">';
				echo '<option value="0"';
				echo '>' . esc_html( __('Select category', 'powerpress') ) . '</option>' . "\n";
				
				while( list($null, $cat_id) = each($GeneralSettings['custom_cat_feeds']) ) {
					$catObj = get_category( $cat_id );
					if( empty($catObj->name ) )
						continue; // Do not allow empty categories forward
					
					$label = $catObj->name; // TODO: Get the category title
					echo '<option value="' . esc_attr( $cat_id ) . '"';
					if ( $cat_id == $cur_cat_id )
						echo ' selected="selected"';
					echo '>' . esc_html( $label ) . '</option>' . "\n";
				}
			echo '</select>';
			?>
				</div>
		</div>
		<?php
		}
		
		// Added filter for other plugins to add fields on a per podcast feed slug basis
		echo apply_filters('powerpress_metabox', '', $object, $FeedSlug);
?>
	</div><!-- end powerpress_podcast_edit_<?php echo $FeedSlug; ?> -->
</div><!-- end powerpress_podcast_box -->
<?php if( !empty($GeneralSettings['episode_box_background_color'][$FeedSlug]) ) { ?>
<script type="text/javascript"><!--
jQuery(document).ready(function($) {
	jQuery('#powerpress-<?php echo $FeedSlug; ?>').css( {'background-color' : '<?php echo $GeneralSettings['episode_box_background_color'][$FeedSlug]; ?>' });
	jQuery('#powerpress-<?php echo $FeedSlug; ?>').css( {'background-image' : '-moz-linear-gradient(center top , <?php echo $GeneralSettings['episode_box_background_color'][$FeedSlug]; ?>, <?php echo $GeneralSettings['episode_box_background_color'][$FeedSlug]; ?>)' });
});
//-->
</script><?php } ?>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function media_upload_powerpress_image() {
	$errors = array();
	$id = 0;

	if ( isset($_POST['html-upload']) && !empty($_FILES) ) {
		// Upload File button was clicked
		$post_id = intval( $_REQUEST['post_id'] ); // precautionary, make sure we're always working with an integer
		$id = media_handle_upload('async-upload', $post_id);
		unset($_FILES);
		if ( is_wp_error($id) ) {
			$errors['upload_error'] = $id;
			$id = false;
		}
	}

	return wp_iframe( 'powerpress_media_upload_type_form', 'powerpress_image', $errors, $id );
}

add_action('media_upload_powerpress_image', 'media_upload_powerpress_image');

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $html
 */
function powerpress_send_to_episode_entry_box($url) {
?>
<script type="text/javascript">
/* <![CDATA[ */
var win = window.dialogArguments || opener || parent || top;
if( win.powerpress_send_to_poster_image )
	win.powerpress_send_to_poster_image('<?php echo addslashes($url); ?>');
/* ]]> */
</script>
<?php
	exit;
}


/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $tabs
 * @return unknown
 */
function powerpress_update_media_upload_tabs($tabs) {
	
	if( !empty($_GET['type'] ) )
	{
		if( $_GET['type'] == 'powerpress_image' ) // We only want to allow uploads
		{
			unset($tabs['type_url']);
			unset($tabs['gallery']);
			unset($tabs['library']);
		}
	}
	return $tabs;
}
add_filter('media_upload_tabs', 'powerpress_update_media_upload_tabs', 100);

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $type
 * @param unknown_type $errors
 * @param unknown_type $id
 */
function powerpress_media_upload_type_form($type = 'file', $errors = null, $id = null)
{
	media_upload_header();

	$post_id = isset( $_REQUEST['post_id'] )? intval( $_REQUEST['post_id'] ) : 0;

	$form_action_url = admin_url("media-upload.php?type=$type&tab=type&post_id=$post_id");
	$form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);
	
	if ( $id && !is_wp_error($id) ) {
		$image_url = wp_get_attachment_url($id);
		powerpress_send_to_episode_entry_box( $image_url );
	}

?>

<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="media-upload-form type-form validate" id="<?php echo $type; ?>-form">
<input type="submit" class="hidden" name="save" value="" />
<input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
<?php wp_nonce_field('media-form'); ?>

<h3 class="media-title"><?php echo __('Select poster image from your computer.', 'powerpress'); ?></h3>

<?php media_upload_form( $errors ); ?>

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready( function() {
	jQuery('#sidemenu').css('display','none');
	jQuery('body').css('margin','0px 20px');
	jQuery('body').css('height','auto');
	jQuery('html').css('height','auto'); // Elimate the weird scroll bar
});
//]]>
</script>
<div id="media-items">
<?php
	if ( $id && is_wp_error($id) ) {
		echo '<div id="media-upload-error">'.esc_html($id->get_error_message()).'</div>';
	}
?>
</div>
</form>
<?php
}

function powerpress_media_upload_use_flash($flash) {
	if( !empty($_GET['type']) && $_GET['type'] == 'powerpress_image' )
	{
		return false;
	}
	return $flash;
}

add_filter('flash_uploader', 'powerpress_media_upload_use_flash');

?>