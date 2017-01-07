<?php


function powerpressadmin_default_steps($Step = 0, $Heading = true, $ThisPage=true)
{
	if( isset($_GET['pp-step']) )
		$Step = $_GET['pp-step'];
?>
<div id="pp-getting-started-box">
	<?php echo ($Heading?'<h2>'. __('Start your podcast in 3 easy steps...', 'powerpress') .'</h2>':''); ?>
	<link rel="stylesheet" type="text/css" href="<?php echo powerpress_get_root_url(); ?>css/steps.css">
	<ul id="powerpress-steps">
	<?php if( $ThisPage ) { ?>
		<li class="pp-step-1<?php echo ($Step >= 0? ' pp-step-active':''); ?>"><h3 class="<?php echo ($Step >= 1? 'pp-step-h-completed':''); ?>"><?php echo __('Fill out the settings on this page', 'powerpress'); ?></h3></li>
	<?php } else { ?>
		<li class="pp-step-1<?php echo ($Step >= 0? ' pp-step-active':''); ?>"><a href=""><h3 class="<?php echo ($Step >= 1? 'pp-step-h-completed':''); ?>"><?php echo __('Fill out the podcast settings', 'powerpress'); ?></a></h3></li>
	<?php } ?>
		<li class="pp-step-2<?php echo ($Step >= 1? ' pp-step-active':''); ?>">
			<h3 class="<?php echo ($Step >= 2? 'pp-step-h-completed':''); ?>"><a href="<?php echo admin_url( 'post-new.php' ); ?>"><?php echo __('Create a blog post with an episode', 'powerpress'); ?></a></h3>
			<p><a href="http://create.blubrry.com/resources/powerpress/using-powerpress/creating-your-first-episode-with-powerpress/" target="_blank"><?php echo __('Need help?', 'powerpress'); ?></a>
		</li>
		<li class="pp-step-3<?php echo ($Step >= 2? ' pp-step-active':''); ?>"><h3 class="<?php echo ($Step >= 3? 'pp-step-h-completed':''); ?>"><a href="http://create.blubrry.com/manual/podcast-promotion/submit-podcast-to-itunes/?podcast-feed=<?php echo urlencode(get_feed_link('podcast')); ?>" target="_blank"><?php echo __('Submit your podcast to iTunes and other podcast directories', 'powerpress'); ?></a></h3></li>
	</ul>
</div><!-- end pp-getting-started-box -->
<?php
}

function powerpress_admin_defaults()
{
	$FeedAttribs = array('type'=>'general', 'feed_slug'=>'', 'category_id'=>0, 'term_taxonomy_id'=>0, 'term_id'=>0, 'taxonomy_type'=>'', 'post_type'=>'');
	
	$General = powerpress_get_settings('powerpress_general');
	$General = powerpress_default_settings($General, 'basic');
	
	$FeedSettings = powerpress_get_settings('powerpress_feed');
	$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed');
	
	// Make sure variables are set
	if( empty($FeedSettings['title']) )
		$FeedSettings['title'] = '';
	
	$Step = 0;
	if( !empty($FeedSettings['itunes_cat_1']) && !empty($FeedSettings['email']) && !empty($FeedSettings['itunes_image']) )
		$Step = 1;
	
	$episode_total = 0;
	if( $Step == 1 )
	{
		$episode_total = powerpress_admin_episodes_per_feed('podcast');
		if( $episode_total > 0 )
			$Step = 2;
	}

	if( $Step == 2 && !empty($FeedSettings['itunes_url']) )
		$Step = 3;
		
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


jQuery(document).ready(function($) {
	jQuery('#powerpress_advanced_mode_button').click( function(event) {
		event.preventDefault();
		jQuery('#powerpress_advanced_mode').val('1');
		jQuery(this).closest("form").submit();
	} );
} );
//-->
</script>
<input type="hidden" name="action" value="powerpress-save-defaults" />
<input type="hidden" id="powerpress_advanced_mode" name="General[advanced_mode_2]" value="0" />

<div id="powerpress_admin_header">
<h2><?php echo __('Blubrry PowerPress Settings', 'powerpress'); ?></h2> 
<span class="powerpress-mode"><?php echo __('Simple Mode', 'powerpress'); ?>
	&nbsp; <a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_basic.php&amp;mode=advanced"); ?>" id="powerpress_advanced_mode_button" class="button-primary  button-blubrry"><?php echo __('Switch to Advanced Mode', 'powerpress'); ?></a>
</span>
</div>

<?php
	
	powerpressadmin_default_steps($Step);
	
	if( $MultiSiteServiceSettings && defined('POWERPRESS_MULTISITE_VERSION') )
	{
		PowerPressMultiSitePlugin::edit_blubrry_services($General);
	}
	else
	{
		powerpressadmin_edit_blubrry_services($General);
	}
?>
<h3><?php echo __('Podcast Settings', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Program Title', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[title]" style="width: 60%;"  value="<?php echo esc_attr($FeedSettings['title']); ?>" maxlength="255" />
(<?php echo __('leave blank to use blog title', 'powerpress'); ?>)
<p><?php echo __('Blog title:', 'powerpress') .' '. get_bloginfo_rss('name'); ?></p>
</td>
</tr>
</table>
<?php
	if( $Step > 1 ) { // Only display if we have episdoes in the feed!
		// TODO: Need to include the settings_tab_destinations.php but only the iTunes option to keep things simple
	}
	// iTunes settings (in simple mode of course)
	powerpressadmin_edit_itunes_feed($FeedSettings, $General, $FeedAttribs);
	
	powerpressadmin_edit_artwork($FeedSettings, $General);
	powerpressadmin_appearance($General, $FeedSettings);
	powerpressadmin_advanced_options($General);
}


?>