<?php

function powerpresssubscribe_get_subscribe_page($Settings)
{
	if( !empty($Settings['subscribe_page_link_id']) && is_numeric($Settings['subscribe_page_link_id']) )
		return get_page_link($Settings['subscribe_page_link_id']);
	if( !empty($Settings['subscribe_page_link_href']) )
		return $Settings['subscribe_page_link_href'];
	return '';
}

function powerpresssubscribe_get_itunes_url($Settings)
{
	if( !empty($Settings['itunes_url']) )
	{
		// Make URL https://, always add ?mt=2 to end of itunes.apple.com URLs, include l1 to load iTunes store if installed, and always remove uo=X if it's there
		return preg_replace("/^http:\/\//i", "https://", add_query_arg( array('uo' => false, 'mt' => '2', 'ls' => '1'), trim($Settings['itunes_url']) ) );
	}

	if( !empty($Settings['feed_url']) ) // && !empty($Settings['itpc']) )
		return preg_replace('/(^https?:\/\/)/i', 'itpc://', $Settings['feed_url']); // Still better than nothing at this point.
	
	return '';
}

function powerpresssubscribe_get_settings($ExtraData, $detect_category=true)
{
	$GeneralSettings = get_option('powerpress_general');
	
	$feed_slug = (empty($ExtraData['feed'])?'podcast': $ExtraData['feed']);
	$post_type = (empty($ExtraData['post_type'])?false: $ExtraData['post_type']);
	$category_id = (empty($ExtraData['cat_id'])?false: $ExtraData['cat_id']);
	$taxonomy_term_id = (empty($ExtraData['taxonomy_term_id'])?false: $ExtraData['taxonomy_term_id']);
	// Special case, strict category specified...
	if( 'podcast' == $feed_slug && empty($post_type) && empty($taxonomy_term_id) && !empty($ExtraData['category']) ) {
		if( !empty($GeneralSettings['cat_casting_strict']) ) // Strict category podcasting, otherwise we let the logic below figure it out!
		{
			$category_id = $ExtraData['category'];
			$ExtraData['subscribe_type'] = 'category';
		}
	}
	
	if( empty($ExtraData['subscribe_type']) ) // Make sure this value is set
		$ExtraData['subscribe_type'] = '';
	
	switch( $ExtraData['subscribe_type'] )
	{
		case 'post_type': {
			$category_id = 0;
			$taxonomy_term_id = 0;
		}; break;
		case 'category': {
			$feed_slug = 'podcast';
			$taxonomy_term_id = 0;
			$post_type = '';
		}; break;
		case 'ttid': {
			$feed_slug = 'podcast';
			$category_id = 0;
			if( empty($post_type) )
				$post_type = get_post_type();
		}; break;
		case 'channel': 
		case 'general': 
		default: {
			$category_id = 0;
			$post_type = '';
			$taxonomy_term_id = 0;
		}; break;
	}
	
	// We need to know if category podcasting is enabled, if it is then we may need to dig deeper for this info....
	if( false != $detect_category && empty($category_id) && !empty($GeneralSettings['cat_casting']) && $feed_slug == 'podcast' && empty($ExtraData['subscribe_type']) )
	{
		if( !$category_id && is_category() )
		{
			$category_id = get_query_var('cat');
		}
		if( !$category_id && is_single() )
		{
			$categories = wp_get_post_categories( get_the_ID() );
			if( count($categories) == 1 )
				list($null,$category_id) = each($categories);
			if( !empty($category_id) )
			{
				$Settings = get_option('powerpress_cat_feed_'.$category_id );
				// Check if it is a podcast category...
				if( !empty($Settings) ) {
					$ExtraData['subscribe_type'] = 'category';
				} else {
					$category_id = false; // Unset it!
				}
			}
		}
	}
	
	// Category
	if( !empty($category_id) && $ExtraData['subscribe_type'] == 'category' )
	{
		$Settings = get_option('powerpress_cat_feed_'.$category_id );
		
		if( !empty($Settings) )
		{
			if( empty($Settings['title']) ) {
				$Settings['title'] = get_cat_name( $category_id ); // Get category title
				$Settings['title'] .= ' '. apply_filters( 'document_title_separator', '-' ) .' ';
				$Settings['title'] .= get_bloginfo_rss('name');
			}
			if( empty($Settings['title']) )	
				$Settings['title'] = get_bloginfo_rss('name'); // Get blog title, best we can do
			if( !empty($Settings['feed_redirect_url']) )
				$Settings['feed_url'] = $Settings['feed_redirect_url'];
			else if( !empty($General['cat_casting_podcast_feeds']) )
				$Settings['feed_url'] = get_category_feed_link($category_id, 'podcast');
			else
				$Settings['feed_url'] = get_category_feed_link( $category_id ); // Get category feed URL
			
			$Settings['subscribe_page_url'] = powerpresssubscribe_get_subscribe_page($Settings);
			$Settings['itunes_url'] = powerpresssubscribe_get_itunes_url($Settings);
			$Settings['image_url'] = $Settings['itunes_image'];
			$Settings['subscribe_feature_rss'] = (!empty($GeneralSettings['subscribe_feature_rss']) || !isset($GeneralSettings['subscribe_feature_rss']) );
			$Settings['subscribe_feature_email'] = (!empty($GeneralSettings['subscribe_feature_email']) );
			$Settings['subscribe_feature_gp'] = (!empty($GeneralSettings['subscribe_feature_gp']) );
			$Settings['subscribe_feature_stitcher'] = (!empty($GeneralSettings['subscribe_feature_stitcher']) );
			$Settings['subscribe_feature_tunein'] = (!empty($GeneralSettings['subscribe_feature_tunein']) );
			return $Settings;
		}
		
		return false; // If we specifically wanted a category, then we need to return false so we don't miss-represent
	}
	
	// Taxonomy
	if( $ExtraData['subscribe_type'] == 'ttid' )
	{
		if( !empty($GeneralSettings['taxonomy_podcasting']) )
		{
			// TODO! Taxonomy Podcasting subscription options
		}
		return false;
	}
	
	// Post Type Podcasting
	if( $ExtraData['subscribe_type'] == 'post_type' )
	{
		if( !empty($GeneralSettings['posttype_podcasting']) )
		{
			if( empty($post_type) && !empty($ExtraData['id']) )
				$post_type = get_post_type( $ExtraData['id'] );
			
			switch( $post_type )
			{
				case 'page':
				case 'post':
				{
					// SWEET, CARRY ON!
				}; break;
				default: {
					$SettingsArray = get_option('powerpress_posttype_'.$post_type);
					$Settings = false;
					if( !empty($SettingsArray[ $feed_slug ]) )
						$Settings = $SettingsArray[ $feed_slug ];
					
					if( !empty($Settings) )
					{
						$Settings['title'] = $Settings['title'];
						if( empty($Settings['title']) ) {
							$obj = get_post_type_object( );
							if( !empty($obj->labels->singular_name) )
								$Settings['title'] = $obj->labels->singular_name;
						}
						if( empty($Settings['title']) ) {
							$Settings['title'] = get_bloginfo_rss('name');
						}
						if( !empty($Settings['feed_redirect_url']) )
							$Settings['feed_url'] = $Settings['feed_redirect_url'];
						else
							$Settings['feed_url'] = get_post_type_archive_feed_link($post_type, $feed_slug); // Get post type feed URL
						$Settings['subscribe_page_url'] = powerpresssubscribe_get_subscribe_page($Settings);
						$Settings['itunes_url'] = powerpresssubscribe_get_itunes_url($Settings);
						$Settings['image_url'] = $Settings['itunes_image'];
						$Settings['subscribe_feature_rss'] = (!empty($GeneralSettings['subscribe_feature_rss']) || !isset($GeneralSettings['subscribe_feature_rss']) );
						$Settings['subscribe_feature_email'] = (!empty($GeneralSettings['subscribe_feature_email']) );
						$Settings['subscribe_feature_gp'] = (!empty($GeneralSettings['subscribe_feature_gp']) );
						$Settings['subscribe_feature_stitcher'] = (!empty($GeneralSettings['subscribe_feature_stitcher']) );
						$Settings['subscribe_feature_tunein'] = (!empty($GeneralSettings['subscribe_feature_tunein']) );
						return $Settings;
					}
				}; break;
			}
		}
		
		return false;
	}
	
	
	// Podcast default and channel feed settings
	$Settings = get_option('powerpress_feed_'. $feed_slug);
	
	if( empty($Settings) && $feed_slug == 'podcast' )
		$Settings = get_option('powerpress_feed'); // Get the main feed settings
	
	if( !empty($Settings) )
	{
		if( empty($Settings['title']) ) {
			$Settings['title'] = get_bloginfo_rss('name'); // Get blog title
		}
		if( !empty($Settings['feed_redirect_url']) )
			$Settings['feed_url'] = $Settings['feed_redirect_url'];
		else
			$Settings['feed_url'] =  get_feed_link($feed_slug); // Get Podcast RSS Feed
		$Settings['subscribe_page_url'] = powerpresssubscribe_get_subscribe_page($Settings);
		$Settings['itunes_url'] = powerpresssubscribe_get_itunes_url($Settings);
		$Settings['image_url'] = $Settings['itunes_image'];
		$Settings['subscribe_feature_rss'] = (!empty($GeneralSettings['subscribe_feature_rss']) || !isset($GeneralSettings['subscribe_feature_rss']) );
		$Settings['subscribe_feature_email'] = (!empty($GeneralSettings['subscribe_feature_email']) );
		$Settings['subscribe_feature_gp'] = (!empty($GeneralSettings['subscribe_feature_gp']) );
		$Settings['subscribe_feature_stitcher'] = (!empty($GeneralSettings['subscribe_feature_stitcher']) );
		$Settings['subscribe_feature_tunein'] = (!empty($GeneralSettings['subscribe_feature_tunein']) );
		if( !empty($FeedSettings['premium']) ) {
			$Settings['subscribe_feature_email'] = false;
			$Settings['subscribe_feature_gp'] = false;
			$Settings['subscribe_feature_stitcher'] = false;
			$Settings['subscribe_feature_tunein'] = false;
		}
		
		return $Settings;
	}
	return false;
}

// 1: Subscribe widget added to the links...
function powerpressplayer_link_subscribe_pre($content, $media_url, $ExtraData = array() )
{
	$detect_category = true;
	if( 'post' != get_post_type() && empty($ExtraData['subscribe_type']) )
	{
		$post_type = get_post_type();
		$ExtraData['subscribe_type'] = 'post_type';
	}
	else if( 'post' == get_post_type() && !empty($ExtraData['category']) ) { // If strict category selected
		$ExtraData['cat_id'] = $ExtraData['category'];
		//$ExtraData['subscribe_type'] = 'category'; // Let the get settings function below figure this out
		$detect_category = false;
	}
	
	$SubscribeSettings = powerpresssubscribe_get_settings( $ExtraData, $detect_category );
	if( empty($SubscribeSettings) )
		return $content;
	
	if( !isset($SubscribeSettings['subscribe_links']) )
		$SubscribeSettings['subscribe_links'] = 1; // Default make this the first link option
		
	if( $SubscribeSettings['subscribe_links'] != 1 ) // beginning of links
		return $content;
		
	$feed_url = $SubscribeSettings['feed_url'];
	$itunes_url = trim($SubscribeSettings['itunes_url']);
	if( empty($itunes_url) )
		$itunes_url = preg_replace('/(^https?:\/\/)/i', 'itpc://', $feed_url);

	$links_array = array();
	if( !empty($itunes_url) ) {
		$links_array[] = "<a href=\"".  htmlspecialchars($itunes_url) ."\" class=\"powerpress_link_subscribe powerpress_link_subscribe_itunes\" title=\"". __('Subscribe on iTunes', 'powerpress') ."\" rel=\"nofollow\">". __('iTunes','powerpress') ."</a>".PHP_EOL_WEB;
	}
	
	if( preg_match('/^(https?:\/\/)(.*)$/i', $feed_url, $matches ) ) {
		
		$android_url =  $matches[1] . 'subscribeonandroid.com/' . $matches[2];
		$links_array[] = "<a href=\"".  htmlspecialchars($android_url) ."\" class=\"powerpress_link_subscribe powerpress_link_subscribe_android\" title=\"". __('Subscribe on Android', 'powerpress') ."\" rel=\"nofollow\">". __('Android','powerpress') ."</a>".PHP_EOL_WEB;
		if( !empty($SubscribeSettings['subscribe_feature_email']) )
		{
			$email_url =  $matches[1] . 'subscribebyemail.com/' . $matches[2];
			$links_array[] = "<a href=\"".  htmlspecialchars($email_url) ."\" class=\"powerpress_link_subscribe powerpress_link_subscribe_email\" title=\"". __('Subscribe by Email', 'powerpress') ."\" rel=\"nofollow\">". __('Email','powerpress') ."</a>".PHP_EOL_WEB;
		}
	}
	
	if( !empty($SubscribeSettings['subscribe_feature_gp']) && !empty($SubscribeSettings['googleplay_url']) )
	{
		$SubscribeSettings['googleplay_url'] = trim($SubscribeSettings['googleplay_url']);
		$links_array[] = "<a href=\"".  esc_attr($SubscribeSettings['googleplay_url'] ) ."\" class=\"powerpress_link_subscribe powerpress_link_subscribe_googleplay\" title=\"". __('Subscribe on Google Play', 'powerpress') ."\" rel=\"nofollow\">". __('Google Play','powerpress') ."</a>".PHP_EOL_WEB;
	}
	
	if( !empty($SubscribeSettings['subscribe_feature_stitcher']) && !empty($SubscribeSettings['stitcher_url']) )
	{
		$SubscribeSettings['stitcher_url'] = trim($SubscribeSettings['stitcher_url']);
		$links_array[] = "<a href=\"".  esc_attr($SubscribeSettings['stitcher_url'] ) ."\" class=\"powerpress_link_subscribe powerpress_link_subscribe_stitcher\" title=\"". __('Subscribe on Stitcher', 'powerpress') ."\" rel=\"nofollow\">". __('Stitcher','powerpress') ."</a>".PHP_EOL_WEB;
	}
	
	if( !empty($SubscribeSettings['subscribe_feature_tunein']) && !empty($SubscribeSettings['tunein_url']) )
	{
		$SubscribeSettings['tunein_url'] = trim($SubscribeSettings['tunein_url']);
		$links_array[] = "<a href=\"".  esc_attr($SubscribeSettings['tunein_url'] ) ."\" class=\"powerpress_link_subscribe powerpress_link_subscribe_tunein\" title=\"". __('Subscribe on TuneIn', 'powerpress') ."\" rel=\"nofollow\">". __('TuneIn','powerpress') ."</a>".PHP_EOL_WEB;
	}

	if( !empty($SubscribeSettings['subscribe_feature_rss']) ) {
		$links_array[] = "<a href=\"". htmlspecialchars($feed_url) ."\" class=\"powerpress_link_subscribe powerpress_link_subscribe_rss\" title=\"". __('Subscribe via RSS', 'powerpress') ."\" rel=\"nofollow\">". __('RSS','powerpress') ."</a>".PHP_EOL_WEB;
	}
	
	if( !empty($SubscribeSettings['subscribe_page_url']) )
	{
		$label = (empty($SubscribeSettings['subscribe_page_link_text'])?__('More', 'powerpress'):$SubscribeSettings['subscribe_page_link_text']);
		$links_array[] = "<a href=\"{$SubscribeSettings['subscribe_page_url']}\" class=\"powerpress_link_subscribe powerpress_link_subscribe_more\" title=\"". htmlspecialchars($label) ."\" rel=\"nofollow\">". htmlspecialchars($label) ."</a>".PHP_EOL_WEB;
	}
	
	$content .= implode(' '.POWERPRESS_LINK_SEPARATOR .' ', $links_array);
	return $content;
}

function powerpressplayer_link_subscribe_post($content, $media_url, $ExtraData = array() )
{
	if( !empty($content) )
	{
		$GeneralSettings = get_option('powerpress_general');
		
		$label = __('Subscribe:', 'powerpress');
		if( !empty($GeneralSettings['subscribe_label']) )
			$label = $GeneralSettings['subscribe_label'];
		// Get label setting from $GeneralSettings
		$prefix = htmlspecialchars($label) . ' ';
		
		$return = '<p class="powerpress_links powerpress_subscribe_links">'. $prefix . $content . '</p>';
		return $return;
	}
	return $content;
}

function powerpress_subscribe_shortcode( $attr ) {
	
	if ( is_feed() ) {
		return '';
	}
	
	// Only works on pages...
	if ( !is_singular() ) {
		return '';
	}

	/*
	extract( shortcode_atts( array(
		'channel'=>'', // Used for PowerPress Podcast Channels
		'slug' => '', // Used for PowerPress (alt for 'channel')
		'feed' => '', // Used for PowerPress (alt for 'channel')
		'post_type' => 'post', // Used for PowerPress 
		'category'=>'', // Used for PowerPress (specify category ID, name or slug)
		'term_taxonomy_id'=>'', // Used for PowerPress (specify term taxonomy ID)
		//'term_id'=>'', // Used for PowerPress (specify term ID, name or slug)
		//'taxonomy'=>'', // Used for PowerPress (specify taxonomy name)
		
		'title'	=> '', // Display custom title of show/program
		'subtitle'=>'', // Subtitle for podcast (optional)
		'feed_url'=>'', // provide subscribe widget for specific RSS feed
		'itunes_url'=>'', // provide subscribe widget for specific iTunes subscribe URL
		'image_url'=>'', // provide subscribe widget for specific iTunes subscribe URL
		'heading'=>'', // heading label for podcast
		
		'itunes_subtitle'=>'', // Set to 'true' to include the iTunes subtitle in subscribe widget
		
		// Appearance attributes
		'itunes_button'=>'', // Set to 'true' to use only the iTunes button
		'itunes_banner'=>'', // Set to 'true' to use only the iTunes banner
		'style'=>'' // Set to 'true' to use only the iTunes banner
	), $attr, 'powerpresssubscribe' ) );
	//return print_r($attr, true);
	*/
	
	/**/
	if( empty($attr['slug']) && !empty($attr['feed']) )
		$attr['slug'] = $attr['feed'];
	else if( empty($attr['slug']) && !empty($attr['channel']) )
		$attr['slug'] = $attr['channel'];
	else if( empty($attr['slug']) )
		$attr['slug'] = 'podcast';
	
	// Set empty args to prevent warnings
	if( !isset($attr['term_taxonomy_id']) )
		$attr['term_taxonomy_id'] = '';
	if( !isset($attr['category_id']) )
		$attr['category_id'] = '';
	if( !isset($attr['post_type']) )
		$attr['post_type'] = '';

	$subscribe_type = '';
	$category_id = '';
		
	if(!empty($attr['category']) )
	{
		$CategoryObj = false;
		if( preg_match('/^[0-9]*$/', $attr['category']) ) // If it is a numeric ID, lets try finding it by ID first...
			$CategoryObj = get_term_by('id', $attr['category'], 'category');
		if( empty($CategoryObj) )
			$CategoryObj = get_term_by('name', $attr['category'], 'category');
		if( empty($CategoryObj) )
			$CategoryObj = get_term_by('slug', $attr['category'], 'category');
		if( !empty($CategoryObj) )
		{
			$category_id = $CategoryObj->term_id;
		}
	}
	

	
	if( !empty($attr['category']) )
		$subscribe_type = 'category';
	else if( !empty($attr['term_taxonomy_id']) )
		$subscribe_type = 'ttid';
	else if( !empty($attr['post_type']) )
		$subscribe_type = 'post_type';
	else if( empty($attr['post_type']) && !empty($attr['slug']) && $attr['slug'] != 'podcast' )
		$subscribe_type = 'channel';
	
	$Settings = array();
	if( !empty($attr['feed_url']) )
	{
		$Settings['feed_url'] = $attr['feed_url'];
	}
	else
	{
		$Settings = powerpresssubscribe_get_settings(  array('feed'=>$attr['slug'], 'taxonomy_term_id'=>$attr['term_taxonomy_id'], 'cat_id'=>$category_id, 'post_type'=>$attr['post_type'], 'subscribe_type'=>$subscribe_type), false );
	}
	
	// Podcast title handling
	if( isset($attr['title']) && empty($attr['title']) && isset($Settings['title']) )
		unset( $Settings['title'] ); // Special case, if the title is unset, then it should not be displayed
	else if( !empty($attr['title']) )
		$Settings['title'] = $attr['title'];
	else if( !isset($Settings['title']) )
		$Settings['title'] = ''; // This way the title can be detected
		
	unset($Settings['subtitle']); // Make sure no subtitle passes this point
	if( !empty($attr['itunes_subtitle']) && !empty($Settings['itunes_subtitle']) ) {
		$Settings['subtitle'] = $Settings['itunes_subtitle'];
	} else if( !empty($attr['subtitle']) ) {
		$Settings['subtitle'] = $attr['subtitle'];
	}
		
	
	if( !empty($attr['itunes_url']) )
		$Settings['itunes_url'] = $attr['itunes_url'];
	if( !empty($attr['style']) )
		$Settings['style'] = $attr['style'];
	if( !empty($attr['image_url']) )
		$Settings['image_url'] = $attr['image_url'];	
	if( isset($attr['heading']) ) // If a custom heading is set
		$Settings['heading'] = $attr['heading'];
		
	if( empty($Settings) )
		return '';	
		
	$Settings['itunes_url'] = powerpresssubscribe_get_itunes_url($Settings);
	wp_enqueue_style( 'powerpress-subscribe-style' );
	
	if( !empty($attr['itunes_button']) && !empty($Settings['itunes_url']) )
	{
		$html = '<div>';
		$html .= '';
		$html .='<a href="';
		$html .= esc_url($Settings['itunes_url']);
		$html .= '" target="itunes_store" style="display:inline-block;overflow:hidden;background:url(https://linkmaker.itunes.apple.com/htmlResources/assets/en_us/images/web/linkmaker/badge_subscribe-lrg.png) no-repeat;width:135px;height:40px;}"></a>';
		$html .= '</div>';
		return $html;
	}
	
	if( !empty($attr['itunes_banner']) && !empty($Settings['itunes_url']) )
	{
		$apple_id = powerpress_get_apple_id($Settings['itunes_url'], true);
		if( !empty($apple_id) && $apple_id > 0 )
		{
			$html = '';
			$html .= '<div id="ibb-widget-root-'.$apple_id.'"></div>';
			$html .= "<script>(function(t,e,i,d){var o=t.getElementById(i),n=t.createElement(e);o.style.height=250;o.style.width=300;o.style.display='inline-block';n.id='ibb-widget',n.setAttribute('src',('https:'===t.location.protocol?'https://':'http://')+d),n.setAttribute('width','300'),n.setAttribute('height','250'),n.setAttribute('frameborder','0'),n.setAttribute('scrolling','no'),o.appendChild(n)})(document,'iframe','ibb-widget-root-".$apple_id."'";
			$html .= ',"banners.itunes.apple.com/banner.html?partnerId=&aId=&bt=catalog&t=catalog_blur&id='.$apple_id.'&c=us&l=en-US&w=300&h=250");</script>';
			return $html;
		}
		return '';
	}
	
	
	return powerpress_do_subscribe_widget($Settings);
}

add_shortcode( 'powerpresssubscribe', 'powerpress_subscribe_shortcode' );
add_shortcode( 'powerpress_subscribe', 'powerpress_subscribe_shortcode' );
	
require_once( POWERPRESS_ABSPATH . '/class.powerpress-subscribe-widget.php' );

function powerpress_do_subscribe_widget($settings)
{
	if( empty($settings['feed_url']) )
	{
		return '';
	}
	
	if( isset($settings['title']) && empty($settings['title']) )
	{
		$settings['title'] = get_bloginfo_rss('name');
	}
	
	if( empty($settings['itunes_url']) )
	{
		$settings['itunes_url'] = powerpresssubscribe_get_itunes_url( $settings );
	}
	
	if( empty($settings['style']) )
	{
		$settings['style'] = 'modern';
	}
	
	if( empty($settings['image_url']) )
	{
		$settings['image_url'] = powerpress_get_root_url() . 'itunes_default.jpg'; // Default PowerPress image used in this case.
	}
	
	$PowerPressSettings = get_option('powerpress_general');
	
	$html = '';
	$html .= '<div class="pp-sub-widget pp-sub-widget-'. esc_attr($settings['style']) .'">';
	if( !empty($settings['title']) )
	{
		if( !isset($settings['heading']) ) { // If not specified in the shortcode
				$settings['heading'] = __('Subscribe to', 'powerpress');
		}

		if( !empty($settings['heading']) ) { // If there is a value set for the heading, lets use it
			$html .= '<div class="pp-sub-h">'.  esc_html($settings['heading']) .'</div>';
		}

		$html .= '<h2 class="pp-sub-t">'.  esc_html( $settings['title'] ) .'</h2>';
	}
	else
	{
		$settings['title'] = ''; // Make sure it's an empty string
	}
	
	if( !empty($settings['subtitle']) )
	{
		$html .= '<p class="pp-sub-st">'.  esc_html( $settings['subtitle'] ) .'</p>';
	}
		
		// Lets build the subscribe box...
			$html .= '<div class="pp-sub-bx">';
				$html .= '<img class="pp-sub-l" src="'. esc_url( $settings['image_url'] ) .'" '. (!empty($settings['title'])?' title="'.  esc_attr($settings['title']).'" ':'') .'/>';
				$html .= '<div class="pp-sub-btns">';
				if( !empty($settings['itunes_url']) ) {
					$html .= '<a href="'.  esc_url( $settings['itunes_url'] ) .'" class="pp-sub-btn pp-sub-itunes" title="'.  esc_attr( __('Subscribe on iTunes', 'powerpress') ) .'"><span class="pp-sub-ic"></span>'.  esc_html( __('on iTunes', 'powerpress') ) .'</a>';
				}
				
				if( preg_match('/^(https?:\/\/)(.*)$/i', $settings['feed_url'], $matches ) ) {
					$android_url =  $matches[1] . 'subscribeonandroid.com/' . $matches[2];
					$html .= '<a href="'.  esc_url( $android_url ) .'" class="pp-sub-btn pp-sub-android" title="'.  esc_attr( __('Subscribe on Android', 'powerpress') ) .'"><span class="pp-sub-ic"></span>'.  esc_html( __('on Android', 'powerpress') ) .'</a>';
					if( true ) {
						$email_url =  $matches[1] . 'subscribebyemail.com/' . $matches[2];
						$html .= '<a href="'.  esc_url( $email_url ) .'" class="pp-sub-btn pp-sub-email" title="'.  esc_attr( __('Subscribe by Email', 'powerpress') ) .'"><span class="pp-sub-ic"></span>'.  esc_html( __('by Email', 'powerpress') ) .'</a>';
					}
				}
				
				if( !empty($settings['googleplay_url']) )
				{
					$html .= '<a href="'.  esc_url( $settings['googleplay_url'] ) .'" class="pp-sub-btn pp-sub-gp" title="'.  esc_attr( __('Subscribe on Google Play', 'powerpress') ) .'"><span class="pp-sub-ic"></span>'.  esc_html( __('on Google Play', 'powerpress') ) .'</a>';
				}
				
				if( !empty($settings['stitcher_url']) )
				{
					$html .= '<a href="'.  esc_url( $settings['stitcher_url'] ) .'" class="pp-sub-btn pp-sub-stitcher" title="'.  esc_attr( __('Subscribe on Stitcher', 'powerpress') ) .'"><span class="pp-sub-ic"></span>'.  esc_html( __('on Stitcher', 'powerpress') ) .'</a>';
				}
				
				if( !empty($settings['tunein_url']) )
				{
					$html .= '<a href="'.  esc_url( $settings['tunein_url'] ) .'" class="pp-sub-btn pp-sub-tunein" title="'.  esc_attr( __('Subscribe on TuneIn', 'powerpress') ) .'"><span class="pp-sub-ic"></span>'.  esc_html( __('on TuneIn', 'powerpress') ) .'</a>';
				}
				
				//$html .= var_dump($settings, true);
				
				if( !empty($settings['subscribe_feature_rss']) ) {
					$html .= '<a href="'.  esc_url( $settings['feed_url'] ) .'" class="pp-sub-btn pp-sub-rss" title="'.  esc_attr( __('Subscribe via RSS', 'powerpress') ) .'"><span class="pp-sub-ic"></span>'.  esc_html( __('via RSS', 'powerpress') ) .'</a>';
				}

			$html .= '</div>';
		$html .= '</div>';
		$html .= '<div class="pp-sub-m">';
			$html .= '<p class="pp-sub-m-p">'.  esc_html( __('Or subscribe with your favorite app by using the address below', 'powerpress') ) .'</p>';
			$html .= '<input class="pp-sub-m-i" type="text" name="NULL'. rand(0,9999) .'" value="'.  esc_attr( $settings['feed_url'] ) .'" onclick="this.focus();this.select();" />';
		$html .= '</div>';
	$html .= '</div>';

	return $html;
}

function powerpress_do_subscribe_sidebar_widget($settings)
{
	
	if( empty($settings['feed_url']) )
	{
		return '';
	}
	
	if( empty($settings['itunes_url']) )
	{
		$settings['itunes_url'] = powerpresssubscribe_get_itunes_url( $settings );
	}
	
	if( empty($settings['style']) )
	{
		$settings['style'] = 'modern';
	}
	
	$html = '';
	$html .= '<div class="pp-ssb-widget pp-ssb-widget-'. esc_attr($settings['style']) .'">';
		if( !empty($settings['itunes_url']) ) {
			$html .= '<a href="'.  esc_url( $settings['itunes_url'] ) .'" class="pp-ssb-btn pp-ssb-itunes" title="'.  esc_attr( __('Subscribe on iTunes', 'powerpress') ) .'"><span class="pp-ssb-ic"></span>'.  esc_html( __('on iTunes', 'powerpress') ) .'</a>';
		}
		
		if( preg_match('/^(https?:\/\/)(.*)$/i', $settings['feed_url'], $matches ) ) {
			$android_url =  $matches[1] . 'subscribeonandroid.com/' . $matches[2];
			$html .= '<a href="'.  esc_url( $android_url ) .'" class="pp-ssb-btn pp-ssb-android" title="'.  esc_attr( __('Subscribe on Android', 'powerpress') ) .'"><span class="pp-ssb-ic"></span>'.  esc_html( __('on Android', 'powerpress') ) .'</a>';
			if( !empty($settings['subscribe_feature_email']) ) {
				$email_url =  $matches[1] . 'subscribebyemail.com/' . $matches[2];
				$html .= '<a href="'.  esc_url( $email_url ) .'" class="pp-ssb-btn pp-ssb-email" title="'.  esc_attr( __('Subscribe by Email', 'powerpress') ) .'"><span class="pp-ssb-ic"></span>'.  esc_html( __('by Email', 'powerpress') ) .'</a>';
			}
		}
		
		if(  !empty($settings['subscribe_feature_gp']) && !empty($settings['googleplay_url']) ) {
			$settings['googleplay_url'] = trim($settings['googleplay_url']);
			$html .= '<a href="'.  esc_url( $settings['googleplay_url'] ) .'" class="pp-ssb-btn pp-ssb-gp" title="'.  esc_attr( __('Subscribe on Google Play', 'powerpress') ) .'"><span class="pp-ssb-ic"></span>'.  esc_html( __('on Google Play', 'powerpress') ) .'</a>';
		}
		
		if( !empty($settings['subscribe_feature_stitcher']) && !empty($settings['stitcher_url']) ) {
			$settings['stitcher_url'] = trim($settings['stitcher_url']);
			$html .= '<a href="'.  esc_url( $settings['stitcher_url'] ) .'" class="pp-ssb-btn pp-ssb-stitcher" title="'.  esc_attr( __('Subscribe on Stitcher', 'powerpress') ) .'"><span class="pp-ssb-ic"></span>'.  esc_html( __('on Stitcher', 'powerpress') ) .'</a>';
		}
		
		if( !empty($settings['subscribe_feature_tunein']) && !empty($settings['tunein_url']) ) {
			$settings['tunein_url'] = trim($settings['tunein_url']);
			$html .= '<a href="'.  esc_url( $settings['tunein_url'] ) .'" class="pp-ssb-btn pp-ssb-tunein" title="'.  esc_attr( __('Subscribe on TuneIn', 'powerpress') ) .'"><span class="pp-ssb-ic"></span>'.  esc_html( __('on TuneIn', 'powerpress') ) .'</a>';
		}
		
		if( !empty($settings['subscribe_feature_rss']) ) {
			$html .= '<a href="'.  esc_url( $settings['feed_url'] ) .'" class="pp-ssb-btn pp-ssb-rss" title="'.  esc_attr( __('Subscribe via RSS', 'powerpress') ) .'"><span class="pp-ssb-ic"></span>'.  esc_html( __('via RSS', 'powerpress') ) .'</a>';
		}
		
		if( !empty($settings['subscribe_page_url']) )
			$html .= '<a href="'.  esc_url( $settings['subscribe_page_url'] ) .'" class="pp-ssb-btn pp-ssb-more" title="'.  esc_attr( __('More Subscribe Options', 'powerpress') ) .'"><span class="pp-ssb-ic"></span>'.  esc_html( __('More Subscribe Options', 'powerpress') ) .'</a>';
	$html .= '</div>';

	return $html;
}