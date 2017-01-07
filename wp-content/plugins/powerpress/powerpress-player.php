<?php
/*
PowerPress player options
*/


function powerpressplayer_get_next_id()
{
	if( !isset($GLOBALS['g_powerpress_player_id']) ) // Use the global unique player id variable for the surrounding div
		$GLOBALS['g_powerpress_player_id'] = rand(0, 10000);
	else
		$GLOBALS['g_powerpress_player_id']++; // increment the player id for the next div so it is unique
	return $GLOBALS['g_powerpress_player_id'];
}

function powerpressplayer_get_extension($media_url, $EpisodeData = array() )
{
	$extension = 'unknown';
	$parts = pathinfo($media_url);
	if( !empty($parts['extension']) )
		$extension = strtolower($parts['extension']);
	
	// Hack to use the audio/mp3 content type to set extension to mp3, some folks use tinyurl.com to mp3 files which remove the file extension...
	if( isset($EpisodeData['type']) && $EpisodeData['type'] == 'audio/mpeg' && $extension != 'mp3' )
		$extension = 'mp3';
	
	return $extension;
}

/*
Initialize powerpress player handling
*/
function powerpressplayer_init($GeneralSettings)
{
	if( !empty($GeneralSettings['seo_video_objects']) )
		add_filter('powerpress_player', 'powerpressplayer_mediaobjects_video', 1, 3); // Before everythign is added
	if( !empty($GeneralSettings['seo_audio_objects']) )
		add_filter('powerpress_player', 'powerpressplayer_mediaobjects_audio', 1, 3); // Before everythign is added
	if( !empty($GeneralSettings['seo_audio_objects']) || !empty($GeneralSettings['seo_video_objects']) )
		add_filter('powerpress_player', 'powerpressplayer_mediaobjects_post', 1000, 3); // After everythign is added

	if( isset($_GET['powerpress_pinw']) )
		powerpress_do_pinw($_GET['powerpress_pinw'], !empty($GeneralSettings['process_podpress']) );
		
	if( isset($_GET['powerpress_embed']) )
	{
		$player = ( !empty($_GET['powerpress_player']) ? $_GET['powerpress_player'] : 'mejs-v' );
		powerpress_do_embed($player, $_GET['powerpress_embed'], !empty($GeneralSettings['process_podpress']) );
	}
	
	// If we are to process podpress data..
	if( !empty($GeneralSettings['process_podpress']) )
	{
		add_shortcode('display_podcast', 'powerpress_shortcode_handler');
	}
	
	/*
	// include what's needed for each plaer
	if( defined('POWERPRESS_JS_DEBUG') )
		wp_enqueue_script( 'powerpress-player', powerpress_get_root_url() .'player.js');
	else
		wp_enqueue_script( 'powerpress-player', powerpress_get_root_url() .'player.min.js');

	
	$enqueue_mejs = false;
	if( !isset($GeneralSettings['player']) || !isset($GeneralSettings['video_player']) )
	{
		$enqueue_mejs = true;
	}
	else if( !empty($GeneralSettings['player']) && $GeneralSettings['player'] == 'mediaelement-audio' )
	{
		$enqueue_mejs = true;
	}
	else if( !empty($GeneralSettings['video_player']) && $GeneralSettings['video_player'] == 'mediaelement-video' )
	{
		$enqueue_mejs = true;
	}
	
	if( $enqueue_mejs )
	{
		wp_enqueue_style('wp-mediaelement');
		wp_enqueue_script('wp-mediaelement');
	}
	*/
}


function powerpress_shortcode_handler( $attributes, $content = null )
{
	global $post, $g_powerpress_player_added;
	
	// We can't add flash players to feeds
	if( is_feed() )
		return '';
	
	$return = '';
	$feed = '';
	$channel = '';
	$slug = ''; // latest and preferred way to specify the feed slug
	$url = '';
	$image = '';
	$width = '';
	$height = '';
	
	extract( shortcode_atts( array(
			'url' => '',
			'feed' => '',
			'channel' => '',
			'slug' => '',
			'image' => '',
			'width' => '',
			'height' => ''
		), $attributes ) );
		
	if( empty($channel) && !empty($feed) ) // Feed for backward compat.
		$channel = $feed;
	if( !empty($slug) ) // Foward compatibility
		$channel = $slug;
	
	if( !$url && $content )
	{
		$content_url = trim($content);
		if( @parse_url($content_url) )
			$url = $content_url;
	}
	
	if( $url )
	{
		$url = powerpress_add_redirect_url($url);
		$content_type = '';
		// Handle the URL differently...
		do_action('wp_powerpress_player_scripts');
		$return = apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($url, 'p'), array('image'=>$image, 'type'=>$content_type,'width'=>$width, 'height'=>$height) );
	}
	else if( $channel )
	{
		$EpisodeData = powerpress_get_enclosure_data($post->ID, $channel);
		if( !empty($EpisodeData['embed']) )
			$return = $EpisodeData['embed'];
		
		// Shortcode over-ride settings:
		if( !empty($image) )
			$EpisodeData['image'] = $image;
		if( !empty($width) )
			$EpisodeData['width'] = $width;
		if( !empty($height) )
			$EpisodeData['height'] = $height;
		
		
		if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !powerpress_premium_content_authorized($channel) )
		{
			$return .= powerpress_premium_content_message($post->ID, $channel, $EpisodeData);
		}
		else
		{
			do_action('wp_powerpress_player_scripts');
			// If the shortcode specifies a channel, than we definitely want to include the player even if $EpisodeData['no_player'] is true...
			if( !isset($EpisodeData['no_player']) )
				$return = apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), array('id'=>$post->ID,'feed'=>$channel, 'channel'=>$channel, 'image'=>$image, 'type'=>$EpisodeData['type'],'width'=>$width, 'height'=>$height) );
			if( empty($EpisodeData['no_links']) ) {
				$return .= apply_filters('powerpress_player_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
				$return .= apply_filters('powerpress_player_subscribe_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
		}
	}
	else
	{
		$GeneralSettings = get_option('powerpress_general');
		if( !isset($GeneralSettings['custom_feeds']['podcast']) )
			$GeneralSettings['custom_feeds']['podcast'] = 'Podcast Feed'; // Fixes scenario where the user never configured the custom default podcast feed.
		
		// If we have post type podcasting enabled, then we need to use the podcast post type feeds instead here...
		if( !empty($GeneralSettings['posttype_podcasting']) )
		{
			$post_type = get_query_var('post_type');
			// Get the feed slugs and titles for this post type
			$PostTypeSettingsArray = get_option('powerpress_posttype_'.$post_type);
			// Loop through this array...
			if( !empty($PostTypeSettingsArray) )
			{
				switch($post_type)
				{
					case 'post':
					case 'page': {
						// Do nothing!, we want the default podcast and channels to appear in these post types
					}; break;
					default: {
						$GeneralSettings['custom_feeds'] = array(); // reset this array since we're working with  a custom post type
					};
				}
				
				while( list($feed_slug, $postTypeSettings) = each($PostTypeSettingsArray) )
				{
					if( !empty( $postTypeSettings['title']) )
						$GeneralSettings['custom_feeds'][ $feed_slug ] = $postTypeSettings['title'];
					else
						$GeneralSettings['custom_feeds'][ $feed_slug ] = $feed_slug;
				}
			}
		}
	
		while( list($feed_slug,$feed_title)  = each($GeneralSettings['custom_feeds']) )
		{
			if( isset($GeneralSettings['disable_player']) && isset($GeneralSettings['disable_player'][$feed_slug]) )
				continue;
			
			$EpisodeData = powerpress_get_enclosure_data($post->ID, $feed_slug);
			if( !$EpisodeData && !empty($GeneralSettings['process_podpress']) && $feed_slug == 'podcast' )
				$EpisodeData = powerpress_get_enclosure_data_podpress($post->ID);
				
			if( !$EpisodeData )
				continue;
				
			if( !empty($EpisodeData['embed']) )
				$return .= $EpisodeData['embed'];
			
			// Shortcode over-ride settings:
			if( !empty($image) )
				$EpisodeData['image'] = $image;
			if( !empty($width) )
				$EpisodeData['width'] = $width;
			if( !empty($height) )
				$EpisodeData['height'] = $height;
				
			if( isset($GeneralSettings['premium_caps']) && $GeneralSettings['premium_caps'] && !powerpress_premium_content_authorized($feed_slug) )
			{
				$return .= powerpress_premium_content_message($post->ID, $feed_slug, $EpisodeData);
				continue;
			}
				
			if( !isset($EpisodeData['no_player']) )
			{
				do_action('wp_powerpress_player_scripts');
				$return .= apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
			if( !isset($EpisodeData['no_links']) )
			{
				do_action('wp_powerpress_player_scripts');
				$return .= apply_filters('powerpress_player_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
				$return .= apply_filters('powerpress_player_subscribe_links', '',  powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), $EpisodeData );
			}
		}
	}
	
	return $return;
}

add_shortcode('powerpress', 'powerpress_shortcode_handler');
if( !defined('PODCASTING_VERSION') )
{
	add_shortcode('podcast', 'powerpress_shortcode_handler');
}

function wp_powerpress_player_scripts()
{
	// include what's needed for each plaer
	if( defined('POWERPRESS_JS_DEBUG') )
		wp_enqueue_script( 'powerpress-player', powerpress_get_root_url() .'player.js');
	else
		wp_enqueue_script( 'powerpress-player', powerpress_get_root_url() .'player.min.js');
}
add_action( 'wp_powerpress_player_scripts', 'wp_powerpress_player_scripts' );


/*
// Everything in $ExtraData except post_id
*/
function powerpress_generate_embed($player, $EpisodeData) // $post_id, $feed_slug, $width=false, $height=false, $media_url = false, $autoplay = false)
{
	if( empty($EpisodeData['id']) && empty($EpisodeData['feed']) )
		return '';
		
	if( $player == 'blubrryaudio' )
	{
		$extension = powerpressplayer_get_extension($EpisodeData['url']);
		if( $extension == 'mp3' || $extension == 'm4a' ) 
		{
			return powerpressplayer_build_blubrryaudio($EpisodeData['url'], $EpisodeData);
		}
		return '';
	}
	
	$width = 0;
	$height = 0;
	if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
		$width = $EpisodeData['width'];
	if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
		$height = $EpisodeData['height'];
	
	// More efficient, only pull the general settings if necessary
	if( $height == 0 || $width == 0 )
	{
		$GeneralSettings = get_option('powerpress_general');
		if( $width == 0 )
		{
			$width = 400;
			if( !empty($GeneralSettings['player_width']) )
				$width = $GeneralSettings['player_width'];
		}
		
		if( $height == 0 )
		{
			$height = 400;
			if( !empty($GeneralSettings['player_height']) )
				$height = $GeneralSettings['player_height'];
		}
		
		$extension = powerpressplayer_get_extension($EpisodeData['url']);
		if( $player == 'mediaelement-audio' )
		{
			if( $extension == 'mp3' || $extension == 'm4a' || $extension == 'oga')
			{
				$height = 30; // Hack for audio to only include the player without the poster art
				$width = 320;
				if( !empty($GeneralSettings['player_width_audio']) )
					$width = $GeneralSettings['player_width_audio'];
			}
		}
		else if ( $player == 'default' )
		{
			if(  ($extension == 'mp3' || $extension == 'm4a' ) && empty($Settings['poster_image_audio']) )
			{
				$height = 24; // Hack for audio to only include the player without the poster art
				$width = 320;
				if( !empty($GeneralSettings['player_width_audio']) )
					$width = $GeneralSettings['player_width_audio'];
			}
		}
	}
	
	$embed = '';
	$url = get_bloginfo('url') .'/?powerpress_embed=' . $EpisodeData['id'] .'-'. $EpisodeData['feed'];
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$url .= '&amp;autoplay=true';
		
	$url .= '&amp;powerpress_player='.$player;
	$embed .= '<iframe';
	//$embed .= ' class="powerpress-player-embed"';
	$embed .= ' width="'. $width .'"';
	$embed .= ' height="'. $height .'"';
	$embed .= ' src="'. $url .'"';
	$embed .= ' frameborder="0" scrolling="no"';
	if($extension != 'mp3' && $extension != 'm4a' && $extension != 'oga')
		$embed .= ' webkitAllowFullScreen mozallowfullscreen allowFullScreen';
	$embed .= '></iframe>';
	return $embed;
}

function do_powerpressplayer_embed($player, $media_url, $EpisodeData = array())
{
	// Includde the stuff we need...
	wp_enqueue_style('wp-mediaelement');
	wp_enqueue_script('wp-mediaelement');
	
	$mejs_video = false;
	$mejs_audio = false;
	$content_type = powerpress_get_contenttype($media_url);
	if( preg_match('/audio\/(mpeg|x-m4a|ogg)/i', $content_type ) )
		$mejs_audio = true;
	else if( preg_match('/video\/(mpeg|mp4|x-m4v|ogg)/i', $content_type ) )
		$mejs_video = true;
	
	$content = '';
	$content .= '<!DOCTYPE html>'. PHP_EOL;
	$content .= '<html xmlns="http://www.w3.org/1999/xhtml">'. PHP_EOL;
	$content .= '<head>'. PHP_EOL;
	$content .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'. PHP_EOL;
	$content .= '<title>'. __('Blubrry PowerPress Player', 'powerpress') .'</title>'. PHP_EOL;
	$content .= '<meta name="robots" content="noindex" />'. PHP_EOL;
	echo $content;
	
	wp_print_styles();
	wp_print_scripts();
	
	$content = '';
	$content .= '<script type="text/javascript"><!--'. PHP_EOL;
	$content .= 'jQuery(document).ready(function($) {'. PHP_EOL;
		$content .= '  powerpress_load_player();'. PHP_EOL;
		$content .= '  jQuery(window).resize(function() {'. PHP_EOL;
		$content .= '    powerpress_resize_player();'. PHP_EOL;
		$content .= '  });'. PHP_EOL;
	$content .= '});'. PHP_EOL;
	
	$content .= 'function powerpress_load_player() {'. PHP_EOL;
		$content .= '  powerpress_resize_player();'.PHP_EOL;
	$content .= '}'. PHP_EOL;
	
	$content .= 'function powerpress_resize_player() { '. PHP_EOL;
	if( $mejs_video )
	{
		$content .= '  if( ( parseInt(jQuery(window).width()) * 0.5625) >= parseInt(jQuery(window).height() ) ) {'. PHP_EOL;
		$content .= '  	var height = parseInt(jQuery(window).height())-10;'. PHP_EOL;
		$content .= '  	var width = Math.round((height*16) / 9)+\'px\';'. PHP_EOL;
		$content .= '  	jQuery(\'.powerpress_player\').css(\'width\', width );'. PHP_EOL;
		$content .= '  	jQuery(\'.powerpress_player\').css(\'height\', height+\'px\' );'. PHP_EOL;
		$content .= '  } else {'. PHP_EOL;
		$content .= '  	jQuery(\'.powerpress_player\').css(\'width\', \'100%\' );'. PHP_EOL;
		$content .= '  	jQuery(\'.powerpress_player\').css(\'height\', \'100%\' );'. PHP_EOL;
		$content .= '  }'. PHP_EOL;
	}
	$content .= '}'. PHP_EOL;
	$content .= "//-->\n";
	$content .= '</script>'. PHP_EOL;
	$content .= '<style type="text/css" media="screen">' . PHP_EOL;
	$content .= '	body { font-size: 13px; font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; background-color:transparent; } img { border: 0; } ' . PHP_EOL;
	
	if( $mejs_video )
	{
	$content .= '
	.powerpress_player { margin: 0 auto; }
	.mejs-container {
		width: 100% !important;
  height: auto !important;
		max-height: 500px  !important;
  padding-top: 57%;
}
.mejs-overlay, .mejs-poster {
  width: 100% !important;
  height: 100% !important;
}
.mejs-mediaelement video {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  width: 100% !important;
  height: 100% !important;
}
';
	}
	else
	{
				$content .= '.powerpress_player  .wp-audio-shortcode {
	max-width: 100% !important;
}';
	}
	$content .= '</style>' . PHP_EOL;
	$content .= '</head>'. PHP_EOL;
	$content .= '<body>'. PHP_EOL;
	
	// Body specific content for player
	if( $mejs_audio )
		$content .= powerpressplayer_build_mediaelementaudio($media_url, $EpisodeData, true);
	else if( $mejs_video )
		$content .= powerpressplayer_build_mediaelementvideo($media_url, $EpisodeData, true);
	else
		$content .= '<strong>'. __('Player Not Available', 'powerpress') .'</strong>';

	$content .= '</body>'. PHP_EOL;
	$content .= '</html>'. PHP_EOL;
	echo $content;
}


/*
Audio Players - Flash/HTML5 compliant mp3 audio

@since 2.0
@content - 
@param string $content Content of post or page to add player to
@param string $media_url Media URL to add player for
@param array $EpisodeData Array of key/value settings that optionally can contribute to player being added
@return string $content The content, possibly modified wih player added
*/
function powerpressplayer_player_audio($content, $media_url, $EpisodeData = array() )
{
	$extension = powerpressplayer_get_extension($media_url);
	switch( $extension )
	{
		// MP3
		case 'mp3':
		{
			$Settings = get_option('powerpress_general');
			if( !isset($Settings['player']) )
				$Settings['player'] = 'mediaelement-audio';
				
			switch( $Settings['player'] )
			{
				case 'blubrryaudio': {
					$content .= powerpressplayer_build_blubrryaudio($media_url, $EpisodeData);
				}; break;
				case 'audio-player': {
					$content .= powerpressplayer_build_1pxoutplayer($media_url, $EpisodeData);
				}; break;
				case 'html5audio': {
					$content .= powerpressplayer_build_html5audio($media_url, $EpisodeData);
				}; break;
				case 'mediaelement-audio': 
				default: {
					$content .= powerpressplayer_build_mediaelementaudio($media_url, $EpisodeData);
				}; break;
			}
		
		}; break;
		case 'm4a': {
		
			$Settings = get_option('powerpress_general');
			if( !isset($Settings['player']) )
				$Settings['player'] = 'mediaelement-audio';
			
			switch( $Settings['player'] )
			{
				case 'blubrryaudio': {
					$content .= powerpressplayer_build_blubrryaudio($media_url, $EpisodeData);
				}; break;
				case 'html5audio': {
					$content .= powerpressplayer_build_html5audio($media_url, $EpisodeData);
				}; break;
				case 'mediaelement-audio': {
					$content .= powerpressplayer_build_mediaelementaudio($media_url, $EpisodeData);
				}; break;
				default: {
					$content .= powerpressplayer_build_playimageaudio($media_url, true);
				};
			}
		
			// Use Flow player if configured
		}; break;
		case 'ogg': {
			if( defined('POWERPRESS_OGG_VIDEO') && POWERPRESS_OGG_VIDEO )
				return $content; // Ogg is handled as video
		}
		case 'oga': {
			if( !isset($Settings['player']) )
				$Settings['player'] = 'mediaelement-audio';
				
			switch( $Settings['player'] )
			{
				case 'mediaelement-audio': {
					$content .= powerpressplayer_build_mediaelementaudio($media_url, $EpisodeData);
				}; break;
				case 'html5audio': 
				default: {
					$content .= powerpressplayer_build_html5audio($media_url, $EpisodeData);
				}
			}
		}; break;
	}

	return $content;
}

/*
Video Players - HTML5/Flash compliant video formats
*/
function powerpressplayer_player_video($content, $media_url, $EpisodeData = array() )
{
	$extension = powerpressplayer_get_extension($media_url);
	switch( $extension )
	{
		// OGG (audio or video)
		case 'ogg': {
			// Ogg special case, we treat as audio unless specified otherwise
			if( !defined('POWERPRESS_OGG_VIDEO') || POWERPRESS_OGG_VIDEO == false )
				return $content;
		}
		// OGG Video / WebM
		case 'webm': 
		case 'ogv': { // Use native player when possible
			$Settings = get_option('powerpress_general');
			if( !isset($Settings['video_player']) )
				$Settings['video_player'] = 'mediaelement-video';
			else if( !isset($Settings['video_player']) )
				$Settings['video_player'] = 'html5video';
			
			// HTML5 Video as an embed
			switch( $Settings['video_player'] )
			{
				case 'videojs-html5-video-player-for-wordpress': {
					$content .= powerpressplayer_build_videojs($media_url, $EpisodeData);
				}; break;
				case 'mediaelement-video': {
					$content .= powerpressplayer_build_mediaelementvideo($media_url, $EpisodeData);
				}; break;
				default: {
					$content .= powerpressplayer_build_html5video($media_url, $EpisodeData);
				}; break;
			}
		}; break;
		// H.264
		case 'm4v':
		case 'mp4':
		// Okay, lets see if we we have a player setup to handle this
		{
			$Settings = get_option('powerpress_general');
			if( !isset($Settings['video_player']) )
				$Settings['video_player'] = 'mediaelement-video';
			
			switch( $Settings['video_player'] )
			{
				case 'videojs-html5-video-player-for-wordpress': {
					$content .= powerpressplayer_build_videojs($media_url, $EpisodeData);
				}; break;
				case 'html5video': {
					// HTML5 Video as an embed
					$content .= powerpressplayer_build_html5video($media_url, $EpisodeData);
				}; break;
				case 'mediaelement-video':
				default: {
					$content .= powerpressplayer_build_mediaelementvideo($media_url, $EpisodeData);
				}; break;
			}
		}; break;
	}
	
	return $content;
}

function powerpressplayer_player_other($content, $media_url, $EpisodeData = array() )
{
	// Very important setting, we need to know if the media should auto play or not...
	$autoplay = false; // (default)
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true;
	$cover_image = '';
	if( !empty($EpisodeData['image']) )
		$cover_image = $EpisodeData['image'];
	
	$extension = powerpressplayer_get_extension($media_url);
	
	switch( $extension )
	{
		// Common formats, we already handle them separately
		case 'mp3':
		case 'mp4':
		case 'm4v':
		case 'webm';
		case 'ogg':
		case 'ogv':
		case 'oga':
		case 'flv':
		case 'm4a': {
			
			return $content; 
		}; break;
		case 'swf': // No more support for flash swf files
		case 'avi':
		case 'mpg':
		case 'mpeg':
		case 'm4b':
		case 'm4r':
		case 'qt':
		case 'mov': { // No more quicktime on multiple platforms, lets display an image with a link and hope for the best
			
			$content .= powerpressplayer_build_playimage($media_url, $EpisodeData, true);
			
		}; break;
		
		// Windows Media:
		case 'wma':
		case 'wmv':
		case 'asf': {
			
			$Settings = get_option('powerpress_general');
			$player_id = powerpressplayer_get_next_id();
			$player_width = 400;
			$player_height = 225;
			if( !empty($Settings['player_width']) )
				$player_width = $Settings['player_width'];
			if( !empty($Settings['player_height']) )
				$player_height = $Settings['player_height'];
			if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
				$player_width = $EpisodeData['width'];
			if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
				$player_height = $EpisodeData['height'];
				
			// If there is no cover image specified, lets use the default...
			if( $cover_image == '' )
				$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
				
			$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
			$firefox = (stripos($_SERVER['HTTP_USER_AGENT'], 'firefox') !== false );
			
			if( (!$cover_image && !$firefox ) || $autoplay ) // if we don't have a cover image or we're supposed to auto play the media anyway...
			{
				$content .= '<object id="winplayer" classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'. $player_width .'" height="'. $player_height .'" standby="..." type="application/x-oleobject">';
				$content .= '	<param name="url" value="'. $media_url .'" />';
				$content .= '	<param name="AutoStart" value="'. ($autoplay?'true':'false') .'" />';
				$content .= '	<param name="AutoSize" value="true" />';
				$content .= '	<param name="AllowChangeDisplaySize" value="true" />';
				$content .= '	<param name="standby" value="Media is loading..." />';
				$content .= '	<param name="AnimationAtStart" value="true" />';
				$content .= '	<param name="scale" value="aspect" />';
				$content .= '	<param name="ShowControls" value="true" />';
				$content .= '	<param name="ShowCaptioning" value="false" />';
				$content .= '	<param name="ShowDisplay" value="false" />';
				$content .= '	<param name="ShowStatusBar" value="false" />';
				$content .= '	<embed type="application/x-mplayer2" src="'. $media_url .'" width="'. $player_width .'" height="'. $player_height .'" scale="ASPECT" autostart="'. ($autoplay?'1':'0') .'" ShowDisplay="0" ShowStatusBar="0" autosize="1" AnimationAtStart="1" AllowChangeDisplaySize="1" ShowControls="1"></embed>';
				$content .= '</object>';
			}
			else
			{
				if( $cover_image == '' )
					$cover_image = powerpress_get_root_url() . 'play_video_default.jpg';
				
				$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="';
				$content .= "return powerpress_embed_winplayer('powerpress_player_{$player_id}', '{$media_url}', {$player_width}, {$player_height} );";
				$content .= '">';
				$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
				$content .= '</a>';
			}
			
			if( $firefox )
			{
				$content .= '<p style="font-size: 85%;margin-top:0;">'. __('Best viewed with', 'powerpress');
				$content .= ' <a href="http://support.mozilla.com/en-US/kb/Using+the+Windows+Media+Player+plugin+with+Firefox#Installing_the_plugin" target="_blank">';
				$content .= __('Windows Media Player plugin for Firefox', 'powerpress') .'</a></p>';
			}
			
			$content .= "</div>\n";
			
		}; break;
		case 'pdf': {
			$content .= powerpressplayer_build_playimagepdf($media_url, true);
		}; break;
		case 'epub': {
			$content .= powerpressplayer_build_playimageepub($media_url, true);
		}; break;
			
		// Default, just display the play image. 
		default: {
			
			$content .= powerpressplayer_build_playimage($media_url, $EpisodeData, true);
			
		}; break;
	}
	
	return $content;
}

function powerpressplayer_mediaobjects_video($content, $media_url, $EpisodeData = array())
{
	$extension = powerpressplayer_get_extension($media_url);
	switch( $extension )
	{
		// OGG (audio or video)
		case 'ogg': {
			// Ogg special case, we treat as audio unless specified otherwise
			if( !defined('POWERPRESS_OGG_VIDEO') || POWERPRESS_OGG_VIDEO == false )
			{
				return $content;
			}
		} // let fall through and handle as video...
		case 'mp4':
		case 'm4v':
		case 'webm':
		case 'ogv': {
			$VideoObject = true;
		}; break;
		default: return $content;
	}
	
	return powerpressplayer_mediaobjects('video', $content, $media_url, $EpisodeData);
}

function powerpressplayer_mediaobjects_audio($content, $media_url, $EpisodeData = array())
{
	$extension = powerpressplayer_get_extension($media_url);
	switch( $extension )
	{
		// OGG (audio or video)
		case 'ogg': {
			// Ogg special case, we treat as audio unless specified otherwise
			if( defined('POWERPRESS_OGG_VIDEO') && POWERPRESS_OGG_VIDEO )
			{
				return $content;
			}
		} // let fall through and handle as video...
		case 'mp3':
		case 'm4a':
		case 'oga': {
			$AudioObject = true;
		}; break;
		default: return $content;
	}
	
	return powerpressplayer_mediaobjects('audio', $content, $media_url, $EpisodeData);
}

function powerpressplayer_mediaobjects($type, $content, $media_url, $EpisodeData = array())
{
	$GLOBALS['g_powerpress_complete_mediaobject'] = true;
	$addhtml = '';
	$addhtml .= '<div itemscope itemtype="http://schema.org/'. ($type=='video'?'VideoObject':'AudioObject') .'">'.PHP_EOL_WEB;
	
	if( !empty($EpisodeData['title']) )
	{
		// We want to use the post title so ignore this for now
	}
	
	$media_url = powerpress_add_flag_to_redirect_url($media_url, 's'); // Search tag
	
	//var_dump($EpisodeData);
	$post_title = get_the_title();
	if( !empty($post_title) )
		$addhtml .= '<meta itemprop="name" content="'.  htmlspecialchars($post_title) .'" />'.PHP_EOL_WEB;
	$addhtml .= '<meta itemprop="encodingFormat" content="'. powerpress_get_contenttype($media_url) .'" />'.PHP_EOL_WEB;
	$addhtml .= '<meta itemprop="duration" content="'. powerpress_iso8601_duration($EpisodeData['duration']) .'" />'.PHP_EOL_WEB; // http://en.wikipedia.org/wiki/ISO_8601#Durations
	if( !empty($EpisodeData['subtitle']) )
	{
		$addhtml .= '<meta itemprop="description" content="'.  htmlspecialchars($EpisodeData['subtitle']) .'" />'.PHP_EOL_WEB;
	}
	else
	{	// Get the current post object...
		$post = get_post( );
		// Get a subtitle from the post content or excerpt...
		$subtitle = strip_tags($post->post_excerpt);
		if( empty($subtitle) )
		{
			$subtitle = $post->post_content;
			$subtitle = strip_shortcodes( $subtitle ); 
			$subtitle = str_replace(']]>', ']]&gt;', $subtitle);
			$subtitle = strip_tags($subtitle);
			
			$length = (function_exists('mb_strlen')?mb_strlen($subtitle):strlen($subtitle) );
			if( $length > 250 ) {
				$subtitle = (function_exists('mb_substr')?mb_substr($subtitle, 0, 250):substr($subtitle, 0, 250) ). '...';
			}
			
			$addhtml .= '<meta itemprop="description" content="'.  htmlspecialchars($subtitle) .'" />'.PHP_EOL_WEB;
		}
		
		if( empty($subtitle) )
			$subtitle = $post_title;
			
		$addhtml .= '<meta itemprop="description" content="'.  htmlspecialchars($subtitle) .'" />'.PHP_EOL_WEB;
	}
	$addhtml .= '<meta itemprop="contentUrl" content="'. htmlspecialchars($media_url) .'" />'.PHP_EOL_WEB;
	
	// For thumbnail image, use the podcast artwork
	if( !empty($EpisodeData['image']) )
	{
		$addhtml .= '<meta itemprop="thumbnailURL" content="'.$EpisodeData['image'] .'" />'.PHP_EOL_WEB;
	}
	
	if( !empty($EpisodeData['size']) )
	{
		$addhtml .= '<meta itemprop="contentSize" content="'. number_format($EpisodeData['size'] / (1024 * 1024), 1) .'" />'.PHP_EOL_WEB;
	}
	
	// <meta itemprop="videoQuality" content="HD"/>
	if( !empty($EpisodeData['height']) && is_numeric($EpisodeData['height']) )
	{
		$addhtml .= '<meta itemprop="height" content="'.$EpisodeData['height'] .'" />'.PHP_EOL_WEB;
	}
	
	if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
	{
		$addhtml .= '<meta itemprop="width" content="'.$EpisodeData['width'] .'" />'.PHP_EOL_WEB;
	}
	
	return $content . $addhtml;
}

function powerpress_iso8601_duration($duration)
{
	$seconds = 0;
	$parts = explode(':', $duration);
	if( count($parts) == 3 )
		$seconds = $parts[2] + ($parts[1]*60) + ($parts[0]*60*60);
	else if ( count($parts) == 2 )
		$seconds = $parts[1] + ($parts[0]*60);
	else
		$seconds = $parts[0];
	
	$hours = 0;
	$minutes = 0;
	if( $seconds >= (60*60) )
	{
		$hours = floor( $seconds /(60*60) );
		$seconds -= (60*60*$hours);
	}
	if( $seconds >= (60) )
	{
		$minutes = floor( $seconds /(60) );
		$seconds -= (60*$minutes);
	}
	
	if( $hours ) // X:XX:XX (readable)
		return sprintf('PT%dH%02dM%02dS', $hours, $minutes, $seconds);
	
	return sprintf('PT%dM%02dS', $minutes, $seconds); // X:XX or 0:XX (readable)
}

function powerpressplayer_mediaobjects_post($content, $media_url, $EpisodeData = array())
{
	if( !empty($GLOBALS['g_powerpress_complete_mediaobject']) )
	{
		$content .= '</div>';
		unset($GLOBALS['g_powerpress_complete_mediaobject']);
	}
	return $content;
}


add_filter('powerpress_player', 'powerpressplayer_player_audio', 10, 3); // Audio players (mp3)
add_filter('powerpress_player', 'powerpressplayer_player_video', 10, 3); // Video players (mp4/m4v, webm, ogv)
add_filter('powerpress_player', 'powerpressplayer_player_other', 10, 3); // Audio/Video flv, wmv, wma, oga, m4a and other non-standard media files


/*
Filters for media links, appear below the selected player
*/
function powerpressplayer_link_download($content, $media_url, $ExtraData = array() )
{
	$GeneralSettings = get_option('powerpress_general');
	if( !isset($GeneralSettings['podcast_link']) )
		$GeneralSettings['podcast_link'] = 1;
	
	$player_links = '';
	if( $GeneralSettings['podcast_link'] == 1 )
	{
		$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_d\" title=\"". POWERPRESS_DOWNLOAD_TEXT ."\" rel=\"nofollow\" download=\"". htmlspecialchars(basename($media_url)) ."\">". POWERPRESS_DOWNLOAD_TEXT ."</a>".PHP_EOL_WEB;
	}
	else if( $GeneralSettings['podcast_link'] == 2 )
	{
		$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_d\" title=\"". POWERPRESS_DOWNLOAD_TEXT ."\" rel=\"nofollow\" download=\"". htmlspecialchars(basename($media_url)) ."\">". POWERPRESS_DOWNLOAD_TEXT ."</a> (".powerpress_byte_size($ExtraData['size']).") ".PHP_EOL_WEB;
	}
	else if( $GeneralSettings['podcast_link'] == 3 )
	{
		if( !empty($ExtraData['duration']) && ltrim($ExtraData['duration'], '0:') != '' )
			$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_d\" title=\"". POWERPRESS_DOWNLOAD_TEXT ."\" rel=\"nofollow\" download=\"". htmlspecialchars(basename($media_url)) ."\">". POWERPRESS_DOWNLOAD_TEXT ."</a> (". htmlspecialchars(POWERPRESS_DURATION_TEXT) .": " . powerpress_readable_duration($ExtraData['duration']) ." &#8212; ".powerpress_byte_size($ExtraData['size']).")".PHP_EOL_WEB;
		else
			$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_d\" title=\"". POWERPRESS_DOWNLOAD_TEXT ."\" rel=\"nofollow\" download=\"". htmlspecialchars(basename($media_url)) ."\">". POWERPRESS_DOWNLOAD_TEXT ."</a> (".powerpress_byte_size($ExtraData['size']).")".PHP_EOL_WEB;
	}
	
	if( $player_links && !empty($content) )
		$content .= ' '.POWERPRESS_LINK_SEPARATOR .' ';
	
	return $content . $player_links;
}

function powerpressplayer_link_pinw($content, $media_url, $ExtraData = array() )
{
	$GeneralSettings = get_option('powerpress_general');
	if( !isset($GeneralSettings['player_function']) )
		$GeneralSettings['player_function'] = 1;
	$is_pdf = (strtolower( substr($media_url, -3) ) == 'pdf' );
	
	$player_links = '';
	switch( $GeneralSettings['player_function'] )
	{
		case 1: // Play on page and new window
		case 3: // Play in new window only
		case 5: { // Play in page and new window
			if( $is_pdf )
				$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_pinw\" target=\"_blank\" title=\"". __('Open in New Window', 'powerpress') ."\" rel=\"nofollow\">". __('Open in New Window', 'powerpress') ."</a>".PHP_EOL_WEB;
			else if( !empty($ExtraData['id']) && !empty($ExtraData['feed']) ) {
				$pinw_url = get_bloginfo('url') ."/?powerpress_pinw={$ExtraData['id']}-{$ExtraData['feed']}";
				$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_pinw\" target=\"_blank\" title=\"". POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT ."\" onclick=\"return powerpress_pinw('". esc_js($pinw_url) ."');\" rel=\"nofollow\">". POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT ."</a>".PHP_EOL_WEB;
			}
			else
				$player_links .= "<a href=\"{$media_url}\" class=\"powerpress_link_pinw\" target=\"_blank\" title=\"". POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT ."\" rel=\"nofollow\">". POWERPRESS_PLAY_IN_NEW_WINDOW_TEXT ."</a>".PHP_EOL_WEB;
		}; break;
	}//end switch	
	
	if( $player_links && !empty($content) )
		$content .= ' '.POWERPRESS_LINK_SEPARATOR .' ';
	
	return $content . $player_links;
}

function powerpressplayer_embedable($media_url, $ExtraData = array())
{
	if( empty($ExtraData['id']) || empty($ExtraData['feed']) )
		return false;
	
	$extension = powerpressplayer_get_extension($media_url);
	$player = false;
	if( preg_match('/(mp3|m4a|oga|mp4|m4v|webm|ogg|ogv)/i', $extension ) )
	{
		$GeneralSettings = get_option('powerpress_general');
		if( empty($GeneralSettings['podcast_embed']) )
			return false;
		if( empty($GeneralSettings['player']) || $GeneralSettings['player'] == 'flow-player-classic' )
			$GeneralSettings['player'] = 'mediaelement-audio';
		
			
		if( empty($GeneralSettings['video_player']) || $GeneralSettings['video_player'] == 'flow-player-classic' )
			$GeneralSettings['video_player'] = 'mediaelement-video';
		
		switch( $extension )
		{
			case 'mp3':
			case 'oga':
			case 'm4a': {
				if( in_array( $GeneralSettings['player'], array('mediaelement-audio', 'default', 'blubrryaudio') ) )
					$player = $GeneralSettings['player'];
			}; break;
			case 'mp4':
			case 'm4v': 
			case 'webm':
			case 'ogg':
			case 'ogv': {
				if( in_array( $GeneralSettings['video_player'], array('mediaelement-video', 'html5video') ) )
					$player = $GeneralSettings['video_player'];
			}; break;
		}
	}
	
	return $player;
}

function powerpressplayer_link_embed($content, $media_url, $ExtraData = array() )
{
	$player_links = '';
	
	$player = powerpressplayer_embedable($media_url, $ExtraData);
	if( $player )
	{
		$player_links .= "<a href=\"#\" class=\"powerpress_link_e\" title=\"". htmlspecialchars(POWERPRESS_EMBED_TEXT) ."\" onclick=\"return powerpress_show_embed('{$ExtraData['id']}-{$ExtraData['feed']}');\" rel=\"nofollow\">". htmlspecialchars(POWERPRESS_EMBED_TEXT) ."</a>";
	}
	
	if( $player_links && !empty($content) )
		$content .= ' '.POWERPRESS_LINK_SEPARATOR .' ';
	return $content . $player_links;
}

function powerpressplayer_link_title($content, $media_url, $ExtraData = array() )
{
	if( $content )
	{
		$extension = 'unknown';
		$parts = pathinfo($media_url);
		if( $parts && isset($parts['extension']) )
			$extension  = strtolower($parts['extension']);
		
		$prefix = '';
		if( $extension == 'pdf' )
			$prefix .= __('E-Book PDF', 'powerpress') . ( $ExtraData['feed']=='pdf'||$ExtraData['feed']=='podcast'?'':" ({$ExtraData['feed']})") .POWERPRESS_TEXT_SEPARATOR;
		else if( $ExtraData['feed'] != 'podcast' )
			$prefix .= htmlspecialchars(POWERPRESS_LINKS_TEXT) .' ('. htmlspecialchars($ExtraData['feed']) .')'. POWERPRESS_TEXT_SEPARATOR;
		else
			$prefix .= htmlspecialchars(POWERPRESS_LINKS_TEXT) . POWERPRESS_TEXT_SEPARATOR;
		if( !empty($prefix) )
			$prefix .= ' ';
		
		$return = '<p class="powerpress_links powerpress_links_'. $extension .'">'. $prefix . $content . '</p>';
		$player = powerpressplayer_embedable($media_url, $ExtraData);
		if( $player )
		{
			if( !empty($ExtraData['embed']) )
				$iframe_src = $ExtraData['embed'];
			else
				$iframe_src = powerpress_generate_embed($player, $ExtraData);
			$return .= '<p class="powerpress_embed_box" id="powerpress_embed_'. "{$ExtraData['id']}-{$ExtraData['feed']}" .'" style="display: none;">';
			$return .= '<input id="powerpress_embed_'. "{$ExtraData['id']}-{$ExtraData['feed']}" .'_t" type="text" value="'. htmlspecialchars($iframe_src) .'" onclick="javascript: this.select();" onfocus="javascript: this.select();" style="width: 70%;" readOnly>';
			$return .= '</p>';
		}
		return $return;
	}
	return '';
}

add_filter('powerpress_player_links', 'powerpressplayer_link_pinw', 30, 3);
add_filter('powerpress_player_links', 'powerpressplayer_link_download', 50, 3);
add_filter('powerpress_player_links', 'powerpressplayer_link_embed', 50, 3);
add_filter('powerpress_player_links', 'powerpressplayer_link_title', 1000, 3);

/*
Do Play in new Window
*/
function powerpress_do_pinw($pinw, $process_podpress)
{
	if( !WP_DEBUG && defined('POWERPRESS_FIX_WARNINGS') )
	{
		@error_reporting( E_ALL | E_CORE_ERROR | E_COMPILE_ERROR  | E_PARSE );
	}
	
	list($post_id, $feed_slug) = explode('-', $pinw, 2);
	$EpisodeData = powerpress_get_enclosure_data($post_id, $feed_slug);
	
	if( $EpisodeData == false && $process_podpress && $feed_slug == 'podcast' )
	{
		$EpisodeData = powerpress_get_enclosure_data_podpress($post_id);
	}
	
	$GeneralSettings = get_option('powerpress_general');
	
	echo '<!DOCTYPE html>'; // HTML5!
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php echo __('Blubrry PowerPress Player', 'powerpress'); ?></title>
	<meta name="robots" content="noindex" />
<?php 
	
	do_action('wp_powerpress_player_scripts');
	
	
	if( !empty($GLOBALS['ClammrPlayer']) ) {
		$GLOBALS['ClammrPlayer']->initialize();
		wp_head();
	}

?>
<style type="text/css">
body { font-size: 13px; font-family: Arial, Helvetica, sans-serif; /* width: 100%; min-height: 100%; } html { height: 100%; */ }
</style>
</head>
<body>
<div style="margin: 5px;">
<?php
	
	if( !$EpisodeData )
	{
		echo '<p>'.  __('Unable to retrieve media information.', 'powerpress') .'</p>';
	}
	else if( !empty($GeneralSettings['premium_caps']) && !powerpress_premium_content_authorized($feed_slug) )
	{
		echo powerpress_premium_content_message($post_id, $feed_slug, $EpisodeData);
	}
	else if( !empty($EpisodeData['embed']) )
	{
		echo $EpisodeData['embed'];
	}
	else //  if( !isset($EpisodeData['no_player']) ) // Even if there is no player set, if the play in new window option is enabled then it should play here...
	{
		echo apply_filters('powerpress_player', '', powerpress_add_flag_to_redirect_url($EpisodeData['url'], 'p'), array('feed'=>$feed_slug, 'autoplay'=>true, 'type'=>$EpisodeData['type']) );
	}
	
	wp_print_styles();
	wp_print_scripts();
?>
</div>
<?php
	if( !empty($GLOBALS['ClammrPlayer']) ) {
		wp_footer();
	}
?>
</body>
</html>
<?php
	exit;
}

/*
Do embed
*/
function powerpress_do_embed($player, $embed, $process_podpress)
{
	list($post_id, $feed_slug) = explode('-', $embed, 2);
	$EpisodeData = powerpress_get_enclosure_data($post_id, $feed_slug);
	
	if( $EpisodeData == false && $process_podpress && $feed_slug == 'podcast' )
	{
		$EpisodeData = powerpress_get_enclosure_data_podpress($post_id);
	}
	
	// Embeds are only available for the following players
	do_powerpressplayer_embed($player, $EpisodeData['url'], $EpisodeData);
	exit;
}

/*
HTTML 5 Video Player
*/
function powerpressplayer_build_html5video($media_url, $EpisodeData=array(), $embed = false )
{
	$player_id = powerpressplayer_get_next_id();
	$cover_image = '';
	$player_width = 400;
	$player_height = 225;
	$autoplay = false;
	// Global Settings
	$Settings = get_option('powerpress_general');
	if( !empty($Settings['player_width']) )
		$player_width = $Settings['player_width'];
	if( !empty($Settings['player_height']) )
		$player_height = $Settings['player_height'];
	if( !empty($Settings['poster_image']) )
		$cover_image = $Settings['poster_image'];
	// Episode Settings
	if( !empty($EpisodeData['image']) )
		$cover_image = $EpisodeData['image'];
	if( !empty($EpisodeData['width']) )
		$player_width = $EpisodeData['width'];
	if( !empty($EpisodeData['height']) )
		$player_height = $EpisodeData['height'];
	if( !empty($EpisodeData['autoplay']) )
		$autoplay = true;
	
	$content = '';
	if( $embed )
	{
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">'.PHP_EOL_WEB;
		$content .= '<video width="'. $player_width .'" height="'. $player_height .'" controls="controls"';
		if( $cover_image )
			$content .= ' poster="'. $cover_image .'"';
		if( $autoplay )
			$content .= ' autoplay="autoplay"';
		else
			$content .= ' preload="none"';
		
		$content .= '>'.PHP_EOL_WEB;
		$content_type = powerpress_get_contenttype($media_url);
		$content .='<source src="'. $media_url .'" type="'. $content_type .'" />';
		
		if( !empty($EpisodeData['webm_src']) )
		{
			$EpisodeData['webm_src'] = powerpress_add_flag_to_redirect_url($EpisodeData['webm_src'], 'p');
			$content .='<source src="'. $EpisodeData['webm_src'] .'" type="video/webm" />';
		}
		
		$content .= powerpressplayer_build_playimage($media_url, $EpisodeData);
		$content .= '</video>'.PHP_EOL_WEB;
		$content .= '</div>'.PHP_EOL_WEB;
	}
	else
	{
		
		if( !$cover_image )
			$cover_image = powerpress_get_root_url() . 'black.png';
		$webm_src = '';
		if( !empty($EpisodeData['webm_src']) )
			$webm_src = powerpress_add_flag_to_redirect_url($EpisodeData['webm_src'], 'p');
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
		$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="return powerpress_embed_html5v(\''.$player_id.'\',\''.$media_url.'\',\''. $player_width .'\',\''. $player_height .'\', \''. $webm_src .'\');" target="_blank" style="position: relative;">';
		if( !empty($EpisodeData['custom_play_button']) ) // This currently does not apply
		{
			$cover_image = $EpisodeData['custom_play_button'];
			$Settings['poster_play_image'] = false;
			$content .= '<img class="powerpress-player-poster" src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" />';
		}
		else
		{
			$content .= '<img class="powerpress-player-poster" src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="width: '. $player_width .'px; height: '. $player_height .'px;" />';
		}
		
		if(!isset($Settings['poster_play_image']) || $Settings['poster_play_image'] )
		{
			$play_image_button_url = powerpress_get_root_url() .'play_video.png';
			if( !empty($Settings['video_custom_play_button']) )
				$play_image_button_url = $Settings['video_custom_play_button'];
			
			$bottom = floor(($player_height/2)-30);
			if( $bottom < 0 )
				$bottom = 0;
			$left = floor(($player_width/2)-30);
			if( $left < 0 )
				$left = 0;
			$content .= '<img class="powerpress-player-play-image" src="'. $play_image_button_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="position: absolute; bottom: '. $bottom .'px; left: '. $left .'px; border:0;" />';
		}
		$content .= '</a>';
		$content .= "</div>\n";
		
		if( $autoplay )
		{
			$content .= '<script type="text/javascript"><!--'.PHP_EOL;
			$content .= "powerpress_embed_html5v('{$player_id}','{$media_url}',{$player_width},{$player_height},'{$webm_src}');\n";
			$content .= "//-->\n";
			$content .= "</script>\n";
		}
	}
	return $content;
}

/*
MediaElement.js Video Player
*/
function powerpressplayer_build_mediaelementvideo($media_url, $EpisodeData=array(), $embed = false )
{
	if( !function_exists('wp_video_shortcode') )
	{
		// Return the HTML5 video shortcode instead
		return powerpressplayer_build_html5video($media_url, $EpisodeData, $embed);
	}
	
	$player_id = powerpressplayer_get_next_id();
	$cover_image = '';
	$player_width = '';
	$player_height = '';
	$autoplay = false;
	// Global Settings
	$Settings = get_option('powerpress_general');
	if( !empty($Settings['player_width']) )
		$player_width = $Settings['player_width'];
	if( !empty($Settings['player_height']) )
		$player_height = $Settings['player_height'];
	if( !empty($Settings['poster_image']) )
		$cover_image = $Settings['poster_image'];
	// Episode Settings
	if( !empty($EpisodeData['image']) )
		$cover_image = $EpisodeData['image'];
	if( !empty($EpisodeData['width']) )
		$player_width = $EpisodeData['width'];
	if( !empty($EpisodeData['height']) )
		$player_height = $EpisodeData['height'];
	if( !empty($EpisodeData['autoplay']) )
		$autoplay = true;
		
	if( $embed )
	{
		$player_height = '123';
		$player_width = '456';
	}
	
	
	$content = '';
	
	$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">'.PHP_EOL_WEB;
	$attr = array('src'=>$media_url, 'poster'=>'', 'loop'=>'', 'autoplay'=>'', 'preload'=>'none'); // , 'width'=>'', 'height'=>'');
	if( !empty($player_width) )
		$attr['width'] = $player_width;
	if( !empty($player_height) )
		$attr['height'] = $player_height;
	if( !empty($cover_image) )
		$attr['poster'] = $cover_image;
	if( !empty($autoplay) )
		$attr['autoplay'] = 'on';
	if( !empty($EpisodeData['webm_src']) )
		$attr['webm'] = powerpress_add_flag_to_redirect_url($EpisodeData['webm_src'], 'p');
	
	// Double check that WordPress is providing the shortcode...
	global $shortcode_tags;
	if( !defined('POWERPRESS_DO_SHORTCODE') ) {
		$shortcode = wp_video_shortcode( $attr );
	} else {
		$shortcode_value = '[video ';
		while( list($tag_name,$tag_value) = each($attr) ) {
			$shortcode_value .= ' '.$tag_name.'="'. esc_attr($tag_value) .'"';
		}
		$shortcode_value .= ']';
		$shortcode .= do_shortcode($shortcode_value);
	}
		
	
	if( $embed )
	{
		$shortcode = str_replace( array('"123"', '"456"', '456px;'), array('"100%"', '"100%"', '100%;'), $shortcode);
	}
	$content .= $shortcode;
	$content .= '</div>'.PHP_EOL_WEB;
	return $content;
}

/*
HTTML 5 Audio Player
*/
function powerpressplayer_build_html5audio($media_url, $EpisodeData=array(), $embed = false )
{
	$player_id = powerpressplayer_get_next_id();
	$autoplay = false;
	// Episode Settings
	if( !empty($EpisodeData['autoplay']) )
		$autoplay = true;
	$content = '';
	if( $embed )
	{
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">'.PHP_EOL_WEB;
		$content .= '<audio controls="controls"';
		$content .=' src="'. $media_url .'"';
		if( $autoplay )
			$content .= ' autoplay="autoplay"';
		else
			$content .= ' preload="none"';
		$content .= '>'.PHP_EOL_WEB;
		
		$content .= powerpressplayer_build_playimageaudio($media_url);
		$content .= '</audio>'.PHP_EOL_WEB;
		$content .= '</div>'.PHP_EOL_WEB;
	}
	else
	{
		$GeneralSettings = get_option('powerpress_general');
		$cover_image = powerpress_get_root_url() . 'play_audio.png';
		$cover_image_default = $cover_image;
		if( !empty($EpisodeData['custom_play_button']) )
		{
			$cover_image = $EpisodeData['custom_play_button'];
		}
		else if( !empty($GeneralSettings['audio_custom_play_button']) )
		{
			$cover_image = $GeneralSettings['audio_custom_play_button'];
		}
		
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
		$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" onclick="return powerpress_embed_html5a(\''.$player_id.'\',\''.$media_url.'\');" target="_blank">';
		if( $cover_image_default == $cover_image )
			$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="border:0;" width="23px" height="24px" />';
		else
			$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="border:0;" />';
		$content .= '</a>';
		$content .= "</div>\n";
		
		if( $autoplay )
		{
			$content .= '<script type="text/javascript"><!--'.PHP_EOL;
			$content .= "powerpress_embed_html5a('{$player_id}','{$media_url}');\n";
			$content .= "//-->\n";
			$content .= "</script>\n";
		}
	}
	
	return $content;
}


/*
*/
function powerpressplayer_build_blubrryaudio($media_url, $EpisodeData=array(), $embed = false )
{
	// media URL is all we need., as long as it's hosted at blubrry.com...
	if( preg_match('/content\.blubrry\.com/', $media_url) )
	{
		$url = '//player.blubrry.com?media_url='. urlencode($media_url);
		if( !empty($EpisodeData['id']) ) {
			// Get permalink URL
			$permalink = get_permalink( $EpisodeData['id'] );
			if( !empty($permalink) )
				$url.= '&amp;podcast_link='. urlencode($permalink);
		}
		return '<iframe src="'. $url .'" scrolling="no" width="100%" height="138px" frameborder="0"></iframe>';
	}
	
	return powerpressplayer_build_mediaelementaudio($media_url, $EpisodeData, $embed);
}

function powerpressplayer_build_blubrryaudio_by_id($directory_episode_id)
{
	return '<iframe src="//player.blubrry.com?podcast_id='. $directory_episode_id .'" scrolling="no" width="100%" height="138px" frameborder="0"></iframe>';
}

/*
MediaElement.js Audio Player
*/
function powerpressplayer_build_mediaelementaudio($media_url, $EpisodeData=array(), $embed = false )
{
	if( !function_exists('wp_audio_shortcode') )
	{
		// Return the HTML5 audio shortcode instead
		return powerpressplayer_build_html5audio($media_url, $EpisodeData, $embed);
	}
	
	$player_id = powerpressplayer_get_next_id();
	$autoplay = false;
	// Episode Settings
	if( !empty($EpisodeData['autoplay']) )
		$autoplay = true;
	$content = '';
	
	
	$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">'.PHP_EOL_WEB;

	$attr = array(
		'src'      => $media_url,
		'loop'     => '', // off
		'autoplay' => ( $autoplay ?'on':''),
		'preload'  => 'none'
	);
	
	if( !empty($GLOBALS['ClammrPlayer']) && is_object($GLOBALS['ClammrPlayer']) && get_option('powerpress_clammr') == 1 ) {
		$content .= do_shortcode( '[audio-clammr src="'.  esc_attr($media_url) .'" autoplay="'. ( $autoplay ?'on':'') .'" loop="" preload="none"]');
	} else {
		// Double check that WordPress is providing the shortcode...
		global $shortcode_tags;
		if( !defined('POWERPRESS_DO_SHORTCODE') ) { // && !empty($shortcode_tags['audio']) && is_string($shortcode_tags['audio']) && $shortcode_tags['audio'] == 'wp_audio_shortcode' ) {
			$content .= wp_audio_shortcode( $attr );
		} else {
			$content .= do_shortcode( '[audio src="'.  esc_attr($media_url) .'" autoplay="'. ( $autoplay ?'on':'') .'" loop="" preload="none"]');
		}
	}
	$content .= '</div>'.PHP_EOL_WEB;
	return $content;
}

/*
HTTML 5 Mobile Player
*/
function powerpressplayer_build_html5mobile($media_url, $EpisodeData)
{
	$content = '';
	$html5 = true;
	// Special logic, we need to check if we're dealing with Android 2.2 or older, in which case we don't want to use HTML5 audio/video due to controls bug
	if( preg_match('/android ([\d\.]+)/i', $_SERVER['HTTP_USER_AGENT'], $matches) )
	{
		if( !empty($matches[1]) && version_compare($matches[1], "2.3") < 0 )
			$html5 = false;
	}
	
	$extension = powerpressplayer_get_extension($media_url);
	switch( $extension )
	{
		case 'mp4':
		case 'webm':
		case 'm4v':
		case 'ogg':
		case 'ogv': {
			// Video
			$Settings = get_option('powerpress_general');
			
			// MEJS is not ready for mobile, using native HTML5 performs more efficiently at this point. Someday though we will be able to use MEJS for mobile.
			if( $html5 && !empty($Settings['video_player']) && $Settings['video_player'] == 'mediaelement-video' )
				$content .= powerpressplayer_build_mediaelementvideo($media_url, $EpisodeData);
			else if( $html5 )
				$content .= powerpressplayer_build_html5video($media_url, $EpisodeData);
			else
				$content .= powerpressplayer_build_playimage($media_url, $EpisodeData, true);
		}; break;
		case 'mp3':
		case 'm4a':
		case 'oga': {
			// Audio
			$Settings = get_option('powerpress_general');
			
			// MEJS is not ready for mobile, using native HTML5 performs more efficiently at this point. Someday though we will be able to use MEJS for mobile.
			if( $html5 && !empty($Settings['player']) && $Settings['player'] == 'mediaelement-audio' )
				$content .= powerpressplayer_build_mediaelementaudio($media_url, $EpisodeData);
			else if( $html5 )
				$content .= powerpressplayer_build_html5audio($media_url, $EpisodeData);
			else
				$content .= powerpressplayer_build_playimageaudio($media_url, true);
		}; break;
	}
	
	return $content;
}


function powerpressplayer_build_playimage($media_url, $EpisodeData = array(), $include_div = false)
{
	$content = '';
	$autoplay = false;
	if( !empty($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true;
	$player_width = 400;
	$player_height = 225;
	$cover_image = '';
	// Global settings
	$Settings = get_option('powerpress_general');
	if( !empty($Settings['player_width']) )
		$player_width = $Settings['player_width'];
	if( !empty($Settings['player_height']) )
		$player_height = $Settings['player_height'];
	if( !empty($Settings['poster_image']) )
		$cover_image = $Settings['poster_image'];
	// episode settings
	if( !empty($EpisodeData['width']) )
		$player_width = $EpisodeData['width'];
	if( !empty($EpisodeData['height']) )
		$player_height = $EpisodeData['height'];
	if( !empty($EpisodeData['image']) )
		$cover_image = $EpisodeData['image'];
		
	if( !$cover_image )
		$cover_image = powerpress_get_root_url() . 'black.png';
	
	if( $include_div )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. powerpressplayer_get_next_id() .'">';
	$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" target="_blank" style="position: relative;">';
	$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="width: '. $player_width .'px; height: '. $player_height .'px;" />';
	if(!isset($Settings['poster_play_image']) || $Settings['poster_play_image'] )
	{
		$play_image_button_url = powerpress_get_root_url() .'play_video.png';
		if( !empty($Settings['video_custom_play_button']) )
			$play_image_button_url = $Settings['video_custom_play_button'];
			
		$bottom = floor(($player_height/2)-30);
		if( $bottom < 0 )
			$bottom = 0;
		$left = floor(($player_width/2)-30);
		if( $left < 0 )
			$left = 0;
		$content .= '<img src="'. $play_image_button_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="position: absolute; bottom:'. $bottom .'px; left:'. $left .'px; border:0;" />';
	}
	$content .= '</a>';
	if( $include_div )
		$content .= "</div>\n";
	return $content;
}

function powerpressplayer_build_playimageaudio($media_url, $include_div = false)
{
	$content = '';
	$cover_image = powerpress_get_root_url() . 'play_audio.png';
	$GeneralSettings = get_option('powerpress_general');
	if( !empty($GeneralSettings['custom_play_button']) )
		$cover_image = $GeneralSettings['custom_play_button'];
		
	if( $include_div )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. powerpressplayer_get_next_id() .'">';
	$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" target="_blank">';
	$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_PLAY_TEXT) .'" style="border:0;" />';
	$content .= '</a>';
	if( $include_div )
		$content .= "</div>\n";
	return $content;
}

function powerpressplayer_build_playimagepdf($media_url, $include_div = false)
{
	$content = '';
	$cover_image = powerpress_get_root_url() . 'play_pdf.png';
	$GeneralSettings = get_option('powerpress_general');
	if( !empty($GeneralSettings['pdf_custom_play_button']) )
		$cover_image = $GeneralSettings['pdf_custom_play_button'];
		
	if( $include_div )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. powerpressplayer_get_next_id() .'">';
	$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" target="_blank">';
	$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" style="border:0;" />';
	$content .= '</a>';
	if( $include_div )
		$content .= "</div>\n";
	return $content;
}

function powerpressplayer_build_playimageepub($media_url, $include_div = false)
{
	$content = '';
	$cover_image = powerpress_get_root_url() . 'play_epub.png';
	$GeneralSettings = get_option('powerpress_general');
	if( !empty($GeneralSettings['epub_custom_play_button']) )
		$cover_image = $GeneralSettings['epub_custom_play_button'];
		
	if( $include_div )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. powerpressplayer_get_next_id() .'">';
	$content .= '<a href="'. $media_url .'" title="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" target="_blank">';
	$content .= '<img src="'. $cover_image .'" title="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" alt="'. htmlspecialchars(POWERPRESS_READ_TEXT) .'" style="border:0;" />';
	$content .= '</a>';
	if( $include_div )
		$content .= "</div>\n";
	return $content;
}

/*
1 pixel out player
*/
function powerpressplayer_build_1pxoutplayer($media_url, $EpisodeData = array())
{
	$content = '';
	$autoplay = false;
	if( isset($EpisodeData['autoplay']) && $EpisodeData['autoplay'] )
		$autoplay = true; // TODO: We need to handle this

	$PlayerSettings = get_option('powerpress_audio-player');
	if( !$PlayerSettings )
	{
		$PlayerSettings = array(
			'width'=>'290',
			'transparentpagebg' => 'yes',
			'lefticon' => '#333333',
			'leftbg' => '#CCCCCC',
			'bg' => '#E5E5E5',
			'voltrack' => '#F2F2F2',
			'volslider' => '#666666',
			'rightbg' => '#B4B4B4',
			'rightbghover' => '#999999',
			'righticon' => '#333333',
			'righticonhover' => '#FFFFFF',
			'loader' => '#009900',
			'track' => '#FFFFFF',
			'tracker' => '#DDDDDD',
			'border' => '#CCCCCC',
			'skip' => '#666666',
			'text' => '#333333',
			'pagebg' => '',
			'noinfo'=>'yes',
			'rtl' => 'no' );
	}

	if( empty($PlayerSettings['titles']) )
		$PlayerSettings['titles'] = 'Blubrry PowerPress';
	else if( strtoupper($PlayerSettings['titles']) == __('TRACK', 'powerpress') )
		unset( $PlayerSettings['titles'] );

	// Set player width
	if( !isset($PlayerSettings['width']) )	
		$PlayerSettings['width'] = 290;
	if( !empty($EpisodeData['width']) && is_numeric($EpisodeData['width']) )
		$PlayerSettings['width'] = $EpisodeData['width'];
	
	$transparency = '<param name="wmode" value="transparent" />';
	$PlayerSettings['transparentpagebg'] = 'yes';
	if( !empty($PlayerSettings['pagebg']) )
	{
		$transparency = '<param name="bgcolor" value="'.$PlayerSettings['pagebg'].'" />';
		$PlayerSettings['transparentpagebg'] = 'no';
	}
	
	$flashvars ='';
	while( list($key,$value) = each($PlayerSettings) )
	{
		$flashvars .= '&amp;'. $key .'='. preg_replace('/\#/','',$value);
	}
	
	if( $autoplay )
	{
		$flashvars .= '&amp;autostart=yes';
	}
	
	// TODO: Add 1 px out audio-player player here
	$player_id = powerpressplayer_get_next_id();
	if( empty($EpisodeData['nodiv']) )
		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';
	$content .= '<object type="application/x-shockwave-flash" data="'.powerpress_get_root_url().'audio-player.swf" id="'.$player_id.'" height="24" width="'. $PlayerSettings['width'] .'">'.PHP_EOL_WEB;
	$content .= '<param name="movie" value="'.powerpress_get_root_url().'audio-player.swf" />'.PHP_EOL_WEB;
	$content .= '<param name="FlashVars" value="playerID='.$player_id.'&amp;soundFile='.urlencode($media_url).$flashvars.'" />'.PHP_EOL_WEB;
	$content .= '<param name="quality" value="high" />'.PHP_EOL_WEB;
	$content .= '<param name="menu" value="false" />'.PHP_EOL_WEB;
	$content .= '<param name="wmode" value="transparent" />'.PHP_EOL_WEB;
	// $content .= powerpressplayer_build_html5audio($media_url, $EpisodeData, true); // Feature removed since it causes double players to be insrted in Safari/Firefox
	$content .=  powerpressplayer_build_playimageaudio($media_url);
	$content .= '</object>'.PHP_EOL_WEB;
	if( empty($EpisodeData['nodiv']) )
		$content .= '</div>'.PHP_EOL_WEB;
	
	return $content;
}

/*
VideoJS for PowerPress 4.0
*/
function powerpressplayer_build_videojs($media_url, $EpisodeData = array())
{
	$content = '';
	if( function_exists('add_videojs_header') )
	{
		// Global Settings
		$Settings = get_option('powerpress_general');
		
		$player_id = powerpressplayer_get_next_id();
		$cover_image = '';
		$player_width = 400;
		$player_height = 225;
		$autoplay = false;
		
		if( !empty($Settings['player_width']) )
			$player_width = $Settings['player_width'];
		if( !empty($Settings['player_height']) )
			$player_height = $Settings['player_height'];
		if( !empty($Settings['poster_image']) )
			$cover_image = $Settings['poster_image'];
		
		// Episode Settings
		if( !empty($EpisodeData['image']) )
			$cover_image = $EpisodeData['image'];
		if( !empty($EpisodeData['width']) )
			$player_width = $EpisodeData['width'];
		if( !empty($EpisodeData['height']) )
			$player_height = $EpisodeData['height'];
		if( !empty($EpisodeData['autoplay']) )
			$autoplay = true;

		// Poster image supplied
		$poster_attribute = '';
		if ($cover_image)
			$poster_attribute = ' poster="'.$cover_image.'"';

		// Autoplay the video?
		$autoplay_attribute = '';
		if ( $autoplay )
			$autoplay_attribute = ' autoplay';
			
		// We never do pre-poading for podcasting as it inflates statistics
		
		// Is there a custom class?
		$class = '';
		if ( !empty($Settings['videojs_css_class']) )
			$class = ' '. $Settings['videojs_css_class'];

		$content .= '<div class="powerpress_player" id="powerpress_player_'. $player_id .'">';

		$content .= '<video id="videojs_player_'. $player_id .'" class="video-js vjs-default-skin'. $class .'" width="'. $player_width .'" height="'. $player_height .'"'. $poster_attribute .' controls '. $autoplay_attribute .' data-setup="{}">';
		
		$content_type = powerpress_get_contenttype($media_url);
		if( $content_type == 'video/x-m4v' )
			$content_type = 'video/mp4'; // Mp4
		$content .='<source src="'. $media_url .'" type="'. $content_type .'" />';
		
		if( !empty($EpisodeData['webm_src']) )
		{
			$EpisodeData['webm_src'] = powerpress_add_flag_to_redirect_url($EpisodeData['webm_src'], 'p');
			$content .='<source src="'. $EpisodeData['webm_src'] .'" type="video/webm; codecs="vp8, vorbis" />';
		}

		$content .= '</video>';
		$content .= "</div>\n";
	}
	else
	{
		$content .= powerpressplayer_build_html5video($media_url, $EpisodeData);
	}

	return $content;
}


