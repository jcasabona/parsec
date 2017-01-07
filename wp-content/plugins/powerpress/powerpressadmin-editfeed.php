<?php

if( !function_exists('add_action') )
	die("access denied.");
	
function powerpress_categories_strict($Categories, $Selected)
{
	$Return = $Categories;
	$StrictArray = array('01-00', '02-00', '04-00', '05-00', '06-00', '07-00', '11-00', '12-00', '13-00', '14-00', '15-00');
	while( list($index,$remove) = each($StrictArray) )
	{
		if( $Selected == $remove )
			continue;
		unset($Return[ $remove ]);
	}
	return $Return;
}

function powerpress_languages()
{
	// List copied from PodPress:
	$langs = array();
	$langs['af'] = __('Afrikaans', 'powerpress');
	$langs['sq'] = __('Albanian', 'powerpress');
	$langs['ar'] = __('Arabic', 'powerpress');
	$langs['ar-sa'] = __('Arabic (Saudi Arabia)', 'powerpress');
	$langs['ar-eg'] = __('Arabic (Egypt)', 'powerpress');
	$langs['ar-dz'] = __('Arabic (Algeria)', 'powerpress');
	$langs['ar-tn'] = __('Arabic (Tunisia)', 'powerpress');
	$langs['ar-ye'] = __('Arabic (Yemen)', 'powerpress');
	$langs['ar-jo'] = __('Arabic (Jordan)', 'powerpress');
	$langs['ar-kw'] = __('Arabic (Kuwait)', 'powerpress');
	$langs['ar-bh'] = __('Arabic (Bahrain)', 'powerpress');
	$langs['eu'] = __('Basque', 'powerpress');
	$langs['be'] = __('Belarusian', 'powerpress');
	$langs['bg'] = __('Bulgarian', 'powerpress');
	$langs['ca'] = __('Catalan', 'powerpress');
	$langs['zh-cn'] = __('Chinese (Simplified)', 'powerpress');
	$langs['zh-tw'] = __('Chinese (Traditional)', 'powerpress');
	$langs['hr'] = __('Croatian', 'powerpress');
	$langs['cs'] = __('Czech', 'powerpress');
	$langs['da'] = __('Danish', 'powerpress');
	$langs['nl'] = __('Dutch', 'powerpress');
	$langs['nl-be'] = __('Dutch (Belgium)', 'powerpress');
	$langs['nl-nl'] = __('Dutch (Netherlands)', 'powerpress');
	$langs['en'] = __('English', 'powerpress');
	$langs['en-au'] = __('English (Australia)', 'powerpress');
	$langs['en-bz'] = __('English (Belize)', 'powerpress');
	$langs['en-ca'] = __('English (Canada)', 'powerpress');
	$langs['en-ie'] = __('English (Ireland)', 'powerpress');
	$langs['en-jm'] = __('English (Jamaica)', 'powerpress');
	$langs['en-nz'] = __('English (New Zealand)', 'powerpress');
	$langs['en-ph'] = __('English (Phillipines)', 'powerpress');
	$langs['en-za'] = __('English (South Africa)', 'powerpress');
	$langs['en-tt'] = __('English (Trinidad)', 'powerpress');
	$langs['en-gb'] = __('English (United Kingdom)', 'powerpress');
	$langs['en-us'] = __('English (United States)', 'powerpress');
	$langs['en-zw'] = __('English (Zimbabwe)', 'powerpress');
	$langs['et'] = __('Estonian', 'powerpress');
	$langs['fo'] = __('Faeroese', 'powerpress');
	$langs['fi'] = __('Finnish', 'powerpress');
	$langs['fr'] = __('French', 'powerpress');
	$langs['fr-be'] = __('French (Belgium)', 'powerpress');
	$langs['fr-ca'] = __('French (Canada)', 'powerpress');
	$langs['fr-fr'] = __('French (France)', 'powerpress');
	$langs['fr-lu'] = __('French (Luxembourg)', 'powerpress');
	$langs['fr-mc'] = __('French (Monaco)', 'powerpress');
	$langs['fr-ch'] = __('French (Switzerland)', 'powerpress');
	$langs['gl'] = __('Galician', 'powerpress');
	$langs['gd'] = __('Gaelic', 'powerpress');
	$langs['de'] = __('German', 'powerpress');
	$langs['de-at'] = __('German (Austria)', 'powerpress');
	$langs['de-de'] = __('German (Germany)', 'powerpress');
	$langs['de-li'] = __('German (Liechtenstein)', 'powerpress');
	$langs['de-lu'] = __('German (Luxembourg)', 'powerpress');
	$langs['de-ch'] = __('German (Switzerland)', 'powerpress');
	$langs['el'] = __('Greek', 'powerpress');
	$langs['haw'] = __('Hawaiian', 'powerpress');
	$langs['he_IL'] = __('Hebrew', 'powerpress');
	$langs['hu'] = __('Hungarian', 'powerpress');
	$langs['is'] = __('Icelandic', 'powerpress');
	$langs['in'] = __('Indonesian', 'powerpress');
	$langs['ga'] = __('Irish', 'powerpress');
	$langs['it'] = __('Italian', 'powerpress');
	$langs['it-it'] = __('Italian (Italy)', 'powerpress');
	$langs['it-ch'] = __('Italian (Switzerland)', 'powerpress');
	$langs['ja'] = __('Japanese', 'powerpress');
	$langs['ko'] = __('Korean', 'powerpress');
	$langs['mk'] = __('Macedonian', 'powerpress');
	$langs['no'] = __('Norwegian', 'powerpress');
	$langs['pl'] = __('Polish', 'powerpress');
	$langs['pt'] = __('Portuguese', 'powerpress');
	$langs['pt-br'] = __('Portuguese (Brazil)', 'powerpress');
	$langs['pt-pt'] = __('Portuguese (Portugal)', 'powerpress');
	$langs['ro'] = __('Romanian', 'powerpress');
	$langs['ro-mo'] = __('Romanian (Moldova)', 'powerpress');
	$langs['ro-ro'] = __('Romanian (Romania)', 'powerpress');
	$langs['ru'] = __('Russian', 'powerpress');
	$langs['ru-mo'] = __('Russian (Moldova)', 'powerpress');
	$langs['ru-ru'] = __('Russian (Russia)', 'powerpress');
	$langs['sr'] = __('Serbian', 'powerpress');
	$langs['sk'] = __('Slovak', 'powerpress');
	$langs['sl'] = __('Slovenian', 'powerpress');
	$langs['es'] = __('Spanish', 'powerpress');
	$langs['es-ar'] = __('Spanish (Argentina)', 'powerpress');
	$langs['es-bo'] = __('Spanish (Bolivia)', 'powerpress');
	$langs['es-cl'] = __('Spanish (Chile)', 'powerpress');
	$langs['es-co'] = __('Spanish (Colombia)', 'powerpress');
	$langs['es-cr'] = __('Spanish (Costa Rica)', 'powerpress');
	$langs['es-do'] = __('Spanish (Dominican Republic)', 'powerpress');
	$langs['es-ec'] = __('Spanish (Ecuador)', 'powerpress');
	$langs['es-sv'] = __('Spanish (El Salvador)', 'powerpress');
	$langs['es-gt'] = __('Spanish (Guatemala)', 'powerpress');
	$langs['es-hn'] = __('Spanish (Honduras)', 'powerpress');
	$langs['es-mx'] = __('Spanish (Mexico)', 'powerpress');
	$langs['es-ni'] = __('Spanish (Nicaragua)', 'powerpress');
	$langs['es-pa'] = __('Spanish (Panama)', 'powerpress');
	$langs['es-py'] = __('Spanish (Paraguay)', 'powerpress');
	$langs['es-pe'] = __('Spanish (Peru)', 'powerpress');
	$langs['es-pr'] = __('Spanish (Puerto Rico)', 'powerpress');
	$langs['es-es'] = __('Spanish (Spain)', 'powerpress');
	$langs['es-uy'] = __('Spanish (Uruguay)', 'powerpress');
	$langs['es-ve'] = __('Spanish (Venezuela)', 'powerpress');
	$langs['sv'] = __('Swedish', 'powerpress');
	$langs['sv-fi'] = __('Swedish (Finland)', 'powerpress');
	$langs['sv-se'] = __('Swedish (Sweden)', 'powerpress');
	$langs['tr'] = __('Turkish', 'powerpress');
	$langs['uk'] = __('Ukranian', 'powerpress');
	return $langs;
}

function powerpress_admin_capabilities()
{
	global $wp_roles;
	
	$capnames = array();
	// Get Role List
	foreach($wp_roles->role_objects as $key => $role) {
		foreach($role->capabilities as $cap => $grant) {
			$capnames[$cap] = ucwords( str_replace('_', ' ',  $cap) );
		}
	}

	$capnames = apply_filters( 'powerpress_admin_capabilities', array_unique($capnames) );
	
	$remove_keys = array('level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10');
	while( list($null,$key) = each($remove_keys) )
		unset($capnames[ $key ]);
	asort($capnames);
	return $capnames;
}


// powerpressadmin_editfeed.php
function powerpress_admin_editfeed($type='', $type_value = '', $feed_slug = false)
{
	$SupportUploads = powerpressadmin_support_uploads();
	$General = powerpress_get_settings('powerpress_general');
	$FeedAttribs = array('type'=>$type, 'feed_slug'=>'', 'category_id'=>0, 'term_taxonomy_id'=>0, 'term_id'=>0, 'taxonomy_type'=>'', 'post_type'=>'');
	$cat_ID = false; $term_taxonomy_id = false;

	
	$FeedTitle = __('Feed Settings', 'powerpress');
	
	switch( $type )
	{
		case 'channel': {
			$feed_slug = $type_value;
			$FeedAttribs['feed_slug'] = $type_value;
			$FeedSettings = powerpress_get_settings('powerpress_feed_'.$feed_slug);
			if( !$FeedSettings )
			{
				$FeedSettings = array();
				$FeedSettings['title'] = '';
				if( !empty($General['custom_feeds'][$feed_slug]) )
					$FeedSettings['title'] = $General['custom_feeds'][$feed_slug];
			}
			$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed_custom');
			
			if( !isset($General['custom_feeds'][$feed_slug]) )
				$General['custom_feeds'][$feed_slug] = __('Podcast (default)', 'powerpress');
				
			$FeedTitle = sprintf( 'Podcast Settings for Channel: %s', htmlspecialchars($General['custom_feeds'][$feed_slug]) );
			echo sprintf('<input type="hidden" name="feed_slug" value="%s" />', $feed_slug);
			echo '<input type="hidden" name="action" value="powerpress-save-channel" />';
			
		}; break;
		case 'category': {
			$cat_ID = $type_value; 
			$FeedAttribs['category_id'] = $type_value;
			$FeedSettings = powerpress_get_settings('powerpress_cat_feed_'.$cat_ID);
			$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed_custom');
			
			$category = get_category_to_edit($cat_ID);
			$FeedTitle = sprintf( __('Podcast Settings for Category: %s', 'powerpress'), htmlspecialchars($category->name) );
			echo sprintf('<input type="hidden" name="cat" value="%s" />', $cat_ID);
			echo '<input type="hidden" name="action" value="powerpress-save-category" />';
			
		}; break;
		case 'ttid': {
			$term_taxonomy_id = $type_value;
			$FeedAttribs['term_taxonomy_id'] = $type_value;
			$FeedSettings = powerpress_get_settings('powerpress_taxonomy_'.$term_taxonomy_id);
			$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed_custom');
			
			global $wpdb;
			$term_info = $wpdb->get_results("SELECT term_id, taxonomy FROM $wpdb->term_taxonomy WHERE term_taxonomy_id = $term_taxonomy_id",  ARRAY_A);
			if( !empty( $term_info[0]['term_id']) ) {
				$term_ID = $term_info[0]['term_id'];
				$taxonomy_type = $term_info[0]['taxonomy'];
				$FeedAttribs['term_id'] = $term_ID;
				$FeedAttribs['taxonomy_type'] = $taxonomy_type;

				$term_object = get_term_to_edit($term_ID, $taxonomy_type);
				$FeedTitle = sprintf( __('Podcast Settings for Taxonomy Term: %s', 'powerpress'), htmlspecialchars($term_object->name));
			}
			else
			{
				$FeedTitle = sprintf( __('Podcast Settings for Taxonomy Term: %s', 'powerpress'), 'Term ID '.htmlspecialchars($term_taxonomy_id));
			}
			echo sprintf('<input type="hidden" name="ttid" value="%s" />', $term_taxonomy_id);
			echo '<input type="hidden" name="action" value="powerpress-save-ttid" />';
			
		}; break;
		case 'post_type': {
			
			$FeedAttribs['post_type'] = $type_value;
			$FeedAttribs['feed_slug'] = $feed_slug;
			$FeedSettingsArray = powerpress_get_settings('powerpress_posttype_'.$FeedAttribs['post_type']);
			if( !is_array($FeedSettingsArray[ $feed_slug ]) )
				$FeedSettingsArray[ $feed_slug ] = array();
			$FeedSettings = powerpress_default_settings($FeedSettingsArray[ $feed_slug ], 'editfeed_custom');
			
			//$category = get_category_to_edit($cat_ID);
			$PostTypeTitle = $FeedAttribs['post_type']; // TODO: Get readable title of post type
			$FeedTitle = sprintf( __('Podcast Settings for Post Type %s with slug %s', 'powerpress'), htmlspecialchars($PostTypeTitle) , htmlspecialchars($feed_slug));
			echo sprintf('<input type="hidden" name="podcast_post_type" value="%s" />', $FeedAttribs['post_type']);
			echo sprintf('<input type="hidden" name="feed_slug" value="%s" />', $feed_slug);
			echo '<input type="hidden" name="action" value="powerpress-save-post_type" />';
			
		}; break;
		default: {
			$FeedSettings = powerpress_get_settings('powerpress_feed');
			$FeedSettings = powerpress_default_settings($FeedSettings, 'editfeed');
			echo '<input type="hidden" name="action" value="powerpress-save-settings" />';
		}; break;
	}
		
	
	echo '<h2>'. $FeedTitle .'</h2>';
	
	if( $cat_ID && (isset($_GET['from_categories']) || isset($_POST['from_categories'])) )
	{
		echo '<input type="hidden" name="from_categories" value="1" />';
	}
	
?>
<div id="powerpress_settings_page" class="powerpress_tabbed_content"> 
  <ul class="powerpress_settings_tabs">
		<li><a href="#feed_tab_feed"><span><?php echo htmlspecialchars(__('Feed', 'powerpress')); ?></span></a></li>
		<li><a href="#feed_tab_itunes"><span><?php echo htmlspecialchars(__('iTunes', 'powerpress')); ?></span></a></li>
		<li><a href="#feed_tab_googleplay"><span><?php echo htmlspecialchars(__('Google Play', 'powerpress')); ?></span></a></li>
		<li><a href="#feed_tab_artwork"><span><?php echo htmlspecialchars(__('Artwork', 'powerpress')); ?></span></a></li>
	<?php if( in_array($FeedAttribs['type'], array('category', 'ttid', 'post_type', 'channel') ) ) { ?>
		<li><a href="#feed_tab_appearance"><span><?php echo htmlspecialchars(__('Website', 'powerpress')); ?></span></a></li>
	<?php } ?>
	<li><a href="#feed_tab_destinations"><span><?php echo htmlspecialchars(__('Destinations', 'powerpress')); ?></span></a></li>
	
	<?php if( in_array($FeedAttribs['type'], array('category', 'ttid', 'post_type', 'channel') ) ) { ?>
		<li><a href="#feed_tab_other"><span><?php echo htmlspecialchars(__('Other Settings', 'powerpress')); ?></span></a></li> 
	<?php } ?>
  </ul>
	
	
	<div id="feed_tab_feed" class="powerpress_tab">
		<?php
		powerpressadmin_edit_feed_settings($FeedSettings, $General, $FeedAttribs );
		if( !empty($General['advanced_mode_2']) ) {
			powerpressadmin_edit_funding($FeedSettings, $feed_slug);
			powerpressadmin_edit_tv($FeedSettings, $feed_slug);
		}
		?>
	</div>
	
	<div id="feed_tab_itunes" class="powerpress_tab">
		<?php
		powerpressadmin_edit_itunes_feed($FeedSettings, $General, $FeedAttribs);
		?>
	</div>
	
	<div id="feed_tab_googleplay" class="powerpress_tab">
		<?php
		powerpressadmin_edit_googleplay($FeedSettings, $General, $FeedAttribs);
		?>
	</div>
	
	<div id="feed_tab_artwork" class="powerpress_tab">
		<?php
		powerpressadmin_edit_artwork($FeedSettings, $General);
		?>
	</div>
	
	<?php if( $feed_slug || $FeedAttribs['type'] == 'category' || $FeedAttribs['type'] == 'ttid' ) { ?>
	<div id="feed_tab_appearance" class="powerpress_tab">
		<?php
		//powerpressadmin_appearance($General);
		//if( $feed_slug )
		//	powerpressadmin_edit_appearance_feed($General, $FeedSettings, $feed_slug, $FeedAttribs);
		
		powerpressadmin_settings_tab_appearance($General, $FeedSettings, $FeedAttribs);
		?>
	</div>
	
	<div id="feed_tab_destinations" class="powerpress_tab">
		<?php
		powerpressadmin_edit_destinations($FeedSettings, $General, $FeedAttribs);
		?>
	</div>
	
	<div id="feed_tab_other" class="powerpress_tab">
		<?php
		powerpressadmin_settings_tab_other($General, $FeedSettings, $feed_slug, $cat_ID, $FeedAttribs)
		?>
	</div>
	<?php } ?>
	
</div>
<div class="clear"></div>
<?php

		
}

function powerpressadmin_edit_podcast_channel($FeedSettings, $General)
{
	// TODO
?>
<input type="hidden" name="action" value="powerpress-save-customfeed" />
<p style="margin-bottom: 0;">
	<?php echo __('Configure your custom podcast feed.', 'powerpress'); ?>
</p>
<?php
}

function powerpressadmin_edit_category_feed($FeedSettings, $General)
{
?>
<input type="hidden" name="action" value="powerpress-save-categoryfeedsettings" />
<p style="margin-bottom: 0;">
	<?php echo __('Configure your category feed to support podcasting.', 'powerpress'); ?>
</p>
<?php
}

function powerpressadmin_edit_feed_general($FeedSettings, $General)
{
	$warning = '';
	$episode_count = powerpress_get_episode_count('podcast');
	if( $episode_count == 0 )
	{
		$warning = __('WARNING: You must create at least one podcast episode for your podcast feed to be valid.', 'powerpress');
	}
?>
<h3><?php echo __('Podcast Feeds', 'powerpress'); ?></h3>
<table class="form-table">

<tr valign="top">
<th scope="row">

<?php echo __('Enhance Feeds', 'powerpress'); ?></th> 
<td>
	<ul>
		<li><p><label><input type="radio" name="Feed[apply_to]" value="1" <?php if( $FeedSettings['apply_to'] == 1 ) echo 'checked'; ?> /> <?php echo __('Enhance All Feeds', 'powerpress'); ?></label> (<?php echo __('Recommended', 'powerpress'); ?>)</p>
		<p style="font-size: 100%; margin: 0 0 0 30px;"><?php echo __('Adds podcasting support to all feeds', 'powerpress'); ?></p>
		</li>
		<li><p><label><input type="radio" name="Feed[apply_to]" value="2" <?php if( $FeedSettings['apply_to'] == 2 ) echo 'checked'; ?> /> <?php echo __('Enhance Main Feed Only', 'powerpress'); ?></label></p>
		<p style="font-size: 100%; margin: 0 0 0 30px;"><?php echo __('Adds podcasting support to your main feed only', 'powerpress'); ?></p></li>
		<li><p><label><input type="radio" name="Feed[apply_to]" value="0" <?php if( $FeedSettings['apply_to'] == 0 ) echo 'checked'; ?> /> <?php echo __('Do Not Enhance Feeds', 'powerpress'); ?></label></p>
		<p style="font-size: 100%; margin: 0 0 0 30px;"><?php echo __('Feed Settings below will only apply to your podcast channel feeds', 'powerpress'); ?></p></li>
	</ul>
</td>
</tr>
<tr valign="top">
<th scope="row">

<?php echo __('Podcast Feeds', 'powerpress'); ?></th> 
<td>
<?php if( $warning ) { ?>
<span class="powerpress-error" style="background-color: #FFEBE8; border-color: #CC0000; padding: 6px 10px;"><?php echo $warning; ?></span>
<?php } ?>
<?php
	
	//$General = get_option('powerpress_general');
	$Feeds = array('podcast'=> __('Special Podcast only Feed', 'powerpress') );
	if( isset($General['custom_feeds']['podcast']) )
		$Feeds = $General['custom_feeds'];
	else if( isset($General['custom_feeds'])&& is_array($General['custom_feeds']) )
		$Feeds += $General['custom_feeds'];
		
	while( list($feed_slug, $feed_title) = each($Feeds) )
	{
		$edit_link = admin_url( 'admin.php?page=powerpress/powerpressadmin_customfeeds.php&amp;action=powerpress-editfeed&amp;feed_slug=') . $feed_slug;
?>
<p><?php echo $feed_title; ?>: <a href="<?php echo get_feed_link($feed_slug); ?>" title="<?php echo $feed_title; ?>" target="_blank"><?php echo get_feed_link($feed_slug); ?></a>
	<?php if( defined('POWERPRESS_FEEDVALIDATOR_URL') ) { ?>
	| <a href="<?php echo POWERPRESS_FEEDVALIDATOR_URL. urlencode(get_feed_link($feed_slug)); ?>" target="_blank"><?php echo __('validate', 'powerpress'); ?></a>
	<?php } ?>
	<?php if( false && $feed_slug != 'podcast' ) { ?>
	| <a href="<?php echo $edit_link; ?>" title="<?php echo __('Edit Podcast Channel', 'powerpress'); ?>"><?php echo __('edit', 'powerpress'); ?></a>
	<?php } ?>
</p>
<?php } ?>
<p><?php echo __('These are podcast only feeds suitable for submission podcast directories such as iTunes.', 'powerpress'); ?></p>
<p class="description"><?php echo __('Note: We do not recommend submitting your main site feed to podcast directories such as iTunes. iTunes and many other podcast directories work best with feeds that do not have regular blog posts mixed in.', 'powerpress');  ?></p>

<input type="hidden" name="General[feed_action_hook]" value="0" />
<p><label><input type="checkbox" name="General[feed_action_hook]" value="1" <?php if( !empty($General['feed_action_hook']) && $General['feed_action_hook'] == 1 ) echo 'checked '; ?>/> <?php echo __('Do not allow other plugins to modify podcast feeds.', 'powerpress'); ?></label></p>
<input type="hidden" name="General[feed_accel]" value="0" />
<p><label><input type="checkbox" name="General[feed_accel]" value="1" <?php if( !empty($General['feed_accel']) && $General['feed_accel'] == 1 ) echo 'checked '; ?>/> <?php echo __('Accelerate feed', 'powerpress'); ?></label></p>

</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Feed Discovery', 'powerpress'); ?></th>
<td>
<p style="margin-top: 10px;"><label><input type="checkbox" name="General[feed_links]" value="1" <?php if( !empty($General['feed_links']) && $General['feed_links'] == 1 ) echo 'checked '; ?>/> <?php echo __('Include podcast feed links in HTML headers.', 'powerpress'); ?></label></p>
<p><?php echo __('Adds "feed discovery" links to your web site\'s headers allowing web browsers and feed readers to auto-detect your podcast feeds.', 'powerpress'); ?></p>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('RSS2 Image', 'powerpress'); ?></th>
<td>
<input type="hidden" name="General[disable_rss_image]" value="1" />
<p><label><input type="checkbox" name="General[disable_rss_image]" value="0" <?php if( empty($General['disable_rss_image']) ) echo 'checked '; ?>/> <?php echo __('Include RSS Image in feeds.', 'powerpress'); ?></label></p>
</td>
</tr>


</table>
<?php
}

function powerpressadmin_edit_feed_settings($FeedSettings, $General, $FeedAttribs = array() )
{
	$SupportUploads = powerpressadmin_support_uploads();
	if( !isset($FeedSettings['posts_per_rss']) )
		$FeedSettings['posts_per_rss'] = '';
	if( !isset($FeedSettings['rss2_image']) )
		$FeedSettings['rss2_image'] = '';
	if( !isset($FeedSettings['copyright']) )
		$FeedSettings['copyright'] = '';
	if( !isset($FeedSettings['title']) )
		$FeedSettings['title'] = '';
	if( !isset($FeedSettings['rss_language']) )
		$FeedSettings['rss_language'] = '';
		
	$feed_link = '';
	switch( $FeedAttribs['type'])
	{
		case 'category': {
			if( !empty($General['cat_casting_podcast_feeds']) )
				$feed_link = get_category_feed_link($FeedAttribs['category_id'], 'podcast');
			else // Use the old link
				$feed_link = get_category_feed_link($FeedAttribs['category_id']);
		}; break;
		case 'ttid': {
			$feed_link = get_term_feed_link($FeedAttribs['term_taxonomy_id'], $FeedAttribs['taxonomy_type'], 'rss2');
		}; break;
		case 'post_type': {
			$feed_link = get_post_type_archive_feed_link($FeedAttribs['post_type'], $FeedAttribs['feed_slug']);
		}; break;
		case 'channel': {
			$feed_link = get_feed_link($FeedAttribs['feed_slug']);
		}; break;
		default: {
			$feed_link = get_feed_link('podcast');
		}; break;
	}
	
	$cat_ID = $FeedAttribs['category_id'];
	
	if( !empty($FeedAttribs['type']) && (  in_array($FeedAttribs['type'], array('category', 'ttid', 'post_type', 'channel') ) ) 	)
	{
?>
<h3><?php echo __('Feed Information', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Feed URL', 'powerpress'); ?>
</th>
<td>
<p style="margin-top: 0;" class="description"><a href="<?php echo $feed_link; ?>" target="_blank"><?php echo $feed_link; ?></a>
	<?php if( defined('POWERPRESS_FEEDVALIDATOR_URL') ) { ?>| <a href="<?php echo POWERPRESS_FEEDVALIDATOR_URL. urlencode( str_replace('&amp;', '&', $feed_link)); ?>" target="_blank"><?php echo __('validate', 'powerpress'); ?></a><?php } ?></p>
<?php
		if( !empty($FeedSettings['premium']) )
		{
			echo __('WARNING: This feed is password protected, it cannot be accessed publicly by the iTunes podcast directory or other podcast services.', 'powerpress');
		} ?>
</td>
</tr>
</table>
<?php
	}
?>
<h3><?php echo __('Feed Settings', 'powerpress'); ?></h3>
<p class="description"><?php echo sprintf(__('Feed settings below apply to feed: %s', 'powerpress'), esc_html($feed_link) ); ?></p>
<table class="form-table">


<tr valign="top">
<th scope="row">
<?php echo __('Feed Title (Show Title)', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[title]" style="width: 60%;"  value="<?php echo esc_attr($FeedSettings['title']); ?>" maxlength="255" />
<?php if( $cat_ID ) { ?>
(<?php echo __('leave blank to use default category title', 'powerpress'); ?>)
<?php } else { ?>
(<?php echo __('leave blank to use blog title', 'powerpress'); ?>)
<?php } ?>
<?php if( $FeedAttribs['type'] == 'ttid' ) { } else if( $cat_ID ) { 
?>
<p class="description"><?php echo esc_html(__('Default Category title:', 'powerpress') .' '. get_cat_name($cat_ID) . ' '.  apply_filters( 'document_title_separator', '-' ) .' '. get_bloginfo_rss('name') ); ?></p>
<?php } else { ?>
<p class="description"><?php echo __('Blog title:', 'powerpress') .' '. get_bloginfo_rss('name'); ?></p>
<?php } ?>
<?php if( !empty($General['seo_itunes']) ) { ?>
			<p>
				<em><?php echo __('Podcasting SEO Suggestion: The show title is very important.', 'powerpress'); ?></em>
			</p>
<?php } ?>
</td>
</tr>

<?php if( !empty($General['advanced_mode_2']) ) { ?>
<!-- start advanced features -->
<tr valign="top">
<th scope="row">
<?php echo __('Feed Description', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[description]" style="width: 60%;"  value="<?php echo esc_attr( !empty($FeedSettings['description'])? $FeedSettings['description']:''); ?>" maxlength="1000" /> 
<?php if( $cat_ID ) { ?>
(<?php echo __('leave blank to use category description', 'powerpress'); ?>)
<?php } else { ?>
(<?php echo __('leave blank to use blog description', 'powerpress'); ?>)
<?php } ?>
</td>
</tr>

<?php
if( $FeedAttribs['type'] != 'general' ) // All types exept general settings
{
?>
<tr valign="top">
<th scope="row">
<?php echo __('Feed Landing Page URL', 'powerpress'); ?> <br />
</th>
<td>
<input type="text" name="Feed[url]" style="width: 60%;"  value="<?php echo esc_attr( !empty($FeedSettings['url'])? $FeedSettings['url']:''); ?>" maxlength="255" />
<?php if( $cat_ID ) { ?>
(<?php echo __('leave blank to use category page', 'powerpress'); ?>)
<?php } else { ?>
(<?php echo __('leave blank to use home page', 'powerpress'); ?>)
<?php } ?>
<?php if( $cat_ID ) { ?>
<p class="description"><?php echo __('Category page URL', 'powerpress'); ?>: <?php echo get_category_link($cat_ID); ?></p>
<?php } else { ?>
<p class="description">e.g. <?php echo get_bloginfo('url'); ?>/custom-page/</p>
<?php } ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('FeedBurner Feed URL', 'powerpress'); ?><br />
<span style="font-size: 85%; margin-left: 5px;"><?php echo __('Recommendation: leave blank', 'powerpress'); ?></span>
</th>
<td>
<input type="text" name="Feed[feed_redirect_url]" style="width: 60%;"  value="<?php echo esc_attr(!empty($FeedSettings['feed_redirect_url'])? $FeedSettings['feed_redirect_url']:''); ?>" maxlength="100" />  (<?php echo __('leave blank to use built-in feed', 'powerpress'); ?>)

<p style="margin-top: 0px; margin-bottomd: 0;" class="description"><?php echo powerpressadmin_notice( __('NOTE: FeedBurner is not required for podcasting.', 'powerpress') ); ?> <br /> 
<?php echo powerpressadmin_notice( __('No support is available from blubrry if you are using Feedburner or other feed hosted services.', 'powerpress') ); ?><br /> 
<a href="http://create.blubrry.com/manual/syndicating-your-podcast-rss-feeds/feedburner-for-podcasting/" target="_blank"><?php echo __('Learn more about FeedBurner and Podcasitng', 'powerpress'); ?></a>
</p>
<p><?php echo __('Use this option to redirect this feed to a hosted feed service such as FeedBurner.', 'powerpress'); ?></p>

<p><?php echo __('We recommend that you disable FeedBurner SmartCast when using FeedBurner with PowerPress.', 'powerpress'); ?></p>
<?php
$link = $feed_link;
if( strstr($link, '?') )
	$link .= "&redirect=no";
else
	$link .= "?redirect=no";
?>
<p class="description"><?php echo __('Bypass Redirect URL', 'powerpress'); ?>: <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></p>
</td>
</tr>

<?php } // End not general settings ?>

<tr valign="top">
<th scope="row">
<?php echo __('Show the most recent', 'powerpress'); ?>
</th>
<td>
<p><input type="text" name="Feed[posts_per_rss]" style="width: 50px;"  value="<?php echo ( !empty($FeedSettings['posts_per_rss'])? $FeedSettings['posts_per_rss']:''); ?>" maxlength="5" /> <?php echo sprintf(__('episodes / posts per feed (site default: %d, maximum: %d)', 'powerpress'), get_option('posts_per_rss'), 300); ?></p>
<p style="margin-top: 5px; margin-bottom: 0; font-weight: bold;"><?php echo __('Please enable the <i>Feed Episode Maximizer</i> option to optimize your feed for more than 10 episodes.', 'powerpress'); ?></p>
</td>
</tr>

<?php
	if( in_array($FeedAttribs['type'], array('channel', 'category', 'post_types', 'general')) )
	{
?>
<tr valign="top">
<th scope="row">
<?php echo __('Feed Episode Maximizer', 'powerpress'); ?>  <?php echo powerpressadmin_new(); ?>
</th>
<td>
<p><input type="checkbox" name="Feed[maximize_feed]" value="1" <?php if( !empty($FeedSettings['maximize_feed']) ) echo 'checked'; ?> />
		<?php echo __('Maximize the number of episodes while maintaining an optimal feed size.', 'powerpress'); ?> <a href="http://create.blubrry.com/resources/powerpress/powerpress-settings/feeds/#maximizer" target="_blank"><?php echo __('Learn more', 'powerpress'); ?></p>
		<p></a></p>
</td>
</tr>
<?php
	}
?>

<tr valign="top">
<th scope="row">

<?php echo __('Feed Language', 'powerpress'); ?></th>
<td>
<select name="Feed[rss_language]" class="bpp_input_med">
<?php
$Languages = powerpress_languages();

echo '<option value="">'. __('Blog Default Language', 'powerpress') .'</option>';
while( list($value,$desc) = each($Languages) )
	echo "\t<option value=\"$value\"". ($FeedSettings['rss_language']==$value?' selected':''). ">". esc_attr($desc)."</option>\n";
?>
</select>
<?php
	$rss_language = get_bloginfo_rss('language');
	$rss_language = strtolower($rss_language);
if( isset($Languages[ $rss_language ]) )
{
?>
 <?php echo __('Blog Default', 'powerpress'); ?>: <?php echo $Languages[ $rss_language ]; ?>
 <?php } else {  ?>
<?php echo __('Blog Default', 'powerpress'); ?>: <?php echo $rss_language; ?>
 <?php } ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Copyright', 'powerpress'); ?>
</th>
<td>
<input type="text" name="Feed[copyright]" style="width: 60%;" value="<?php echo esc_attr($FeedSettings['copyright']); ?>" maxlength="255" />
</td>
</tr>
<tr valign="top">
<th scope="row">
<?php echo __('Caching Debug Comments', 'powerpress'); ?>
</th>
<td>
<label><input type="checkbox" name="General[allow_feed_comments]" value="1" <?php if( !empty($General['allow_feed_comments']) ) echo 'checked'; ?> />
	<?php echo __('Allow WP Super Cache or W3 Total Cache to add HTML Comments to the bottom of your feeds', 'powerpress'); ?></label>
	(<?php echo __('Recommended unchecked', 'powerpress'); ?>)
<p><?php echo __('iTunes is known to have issues with feeds that have HTML comments at the bottom.', 'powerpress'); ?></p>
<p style="margin-bottom: 0;" class="description"><?php echo __('NOTE: This setting should only be enabled for debugging purposes.', 'powerpress'); ?></p>
</td>
</tr>

<!-- end advanced features -->
<?php  } ?>
</table>

<!-- Location and frequency information -->
<?php
	if( !isset($FeedSettings['location']) )
		$FeedSettings['location'] = '';
	if( !isset($FeedSettings['frequency']) )
		$FeedSettings['frequency'] = '';
?>
<?php if( !empty($General['advanced_mode_2']) ) { ?>
<!-- start advanced features -->
<h3><?php echo __('Basic Show Information', 'powerpress'); ?></h3>
<div id="rawvoice_basic_options">
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Location', 'powerpress'); ?></th> 
<td>
	<input type="text" style="width: 300px;" name="Feed[location]" value="<?php echo esc_attr($FeedSettings['location']); ?>" maxlength="50" /> (<?php echo __('optional', 'powerpress'); ?>)
	<p><?php echo __('e.g. Cleveland, Ohio', 'powerpress'); ?></p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php echo __('Episode Frequency', 'powerpress'); ?></th> 
<td>
	<input type="text" style="width: 300px;" name="Feed[frequency]" value="<?php echo esc_attr($FeedSettings['frequency']); ?>" maxlength="50" /> (<?php echo __('optional', 'powerpress'); ?>)
	<p><?php echo __('e.g. Weekly', 'powerpress'); ?></p>
</td>
</tr>
</table>
</div>
<!-- end advanced features -->
<?php
	}
}


function powerpressadmin_settings_tab_other($General, $FeedSettings, $feed_slug, $cat_ID = false,  $FeedAttribs = array() ) {
	require_once( dirname(__FILE__).'/views/settings_tab_other.php' );
}

function powerpressadmin_settings_tab_appearance($General, $FeedSettings, $FeedAttribs = array()) {
	require_once( dirname(__FILE__).'/views/settings_tab_appearance.php' );
}

function powerpressadmin_edit_appearance_feed($General,  $FeedSettings, $feed_slug)
{
	// Appearance Settings
?>
<h3><?php echo __('Website Settings', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('Disable Player', 'powerpress'); ?>
</th>
<td>
	<input name="DisablePlayerFor" type="checkbox" <?php if( isset($General['disable_player'][$feed_slug]) ) echo 'checked '; ?> value="1" /> <?php echo __('Do not display web player or links for this podcast.', 'powerpress'); ?>
	<input type="hidden" name="UpdateDisablePlayer" value="<?php echo $feed_slug; ?>" />
</td>
</tr>
</table>
<?php

}

function powerpressadmin_edit_itunes_feed($FeedSettings, $General, $FeedAttribs = array() )
{
	$feed_slug = $FeedAttribs['feed_slug'];
	$cat_ID = $FeedAttribs['category_id'];
	
	$SupportUploads = powerpressadmin_support_uploads();
	if( !isset($FeedSettings['itunes_subtitle']) )
		$FeedSettings['itunes_subtitle'] = '';
	if( !isset($FeedSettings['itunes_summary']) )
		$FeedSettings['itunes_summary'] = '';
	if( !isset($FeedSettings['itunes_keywords']) )
		$FeedSettings['itunes_keywords'] = '';	
	if( !isset($FeedSettings['itunes_cat_1']) )
		$FeedSettings['itunes_cat_1'] = '';
	if( !isset($FeedSettings['itunes_cat_2']) )
		$FeedSettings['itunes_cat_2'] = '';
	if( !isset($FeedSettings['itunes_cat_3']) )
		$FeedSettings['itunes_cat_3'] = '';
	if( !isset($FeedSettings['itunes_explicit']) )
		$FeedSettings['itunes_explicit'] = 0;
	if( !isset($FeedSettings['itunes_talent_name']) )
		$FeedSettings['itunes_talent_name'] = '';
	if( !isset($FeedSettings['email']) )
		$FeedSettings['email'] = '';
	if( !isset($FeedSettings['itunes_new_feed_url_podcast']) )
		$FeedSettings['itunes_new_feed_url_podcast'] = '';
	if( !isset($FeedSettings['itunes_new_feed_url']) )
		$FeedSettings['itunes_new_feed_url'] = '';
	
	$AdvancediTunesSettings = !empty($FeedSettings['itunes_summary']);
	if( !empty($FeedSettings['itunes_subtitle']) )
		$AdvancediTunesSettings = true;

?>

<h3><?php echo __('iTunes Settings', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Program Subtitle', 'powerpress'); ?> <br />
</th>
<td>
<input type="text" name="Feed[itunes_subtitle]" style="width: 60%;"  value="<?php echo esc_attr($FeedSettings['itunes_subtitle']); ?>" maxlength="255" />
</td>
</tr>

<tr valign="top">
<th scope="row">

<?php echo __('iTunes Program Summary', 'powerpress'); ?></th>
<td>
<p style="margin-top: 5px;"><?php echo __('Your summary cannot exceed 4,000 characters in length and should not include HTML, except for hyperlinks', 'powerpress'); ?></p>

<textarea name="Feed[itunes_summary]" rows="5" style="width:80%;" ><?php echo esc_textarea($FeedSettings['itunes_summary']); ?></textarea>
<div>
<input type="hidden" name="General[itunes_cdata]" value="0" />
<input type="checkbox" name="General[itunes_cdata]" value="1" <?php echo ( !empty($General['itunes_cdata'])?'checked ':''); ?>/> <?php echo __('Wrap summary values with &lt;![CDATA[ ... ]]&gt; tags', 'powerpress'); ?>
</div>
</td>
</tr>

<?php
	if( !empty($General['advanced_mode_2']) ) 
	{
?>
<tr valign="top">
<th scope="row">

<?php echo __('iTunes Episode Summary', 'powerpress'); ?></th>
<td>
<div style="margin-top: 15px;"><input type="checkbox" name="Feed[enhance_itunes_summary]" value="1" <?php echo ( !empty($FeedSettings['enhance_itunes_summary'])?'checked ':''); ?>/> <?php echo __('Optimize iTunes Summary from Blog Posts', 'powerpress'); ?>
</div>
<p>
	<?php echo __('Creates a friendlier view of your post/episode content.', 'powerpress'); ?>
</p>
</td>
</tr>
</table>


<table class="form-table">
<?php
		if( !empty($FeedSettings['itunes_keywords']) )
		{
?>
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Program Keywords', 'powerpress'); ?> <br />
</th>
<td>
<input type="text" name="Feed[itunes_keywords]" style="width: 60%;"  value="<?php echo esc_attr($FeedSettings['itunes_keywords']); ?>" maxlength="255" />
<p><?php echo __('Feature Deprecated by Apple. Keywords above are for your reference only.', 'powerpress'); ?></p>
</td>
</tr>
<?php
		} // End iTunes keywords
	} // end advanced mode
?>

<tr valign="top">
<th scope="row">
<?php echo __('iTunes Category', 'powerpress'); ?> 
<span class="powerpress-required"><?php echo __('Required', 'powerpress'); ?></span>
</th>
<td>
<select name="Feed[itunes_cat_1]" class="bpp_input_med">
<?php

$MoreCategories = false;
if( !empty($FeedSettings['itunes_cat_2']) )
	$MoreCategories = true;
else if( !empty($FeedSettings['itunes_cat_3']) ) 
	$MoreCategories = true;

$Categories = powerpress_itunes_categories(true);

echo '<option value="">'. __('Select Category', 'powerpress') .'</option>';

$UseCategories = powerpress_categories_strict($Categories, $FeedSettings['itunes_cat_1']);
while( list($value,$desc) = each($UseCategories) )
	echo "\t<option value=\"$value\"". ($FeedSettings['itunes_cat_1']==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";

reset($Categories);
?>
</select>
<?php
	if( !$MoreCategories && empty($General['seo_itunes']) ) { ?>
	<a href="#" onclick="document.getElementById('more_itunes_cats').style.display='block';return false;"><?php echo __('more', 'powerpress'); ?></a>
<?php } ?>
	<p>
		<?php echo __('The category above is where you will appear when browsing iTunes.', 'powerpress'); ?>
	</p>
</td>
</tr>
</table>


<!-- start advanced features -->
<div id="more_itunes_cats" style="display: <?php echo ( ($MoreCategories||!empty($General['seo_itunes']) )?'block':'none'); ?>;">
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Category 2', 'powerpress'); ?> 
</th>
<td>
<select name="Feed[itunes_cat_2]" class="bpp_input_med">
<?php


echo '<option value="">'. __('Select Category', 'powerpress')  .'</option>';
$UseCategories = powerpress_categories_strict($Categories, $FeedSettings['itunes_cat_2']);
while( list($value,$desc) = each($UseCategories) )
	echo "\t<option value=\"$value\"". ($FeedSettings['itunes_cat_2']==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";

reset($Categories);

?>
</select>
<?php if( !empty($General['seo_itunes']) ) { ?>
			<p>
				<em><?php echo __('Podcasting SEO Suggestion: Select a second category.', 'powerpress'); ?></em>
			</p>
<?php } ?>

</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('iTunes Category 3', 'powerpress'); ?> 
</th>
<td>
<select name="Feed[itunes_cat_3]" class="bpp_input_med">
<?php

echo '<option value="">'. __('Select Category', 'powerpress')  .'</option>';
$UseCategories = powerpress_categories_strict($Categories, $FeedSettings['itunes_cat_3']);
while( list($value,$desc) = each($UseCategories) )
	echo "\t<option value=\"$value\"". ($FeedSettings['itunes_cat_3']==$value?' selected':''). ">".htmlspecialchars($desc)."</option>\n";

reset($Categories);
?>
</select>
<?php if( !empty($General['seo_itunes']) ) { ?>
			<p>
				<em><?php echo __('Podcasting SEO Suggestion: Select a third category.', 'powerpress'); ?></em>
			</p>
<?php } ?>
</td>
</tr>
</table>
</div>
<!-- end advanced features -->


<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Explicit', 'powerpress'); ?> 
<span class="powerpress-required"><?php echo __('Required', 'powerpress'); ?></span>
</th>
<td>
<select name="Feed[itunes_explicit]" style="width: 70%;">
<?php
$explicit = array(0=> __('No option selected', 'powerpress'), 1=>__('Yes - explicit content', 'powerpress'), 2=>__('Clean - no explicit content', 'powerpress'));

while( list($value,$desc) = each($explicit) )
	echo "\t<option value=\"$value\"". ($FeedSettings['itunes_explicit']==$value?' selected':''). (($FeedSettings['itunes_explicit']!=0&&$value==0)?'disabled':''). ">$desc</option>\n";

?>
</select>
			<p>
				<em><?php echo __('Note: As of February, 2016, you must select either Yes or Clean.', 'powerpress'); ?></em>
			</p>
</td>
</tr>
<?php if( !empty($General['advanced_mode_2']) ) { ?>
<!-- start advanced features -->
<tr valign="top">
<th scope="row">
<?php echo __('iTunes Author Name', 'powerpress'); ?> 
</th>
<td>
<input type="text" name="Feed[itunes_talent_name]" class="bpp_input_med" value="<?php echo esc_attr($FeedSettings['itunes_talent_name']); ?>" maxlength="255" /><br />
<div><input type="checkbox" name="Feed[itunes_author_post]" value="1" <?php echo ( !empty($FeedSettings['itunes_author_post'])?'checked ':''); ?>/> <?php echo __('Use blog post author\'s name for individual episodes.', 'powerpress'); ?></div>

<?php if( !empty($General['seo_itunes']) ) { ?>
			<p>
				<em><?php echo __('Podcasting SEO Suggestion: Include talent names and nicknames not mentioned in the show title.', 'powerpress'); ?></em>
			</p>
<?php } ?>
</td>
</tr>
<!-- end advanced features -->
<?php } ?>

<tr valign="top">
<th scope="row">
<?php echo __('iTunes Email', 'powerpress'); ?> 
<span class="powerpress-required"><?php echo __('Required', 'powerpress'); ?></span>
</th>
<td>
<input type="text" name="Feed[email]" class="bpp_input_med" value="<?php echo esc_attr($FeedSettings['email']); ?>" maxlength="255" />
<div>(<?php echo __('iTunes will email this address when your podcast is accepted into the iTunes Directory.', 'powerpress'); ?>)</div>
</td>
</tr>
</table>

<?php if( !empty($General['advanced_mode_2']) ) { ?>
<!-- start advanced features -->
<table class="form-table">
	<tr valign="top">
	<th scope="row" >

<?php echo __('iTunes New Feed URL', 'powerpress'); ?></th> 
	<td>
		<div id="new_feed_url_step_1" style="display: <?php echo ( !empty($FeedSettings['itunes_new_feed_url']) || !empty($FeedSettings['itunes_new_feed_url_podcast'])  ?'none':'block'); ?>;">
			 <p style="margin-top: 5px;"><strong><a href="#" onclick="return powerpress_new_feed_url_prompt();"><?php echo __('Set iTunes New Feed URL', 'powerpress'); ?></a></strong></p>
			 <p><strong>
			 <?php echo __('The iTunes New Feed URL option works primarily for Apple\'s iTunes application only, and should only be used if you are unable to implement a HTTP 301 redirect.', 'powerpress'); ?>
			 <?php echo __('A 301 redirect will route <u>all podcast clients including iTunes</u> to your new feed address.', 'powerpress'); ?>
			 </strong> 
			 </p>
			 <p>
			 <?php echo __('Learn more:', 'powerpress'); ?> <a href="http://create.blubrry.com/manual/syndicating-your-podcast-rss-feeds/changing-your-podcast-rss-feed-address-url/" target="_blank"><?php echo __('Changing Your Podcast RSS Feed Address (URL)', 'powerpress'); ?></a>
			</p>
		</div>
		<div id="new_feed_url_step_2" style="display: <?php echo ( !empty($FeedSettings['itunes_new_feed_url']) || !empty($FeedSettings['itunes_new_feed_url_podcast'])  ?'block':'none'); ?>;">
			<p style="margin-top: 5px;"><strong><?php echo __('WARNING: Changes made here are permanent. If the New Feed URL entered is incorrect, you will lose subscribers and will no longer be able to update your listing in the iTunes Store.', 'powerpress'); ?></strong></p>
			<p><strong><?php echo __('DO NOT MODIFY THIS SETTING UNLESS YOU ABSOLUTELY KNOW WHAT YOU ARE DOING.', 'powerpress'); ?></strong></p>
			<p>
				<?php echo htmlspecialchars( sprintf(__('Apple recommends you maintain the %s tag in your feed for at least two weeks to ensure that most subscribers will receive the new New Feed URL.', 'powerpress'), '<itunes:new-feed-url>' ) ); ?>
			</p>
			<p>
			<?php 
			$FeedName = __('Main RSS2 feed', 'powerpress');
			$FeedURL = get_feed_link('rss2');
			if( $cat_ID )
			{
				$category = get_category_to_edit($cat_ID);
				$FeedName = sprintf( __('%s category feed', 'powerpress'), htmlspecialchars($category->name) );
				if( !empty($General['cat_casting_podcast_feeds']) )
					$FeedURL = get_category_feed_link($cat_ID, 'podcast');
				else
					$FeedURL = get_category_feed_link($cat_ID);
			}
			else if( $feed_slug )
			{
				if( !empty($General['custom_feeds'][ $feed_slug ]) )
					$FeedName = $General['custom_feeds'][ $feed_slug ];
				else
					$FeedName = __('Podcast', 'powerpress');
				$FeedName = trim($FeedName).' '.__('feed', 'powerpress');
				$FeedURL = get_feed_link($feed_slug);
			}
			else if( $FeedAttribs['type'] == 'ttid' )
			{
				$term_object = get_term_to_edit($FeedAttribs['term_id'],$FeedAttribs['taxonomy_type']);
				$FeedName = sprintf( __('%s taxonomy term feed', 'powerpress'), htmlspecialchars($term_object->name) );
				$FeedURL = get_term_feed_link($FeedAttribs['term_id'],$FeedAttribs['taxonomy_type'], 'rss2');
			}
			
			echo sprintf(__('The New Feed URL value below will be applied to the %s (%s).', 'powerpress'), $FeedName, $FeedURL);
?>
			</p>
			<p style="margin-bottom: 0;">
				<label style="width: 25%; float:left; display:block; font-weight: bold;"><?php echo __('New Feed URL', 'powerpress'); ?></label>
				<input type="text" name="Feed[itunes_new_feed_url]" style="width: 55%;"  value="<?php echo esc_attr($FeedSettings['itunes_new_feed_url']); ?>" maxlength="255" />
			</p>
			<p style="margin-left: 25%;margin-top: 0;font-size: 90%;">(<?php echo __('Leave blank for no New Feed URL', 'powerpress'); ?>)</p>
			
			<p><a href="http://www.apple.com/itunes/whatson/podcasts/specs.html#changing" target="_blank"><?php echo __('More information regarding the iTunes New Feed URL is available here.', 'powerpress'); ?></a></p>
			<p>
<?php
			if( !$cat_ID && !$feed_slug )
			{
				if( empty($General['channels']) )
					echo sprintf(__('Please activate the \'Custom Podcast Channels\' Advanced Option to set the new-feed-url for your podcast only feed (%s)', 'powerpress'), get_feed_link('podcast') );
				else
					echo sprintf(__('Please navigate to the \'Custom Podcast Channels\' section to set the new-feed-url for your podcast only feed (%s)', 'powerpress'), get_feed_link('podcast') );
			}
?>
			</p>
		</div>
	</td>
	</tr>
</table>

<fieldset style="border: 1px dashed #333333;">
<legend style="margin: 0 20px; padding: 0 5px; font-weight: bold;"><?php echo __('Advanced Options', 'powerpress');  ?></legend>

	<div style="margin-left: 230px; margin-bottom: 10px;">
		<p>
			<strong style="color: #CC0000; font-weight: bold;"><?php echo __('SETTINGS BELOW HAVE PERMANENT CONSEQUENCES.', 'powerpress'); ?></strong>
		</p>
		<p style="margin-bottom: 0;">
			<?php echo __('Feeds affected', 'powerpress'); ?>: 
		</p>
		<div style="margin-left: 20px;">
			<?php
			// $General, $feed_slug=false, $cat_ID=false
			
			if( $feed_slug )
			{
				echo '<a href="';
				echo esc_attr( get_feed_link($feed_slug) );
				echo '" target="_blank">';
				echo esc_html( get_feed_link($feed_slug) );
				echo '</a>';
			}
			else if( $cat_ID )
			{
				if( !empty($General['cat_casting_podcast_feeds']) )
					$feed_url = get_category_feed_link($cat_ID, 'podcast');
				else
					$feed_url = get_category_feed_link($cat_ID);
				echo '<a href="';
				echo esc_attr( $feed_url );
				echo '" target="_blank">';
				echo esc_html( $feed_url );
				echo '</a>';
			}
			else
			{
				echo '<a href="';
				echo esc_attr( get_feed_link('feed') );
				echo '" target="_blank">';
				echo esc_html( get_feed_link('feed') );
				echo '</a>';
				
				if( empty($General['custom_feeds']['podcast']) )
				{
					echo '<br /><a href="';
					echo esc_attr( get_feed_link('podcast') );
					echo '" target="_blank">';
					echo esc_html( get_feed_link('podcast') );
					echo '</a>';
				}
			}
			
			?>
		</div>
		
	</div>
<div id="permanent_itunes_settings">
<table class="form-table">
	
	<tr valign="top">
	<th scope="row" >

<span style="margin-left: 10px;"><?php echo __('iTunes Block', 'powerpress'); ?></span></th> 
	<td>
		<input type="checkbox" name="Feed[itunes_block]" value="1" <?php if( !empty($FeedSettings['itunes_block']) ) echo 'checked'; ?> />
		<?php echo __('Prevent the entire podcast from appearing in the iTunes Podcast directory.', 'powerpress'); ?>
	</td>
	</tr>
	
	<tr valign="top">
	<th scope="row" >

<span style="margin-left: 10px;"><?php echo __('iTunes Complete', 'powerpress'); ?></span></th> 
	<td>
		<input type="checkbox" name="Feed[itunes_complete]" value="1" <?php if( !empty($FeedSettings['itunes_complete']) ) echo 'checked'; ?> />
		<?php echo __('Indicate the completion of a podcast. iTunes will no longer update your listing in the iTunes Podcast directory.', 'powerpress'); ?>
	</td>
	</tr>
</table>
</div>
</fieldset>
<!-- end advanced features -->
<?php } // end other advanced options ?>
<?php
}
	
?>