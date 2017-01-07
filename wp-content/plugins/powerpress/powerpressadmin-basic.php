<?php

function powerpress_admin_basic()
{
	$FeedAttribs = array('type'=>'general', 'feed_slug'=>'', 'category_id'=>0, 'term_taxonomy_id'=>0, 'term_id'=>0, 'taxonomy_type'=>'', 'post_type'=>'');
	// feed_slug = channel
	
	$General = powerpress_get_settings('powerpress_general');
	$General = powerpress_default_settings($General, 'basic');
	if( !isset($General['advanced_mode_2']) )
		$General['advanced_mode_2'] = true;
	
	$FeedSettings = powerpress_get_settings('powerpress_feed');
	$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed');
	
	$CustomFeed = get_option('powerpress_feed_'.'podcast'); // Get the custom podcast feed settings saved in the database
	if( $CustomFeed ) // If they enabled custom podast channels...
	{
		$FeedSettings = powerpress_merge_empty_feed_settings($CustomFeed, $FeedSettings);
		$FeedAttribs['channel_podcast'] = true;
	}
	
	$MultiSiteServiceSettings = false;
	if( is_multisite() )
	{
		$MultiSiteSettings = get_site_option('powerpress_multisite');
		if( !empty($MultiSiteSettings['services_multisite_only']) )
		{
			$MultiSiteServiceSettings = true;
		}
	}
	
?>
<script type="text/javascript"><!--
function CheckRedirect(obj)
{
	if( obj.value )
	{
		if( obj.value.indexOf('rawvoice') == -1 && obj.value.indexOf('techpodcasts') == -1 && 
			obj.value.indexOf('blubrry') == -1 && obj.value.indexOf('podtrac') == -1 )
		{
			if( !confirm('<?php echo __('The redirect entered is not recongized as a supported statistics redirect service.', 'powerpress'); ?>\n\n<?php echo __('Are you sure you wish to continue with this redirect url?', 'powerpress'); ?>') )
			{
				obj.value = '';
				return false;
			}
		}
	}
	return true;
}

function SelectEmbedField(checked)
{
	if( checked )
		jQuery('#embed_replace_player').removeAttr("disabled");
	else
		jQuery('#embed_replace_player').attr("disabled","disabled");
}

jQuery(document).ready(function($) {
	
	jQuery('#powerpress_advanced_mode_button').click( function(event) {
		event.preventDefault();
		jQuery('#powerpress_advanced_mode').val('0');
		jQuery(this).closest("form").submit();
	});
	
	jQuery('#episode_box_player_links_options').change(function () {
		
		var objectChecked = jQuery('#episode_box_player_links_options').attr('checked');
		if(typeof jQuery.prop === 'function') {
			objectChecked = jQuery('#episode_box_player_links_options').prop('checked');
		}
		
		if( objectChecked == true ) {
			jQuery('#episode_box_player_links_options_div').css("display", 'block' );
		}
		else {
			jQuery('#episode_box_player_links_options_div').css("display", 'none' );
			jQuery('.episode_box_no_player_or_links').attr("checked", false );
			jQuery('#episode_box_no_player_and_links').attr("checked", false );
			if(typeof jQuery.prop === 'function') {
				jQuery('.episode_box_no_player_or_links').prop("checked", false );
				jQuery('#episode_box_no_player_and_links').prop("checked", false );
			}
		}
	} );
	
	jQuery('#episode_box_no_player_and_links').change(function () {
		
		var objectChecked = jQuery(this).attr("checked");
		if(typeof jQuery.prop === 'function') {
			objectChecked = jQuery(this).prop("checked");
		}
		
		if( objectChecked == true ) {
			jQuery('.episode_box_no_player_or_links').attr("checked", false );
			if(typeof jQuery.prop === 'function') {
				jQuery('.episode_box_no_player_or_links').prop("checked", false );
			}
		}
	} );

	jQuery('.episode_box_no_player_or_links').change(function () {
		var objectChecked = jQuery(this).attr("checked");
		if(typeof jQuery.prop === 'function') {
			objectChecked = jQuery(this).prop("checked");
		}
		
		if( objectChecked == true) {
			jQuery('#episode_box_no_player_and_links').attr("checked", false );
			if(typeof jQuery.prop === 'function') {
				jQuery('#episode_box_no_player_and_links').prop("checked", false );
			}
		}
	} );
	
	jQuery('#episode_box_feature_in_itunes').change( function() {
		var objectChecked = jQuery('#episode_box_feature_in_itunes').attr('checked');
		if(typeof jQuery.prop === 'function') {
			objectChecked = jQuery('#episode_box_feature_in_itunes').prop('checked');
		}
		if( objectChecked ) {
			$("#episode_box_order").attr("disabled", true);
		} else {
			$("#episode_box_order").removeAttr("disabled");
		}
	});

} );
//-->
</script>

<input type="hidden" name="action" value="powerpress-save-settings" />


<input type="hidden" id="powerpress_advanced_mode" name="General[advanced_mode_2]" value="1" />
<input type="hidden" id="save_tab_pos" name="tab" value="<?php echo (empty($_POST['tab'])?0: intval($_POST['tab']) ); ?>" />

<div id="powerpress_admin_header">
<h2><?php echo __('Blubrry PowerPress Settings', 'powerpress'); ?></h2> 
	<span class="powerpress-mode"><?php echo __('Advanced Mode', 'powerpress'); ?>
		&nbsp; <a href="<?php echo admin_url("admin.php?page=". powerpress_admin_get_page() ."&amp;mode=simple"); ?>" id="powerpress_advanced_mode_button" class="button-primary button-blubrry"><?php echo __('Switch to Simple Mode', 'powerpress'); ?></a>
	</span>
</div>

<div id="powerpress_settings_page" class="powerpress_tabbed_content"> 
  <ul class="powerpress_settings_tabs">
		<li><a href="#tab0"><span><?php echo htmlspecialchars(__('Welcome', 'powerpress')); ?></span></a></li> 
		<li><a href="#tab1"><span><?php echo htmlspecialchars(__('Episodes', 'powerpress')); ?></span></a></li> 
		<li><a href="#tab2"><span><?php echo htmlspecialchars(__('Services & Stats', 'powerpress')); ?></span></a></li>
		<li><a href="#tab3"><span><?php echo htmlspecialchars(__('Website', 'powerpress')); ?></span></a></li>
		<li><a href="#tab4"><span><?php echo htmlspecialchars(__('Feeds', 'powerpress')); ?></span></a></li>
		<li><a href="#tab5"><span><?php echo htmlspecialchars(__('iTunes', 'powerpress')); ?></span></a></li>
		<li><a href="#tab6"><span><?php echo htmlspecialchars(__('Google Play', 'powerpress')); ?></span></a></li>
		<li><a href="#tab7"><span><?php echo htmlspecialchars(__('Artwork', 'powerpress')); ?></span></a></li>
		<li><a href="#tab-dest"><span><?php echo htmlspecialchars(__('Destinations', 'powerpress')); ?></span></a></li>
  </ul>
	
	<div id="tab0" class="powerpress_tab">
	<?php	powerpressadmin_welcome($General); ?>
	</div>
	
  <div id="tab1" class="powerpress_tab">
		<?php
		powerpressadmin_edit_entry_options($General);
		powerpressadmin_edit_podpress_options($General);
		?>
	</div>
	
	<div id="tab2" class="powerpress_tab">
		<?php
	if( $MultiSiteServiceSettings && defined('POWERPRESS_MULTISITE_VERSION') )
	{
		PowerPressMultiSitePlugin::edit_blubrry_services($General);
	}
	else
	{
		powerpressadmin_edit_blubrry_services($General);
		powerpressadmin_edit_media_statistics($General);
	}
		?>
	</div>
	
	<div id="tab3" class="powerpress_tab">
		<?php
		powerpressadmin_appearance($General, $FeedSettings);
		?>
	</div>
	
	<div id="tab4" class="powerpress_tab">
		<?php
		powerpressadmin_edit_feed_general($FeedSettings, $General);
		powerpressadmin_edit_feed_settings($FeedSettings, $General, $FeedAttribs);
		powerpressadmin_edit_funding($FeedSettings);
		powerpressadmin_edit_tv($FeedSettings);
		?>
	</div>
	
	<div id="tab5" class="powerpress_tab">
		<?php
		powerpressadmin_edit_itunes_feed($FeedSettings, $General, $FeedAttribs);
		?>
	</div>
	<div id="tab6" class="powerpress_tab">
		<?php
		powerpressadmin_edit_googleplay($FeedSettings, $General, $FeedAttribs);
		?>
	</div>
	<div id="tab7" class="powerpress_tab">
		<?php
		powerpressadmin_edit_artwork($FeedSettings, $General);
		?>
	</div>
	<div id="tab-dest" class="powerpress_tab">
		<?php
		powerpressadmin_edit_destinations($FeedSettings, $General, $FeedAttribs);
		?>
	</div>
	
</div>
<div class="clear"></div>

<?php

	powerpressadmin_advanced_options($General);
}

function powerpressadmin_advanced_options($General)
{
	// Break the bottom section here out into it's own function
	$ChannelsCheckbox = '';
	if( !empty($General['custom_feeds']) )
		$ChannelsCheckbox = ' onclick="alert(\''.  __('You must delete all of the Podcast Channels to disable this option.', 'powerpress')  .'\');return false;"';
	$CategoryCheckbox = '';
	//if( !empty($General['custom_cat_feeds']) ) // Decided ont to include this warning because it may imply that you have to delete the actual category, which is not true.
	//	$CategoryCheckbox = ' onclick="alert(\'You must remove podcasting from the categories to disable this option.\');return false;"';
?>
<script language="javascript"><!--

jQuery(document).ready( function() {
	
	jQuery('.pp-expand-section').click( function(e) {
		e.preventDefault();
		
		if( jQuery(this).hasClass('pp-expand-section-expanded') ) {
			jQuery(this).removeClass('pp-expand-section-expanded');
			jQuery(this).parent().next('div').hide(400);
			jQuery(this).blur();
		} else {
			jQuery(this).addClass('pp-expand-section-expanded');
			jQuery(this).parent().next('div').show(400);
			jQuery(this).blur();
		}
	});
});

//-->
</script>
<div style="margin-left: 10px;">
	<h3><a href="#" class="pp-expand-section pp-expand-section-expanded"><?php echo __('Advanced Options', 'powerpress'); ?></a></h3>
	<div style="margin-left: 50px;" >
		<div>
			<input type="checkbox" name="NULL[import_podcast]" value="1" checked disabled /> 
			<strong><a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_import_feed.php'); ?>"><?php echo __('Import Podcast', 'powerpress'); ?></a></strong> <?php echo powerpressadmin_new(); ?> - 
			<?php echo __('Import podcast feed from SoundCloud, LibSyn, PodBean or other podcast service.', 'powerpress'); ?> 
		</div>
		<div>
			<input type="checkbox" name="NULL[migrate_media]" value="1" checked disabled /> 
			<strong><a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_migrate.php'); ?>"><?php echo __('Migrate Media', 'powerpress'); ?></a></strong> <?php echo powerpressadmin_new(); ?> - 
			<?php echo __('Migrate media files to Blubrry Podcast Media Hosting with only a few clicks.', 'powerpress'); ?> 
		</div>
		<div>
			<input type="checkbox" name="NULL[podcasting_seo]" value="1" checked disabled /> 
			<strong><a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_search.php'); ?>"><?php echo __('Podcasting SEO', 'powerpress'); ?></a></strong> <?php echo powerpressadmin_new(); ?> - 
			<?php echo __('Select from 3 different web based audio players.', 'powerpress'); ?> 
		</div>
		
		<div>
			<input type="checkbox" name="NULL[player_options]" value="1" checked disabled /> 
			<strong><?php echo __('Audio Player Options', 'powerpress'); ?></strong> - 
			<?php echo __('Select from 3 different web based audio players.', 'powerpress'); ?> 
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_player.php&sp=1'); ?>"><?php echo __('configure audio player', 'powerpress'); ?></a>)</span>
		</div>
		<div>
			<input type="checkbox" name="NULL[video_player_options]" value="1" checked disabled /> 
			<strong><?php echo __('Video Player Options', 'powerpress'); ?></strong> - 
			<?php echo __('Select from 3 different web based video players.', 'powerpress'); ?> 
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_videoplayer.php&sp=1'); ?>"><?php echo __('configure video player', 'powerpress'); ?></a>)</span>
			
		</div>
		<div>
			<input type="hidden" name="General[channels]" value="0" />
			<input type="checkbox" name="General[channels]" value="1" <?php echo ( !empty($General['channels']) ?' checked':''); echo $ChannelsCheckbox; ?> /> 
			<strong><?php echo __('Custom Podcast Channels', 'powerpress'); ?></strong> - 
			<?php echo __('Manage multiple media files and/or formats to one blog post.', 'powerpress'); ?> 
			<?php if( empty($General['channels']) ) { ?>
			<span style="font-size: 85%;">(<?php echo __('feature will appear in left menu when enabled', 'powerpress'); ?>)</span>
			<?php } else { ?>
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_customfeeds.php'); ?>"><?php echo __('configure podcast channels', 'powerpress'); ?></a>)</span>
			<?php } ?>
		</div>
		<div>
			<input type="hidden" name="General[cat_casting]" value="0" />
			<input type="checkbox" name="General[cat_casting]" value="1" <?php echo ( !empty($General['cat_casting']) ?' checked':'');  echo $CategoryCheckbox;  ?> /> 
			<strong><?php echo __('Category Podcasting', 'powerpress'); ?></strong> - 
			<?php echo __('Manage podcasting for specific categories.', 'powerpress'); ?> 
			<?php if( empty($General['cat_casting']) ) { ?>
			<span style="font-size: 85%;">(<?php echo __('feature will appear in left menu when enabled', 'powerpress'); ?>)</span>
			<?php } else { ?>
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_categoryfeeds.php'); ?>"><?php echo __('configure podcast categories', 'powerpress'); ?></a>)</span>
			<?php } ?>
		</div>
		<div>
			<input type="checkbox" name="General[metamarks]" value="1" <?php echo ( !empty($General['metamarks']) ?' checked':'');  ?> /> 
			<strong><?php echo __('Meta Marks', 'powerpress'); ?></strong> - 
			<?php echo __('Add additional meta information to your media for syndication.', 'powerpress'); ?> 
			<?php echo powerpress_help_link('http://www.powerpresspodcast.com/metamarks/'); ?> 
			<span style="font-size: 85%;">(<?php echo __('feature will appear in episode entry box', 'powerpress'); ?>)</span>
		</div>
		
		
		<div>
			<input type="hidden" name="General[taxonomy_podcasting]" value="0" />
			<input type="checkbox" name="General[taxonomy_podcasting]" value="1" <?php echo ( !empty($General['taxonomy_podcasting']) ?' checked':''); ?> /> 
			<strong><?php echo __('Taxonomy Podcasting', 'powerpress'); ?></strong> 
			<span style="font-size: 14px;">(<?php echo __('Feature sponsored by', 'powerpress'); ?> <a href="http://afterbuzztv.com/" target="_blank">AfterBuzzTV.com</a>)</span> - 
			<?php echo __('Manage podcasting for specific taxonomies.', 'powerpress'); ?> 
			<?php if( empty($General['taxonomy_podcasting']) ) { ?>
			<span style="font-size: 85%;">(<?php echo __('feature will appear in left menu when enabled', 'powerpress'); ?>)</span>
			<?php } else { ?>
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_taxonomyfeeds.php'); ?>"><?php echo __('configure taxonomy podcasting', 'powerpress'); ?></a>)</span>
			<?php } ?>
		</div>
		<div>
			<input type="hidden" name="General[posttype_podcasting]" value="0" />
			<input type="checkbox" name="General[posttype_podcasting]" value="1" <?php echo ( !empty($General['posttype_podcasting']) ?' checked':''); ?> /> 
			<strong><?php echo __('Post Type Podcasting', 'powerpress'); ?></strong> - 
			<?php echo __('Manage multiple media files and/or formats to specific post types.', 'powerpress'); ?> 
			<?php if( empty($General['posttype_podcasting']) ) { ?>
			<span style="font-size: 85%;">(<?php echo __('feature will appear in left menu when enabled', 'powerpress'); ?>)</span>
			<?php } else { ?>
			<span style="font-size: 85%;">(<a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_posttypefeeds.php'); ?>"><?php echo __('configure post type podcasting', 'powerpress'); ?></a>)</span>
			<?php } ?>
		</div>
		<div>
			<input type="checkbox" name="General[playlist_player]" value="1" <?php echo ( !empty($General['playlist_player']) ?' checked':''); ?> /> 
			<strong><?php echo __('PowerPress Playlist Player', 'powerpress'); ?></strong> - 
			<?php echo __('Create playlists for your podcasts.', 'powerpress'); ?> 
			<span style="font-size: 85%;">(<a href="http://create.blubrry.com/resources/powerpress/advanced-tools-and-options/powerpress-playlist-shortcode/" target="_blank"><?php echo __('learn more', 'powerpress'); ?></a>)</span>
		</div>
	</div>
</div>

<?php
	$link_action_url = admin_url('admin.php?action=powerpress-jquery-account');
	$link_action = 'powerpress-jquery-account';
?>
<div style="margin-left: 10px;">
	<h3><?php echo __('Link Blubrry Account', 'powerpress'); ?></h3>
	<p style="font-size: 125%;">
		<strong><a class="button-primary  button-blubrry thickbox" title="<?php echo esc_attr(__('Blubrry Services Integration', 'powerpress')); ?>" href="<?php echo wp_nonce_url($link_action_url, $link_action); ?>&amp;KeepThis=true&amp;TB_iframe=true&amp;width=600&amp;height=400&amp;modal=false" target="_blank"><?php echo __('Click here to link Blubrry account', 'powerpress'); ?></a></strong>
	</p>
	<p>
		<?php echo __('Link your blubrry.com account if you have a Blubrry Podcast Hosting or Blubrry Podcast Statistics services.', 'powerpress'); ?>
	</p>
</div>

<div style="margin-left: 10px;">
	<h3 style="margin-bottom: 5px;"><?php echo __('Looking for Support, Consulting or Custom Development?', 'powerpress'); ?></h3>
	<p style="margin: 0  0 0 50px;">
		<?php echo __('Blubrry offers a variety of options, free and paid, to assist you with your podcasting and Internet media needs. Whether you need your theme customized for podcasting or you want consulting on what video format is best for your audience, we have the staff and knowledge to assist.', 'powerpress'); ?>
	</p>
	<p style="margin: 5px 0 0 50px;">
	<strong><?php echo '<a href="http://create.blubrry.com/support/" target="_blank">'. __('Learn More about Blubrry Support Options', 'powerpress') .'</a>'; ?></strong>
	</p>
</div>

<?php
	return;
	
	// We will not pester folks with this stuff in PowerPress 7.0+
	
	if( isset($General['timestamp']) && $General['timestamp'] > 0 && $General['timestamp'] < ( time()- (60*60*24*14) ) ) // Lets wait 14 days before we annoy them asking for support
	{
?>
<div style="margin-left: 10px;">
	<h3 style="margin-bottom: 5px;"><?php echo __('Like The Plugin?', 'powerpress'); ?></h3>
	<p style="margin-top: 0;">
		<?php echo __('This plugin is great, don\'t you think? If you like the plugin we\'d be ever so grateful if you\'d give it your support. Here\'s how:', 'powerpress'); ?>
	</p>
	<ul id="powerpress_support">
		<li><?php echo sprintf(__('Rate this plugin 5 stars in the %s.', 'powerpress'), 
			'<a href="http://wordpress.org/extend/plugins/powerpress/" target="_blank">'. __('WordPress Plugins Directory', 'powerpress') .'</a>');
		
		?>
		</li>
		<li><?php echo __('Tell the world about PowerPress by writing about it on your blog', 'powerpress'); ?>, 
		<a href="http://twitter.com/home/?status=<?php echo urlencode( __('I\'m podcasting with Blubrry PowerPress (http://blubrry.com/powerpress/) #powerpress #wordpress', 'powerpress') ); ?>" target="_blank"><?php echo __('Twitter', 'powerpress'); ?></a>, 
		<a href="http://www.facebook.com/share.php?u=<?php echo urlencode('http://www.blubrry.com/powerpress'); ?>&amp;t=<?php echo urlencode( __('I podcast with Blubrry PowerPress', 'powerpress')); ?>" target="_blank"><?php echo __('Facebook', 'powerpress'); ?></a>,
		<a href="https://plus.google.com/share?url==<?php echo urlencode('http://www.blubrry.com/powerpress'); ?>" target="_blank"><?php echo __('Google+', 'powerpress'); ?></a>,
		etc...</li>
		<li><a href="http://www.blubrry.com/contact.php" target="_blank"><?php echo __('Send us feedback', 'powerpress'); ?></a> (<?php echo __('we love getting suggestions for new features!', 'powerpress'); ?>)</li>
	</ul>
</div>
<?php
	}
?>
<div style="margin-left: 10px;">
	<h3 style="margin-bottom: 5px;"><?php echo __('Become a PowerPress Patron!', 'powerpress'); ?></h3>
	<p style="margin: 0; padding-left: 50px;">
		<?php echo __('Help support your favorite podcasting plugin via Patreon.', 'powerpress'); ?>
	</p>
	<p style="margin-top: 0; padding-left: 50px;"><?php echo '<a href="https://www.patreon.com/blubrry?ty=h" target="_blank">'. __('Visit Blubrry\'s Patreon page', 'powerpress') .'</a>'; ?>
	</p>
</div>
<?php
}

function powerpressadmin_edit_entry_options($General)
{
	if( !isset($General['default_url']) )
		$General['default_url'] = '';
	if( !isset($General['episode_box_mode']) )
		$General['episode_box_mode'] = 0; // Default not set, 1 = no duration/file size, 2 = yes duration/file size (default if not set)
	if( !isset($General['episode_box_embed']) )
		$General['episode_box_embed'] = 0;
	if( !isset($General['set_duration']) )
		$General['set_duration'] = 0;
	if( !isset($General['set_size']) )
		$General['set_size'] = 0;
	if( !isset($General['auto_enclose']) )
		$General['auto_enclose'] = 0;
	if( !isset($General['episode_box_player_size']) )
		$General['episode_box_player_size'] = 0;
	if( !isset($General['episode_box_closed_captioned']) )
		$General['episode_box_closed_captioned'] = 0;
	if( !isset($General['episode_box_order']) )
		$General['episode_box_order'] = 0;	
	if( !isset($General['episode_box_feature_in_itunes']) )
		$General['episode_box_feature_in_itunes'] = 0;
		
?>
<h3><?php echo __('Episode Entry Options', 'powerpress'); ?></h3>


<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('Podcast Entry Box', 'powerpress'); ?></th> 
<td>
	<p style="margin-top: 5px;">
		<?php echo __('Configure your podcast episode entry box with the options that fit your needs.', 'powerpress'); ?>
	</p>
				<div id="episode_box_mode_adv">
				
					<p style="margin-top: 15px;"><input class="episode_box_option" name="Null[ignore]" type="checkbox" value="1" checked onclick="return false" onkeydown="return false" /> <?php echo __('Media URL', 'powerpress'); ?>
						(<?php echo __('Specify URL to episode\'s media file', 'powerpress'); ?>)</p>
					
					<p style="margin-top: 15px;"><input id="episode_box_mode" class="episode_box_option" name="General[episode_box_mode]" type="checkbox" value="2" <?php if( empty($General['episode_box_mode']) || $General['episode_box_mode'] != 1 ) echo ' checked'; ?> /> <?php echo __('Media File Size and Duration', 'powerpress'); ?>
						(<?php echo __('Specify episode\'s media file size and duration', 'powerpress'); ?>)</p>
						
					<p style="margin-top: 15px; margin-bottom: 0;"><input id="episode_box_embed" class="episode_box_option" name="General[episode_box_embed]" type="checkbox" value="1"<?php if( !empty($General['episode_box_embed']) ) echo ' checked'; ?> onclick="SelectEmbedField(this.checked);"  /> <?php echo __('Embed Field', 'powerpress'); ?>
						(<?php echo __('Enter embed code from sites such as YouTube', 'powerpress'); ?>)</p>
							<p style="margin-top: 5px; margin-left: 20px; font-size: 90%;"><input id="embed_replace_player" class="episode_box_option" name="General[embed_replace_player]" type="checkbox" value="1"<?php if( !empty($General['embed_replace_player']) ) echo ' checked'; ?> /> <?php echo __('Replace Player with Embed', 'powerpress'); ?>
								(<?php echo __('Do not display default player if embed present for episode.', 'powerpress'); ?>)</p>
					
					<p style="margin-top: 15px;"><input id="episode_box_player_links_options" class="episode_box_option" name="NULL[episode_box_player_links_options]" type="checkbox" value="1"<?php if( !empty($General['episode_box_no_player_and_links']) || !empty($General['episode_box_no_player']) || !empty($General['episode_box_no_links']) ) echo ' checked'; ?> /> <?php echo __('Display Player and Links Options', 'powerpress'); ?>
					</p>
					<div id="episode_box_player_links_options_div" style="margin-left: 20px;<?php if( empty($General['episode_box_no_player_and_links']) && empty($General['episode_box_no_player']) && empty($General['episode_box_no_links']) ) echo 'display:none;'; ?>">
						
						<p style="margin-top: 0px; margin-bottom: 5px;"><input id="episode_box_no_player_and_links" class="episode_box_option" name="General[episode_box_no_player_and_links]" type="checkbox" value="1"<?php if( !empty($General['episode_box_no_player_and_links']) ) echo ' checked'; ?> /> <?php echo htmlspecialchars(__('No Player & Links Option', 'powerpress')); ?>
							(<?php echo __('Disable media player and links on a per episode basis', 'powerpress'); ?>)</p>
						
						<p style="margin-top: 0; margin-bottom: 0; margin-left: 20px;"><?php echo __('- or -', 'powerpress'); ?></p>
						
						<p style="margin-top: 5px;  margin-bottom: 10px;"><input id="episode_box_no_player" class="episode_box_option episode_box_no_player_or_links" name="General[episode_box_no_player]" type="checkbox" value="1"<?php if( !empty($General['episode_box_no_player']) ) echo ' checked'; ?> /> <?php echo __('No Player Option', 'powerpress'); ?>
							(<?php echo __('Disable media player on a per episode basis', 'powerpress'); ?>)</p>
						
						<p style="margin-top: 5px;  margin-bottom: 20px;"><input id="episode_box_no_links" class="episode_box_option episode_box_no_player_or_links" name="General[episode_box_no_links]" type="checkbox" value="1"<?php if( !empty($General['episode_box_no_links']) ) echo ' checked'; ?> /> <?php echo __('No Links Option', 'powerpress'); ?>
							(<?php echo __('Disable media links on a per episode basis', 'powerpress'); ?>)</p>
						
					</div>
				
					<p style="margin-top: 15px;"><input id="episode_box_cover_image" class="episode_box_option" name="General[episode_box_cover_image]" type="checkbox" value="1"<?php if( !empty($General['episode_box_cover_image']) ) echo ' checked'; ?> /> <?php echo __('Poster Image', 'powerpress'); ?>
						(<?php echo __('Specify URL to poster artwork specific to each episode', 'powerpress'); ?>)</p>
						
					<p style="margin-top: 15px;"><input id="episode_box_player_size" class="episode_box_option" name="General[episode_box_player_size]" type="checkbox" value="1"<?php if( !empty($General['episode_box_player_size']) ) echo ' checked'; ?> /> <?php echo __('Player Width and Height', 'powerpress'); ?> 
						(<?php echo __('Customize player width and height on a per episode basis', 'powerpress'); ?>)</p>
					<p style="margin-top: 15px;"><input id="episode_box_subtitle" class="episode_box_option" name="General[episode_box_subtitle]" type="checkbox" value="1"<?php if( !empty($General['episode_box_subtitle']) ) echo ' checked'; ?> /> <?php echo __('iTunes Subtitle Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use the first 250 characters of your blog post', 'powerpress'); ?>)</p>
					<p style="margin-top: 15px;"><input id="episode_box_summary" class="episode_box_option" name="General[episode_box_summary]" type="checkbox" value="1"<?php if( !empty($General['episode_box_summary']) ) echo ' checked'; ?> /> <?php echo __('iTunes Summary Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use your blog post', 'powerpress'); ?>)</p>
					
					
						
					<p style="margin-top: 15px;"><input id="episode_box_author" class="episode_box_option" name="General[episode_box_author]" type="checkbox" value="1"<?php if( !empty($General['episode_box_author']) ) echo ' checked'; ?> /> <?php echo __('iTunes Author Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to the post author name', 'powerpress'); ?>)</p>
					
					<p style="margin-top: 15px;"><input id="episode_box_explicit" class="episode_box_option" name="General[episode_box_explicit]" type="checkbox" value="1"<?php if( !empty($General['episode_box_explicit']) ) echo ' checked'; ?> /> <?php echo __('iTunes Explicit Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use your feed\'s explicit setting', 'powerpress'); ?>)</p>
					
						
					<p style="margin-top: 15px;"><label><input id="episode_box_itunes_image" class="episode_box_option" name="General[episode_box_itunes_image]" type="checkbox" value="1"<?php if( !empty($General['episode_box_itunes_image']) ) echo ' checked'; ?> /> <?php echo __('iTunes Episode Image Field', 'powerpress'); ?></label> <?php echo powerpressadmin_new(); ?>
						(<?php echo __('Leave unchecked to use the image embedded into your media files.', 'powerpress'); ?>)</p>	
						
					<p style="margin-top: 15px;"><label><input id="episode_box_closed_captioned" class="episode_box_option" name="General[episode_box_closed_captioned]" type="checkbox" value="1"<?php if( !empty($General['episode_box_closed_captioned']) ) echo ' checked'; ?> /> <?php echo __('iTunes Closed Captioned', 'powerpress'); ?></label> 
						(<?php echo __('Leave unchecked if you do not distribute closed captioned media', 'powerpress'); ?>)</p>
						
					<p style="margin-top: 15px;"><label><input id="episode_box_order" class="episode_box_option" name="General[episode_box_order]" type="checkbox" value="1"<?php if( !empty($General['episode_box_order']) ) echo ' checked'; ?> <?php if( !empty($General['episode_box_feature_in_itunes']) ) echo ' disabled'; ?> /> <?php echo __('iTunes Order', 'powerpress'); ?></label> 
						(<?php echo __('Override the default ordering of episodes on the iTunes and Google Play Music podcast directories', 'powerpress'); ?>)</p>
						<em><strong><?php echo __('If conflicting values are present the directories will use the default ordering.', 'powerpress'); ?></strong></em><br />
						<em><strong><?php echo __('This feature only applies to the default podcast feed and Custom Podcast Channel feeds added by PowerPress.', 'powerpress'); ?></strong></em>
					
					<p style="margin-top: 15px;"><label><input id="episode_box_feature_in_itunes" class="episode_box_option" name="General[episode_box_feature_in_itunes]" type="checkbox" value="1"<?php if( !empty($General['episode_box_feature_in_itunes']) ) echo ' checked'; ?> /> <?php echo __('Feature Episode in iTunes and Google Play Music', 'powerpress'); ?></label>
						(<?php echo __('Display selected episode at top of your iTunes and Google Play Music directory listings', 'powerpress'); ?>)</p>
						<em><strong><?php echo __('All other episodes will be listed following the featured episode.', 'powerpress'); ?></strong></em><br />
						<em><strong><?php echo __('This feature only applies to the default podcast feed and Custom Podcast Channel feeds added by PowerPress.', 'powerpress'); ?></strong></em>
						
						
					<p style="margin-top: 15px;"><input id="episode_box_gp_desc" class="episode_box_option" name="General[episode_box_gp_desc]" type="checkbox" value="1"<?php if( !empty($General['episode_box_gp_desc']) ) echo ' checked'; ?> /> <?php echo __('Google Play Description Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use your blog post', 'powerpress'); ?>)</p>
					<p style="margin-top: 15px;"><input id="episode_box_gp_explicit" class="episode_box_option" name="General[episode_box_gp_explicit]" type="checkbox" value="1"<?php if( !empty($General['episode_box_gp_explicit']) ) echo ' checked'; ?> /> <?php echo __('Google Play Explicit Field', 'powerpress'); ?>
						(<?php echo __('Leave unchecked to use your feed\'s explicit setting', 'powerpress'); ?>)</p>
						
				<fieldset style="border: 1px dashed #333333; margin: 10px 0 10px -20px;">
					<legend style="margin: 0 20px; padding: 0 5px 5px 5px; font-weight: bold;"><?php echo __('Advanced Options', 'powerpress');  ?></legend>
					<p style="margin: 15px 0 0 20px;"><label><input id="episode_box_block" class="episode_box_option" name="General[episode_box_block]" type="checkbox" value="1"<?php if( !empty($General['episode_box_block']) ) echo ' checked'; ?> /> <?php echo __('iTunes Block', 'powerpress'); ?> (<?php echo htmlspecialchars('<itunes:block>yes</itunes:block>'); ?>)</label></p>
					<div style="margin: 0 10px 10px 20px;"><em><strong><?php echo __('Prevent episodes from appearing in iTunes and other diretories that support the iTunes:block tag. Episodes may still appear in other directories and applications.', 'powerpress'); ?></strong></em></div>
					
					<p style="margin: 15px 0 0 20px;"><label><input id="episode_box_gp_block" class="episode_box_option" name="General[episode_box_gp_block]" type="checkbox" value="1"<?php if( !empty($General['episode_box_gp_block']) ) echo ' checked'; ?> /> <?php echo __('Google Play Block', 'powerpress'); ?> (<?php echo htmlspecialchars('<googleplay:block>yes</itunes:googleplay>'); ?>)</label></p>
					<div style="margin: 0 10px 10px 20px;"><em><strong><?php echo __('Prevent episodes from appearing in Google Play Music. Episodes may still appear in other directories and applications.', 'powerpress'); ?></strong></em></div>
				</fieldset>

				</div>

</td>
</tr>
</table>
<script language="javascript"><!--
SelectEmbedField(<?php echo $General['episode_box_embed']; ?>);
//-->
</script>

<?php
	
	$AdvanecdOptions = false;
	if( !empty($General['default_url']) )
		$AdvanecdOptions = true;
	if( !empty($General['set_duration']) )
		$AdvanecdOptions = true;
	if( !empty($General['set_size']) )
		$AdvanecdOptions = true;
	if( !empty($General['auto_enclose']) )
		$AdvanecdOptions = true;
	if( !empty($General['permalink_feeds_only']) )
		$AdvanecdOptions = true;
	if( !empty($General['hide_warnings']) )
		$AdvanecdOptions = true;
		
	$DefaultMediaURL = false;
	
	if( !empty($General['default_url']) )
		$DefaultMediaURL = true;

	if( !$AdvanecdOptions ) {
?>
	<div style="margin-left: 10px; font-weight: bold;" id="advanced_basic_options_show_link"><a href="#" onclick="document.getElementById('advanced_basic_options').style.display='block';document.getElementById('advanced_basic_options_show_link').style.display='none';return false;"><?php echo __('Show Advanced Episode Entry Settings', 'powerpress'); ?></a></div>
<?php } ?>
<!-- start advanced features -->
<div id="advanced_basic_options" <?php echo ($AdvanecdOptions?'':'style="display:none;"'); ?>>
<?php if( $DefaultMediaURL || defined('POWERPRESS_DEFAULT_MEDIA_URL') ) { ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Default Media URL', 'powerpress'); ?></th> 
<td>
	<input type="text" style="width: 80%;" name="General[default_url]" value="<?php echo esc_attr($General['default_url']); ?>" maxlength="255" />
	<p><?php echo __('e.g. http://example.com/mediafolder/', 'powerpress'); ?></p>
	<p><?php echo __('URL above will prefix entered file names that do not start with \'http://\'. URL above must end with a trailing slash. You may leave blank if you always enter the complete URL to your media when creating podcast episodes.', 'powerpress'); ?>
	</p>
</td>
</tr>
</table>
<?php } ?>

<div id="episode_entry_settings">
<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('File Size Default', 'powerpress'); ?></th> 
<td>
		<select name="General[set_size]" class="bpp_input_med">
<?php
$options = array(0=>__('Auto detect file size', 'powerpress'), 1=>__('User specify', 'powerpress') );
	
while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($General['set_size']==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select> (<?php echo __('specify default file size option when creating a new episode', 'powerpress'); ?>)
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Duration Default', 'powerpress'); ?></th> 
<td>
		<select name="General[set_duration]" class="bpp_input_med">
<?php
$options = array(0=>__('Auto detect duration', 'powerpress'), 1=>__('User specify', 'powerpress'), -1=>__('Not specified (not recommended)', 'powerpress') );
	
while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($General['set_duration']==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select> (<?php echo __('specify default duration option when creating a new episode', 'powerpress'); ?>)
</td>
</tr>
</table>
</div>

<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Auto Add Media', 'powerpress'); ?></th> 
<td>
		<select name="General[auto_enclose]" class="bpp_input_med">
<?php
$options = array(0=>__('Disabled (default)', 'powerpress'), 1=>__('First media link found in post content', 'powerpress'), 2=>__('Last media link found in post content', 'powerpress') );
	
while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($General['auto_enclose']==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select>
		<p><?php echo __('When enabled, the first or last media link found in the post content is automatically added as your podcast episode.', 'powerpress'); ?></p>
		<p style="margin-bottom: 0;" class="description"><em><?php echo __('NOTE: Use this feature with caution. Links to media files could unintentionally become podcast episodes.', 'powerpress'); ?></em></p>
		<p><em><?php echo __('WARNING: Episodes created with this feature will <u>not</u> include Duration (total play time) information.', 'powerpress'); ?></em></p>
</td>
</tr>
<tr valign="top">
<th scope="row">
<?php echo __('Disable Warnings', 'powerpress'); ?></th> 
<td>
		<select name="General[hide_warnings]" class="bpp_input_med">
<?php
$options = array(0=>__('No (default)', 'powerpress'), 1=>__('Yes', 'powerpress') );
$current_value = (!empty($General['hide_warnings'])?$General['hide_warnings']:0);
while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($current_value==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select>
		<p><?php echo __('Disable warning messages displayed in episode entry box. Errors are still displayed.', 'powerpress'); ?></p>
</td>
</tr>
</table>
</div>
<!-- end advanced features -->
<?php
		

		global $wp_rewrite;
		if( $wp_rewrite->permalink_structure ) // Only display if permalinks is enabled in WordPress
		{
?>
<h3><?php echo __('Permalinks', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Podcast Permalinks', 'powerpress'); ?></th> 
<td>
		<select name="General[permalink_feeds_only]" class="bpp_input_normal">
<?php
$options = array(0=>__('Default WordPress Behavior', 'powerpress'), 1=>__('Match Feed Name to Page/Category', 'powerpress') );
$current_value = (!empty($General['permalink_feeds_only'])?$General['permalink_feeds_only']:0);

while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($current_value==$value?' selected':''). ">$desc</option>\n";
	
?>
		</select>
		<p><?php echo sprintf(__('When configured, %s/podcast/ is matched to page/category named \'podcast\'.', 'powerpress'), get_bloginfo('url') ); ?></p>
</td>
</tr>
</table>
<?php
		}
?>


<?php
}

function powerpressadmin_edit_podpress_options($General)
{
	if( !empty($General['process_podpress']) || powerpress_podpress_episodes_exist() )
	{
		if( !isset($General['process_podpress']) )
			$General['process_podpress'] = 0;
		if( !isset($General['podpress_stats']) )	
			$General['podpress_stats'] = 0;
?>

<h3><?php echo __('PodPress Options', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">

<?php echo __('PodPress Episodes', 'powerpress'); ?></th> 
<td>
<select name="General[process_podpress]" class="bpp_input_med">
<?php
$options = array(0=>__('Ignore', 'powerpress'), 1=>__('Include in Posts and Feeds', 'powerpress') );

while( list($value,$desc) = each($options) )
	echo "\t<option value=\"$value\"". ($General['process_podpress']==$value?' selected':''). ">$desc</option>\n";
	
?>
</select>  (<?php echo __('includes podcast episodes previously created in PodPress', 'powerpress'); ?>)
</td>
</tr>
	<?php if( !empty($General['podpress_stats']) || powerpress_podpress_stats_exist() ) { ?>
	<tr valign="top">
	<th scope="row">

	<?php echo __('PodPress Stats Archive', 'powerpress'); ?></th> 
	<td>
	<select name="General[podpress_stats]" class="bpp_input_sm">
	<?php
	$options = array(0=>__('Hide', 'powerpress'), 1=>__('Display', 'powerpress') );

	while( list($value,$desc) = each($options) )
		echo "\t<option value=\"$value\"". ($General['podpress_stats']==$value?' selected':''). ">$desc</option>\n";
		
	?>
	</select>  (<?php echo __('display archive of old PodPress statistics', 'powerpress'); ?>)
	</td>
	</tr>
	<?php } ?>
	</table>
<?php
	}
}


function powerpressadmin_edit_googleplay($FeedSettings, $General, $FeedAttribs = array() )
{
	$feed_slug = $FeedAttribs['feed_slug'];
	$cat_ID = $FeedAttribs['category_id'];

	// Set default settings (if not set)
	if( empty($FeedSettings['googleplay_url']) )
		$FeedSettings['googleplay_url'] = '';
	if( empty($FeedSettings['googleplay_email']) )
		$FeedSettings['googleplay_email'] = '';
	if( empty($FeedSettings['googleplay_author']) )
		$FeedSettings['googleplay_author'] = '';
	if( empty($FeedSettings['googleplay_description']) )
		$FeedSettings['googleplay_description'] = '';
	if( empty($FeedSettings['googleplay_explicit']) )
		$FeedSettings['googleplay_explicit'] = '';
	if( empty($FeedSettings['googleplay_cat']) )
		$FeedSettings['googleplay_cat'] = '';

	$gp_feed_url = '';
	switch( $FeedAttribs['type'] )
	{
		case 'ttid': {
			$gp_feed_url = get_term_feed_link($FeedAttribs['term_taxonomy_id'], $FeedAttribs['taxonomy_type'], 'rss2' );
		}; break;
		case 'category': {
			if( !empty($General['cat_casting_podcast_feeds']) )
				$gp_feed_url = get_category_feed_link($cat_ID, 'podcast');
			else
				$gp_feed_url = get_category_feed_link($cat_ID);
		}; break;
		case 'channel': {
			$gp_feed_url = get_feed_link($feed_slug);
		}; break;
		case 'post_type': {
			$gp_feed_url = get_post_type_archive_feed_link($FeedAttribs['post_type'], $feed_slug);
		}; break;
		case 'general':
		default: {
			$gp_feed_url = get_feed_link('podcast');
		}
	}

	?>
	<h3><?php echo __('Google Play Settings', 'powerpress'); ?> <?php echo powerpressadmin_new(); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Google Play Email', 'powerpress'); ?> 
</th>
<td>
<input type="text" name="Feed[googleplay_email]" class="bpp_input_med" value="<?php echo esc_attr($FeedSettings['googleplay_email']); ?>" maxlength="255" />
<div>(<?php echo __('Google will email this address when your podcast is accepted to the Google Play Music Podcast directory.', 'powerpress'); ?>)</div>
<p><?php echo __('iTunes email setting will be used if left blank.', 'powerpress'); ?></p>
</td>
</tr>
<tr valign="top">
<th scope="row">
<?php echo __('Google Play Author', 'powerpress'); ?> 
</th>
<td>
<input type="text" name="Feed[googleplay_author]" class="bpp_input_med" value="<?php echo esc_attr($FeedSettings['googleplay_author']); ?>" maxlength="255" />
	<p><?php echo __('iTunes Author will be used if left blank', 'powerpress'); ?></p>
	<?php if( !empty($General['seo_itunes']) ) { ?>
	<p class="description"><?php echo __('Podcasting SEO Suggestion: This field may be indexed for Google Play Music search.', 'powerpress'); ?></p>
	<?php } ?>
</td>
</tr>
<tr valign="top">
<th scope="row">
<?php echo __('Google Play Description', 'powerpress'); ?> 
</th>
<td>
<p style="margin-top: 5px;"><?php echo __('Your description cannot exceed 4,000 characters in length.', 'powerpress'); ?></p>
<textarea name="Feed[googleplay_description]" rows="5" class="bpp-input-normal"><?php echo esc_textarea($FeedSettings['googleplay_description']); ?></textarea>
<p><?php echo __('iTunes Summary will be used if left blank', 'powerpress'); ?></p>
	<?php if( !empty($General['seo_itunes']) ) { ?>
	<p class="description"><?php echo __('Podcasting SEO Suggestion: This field may be indexed for Google Play Music search.', 'powerpress'); ?></p>
	<?php } ?>
</td>
</tr>
<tr valign="top">
<th scope="row">
<?php echo __('Google Play Explicit', 'powerpress'); ?> 
</th>
<td>
<select name="Feed[googleplay_explicit]" class="bpp_input_med">
<?php
$explicit = array(0=> __('No - display nothing', 'powerpress'), 1=>__('Yes - explicit content', 'powerpress') );

while( list($value,$desc) = each($explicit) )
	echo "\t<option value=\"$value\"". ($FeedSettings['googleplay_explicit']==$value?' selected':''). ">$desc</option>\n";

?>
</select>
</td>
</tr>
<tr valign="top">
<th scope="row">
<?php echo __('Google Play Category', 'powerpress'); ?>
<span class="powerpress-required"><?php echo __('Required', 'powerpress'); ?></span>
</th>
<td>
<select name="Feed[googleplay_cat]" class="bpp_input_med">
<?php

$MoreCategories = false;


$Categories = powerpress_googleplay_categories();

echo '<option value="">'. __('Select Category', 'powerpress') .'</option>';
while( list($value,$desc) = each($Categories) )
	echo "\t<option value=\"$value\"". ($FeedSettings['googleplay_cat']==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";

?>
</select>
</td>
</tr>

</table>
	<?php
}

function powerpressadmin_edit_itunes_general($FeedSettings, $General, $FeedAttribs = array() )
{
	// Set default settings (if not set)
	if( !empty($FeedSettings) )
	{
		if( !isset($FeedSettings['itunes_url']) )
			$FeedSettings['itunes_url'] = '';
	}
	if( !isset($General['itunes_url']) )
		$General['itunes_url'] = '';
	else if( !isset($FeedSettings['itunes_url']) ) // Should almost never happen
		$FeedSettings['itunes_url'] = $General['itunes_url'];
	
	$feed_slug = $FeedAttribs['feed_slug'];
	$cat_ID = $FeedAttribs['category_id'];
	
	if( $feed_slug == 'podcast' && $FeedAttribs['type'] == 'general' )
	{
		if( empty($FeedSettings['itunes_url']) && !empty($General['itunes_url']) )
			$FeedSettings['itunes_url'] = $General['itunes_url'];
	}
	
	$itunes_feed_url = '';

	switch( $FeedAttribs['type'] )
	{
		case 'ttid': {
			$itunes_feed_url = get_term_feed_link($FeedAttribs['term_taxonomy_id'], $FeedAttribs['taxonomy_type'], 'rss2');
		}; break;
		case 'category': {
			if( !empty($General['cat_casting_podcast_feeds']) )
				$itunes_feed_url = get_category_feed_link($cat_ID, 'podcast');
			else
				$itunes_feed_url = get_category_feed_link($cat_ID);
		}; break;
		case 'channel': {
			$itunes_feed_url = get_feed_link($feed_slug);
		}; break;
		case 'post_type': {
			$itunes_feed_url = get_post_type_archive_feed_link($FeedAttribs['post_type'], $feed_slug);
		}; break;
		case 'general':
		default: {
			$itunes_feed_url = get_feed_link('podcast');
		}
	}
	
?>
<h3><?php echo __('iTunes Listing Information', 'powerpress'); ?></h3>

<?php
} // end itunes general

function powerpressadmin_edit_blubrry_services($General, $action_url = false, $action = false)
{
	$DisableStatsInDashboard = false;
	if( !empty($General['disable_dashboard_stats']) )
		$DisableStatsInDashboard = true;
		
	
	if( $action_url == false )
		$action_url = admin_url('admin.php?action=powerpress-jquery-account');
	if( $action == false )
		$action = 'powerpress-jquery-account';
?>
<h3><?php echo __('Integrate Blubrry Services', 'powerpress'); ?></h3>
<ul><li><ul>
	<li style="margin-left: 30px; font-size:115%;"><?php echo sprintf(__('Track your podcast downloads with Blubrry\'s <a href="%s" target="_blank">FREE Basic Statistics</a> or <a href="%s" target="_blank">Professional Media Statistics</a>.','powerpress'), 'http://create.blubrry.com/resources/podcast-media-download-statistics/basic-statistics/', 'http://create.blubrry.com/resources/podcast-media-download-statistics/'); ?></li>
	<li style="margin-left: 30px; font-size:115%;"><?php echo sprintf(__('Upload and publish podcast media directly from your blog with <a href="%s" target="_blank">Blubrry Media Hosting</a>.','powerpress'), 'http://create.blubrry.com/resources/podcast-media-hosting/'); ?></li>
</ul></li></ul>
<div style="margin-left: 40px;">
	<p style="font-size: 125%;">
		<strong><a class="button-primary  button-blubrry thickbox" title="<?php echo esc_attr(__('Blubrry Services Integration', 'powerpress')); ?>" href="<?php echo wp_nonce_url( $action_url, $action); ?>&amp;KeepThis=true&amp;TB_iframe=true&amp;width=600&amp;height=400&amp;modal=false" target="_blank"><?php echo __('Click here to configure Blubrry Statistics and Hosting services', 'powerpress'); ?></a></strong>
	</p>
	<?php
	if( !empty($General['blubrry_program_keyword']) )
	{
		// Check that the redirect is in the settings...
		$RedirectURL = 'http://media.blubrry.com/'.$General['blubrry_program_keyword'].'/';
		$Error = true;
		if( stripos($General['redirect1'], $RedirectURL ) !== false )
			$Error = false;
		else if( stripos($General['redirect2'], $RedirectURL ) !== false )
			$Error = false;
		else if( stripos($General['redirect3'], $RedirectURL ) !== false )
			$Error = false;
		if( $Error )
		{
	?>
	<p style="font-weight: bold; color: #CC0000;">
	<?php 
		echo __('Statistics are not implemented correctly on this blog. Please click the button above to re-configure your services.', 'powerpress');
		?>
	</p>
	<?php
		}
		else
		{
	?>
	<p style="font-weight: bold;">
	<img src="<?php echo powerpress_get_root_url(); ?>images/Check.png" style="width: 25px; height: 20px;"  alt="<?php echo __('Enabled!', 'powerpress'); ?>" />
	<?php 
		if( empty($General['blubrry_hosting']) || $General['blubrry_hosting'] === 'false' )
			echo __('Blubrry Statistics Enabled!', 'powerpress');
		else
			echo __('Blubrry Statistics and Media Hosting Enabled!', 'powerpress');
		?>
	</p>
	<?php
		}
		
		if( empty($General['blubrry_hosting']) || $General['blubrry_hosting'] === 'false' )
		{
	?>
	<p>
	<?php echo __('Recently upgraded to Blubrry Hosting?', 'powerpress'); ?> 
	<a class="thickbox" title="<?php echo esc_attr(__('Blubrry Services Integration', 'powerpress')); ?>" href="<?php echo admin_url(); echo wp_nonce_url( "admin.php?action=powerpress-jquery-account", 'powerpress-jquery-account'); ?>&amp;KeepThis=true&amp;TB_iframe=true&amp;width=600&amp;height=400&amp;modal=false" target="_blank"><?php echo __('Click here to enter your account information.', 'powerpress'); ?></a>
	</p>
	<?php
		}
	}
	?>
</div>
<?php
	if( empty($General['blubrry_hosting']) || $General['blubrry_hosting'] === 'false' ) // Not signed up for hosting?
	{
?>
<div class="blubrry-services">
	<div class="blubrry-hosting">
		<p class="top-lines"><?php echo __('Need a reliable host for your podcast media?', 'powerpress'); ?></p>
		<p><?php echo __('Blubrry Media Hosting packages start at $12.', 'powerpress'); ?></p>
		<p><a href="http://create.blubrry.com/resources/podcast-media-hosting/" target="_blank"><?php echo __('Learn More', 'powerpress'); ?></a></p>
	</div>
	<div class="blubrry-stats">
		<p class="top-lines"><?php echo __('Measure your audience for <strong>free</strong> and add more detailed', 'powerpress'); ?></p>
		<p><?php echo __('reporting for only $5 per month.', 'powerpress'); ?></p>
		<p>&nbsp;</p>
		<p><a href="http://create.blubrry.com/resources/podcast-media-download-statistics/" target="_blank"><?php echo __('Learn More', 'powerpress'); ?></a></p>
	</div>
	<div class="clear"></div>
</div>
<?php
	} // end not signed up for hosting
	
?>
<div style="margin-left: 40px;">
	<p style="margin-top: 10px;">
	<input name="DisableStatsInDashboard" type="checkbox" value="1"<?php if( $DisableStatsInDashboard == true ) echo ' checked'; ?> />
	<?php echo __('Remove Statistics from WordPress Dashboard', 'powerpress'); ?></p>
</div>
<?php
}

function powerpressadmin_edit_media_statistics($General)
{
	if( !isset($General['redirect1']) )
		$General['redirect1'] = '';
	if( !isset($General['redirect2']) )
		$General['redirect2'] = '';
	if( !isset($General['redirect3']) )
		$General['redirect3'] = '';
		
	$StatsIntegrationURL = '';
	if( !empty($General['blubrry_program_keyword']) )
		$StatsIntegrationURL = 'http://media.blubrry.com/'.$General['blubrry_program_keyword'].'/';
?>
<div id="blubrry_stats_settings">
<h3><?php echo __('Media Statistics', 'powerpress'); ?></h3>
	<div style="margin-left: 40px;">
		<p>
		<?php echo __('Enter your Redirect URL issued by your media statistics service provider below.', 'powerpress'); ?>
		</p>

		<div style="position: relative; margin-left: 40px; padding-bottom: 10px;">
			<table class="form-table">
			<tr valign="top">
			<th scope="row">
			<?php echo __('Redirect URL 1', 'powerpress'); ?> 
			</th>
			<td>
			<input type="text" style="width: 60%;" name="<?php if( stripos($General['redirect1'], $StatsIntegrationURL) !== false ) echo 'NULL[redirect1]'; else echo 'General[redirect1]'; ?>" value="<?php echo esc_attr($General['redirect1']); ?>" onChange="return CheckRedirect(this);" maxlength="255" <?php if( stripos($General['redirect1'], $StatsIntegrationURL) !== false ) { echo ' readOnly="readOnly"';  $StatsIntegrationURL = false; } ?> /> 
			</td>
			</tr>
			</table>
			<?php if( empty($General['redirect2']) && empty($General['redirect3']) ) { ?>
			<div style="position: absolute;bottom: -2px;left: -40px;" id="powerpress_redirect2_showlink">
				<a href="#" onclick="javascript:document.getElementById('powerpress_redirect2_table').style.display='block';document.getElementById('powerpress_redirect2_showlink').style.display='none';return false;"><?php echo __('Add Another Redirect', 'powerpress'); ?></a>
			</div>
			<?php } ?>
		</div>
	
		
		<div id="powerpress_redirect2_table" style="position: relative;<?php if( empty($General['redirect2']) && empty($General['redirect3']) ) echo 'display:none;'; ?> margin-left: 40px; padding-bottom: 10px;">
			<table class="form-table">
			<tr valign="top">
			<th scope="row">
			<?php echo __('Redirect URL 2', 'powerpress'); ?> 
			</th>
			<td>
			<input type="text"  style="width: 60%;" name="<?php if( stripos($General['redirect2'], $StatsIntegrationURL) !== false ) echo 'NULL[redirect2]'; else echo 'General[redirect2]'; ?>" value="<?php echo esc_attr($General['redirect2']); ?>" onblur="return CheckRedirect(this);" maxlength="255" <?php if( stripos($General['redirect2'], $StatsIntegrationURL) !== false ) { echo ' readOnly="readOnly"';  $StatsIntegrationURL = false; } ?> />
			</td>
			</tr>
			</table>
			<?php if( $General['redirect3'] == '' ) { ?>
			<div style="position: absolute;bottom: -2px;left: -40px;" id="powerpress_redirect3_showlink">
				<a href="#" onclick="javascript:document.getElementById('powerpress_redirect3_table').style.display='block';document.getElementById('powerpress_redirect3_showlink').style.display='none';return false;"><?php echo __('Add Another Redirect', 'powerpress'); ?></a>
			</div>
			<?php } ?>
		</div>

		<div id="powerpress_redirect3_table" style="<?php if( empty($General['redirect3']) ) echo 'display:none;'; ?> margin-left: 40px;">
			<table class="form-table">
			<tr valign="top">
			<th scope="row">
			<?php echo __('Redirect URL 3', 'powerpress'); ?> 
			</th>
			<td>
			<input type="text" style="width: 60%;" name="<?php if( stripos($General['redirect3'], $StatsIntegrationURL) !== false ) echo 'NULL[redirect3]'; else echo 'General[redirect3]'; ?>" value="<?php echo esc_attr($General['redirect3']); ?>" onblur="return CheckRedirect(this);" maxlength="255" <?php if( stripos($General['redirect3'], $StatsIntegrationURL) !== false ) echo ' readOnly="readOnly"'; ?> />
			</td>
			</tr>
			</table>
		</div>
	<style type="text/css">
	#TB_window {
		border: solid 1px #3D517E;
	}
	</style>
	</div>
</div><!-- end blubrry_stats_settings -->
<?php
}
	
function powerpressadmin_appearance($General=false, $Feed = false)
{
	if( $General === false )
		$General = powerpress_get_settings('powerpress_general');
	$General = powerpress_default_settings($General, 'appearance');
	if( !isset($General['player_function']) )
		$General['player_function'] = 1;
	if( !isset($General['player_aggressive']) )
		$General['player_aggressive'] = 0;
	if( !isset($General['new_window_width']) )
		$General['new_window_width'] = '';
	if( !isset($General['new_window_height']) )
		$General['new_window_height'] = '';
	if( !isset($General['player_width']) )
		$General['player_width'] = '';
	if( !isset($General['player_height']) )
		$General['player_height'] = '';
	if( !isset($General['player_width_audio']) )
		$General['player_width_audio'] = '';	
	if( !isset($General['disable_appearance']) )
		$General['disable_appearance'] = false;
	if( !isset($General['subscribe_links']) )
		$General['subscribe_links'] = true;
	if( !isset($General['subscribe_label']) )
		$General['subscribe_label'] = '';	
		
		
	/*
	$Players = array('podcast'=>__('Default Podcast (podcast)', 'powerpress') );
	if( isset($General['custom_feeds']) )
	{
		while( list($podcast_slug, $podcast_title) = each($General['custom_feeds']) )
		{
			if( $podcast_slug == 'podcast' )
				continue;
			$Players[$podcast_slug] = sprintf('%s (%s)', $podcast_title, $podcast_slug);
		}
	}
	*/

?>

<!-- start advanced features -->
<?php if( !empty($General['advanced_mode_2']) ) { ?>
<h3><?php echo __('Website Settings', 'powerpress'); ?></h3>
<div id="enable_presentation_settings">
<table class="form-table">
<tr valign="top">
<th scope="row">&nbsp;	</th> 
<td>
	<ul>
		<li><p><label><input type="radio" name="General[disable_appearance]" value="0" <?php if( $General['disable_appearance'] == 0 ) echo 'checked'; ?> onclick="javascript: jQuery('#presentation_settings').css('display', (this.checked?'block':'none') );" /> <?php echo __('Enable PowerPress Media Players and Links', 'powerpress'); ?></label> (<?php echo __('default', 'powerpress'); ?>)</p>
			<ul><li>
				<p class="description"><?php echo __('PowerPress will add media players and links to your site.', 'powerpress'); ?></p>
			</li></ul>
		</li>
		
		<li><p><label><input type="radio" name="General[disable_appearance]" value="1" <?php if( $General['disable_appearance'] == 1 ) echo 'checked'; ?> onclick="javascript: jQuery('#presentation_settings').css('display', (this.checked?'none':'block') );" /> <?php echo __('Disable PowerPress Media Players and Links', 'powerpress'); ?></label></p>
			<ul><li>
				<p class="description"><?php echo __('PowerPress will <u>not</u> add any media players or media links to your site. PowerPress will only be used to add podcasting support to your feeds.', 'powerpress'); ?></p>
			</li></ul>
		</li>
	</ul>
</td>
</tr>
</table>
</div><!-- end enable_presentation_settings -->
<div id="presentation_settings"<?php if($General['disable_appearance']) echo ' style="display: none;"'; ?>>
<!-- start presentation_settings in advanced mode -->
<!-- end advanced features -->
<?php } ?>

<h3><?php echo __('Blog Posts and Pages', 'powerpress'); ?></h3>


<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo htmlspecialchars(__('Display Media & Links', 'powerpress')); ?></th> 
<td>
	<ul>
		<li><p><label><input type="radio" name="General[display_player]" value="1" <?php if( $General['display_player'] == 1 ) echo 'checked'; ?> /> <?php echo __('Below page content', 'powerpress'); ?></label> (<?php echo __('default', 'powerpress'); ?>)</p>
				<ul><li>
					<p class="description"><?php echo __('Player and media links will appear <u>below</u> your post and page content.', 'powerpress'); ?><p>
				</li></ul>
		</li>
		<li><p><label><input type="radio" name="General[display_player]" value="2" <?php if( $General['display_player'] == 2 ) echo 'checked'; ?> /> <?php echo __('Above page content', 'powerpress'); ?></label></p>
			<ul><li>
				<p class="description"><?php echo __('Player and media links will appear <u>above</u> your post and page content.', 'powerpress'); ?></p>
			</li></ul>
		</li>
		<li>
			<p><label><input type="radio" name="General[display_player]" value="0" <?php if( $General['display_player'] == 0 ) echo 'checked'; ?> /> <?php echo __('Disable', 'powerpress'); ?></label></p>
			<ul><li>
				<p class="description"><?php echo __('Player and media links will <u>NOT</u> appear in your post and page content. Media player and links can be added manually by using the <i>shortcode</i> below.', 'powerpress'); ?></p>
			</li></ul>
		</li>
	</ul>
	<p><input name="General[display_player_excerpt]" type="checkbox" value="1" <?php if( !empty($General['display_player_excerpt']) ) echo 'checked '; ?>/> <?php echo __('Display media / links in:', 'powerpress'); ?> <a href="http://codex.wordpress.org/Template_Tags/the_excerpt" title="<?php echo __('WordPress Excerpts', 'powerpress'); ?>" target="_blank"><?php echo __('WordPress Excerpts', 'powerpress'); ?></a>  (<?php echo __('e.g. search results', 'powerpress'); ?>)</p>
</td>
</tr>
</table>

<?php if( !empty($General['advanced_mode_2']) ) { ?>
<!-- start advanced features -->
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Media Player', 'powerpress'); ?></th>
<td>

<p><label><input type="checkbox" name="PlayerSettings[display_media_player]" value="2" <?php if( $General['player_function'] == 1 || $General['player_function'] == 2 ) echo 'checked '; ?>/> <?php echo __('Display Player', 'powerpress'); ?></label></p>
<?php /* ?>
<p style="margin-left: 35px;"><input type="checkbox" name="General[display_player_disable_mobile]" value="1" <?php if( !empty($General['display_player_disable_mobile']) ) echo 'checked '; ?>/> <?php echo __('Disable Media Player for known mobile devices.', 'powerpress'); ?></p>
<?php */ ?>
<p><?php echo __('Detected mobile and tablet devices use an HTML5 player with a fallback link to download the media.', 'powerpress'); ?></p>
</td>
</tr>
</table>




<table class="form-table">

<tr valign="top">
<th scope="row">

<?php echo __('Media Links', 'powerpress'); ?></th> 
<td>
	<p style="padding-top: 8px;"><label><input type="checkbox" name="PlayerSettings[display_pinw]" value="3" <?php if( $General['player_function'] == 3 || $General['player_function'] == 1 ) echo 'checked '; ?>/> <?php echo __('Display Play in new Window Link', 'powerpress'); ?></label></p>
	
	<p><label><input type="checkbox" name="PlayerSettings[display_download]" value="1" <?php if( $General['podcast_link'] != 0 ) echo 'checked '; ?>/> <?php echo __('Display Download Link', 'powerpress'); ?></label></p>
	
	<p style="margin-left: 35px;"><input type="checkbox" id="display_download_size" name="PlayerSettings[display_download_size]" value="1" <?php if( $General['podcast_link'] == 2 || $General['podcast_link'] == 3 ) echo 'checked'; ?> onclick="if( !this.checked ) { jQuery('#display_download_duration').removeAttr('checked'); }" /> <?php echo __('Include file size', 'powerpress'); ?>
	<input type="checkbox" style="margin-left: 30px;" id="display_download_duration" name="PlayerSettings[display_download_duration]" value="1" <?php if( $General['podcast_link'] == 3 ) echo 'checked'; ?> onclick="if( this.checked ) { jQuery('#display_download_size').attr('checked','checked'); }" /> <?php echo __('Include file size and duration', 'powerpress'); ?></p>
	
	<p><label><input type="checkbox" name="General[podcast_embed]" value="1" <?php if( !empty($General['podcast_embed']) ) echo 'checked '; ?>/> <?php echo __('Display Player Embed Link', 'powerpress'); ?> </label></p>
	<p style="margin-left: 35px;">
		<input type="checkbox" name="General[podcast_embed_in_feed]" value="1" <?php if( !empty($General['podcast_embed_in_feed']) ) echo 'checked'; ?>  /> <?php echo __('Include embed in feeds', 'powerpress'); ?>
	</p>
	<p><?php echo __('Embed option works with the MediaElement.js Media Player for audio and video, Flow Player Classic for audio and HTML5 Video player for video.', 'powerpress'); ?></p>
</td>
</tr>
</table>

<table class="form-table">

<tr valign="top">
<th scope="row">
<?php echo __('Subscribe Links', 'powerpress'); ?> <?php echo powerpressadmin_new(); ?></th> 
<td>
	<p style="padding-top: 8px;"><label><input type="checkbox" name="General[subscribe_links]" value="1" <?php if( $General['subscribe_links'] == 1 ) echo 'checked '; ?>/> 
	<?php echo __('Display subscribe links below player and media links.', 'powerpress'); ?></label></p>
	<ul>
	<li><label for="subscribe_label">Subscribe label: <input type="text" id="subscribe_label" value="<?php echo esc_attr($General['subscribe_label']); ?>" name="General[subscribe_label]" placeholder="Subscribe:" /></label>
	<?php echo __('(leave blank for default)', 'powerpress'); ?>
	</li>
	</ul>

<p style="padding-top:10px;"><input type="checkbox" name="NULL[subscribe_feature_itunes]" value="1" checked disabled /> <label><?php echo __('Subscribe on iTunes', 'powerpress'); ?></label></p>
<div style="margin-left: 24px;">
	<p><?php echo __('Link to your one click iTunes Subscription URL.', 'powerpress'); ?></p>
	
	<p><a href="<?php echo 'https://linkmaker.itunes.apple.com/?q='.urlencode( get_bloginfo('name') ); ?>&amp;media=podcasts" target="_blank"><?php echo __('Find your iTunes Subscription URL', 'powerpress'); ?></a></p>
</div>

<p><input type="checkbox" name="NULL[subscribe_feature_android]" value="1" checked disabled /> <label><?php echo __('Subscribe on Android', 'powerpress'); ?></label> <?php echo powerpressadmin_new(); ?></p>
<div style="margin-left: 24px;">
	<p><?php echo __('Link to your one click Subscribe on Android URL.', 'powerpress'); ?></p>
	<p><a href="http://subscribeonandroid.com/podcasters/" target="_blank"><?php echo __('Learn more about Subscribe on Android', 'powerpress'); ?></a></p>
</div>

<p><input type="hidden" name="General[subscribe_feature_rss]" value="0" /><input type="checkbox" name="General[subscribe_feature_rss]" value="1" id="subscribe_feature_rss" <?php if( !empty($General['subscribe_feature_rss']) || !isset($General['subscribe_feature_rss']) ) echo 'checked '; ?>/> <label for="subscribe_feature_rss"><?php echo __('Subscribe via RSS', 'powerpress'); ?></label></p>
<div style="margin-left: 24px;">
	<p><?php echo __('Link to your podcast RSS feed.', 'powerpress'); ?></p>
</div>

<p><input type="checkbox" id="subscribe_feature_email" name="General[subscribe_feature_email]" value="1" <?php if( !empty($General['subscribe_feature_email']) ) echo 'checked '; ?>/> <label for="subscribe_feature_email"><?php echo __('Subscribe By Email', 'powerpress'); ?></label> <?php echo powerpressadmin_new(); ?></p>
<div style="margin-left: 24px;">
	<p><?php echo __('Link to your one click Subscribe by Email URL.', 'powerpress'); ?></p>
	<p>
	<?php echo __('Subscribe By Email is a service that allows listeners to subscribe to their favorite podcasts by email.', 'powerpress'); ?>
	</p>
	<p><a href="http://subscribebyemail.com/podcasters/" target="_blank"><?php echo __('Learn more about Subscribe by Email', 'powerpress'); ?></a></p>
	<p><?php echo __('Note: Subscribe by Email does not replace newsletters or mailing lists. It is only for podcast syndication.', 'powerpress'); ?>
	</p>
</div>

<p><input type="hidden" name="General[subscribe_feature_gp]" value="0" /><input type="checkbox" id="subscribe_feature_gp" name="General[subscribe_feature_gp]" value="1" <?php if( !empty($General['subscribe_feature_gp']) ) echo 'checked '; ?>/> <label for="subscribe_feature_gp"><?php echo __('Subscribe on Google Play', 'powerpress'); ?></label> <?php echo powerpressadmin_new(); ?></p>

<p><input type="hidden" name="General[subscribe_feature_stitcher]" value="0" /><input type="checkbox" id="subscribe_feature_stitcher" name="General[subscribe_feature_stitcher]" value="1" <?php if( !empty($General['subscribe_feature_stitcher']) ) echo 'checked '; ?>/> <label for="subscribe_feature_stitcher"><?php echo __('Subscribe on Stitcher', 'powerpress'); ?></label> <?php echo powerpressadmin_new(); ?></p>

<p><input type="hidden" name="General[subscribe_feature_tunein]" value="0" /><input type="checkbox" id="subscribe_feature_tunein" name="General[subscribe_feature_tunein]" value="1" <?php if( !empty($General['subscribe_feature_tunein']) ) echo 'checked '; ?>/> <label for="subscribe_feature_tunein"><?php echo __('Subscribe on TuneIn', 'powerpress'); ?></label> <?php echo powerpressadmin_new(); ?></p>

</td>
</tr>
</table>

<?php powerpressadmin_settings_tab_appearance($General, $Feed, false); ?>
<!-- end advanced features -->
<?php } ?>


<table class="form-table">
<tr valign="top">
<th scope="row" style="background-image: url(../wp-includes/images/smilies/icon_exclaim.gif); background-position: 10px 10px; background-repeat: no-repeat; ">

<div style="margin-left: 24px;"><?php echo __('Having Issues?', 'powerpress'); ?></div></th>
<td>
	<select name="General[player_aggressive]" class="bpp_input_med">
<?php
$linkoptions = array(0=>__('No, everything is working', 'powerpress'),
		1=>__('Yes, please try to fix', 'powerpress'),
		2=>__('Yes, alternative fix', 'powerpress'),
		3=>__('Yes, excluding excerpts', 'powerpress'),
		4=>__('Yes, wp_head check', 'powerpress') );
	
while( list($value,$desc) = each($linkoptions) )
	echo "\t<option value=\"$value\"". ($General['player_aggressive']==$value?' selected':''). ">$desc</option>\n";
	
?>
</select> <a href="http://create.blubrry.com/resources/powerpress/powerpress-settings/media-appearance/resolving-plugin-theme-conflict-issues/" target="_blank"><?php echo __('Learn More', 'powerpress'); ?></a>
<p style="margin-top: 5px;">
	<?php echo __('Use this option if you are having problems with the players not appearing on some or all of your pages.', 'powerpress'); ?>
</p>
<?php if( !empty($General['advanced_mode_2']) ) { ?>
<p style="margin-top: 20px; margin-bottom:0;">
	<?php echo __('If the above option fixes the player issues, then you most likely have a conflicting theme or plugin activated. You can verify your theme is not causing the problem by testing your site using the latest default WordPress theme (twentyfourteen). For plugins, disable them one by one until the player re-appears, which indicates the last plugin deactivated caused the conflict.', 'powerpress'); ?>
</p>
<?php } ?>
</td>
</tr>
</table>

<?php if( !empty($General['advanced_mode_2']) ) { ?>
<!-- start advanced features -->
<div id="new_window_settings" style="display: <?php echo ( $General['player_function']==1 || $General['player_function']==3 ?'block':'none'); ?>">
<h3><?php echo __('Play in New Window Settings', 'powerpress'); ?></h3>
<table class="form-table">

<tr valign="top">
<th scope="row">
<?php echo __('New Window Width', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[new_window_width]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo esc_attr($General['new_window_width']); ?>" maxlength="4" />
<?php echo __('Width of new window (leave blank for 420 default)', 'powerpress'); ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('New Window Height', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[new_window_height]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo esc_attr($General['new_window_height']); ?>" maxlength="4" />
<?php echo __('Height of new window (leave blank for 240 default)', 'powerpress'); ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
&nbsp;
</th>
<td>

	<p style="margin: 8px 0 0 0;">
		<label><input type="checkbox" name="General[new_window_nofactor]" value="1" <?php if( !empty($General['new_window_nofactor']) ) echo 'checked'; ?>  /> <?php echo __('Do not factor in scroll bars', 'powerpress'); ?></label>
	</p>
	<div style="margin: 0 0 0 20px;"><?php echo __('By default, PowerPress adds to the width and height above to compensate for possible vertical and horizontal scroll bars. Check this option if you do not want PowerPress to compensate for browser scroll bars.', 'powerpress'); ?></div>
</td>

</table>
</div><!-- end new_window_settings -->

</div><!-- end presentation_settings in advanced mode -->
<!-- end presentation settings -->
<!-- end advanced features -->
<?php } ?>
<?php  
} // End powerpress_admin_appearance()


function powerpressadmin_welcome($GeneralSettings)
{
?>
<div>
	<div class="powerpress-welcome-news">
		<h2><?php echo __('Blubrry PowerPress and Community Podcast', 'powerpress'); ?></h2>
		<?php powerpressadmin_community_news(); ?>
		<p style="margin-bottom: 0; font-size: 85%;">
			<input type="checkbox" name="General[disable_dashboard_news]" value="1" <?php echo (empty($GeneralSettings['disable_dashboard_news'])?'':'checked'); ?> /> <?php echo __('Remove from dashboard', 'powerpress'); ?>
		</p>
	</div>
	<div class="powerpress-welcome-highlighted">
		<div>
			<h2><?php echo __('Highlighted Topics', 'powerpress'); ?></h2>
			<?php powerpressadmin_community_highlighted(); ?>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php
} // End powerpressadmin_welcome()

function powerpressadmin_edit_funding($FeedSettings = false, $feed_slug='podcast', $cat_ID=false)
{
	if( !isset($FeedSettings['donate_link']) )
		$FeedSettings['donate_link'] = 0;
	if( !isset($FeedSettings['donate_url']) )
		$FeedSettings['donate_url'] = '';
	if( !isset($FeedSettings['donate_label']) )
		$FeedSettings['donate_label'] = '';
?>
<!--  Donate link and label -->
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Donate Link', 'powerpress'); ?> <?php echo powerpressadmin_new(); ?></th> 
<td>
	<p style="padding-top: 8px;"><label for="donate_link"><input type="checkbox" id="donate_link" name="Feed[donate_link]" value="1" <?php if( $FeedSettings['donate_link'] == 1 ) echo 'checked '; ?>/> 
	<?php echo __('Syndicate a donate link with your podcast. Create your own croudfunding page with PayPal donate buttons, or link to a service such as Patreon.', 'powerpress'); ?></label></p>
	<ul>
	<li><label for="donate_url" style="width: 100px; display:inline-block; text-align: right;">Donate URL:</label> <input type="text" id="donate_url" value="<?php echo esc_attr($FeedSettings['donate_url']); ?>" name="Feed[donate_url]" style="width:50%; max-width: 300px;" />
	</li>
	<li><label for="donate_label" style="width: 100px; display:inline-block; text-align: right;">Donate label:</label> <input type="text" id="donate_label" value="<?php echo esc_attr($FeedSettings['donate_label']); ?>" name="Feed[donate_label]" style="width: 50%; max-width: 300px;" />
	<?php echo __('(optional)', 'powerpress'); ?>
	</li>
	</ul>
	<p><a href="http://create.blubrry.com/resources/powerpress/advanced-tools-and-options/syndicating-a-donate-link-in-your-podcast/" target="_blank"><?php echo __('Learn more about syndicating donate links for podcasting', 'powerpress'); ?></a></p>
</td>
</tr>
</table>
<?php
}

function powerpressadmin_edit_tv($FeedSettings = false, $feed_slug='podcast', $cat_ID=false)
{
	if( !isset($FeedSettings['parental_rating']) )
		$FeedSettings['parental_rating'] = '';

?>
<h3><?php echo __('T.V. Settings', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
 <?php echo __('Parental Rating', 'powerpress'); ?>  </th>
<td>
	<p><?php echo sprintf(__('A parental rating is used to display your content on %s applications available on Internet connected TV\'s. The TV Parental Rating applies to both audio and video media.', 'powerpress'), '<strong><a href="http://www.blubrry.com/roku_blubrry/" target="_blank">Blubrry</a></strong>'); ?></p>
<?php
	$Ratings = array(''=>__('No rating specified', 'powerpress'),
			'TV-Y'=>__('Children of all ages', 'powerpress'),
			'TV-Y7'=>__('Children 7 years and older', 'powerpress'),
			'TV-Y7-FV'=>__('Children 7 years and older [fantasy violence]', 'powerpress'),
			'TV-G'=>__('General audience', 'powerpress'),
			'TV-PG'=>__('Parental guidance suggested', 'powerpress'),
			'TV-14'=>__('May be unsuitable for children under 14 years of age', 'powerpress'),
			'TV-MA'=>__('Mature audience - may be unsuitable for children under 17', 'powerpress')
		);
	$RatingsTips = array(''=>'',
				'TV-Y'=>__('Whether animated or live-action, the themes and elements in this program are specifically designed for a very young audience, including children from ages 2-6. These programs are not expected to frighten younger children.  Examples of programs issued this rating include Sesame Street, Barney & Friends, Dora the Explorer, Go, Diego, Go! and The Backyardigans.', 'powerpress'),
				'TV-Y7'=>__('These shows may or may not be appropriate for some children under the age of 7. This rating may include crude, suggestive humor, mild fantasy violence, or content considered too scary or controversial to be shown to children under seven. Examples include Foster\'s Home for Imaginary Friends, Johnny Test, and SpongeBob SquarePants.', 'powerpress'),
				'TV-Y7-FV'=>__('When a show has noticeably more fantasy violence, it is assigned the TV-Y7-FV rating. Action-adventure shows such Pokemon series and the Power Rangers series are assigned a TV-Y7-FV rating.', 'powerpress'),
				'TV-G'=>__('Although this rating does not signify a program designed specifically for children, most parents may let younger children watch this program unattended. It contains little or no violence, no strong language and little or no sexual dialogue or situation. Networks that air informational, how-to content, or generally inoffensive content.', 'powerpress'),
				'TV-PG'=>__('This rating signifies that the program may be unsuitable for younger children without the guidance of a parent. Many parents may want to watch it with their younger children. Various game shows and most reality shows are rated TV-PG for their suggestive dialog, suggestive humor, and/or coarse language. Some prime-time sitcoms such as Everybody Loves Raymond, Fresh Prince of Bel-Air, The Simpsons, Futurama, and Seinfeld  usually air with a TV-PG rating.', 'powerpress'),
				'TV-14'=>__('Parents are strongly urged to exercise greater care in monitoring this program and are cautioned against letting children of any age watch unattended. This rating may be accompanied by any of the following sub-ratings:', 'powerpress'),
				'TV-MA'=>__('A TV-MA rating means the program may be unsuitable for those below 17. The program may contain extreme graphic violence, strong profanity, overtly sexual dialogue, very coarse language, nudity and/or strong sexual content. The Sopranos is a popular example.', 'powerpress')
		);
			
	
	while( list($rating,$title) = each($Ratings) )
	{
		$tip = $RatingsTips[ $rating ];
?>
	<div style="margin-bottom: 10px;"><label><input type="radio" name="Feed[parental_rating]" value="<?php echo $rating; ?>" <?php if( $FeedSettings['parental_rating'] == $rating) echo 'checked'; ?> /> <?php if( $rating ) { ?><strong><?php echo $rating; ?></strong><?php } else { ?><strong><?php echo htmlspecialchars($title); ?></strong><?php } ?></label>
	<?php if( $rating ) { ?>  <span style="margin-left: 8px;"><a href="#" class="powerpress-parental-rating-tip" id="rating_tip_<?php echo $rating; ?>"><?php echo htmlspecialchars($title); ?></a></span><?php } ?>
	<p style="margin: 5px 50px; display: none;" id="rating_tip_<?php echo $rating; ?>_p" class="powerpress-parental-rating-tip-p"><?php echo htmlspecialchars($tip); ?></p>
	</div>
	<?php
	}
?>
</td>
</tr>
</table>
<?php
}

function powerpressadmin_edit_artwork($FeedSettings, $General)
{
	$SupportUploads = powerpressadmin_support_uploads();
?>
<h3><?php echo __('Artwork and Images', 'powerpress'); ?></h3>

<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Image', 'powerpress'); ?> 
<span class="powerpress-required"><?php echo __('Required', 'powerpress'); ?></span>
<?php echo powerpressadmin_updated( __('recommended size changed February, 2016', 'powerpress') ); ?>
</th>
<td>
<input type="text" id="itunes_image" name="Feed[itunes_image]" style="width: 60%; margin-top: 10px;" value="<?php echo esc_attr( !empty($FeedSettings['itunes_image'])? $FeedSettings['itunes_image']:''); ?>" maxlength="255" />
<a href="#" onclick="javascript: window.open( document.getElementById('itunes_image').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('iTunes image must be at least 1400 x 1400 pixels in .jpg or .png format. iTunes image must not exceed 3000 x 3000 pixels and must use RGB color space.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/itunes.jpg
 </p>

<p><strong><?php echo __('A square 3000 x 3000 pixel image in .jpg format is recommended.', 'powerpress'); ?></strong></p>

<p>
<?php echo __('This image is for your listing on the iTunes podcast directory and may also be used by other directories like Blubrry. It is not the artwork that is displayed during episode playback. That artwork needs to be saved into the media file in the form of tags (ID3 tags for mp3) following the production of the media file.', 'powerpress'); ?>
</p>

<p class="description"><?php echo __('Note: If you change the iTunes image without changing the file name it may take some time (days or even months) for iTunes to update the image in the iTunes Podcast Directory.', 'powerpress'); ?> 
<?php echo sprintf( __('Please contact %s if you are having issues with your image changes not appearing in iTunes.', 'powerpress'), '<a href="http://www.apple.com/support/itunes/contact/">'. __('iTunes Support', 'powerpress') .'</a>'); ?></p>
<?php if( $SupportUploads ) { ?>

<p><label class="powerpress-normal-font"><input name="itunes_image_checkbox" type="checkbox" onchange="powerpress_show_field('itunes_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?></label> &nbsp; 
	<span style="font-size:85%;">(<?php echo __('Using this option should update your image on iTunes within 24 hours', 'powerpress'); ?>)</span>
</p>
<div style="display:none" id="itunes_image_upload">
	<label for="itunes_image_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" id="itunes_image_file" name="itunes_image_file"  /><br />
	<?php if( !empty($General['advanced_mode_2']) ) { ?>
	<div style="margin-left: 85px;"><label class="powerpress-normal-font"><input name="itunes_image_checkbox_as_rss" type="checkbox" value="1" onchange="powerpress_show_field('rss_image_upload_container', !this.checked)" /> <?php echo __('Also use as RSS image', 'powerpress'); ?></label></div>
	<?php } else { ?>
	<input type="hidden" name="itunes_image_checkbox_as_rss" value="1" />
	<?php }  ?>
</div>
<?php } ?>
</td>
</tr>
</table>


<?php if( !empty($General['advanced_mode_2']) ) { ?>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Episode Image', 'powerpress'); ?></th>
<td>

<p style="padding-top: 10px;"><label><input type="checkbox" name="Feed[episode_itunes_image]" value="1" <?php if( !empty($FeedSettings['episode_itunes_image']) ) echo 'checked '; ?>/> <?php echo __('Use iTunes image above', 'powerpress'); ?></label></p>
<p><?php echo __('Use the program iTunes image above as your iTunes episode image.', 'powerpress'); ?></p>
<p class="description"><?php echo __('NOTE: You must still save artwork into your media files to guarantee your artwork is displayed during playback.', 'powerpress'); ?></p>
</td>
</tr>

<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Google Play Image', 'powerpress'); ?>  <?php echo powerpressadmin_new(); ?>
</th>
<td>
<input type="text" id="googleplay_image" name="Feed[googleplay_image]" style="width: 60%; margin-top: 10px;" value="<?php echo esc_attr( !empty($FeedSettings['googleplay_image'])? $FeedSettings['googleplay_image']:''); ?>" maxlength="255" />
<a href="#" onclick="javascript: window.open( document.getElementById('googleplay_image').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('Google Play image must be at least 1200 x 1200 pixels in .jpg or .png format to be eligible for featuring. Image must not exceed 7000 x 7000 pixels.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/googleplay.jpg
 </p>

<p><strong><?php echo __('Leave this setting blank to use the iTunes image (recommended).', 'powerpress'); ?></strong></p>

<?php if( $SupportUploads ) { ?>

<p><label class="powerpress-normal-font"><input name="googleplay_image_checkbox" type="checkbox" onchange="powerpress_show_field('googleplay_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?></label> &nbsp; 
	<span style="font-size:85%;">(<?php echo __('Using this option should update your image on Google Play Music within 24 hours', 'powerpress'); ?>)</span>
</p>
<div style="display:none" id="googleplay_image_upload">
	<label for="googleplay_image_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" id="googleplay_image_file" name="googleplay_image_file"  /><br />
</div>
<?php } ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('RSS2 Image', 'powerpress'); ?> <br />
<span style="font-size: 85%; margin-left: 5px;"><?php echo __('Recommendation: Use iTunes image', 'powerpress'); ?></span>
</th>
<td>
<input type="text" id="rss2_image" name="Feed[rss2_image]" style="width: 60%; margin-top: 10px;" value="<?php echo esc_attr( !empty($FeedSettings['rss2_image'])? $FeedSettings['rss2_image']:''); ?>" maxlength="255" />
<a href="#" onclick="javascript: window.open( document.getElementById('rss2_image').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('Place the URL to the RSS image above.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?> http://mysite.com/images/rss.jpg</p>

<!--
<p><?php echo __('RSS image should be at least 88 pixels wide and at least 31 pixels high in either .gif, .jpg and .png format.', 'powerpress'); ?></p>
<p><strong><?php echo __('A square image that is 300 x 300 pixel or larger in .jpg format is recommended.', 'powerpress'); ?></strong></p>
-->

<?php if( $SupportUploads ) { ?>
<div id="rss_image_upload_container">
<p><input name="rss2_image_checkbox" type="checkbox" onchange="powerpress_show_field('rss_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?></p>
<div style="display:none" id="rss_image_upload">
	<label for="rss2_image"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="rss2_image_file"  />
</div>
</div>
<?php } ?>
</td>
</tr>
</table>
<?php
	}
}


function powerpressadmin_edit_destinations($FeedSettings, $General, $FeedAttribs)
{
	require_once( dirname(__FILE__).'/views/settings_tab_destinations.php' );
}

