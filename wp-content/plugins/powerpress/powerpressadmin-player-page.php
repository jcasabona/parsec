<?php
// PowerPress Player settings page

require_once( POWERPRESS_ABSPATH. '/powerpress-player.php'); // Include, if not included already

function powerpressplayer_mediaelement_info($full_info = true)
{
?>
	<p>
		<?php echo __('MediaElement.js is an open source HTML5 audio and video player that supports both audio (mp3, m4a and oga) and video (mp4, m4v, webm, ogv and flv) media files. It includes all the necessary features for playback including a play/pause button, scroll-able position bar, elapsed time, total time, mute button and volume control.', 'powerpress'); ?>
	</p>
	<?php
	if( $full_info )
	{ 
	?>
	<p>
		<?php echo __('MediaElement.js is the default player in Blubrry PowerPress because it is HTML and CSS based, meets accessibility standards including WebVTT, and will play in any browser using either HTML5, Flash or Silverlight for playback.', 'powerpress'); ?>
	</p>
<?php
	}
}


function powerpressplayer_videojs_info()
{
	$plugin_link = '';
	
	if( !function_exists('add_videojs_header') && file_exists( WP_PLUGIN_DIR . '/' . 'videojs-html5-video-player-for-wordpress' ) ) // plugin downloaded but not activated...
	{
		$plugin_file = 'videojs-html5-video-player-for-wordpress' . '/' . 'video-js.php';
		$plugin_link = '<a href="' . esc_url(wp_nonce_url(admin_url('plugins.php?plugin_status=active&action=activate&plugin=' . $plugin_file ), 'activate-plugin_' . $plugin_file)) .
										'"title="' . esc_attr__('Activate Plugin') . '"">' . __('VideoJS - HTML5 Video Player for WordPress plugin', 'powerpress') . '</a>';
	
	
	} else {
		$plugin_link = '<a href="'. esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . 'videojs-html5-video-player-for-wordpress' .
									'&TB_iframe=true&width=600&height=550' ) ) .'" class="thickbox" title="' .
									esc_attr__('Install Plugin') . '">'. __('VideoJS - HTML5 Video Player for WordPress plugin', 'powerpress') . '</a>';
	}
?>
	<p style="margin-bottom: 20px;">
		<?php echo __('VideoJS is a HTML5 JavaScript and CSS video player with fallback to Flash. ', 'powerpress'); ?>
	</p>
	
	<?php if( $plugin_link ) { ?>
	<div class="fade powerpress-notice" <?php echo ( function_exists('add_videojs_header') ?'':' styleX="background-color: #FFFFE0; border: 1px solid #E6DB55; padding: 8px 12px; line-height: 29px; font-weight: bold; font-size: 14px; display:inline;"'); ?>><p>
		<?php echo sprintf(__('The %s must be installed and activated in order to enable this feature.', 'powerpress'), $plugin_link ); ?>
	</p></div>
	<?php } ?>
<?php
}

function powerpress_admin_players($type='audio')
{
	$General = powerpress_get_settings('powerpress_general');
	
	$select_player = true;
	if( isset($_REQUEST['ep']) )
	{
		$select_player = false;
	}
	
	if( isset($_GET['sp']) )
	{
		$select_player = true;
	}
	else if( $type == 'video' )
	{
		if( empty($General['video_player']) ) {
			$select_player = true;
		} else {
			switch( $General['video_player'] ) {
				case 'mediaelement-video':
				case 'videojs-html5-video-player-for-wordpress':
				case 'html5video': break;
				default: {
					$select_player = true;
				};
			}
		}
	}
	else
	{
		if( empty($General['player']) )
		{
			$select_player = true;
		}
		else
		{
			switch( $General['player'] )
			{
				case 'blubrryaudio':
				case 'mediaelement-audio':
				case 'html5audio':
				case 'audio-player': break;
				default: {
					$select_player = true;
				};
			}
		}
	}
	
	if( empty($General['player']) )
		$General['player'] = 'mediaelement-audio';
	
	if( empty($General['player']) )
		$General['video_player'] = 'mediaelement-video';
	
	if( empty($General['audio_custom_play_button']) )
		$General['audio_custom_play_button'] = '';
	
	
		
	$Audio = array();
	$Audio['audio-player'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/1_Pixel_Out_Flash_Player.mp3';
	$Audio['html5audio'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/html5.mp3';
	$Audio['mediaelement-audio'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/MediaElement_audio.mp3';
	$Audio['blubrryaudio'] = ''; // Set hardcoded by ID
		
	
	$Video = array();
	$Video['html5video'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/html5.mp4';
	$Video['videojs-html5-video-player-for-wordpress'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/videojs.mp4';
	$Video['mediaelement-video'] = 'http://media.blubrry.com/blubrry/content.blubrry.com/blubrry/MediaElement_video.mp4';
		
		wp_enqueue_style( 'wp-color-picker' );
		
		if( $type == 'video' && function_exists('add_videojs_header') )
			add_videojs_header();
?>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>3rdparty/colorpicker/css/colorpicker.css" type="text/css" />
<script type="text/javascript" src="<?php echo powerpress_get_root_url(); ?>3rdparty/colorpicker/js/colorpicker.js"></script>
<script type="text/javascript" src="<?php echo powerpress_get_root_url(); ?>player.min.js"></script>
<script type="text/javascript"><!--

function rgb2hex(rgb) {
 
 rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
 function hex(x) {
  hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
  return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
 }
 
 if( rgb )
	return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
 return '';
}

function UpdatePlayerPreview(name, value)
{
	if( typeof(generator) != "undefined" ) // Update the Maxi player...
	{
		generator.updateParam(name, value);
		generator.updatePlayer();
	}
	
	if( typeof(update_audio_player) != "undefined" ) // Update the 1 px out player...
		update_audio_player();
}
				
jQuery(document).ready(function($) {
	
	jQuery('.color_preview').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			jQuery(el).css({ 'background-color' : '#' + hex });
			jQuery(el).ColorPickerHide();
			var Id = jQuery(el).attr('id');
			Id = Id.replace(/_prev/, '');
			jQuery('#'+ Id  ).val( '#' + hex );
			UpdatePlayerPreview(Id, '#'+hex );
		},
		onBeforeShow: function () {
			jQuery(this).ColorPickerSetColor( rgb2hex( jQuery(this).css("background-color") ) );
		}
	})
	.bind('keyup', function(){
		jQuery(this).ColorPickerSetColor( rgb2hex( jQuery(this).css("background-color") ) );
	});
	
	jQuery('.color_field').bind('change', function () {
		var Id = jQuery(this).attr('id');
		jQuery('#'+ Id + '_prev'  ).css( { 'background-color' : jQuery(this).val() } );
		if( typeof(update_audio_player) != "undefined" ) // Update the 1 px out player...
			update_audio_player();
	});
	
	jQuery('.other_field').bind('change', function () {
		if( typeof(update_audio_player) != "undefined" ) // Update the 1 px out player...
			update_audio_player();
	});

});
//-->
</script>


<!-- special page styling goes here -->
<style type="text/css">
div.color_control { display: block; float:left; width: 100%; padding:  0; }
div.color_control input { display: inline; float: left; }
div.color_control div.color_picker { display: inline; float: left; margin-top: 3px; }
#player_preview { margin-bottom: 0px; height: 50px; margin-top: 8px;}
input#colorpicker-value-input {
	width: 60px;
	height: 16px;
	padding: 0;
	margin: 0;
	font-size: 12px;
	border-spacing: 0;
	border-width: 0;
}
table.html5formats {
	width: 600px;
	margin: 0;
	padding: 0;
}
table.html5formats tr {
	margin: 0;
	padding: 0;
}
table.html5formats tr th {
	font-weight: bold;
	border-bottom: 1px solid #000000;
	margin: 0;
	padding: 0 5px;
	width: 25%;
}
table.html5formats tr td {
	
	border-right: 1px solid #000000;
	border-bottom: 1px solid #000000;
	margin: 0;
	padding: 0 10px;
}
table.html5formats tr > td:first-child {
	border-left: 1px solid #000000;
}
</style>
<?php
	
	// mainly 2 pages, first page selects a player, second configures the player, if there are optiosn to configure for that player. If the user is on the second page,
	// a link should be provided to select a different player.
	if( $select_player )
	{
?>
<input type="hidden" name="action" value="powerpress-select-player" />
<h2><?php echo __('Blubrry PowerPress Player Options', 'powerpress'); ?></h2>
<p style="margin-bottom: 0;"><?php echo __('Select the media player you would like to use.', 'powerpress'); ?></p>

<?php
		if( $type == 'video' ) // Video player
		{
			if( empty($General['video_player']) )
				$General['video_player'] = '';
?>
<input type="hidden" name="ep" value="1" />
<table class="form-table">
<tr valign="top">
<th scope="row">&nbsp;</th>  
<td>
	<ul>
		<li><label><input type="radio" name="VideoPlayer[video_player]" id="player_mediaelement_video" value="mediaelement-video" <?php if( $General['video_player'] == 'mediaelement-video' ) echo 'checked'; ?> />
		<?php echo __('MediaElement.js Media Player (default)', 'powerpress'); ?></label>
			 <strong style="padding-top: 8px; margin-left: 20px;"><a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_videoplayer.php&amp;ep=1"); ?>" id="activate_mediaelement_video" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<div style="max-width: 70%;">
				<div class="powerpressadmin-mejs-video">
<?php
			echo powerpressplayer_build_mediaelementvideo( $Video['mediaelement-video'] );
?>
				</div>
			</div>
			<?php powerpressplayer_mediaelement_info(); ?>
		</li>

		<li><label><input type="radio" name="VideoPlayer[video_player]" id="player_html5video" value="html5video" <?php if( $General['video_player'] == 'html5video' ) echo 'checked'; ?> /> <?php echo __('HTML5 Video Player', 'powerpress'); ?>  </label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_videoplayer.php&amp;ep=1"); ?>" id="activate_html5video" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
            <?php
						echo powerpressplayer_build_html5video($Video['html5video']);
					?>
			</p>
			<p>
				<?php echo __('HTML5 Video is an element introduced in the latest HTML specification (HTML5) for the purpose of playing videos.', 'powerpress'); ?>
			</p>
		</li>
		
		<!-- videojs-html5-video-player-for-wordpress -->
		<li><label><input type="radio" name="VideoPlayer[video_player]" id="player_videojs_html5_video_player_for_wordpress" value="videojs-html5-video-player-for-wordpress" <?php if( $General['video_player'] == 'videojs-html5-video-player-for-wordpress' ) echo 'checked'; ?> <?php echo (function_exists('add_videojs_header')?'':'disabled');  ?> />
		<?php echo __('VideoJS', 'powerpress'); ?></label> 
		<?php if ( function_exists('add_videojs_header') ) { ?>
			 <strong style="padding-top: 8px; margin-left: 20px;"><a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_videoplayer.php&amp;ep=1"); ?>" id="activate_videojs_html5_video_player_for_wordpress" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		<?php } ?>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
<?php
		if ( function_exists('add_videojs_header') ) {
			echo powerpressplayer_build_videojs( $Video['videojs-html5-video-player-for-wordpress'] );
		}
?>
			</p>
<?php
	powerpressplayer_videojs_info();
?>
		</li>
		
	</ul>

</td>
</tr>
</table>
<?php
		}
		else // audio player
		{
?>
<input type="hidden" name="ep" value="1" />
<table class="form-table">
<tr valign="top">
<th scope="row">&nbsp;</th>  
<td>
	<ul>
		<li><label><input type="radio" name="Player[player]" id="player_blubrryaudio" value="blubrryaudio" <?php if( $General['player'] == 'blubrryaudio' ) echo 'checked'; ?> /> <?php echo __('Blubrry Audio Player', 'powerpress'); ?>  <?php echo powerpressadmin_new(); ?></label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_player.php&ep=1'); ?>" id="activate_blubrryaudio" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<?php print_blubrry_player_demo(); ?>
		</li>

		<li><label><input type="radio" name="Player[player]" id="player_mediaelement_audio" value="mediaelement-audio" <?php if( $General['player'] == 'mediaelement-audio' ) echo 'checked'; ?> />
		<?php echo __('MediaElement.js Media Player (default)', 'powerpress'); ?></label> 
			 <strong style="padding-top: 8px; margin-left: 20px;"><a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_player.php&ep=1'); ?>" id="activate_mediaelement_audio" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
<?php
			echo powerpressplayer_build_mediaelementaudio( $Audio['mediaelement-audio'] );
?>
			</p>
			<?php powerpressplayer_mediaelement_info(); ?>
			<div style="margin: 30px 0;">
			<?php powerpresspartner_clammr_info(false); ?>
			</div>
		</li>
		
		<li><label><input type="radio" name="Player[player]" id="player_html5audio" value="html5audio" <?php if( $General['player'] == 'html5audio' ) echo 'checked'; ?> /> <?php echo __('HTML5 Audio Player', 'powerpress'); ?>  </label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_player.php&ep=1'); ?>" id="activate_html5audio" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
			<?php
			echo powerpressplayer_build_html5audio($Audio['html5audio']);
			?>
			</p>
			<p>
				<?php echo __('HTML5 audio is an element introduced in the latest HTML specification (HTML5) for the purpose of playing audio.', 'powerpress'); ?>
			</p>
		</li>
				
		<li><label><input type="radio" name="Player[player]" id="player_audio_player" value="audio-player" <?php if( $General['player'] == 'audio-player' ) echo 'checked'; ?> /> <?php echo __('1 Pixel Out Audio Player', 'powerpress'); ?></label>
			<strong style="padding-top: 8px; margin-left: 20px;"><a href="<?php echo admin_url('admin.php?page=powerpress/powerpressadmin_player.php&ep=1'); ?>" id="activate_audio_player" class="activate-player"><?php echo __('Activate and Configure Now', 'powerpress'); ?></a></strong>
		</li>
		<li>
			<div class="updated fade powerpress-notice inline">
			<?php echo __('NOTICE: The 1 pixel out Audio Player will be removed from PowerPress 7.1. We highly recommend picking an HTM5 based player.', 'powerpress'); ?>
			</div>
		</li>
		<li style="margin-left: 30px; margin-bottom:16px;">
			<p>
				<?php  echo powerpressplayer_build_1pxoutplayer( $Audio['audio-player'] ); ?>
			</p>
			<p>
				<?php echo __('1 Pixel Out Audio Player is a popular customizable audio (mp3 only) flash player. Features include an animated play/pause button, scroll-able position bar, elapsed/remaining time, volume control and color styling options.', 'powerpress'); ?>
			</p>
		</li>
		
	</ul>

</td>
</tr>
</table>
<?php
		}
?>
<h4 style="margin-bottom: 0;"><?php echo __('Click \'Save Changes\' to activate and configure selected player.', 'powerpress'); ?></h4>
<?php
	}
	else
	{
?>
<input type="hidden" name="ep" value="1" />
<h2><?php echo __('Configure Player', 'powerpress'); ?></h2>
<?php if( $type == 'audio' ) { ?>
<p style="margin-bottom: 20px;"><strong>&#8592;  <a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_player.php&amp;sp=1"); ?>"><?php echo __('Select a different audio player', 'powerpress'); ?></a></strong></p>
<?php } else if( $type == 'video' ) { ?>
<p style="margin-bottom: 20px;"><strong>&#8592;  <a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_videoplayer.php&amp;sp=1"); ?>"><?php echo __('Select a different video player', 'powerpress'); ?></a></strong></p>
<?php } else { ?>
<p style="margin-bottom: 20px;"><strong>&#8592;  <a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_mobileplayer.php&amp;sp=1"); ?>"><?php echo __('Select a different mobile player', 'powerpress'); ?></a></strong></p>
<?php 
	}
		
	 // Start adding logic here to display options based on the player selected...
	 if( $type == 'audio' )
	 {
		if( empty($General['player']) )
			$General['player'] = '';
		
		switch( $General['player'] )
		{
			case 'audio-player': {
			
				$PlayerSettings = powerpress_get_settings('powerpress_audio-player');
				if($PlayerSettings == ""):
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
						'rtl' => 'no',
						'initialvolume'=>'60',
						'animation'=>'yes',
						'remaining'=>'no',
					);
				endif;

				if( empty($PlayerSettings['remaining']) )
					$PlayerSettings['remaining'] = 'no'; // New default setting
				if( !isset($PlayerSettings['buffer']) )
					$PlayerSettings['buffer'] = ''; // New default setting	
				if( !isset($PlayerSettings['titles']) )
					$PlayerSettings['titles'] = '';
?>
<script type="text/javascript"><!--

function update_audio_player()
{
	var myParams = new Array("lefticon","leftbg", "bg", "voltrack", "rightbg", "rightbghover", "righticon", "righticonhover", "loader", "track", "tracker", "border", "skip", "text", "pagebg", "rtl", "animation", "titles", "initialvolume");
	var myWidth = document.getElementById('player_width').value;
	var myBackground = '';
	if( myWidth < 10 || myWidth > 900 )
		myWidth = 290;
	
	var out = '<object type="application/x-shockwave-flash" data="<?php echo powerpress_get_root_url();?>audio-player.swf" width="'+myWidth+'" height="24">'+"\n";
	out += '    <param name="movie" value="<?php echo powerpress_get_root_url();?>audio-player.swf" />'+"\n";
	out += '    <param name="FlashVars" value="playerID=1&amp;soundFile=<?php echo $Audio['audio-player']; ?>';
	
	var x = 0;
	for( x = 0; x < myParams.length; x++ )
	{
		if( myParams[ x ] == 'border' )
			var Element = document.getElementById( 'player_border' );
		else
			var Element = document.getElementById( myParams[ x ] );
		
		if( Element )
		{
			if( Element.value != '' )
			{
				out += '&amp;';
				out += myParams[ x ];
				out += '=';
				out += Element.value.replace(/^#/, '');
				if( myParams[ x ] == 'pagebg' )
				{
					myBackground = '<param name="bgcolor" value="'+ Element.value +'" />';
					out += '&amp;transparentpagebg=no';
				}
			}
			else
			{
				if( myParams[ x ] == 'pagebg' )
				{
					out += '&amp;transparentpagebg=yes';
					myBackground = '<param name="wmode" value="transparent" />';
				}
			}
		}
	}
	
	out += '" />'+"\n";
	out += '<param name="quality" value="high" />';
	out += '<param name="menu" value="false" />';
	out += myBackground;
	out += '</object>';
	
	var player = document.getElementById("player_preview");
	player.innerHTML = out;
}

function audio_player_defaults()
{
 	if( confirm('<?php echo __("Set defaults, are you sure?\\n\\nAll of the current settings will be overwritten!", 'powerpress'); ?>') )
	{
		jQuery('#player_width').val('290');
		UpdatePlayerPreview('player_width',jQuery('#player_width').val() );
		
		jQuery('#transparentpagebg').val( 'yes');
		UpdatePlayerPreview('transparentpagebg',jQuery('#transparentpagebg').val() );
		
		jQuery('#lefticon').val( '#333333');
		UpdatePlayerPreview('lefticon',jQuery('#lefticon').val() );
		jQuery('#lefticon_prev'  ).css( { 'background-color' : '#333333' } );
		
		jQuery('#leftbg').val( '#CCCCCC');
		UpdatePlayerPreview('leftbg',jQuery('#leftbg').val() );
		jQuery('#leftbg_prev'  ).css( { 'background-color' : '#CCCCCC' } );
		
		jQuery('#bg').val( '#E5E5E5');
		UpdatePlayerPreview('bg',jQuery('#bg').val() );
		jQuery('#bg_prev'  ).css( { 'background-color' : '#E5E5E5' } );
		
		jQuery('#voltrack').val( '#F2F2F2');
		UpdatePlayerPreview('voltrack',jQuery('#voltrack').val() );
		jQuery('#voltrack_prev'  ).css( { 'background-color' : '#F2F2F2' } );
		
		jQuery('#volslider').val( '#666666');
		UpdatePlayerPreview('volslider',jQuery('#volslider').val() );
		jQuery('#volslider_prev'  ).css( { 'background-color' : '#666666' } );
		
		jQuery('#rightbg').val( '#B4B4B4');
		UpdatePlayerPreview('rightbg',jQuery('#rightbg').val() );
		jQuery('#rightbg_prev'  ).css( { 'background-color' : '#B4B4B4' } );
		
		jQuery('#rightbghover').val( '#999999');
		UpdatePlayerPreview('rightbghover',jQuery('#rightbghover').val() );
		jQuery('#rightbghover_prev'  ).css( { 'background-color' : '#999999' } );
		
		jQuery('#righticon').val( '#333333');
		UpdatePlayerPreview('righticon',jQuery('#righticon').val() );
		jQuery('#righticon_prev'  ).css( { 'background-color' : '#333333' } );
		
		jQuery('#righticonhover').val( '#FFFFFF');
		UpdatePlayerPreview('righticonhover',jQuery('#righticonhover').val() );
		jQuery('#righticonhover_prev'  ).css( { 'background-color' : '#FFFFFF' } );
		
		jQuery('#loader').val( '#009900');
		UpdatePlayerPreview('loader',jQuery('#loader').val() );
		jQuery('#loader_prev'  ).css( { 'background-color' : '#009900' } );
		
		jQuery('#track').val( '#FFFFFF');
		UpdatePlayerPreview('track',jQuery('#track').val() );
		jQuery('#track_prev'  ).css( { 'background-color' : '#FFFFFF' } );
		
		jQuery('#tracker').val( '#DDDDDD');
		UpdatePlayerPreview('tracker',jQuery('#tracker').val() );
		jQuery('#tracker_prev'  ).css( { 'background-color' : '#DDDDDD' } );
		
		jQuery('#player_border').val( '#CCCCCC');
		UpdatePlayerPreview('player_border',jQuery('#player_border').val() );
		jQuery('#player_border_prev'  ).css( { 'background-color' : '#CCCCCC' } );
		
		jQuery('#skip').val( '#666666');
		UpdatePlayerPreview('skip',jQuery('#skip').val() );
		jQuery('#skip_prev'  ).css( { 'background-color' : '#666666' } );
		
		jQuery('#text').val( '#333333');
		UpdatePlayerPreview('text',jQuery('#text').val() );
		jQuery('#text_prev'  ).css( { 'background-color' : '#333333' } );
		
		jQuery('#pagebg').val( '');
		UpdatePlayerPreview('pagebg',jQuery('#pagebg').val() );
		
		jQuery('#animation').val( 'yes');
		UpdatePlayerPreview('animation',jQuery('#animation').val() );
		
		jQuery('#remaining').val( 'no');
		UpdatePlayerPreview('remaining',jQuery('#remaining').val() );
		
		jQuery('#buffer').val( '');
		UpdatePlayerPreview('buffer',jQuery('#buffer').val() );
		
		jQuery('#rtl' ).val( 'no' );
		UpdatePlayerPreview('rtl',jQuery('#rtl').val() );
		
		jQuery('#initialvolume').val('60');
		UpdatePlayerPreview('initialvolume',jQuery('#initialvolume').val() );
		
		update_audio_player();
	}
}
//-->
</script>
	<input type="hidden" name="action" value="powerpress-audio-player" />
	<div class="updated fade powerpress-notice inline">
		<?php echo __('NOTICE: The 1 pixel out Audio Player will be removed from PowerPress 7.1. We highly recommend picking an HTM5 based player.', 'powerpress'); ?>
	</div>
	<?php echo __('Configure the 1 pixel out Audio Player', 'powerpress'); ?>
	
	
<table class="form-table">
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?>
		</th>
		<td><div id="player_preview">
		<?php
			echo powerpressplayer_build_1pxoutplayer($Audio['audio-player'], array('nodiv'=>true) );
		?>
			</div>
		</td>
	</tr>
</table>

<div id="powerpress_settings_page" class="powerpress_tabbed_content" style="position: relative;">
	<div style="position: absolute; top: 6px; right:0px;">
		<a href="#" onclick="audio_player_defaults();return false;"><?php echo __('Set Defaults', 'powerpress'); ?></a>
	</div>
  <ul class="powerpress_settings_tabs"> 
		<li><a href="#tab_general"><span><?php echo __('Basic Settings', 'powerpress'); ?></span></a></li> 
		<li><a href="#tab_progress"><span><?php echo __('Progress Bar', 'powerpress'); ?></span></a></li> 
		<li><a href="#tab_volume"><span><?php echo __('Volume Button', 'powerpress'); ?></span></a></li>
		<li><a href="#tab_play"><span><?php echo __('Play / Pause Button', 'powerpress'); ?></span></a></li>
  </ul>
	
 <div id="tab_general" class="powerpress_tab">
 <h3><?php echo __('General Settings', 'powerpress'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Page Background Color', 'powerpress'); ?>
                        
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="pagebg" name="Player[pagebg]" class="color_field" value="<?php echo esc_attr($PlayerSettings['pagebg']); ?>" maxlength="20" />
				<img id="pagebg_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['pagebg']; ?>;" class="color_preview" />
			</div>
			<small>(<?php echo __('leave blank for transparent', 'powerpress'); ?>)</small>
		</td>
	</tr>	<tr valign="top">
		<th scope="row">
			<?php echo __('Player Background Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="bg" name="Player[bg]" class="color_field" value="<?php echo esc_attr($PlayerSettings['bg']); ?>" maxlength="20" />
				<img id="bg_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['bg']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<?php echo __('Width (in pixels)', 'powerpress'); ?>
		</th>
		<td>
          <input type="text" style="width: 50px;" id="player_width" name="Player[width]" class="other_field" value="<?php echo esc_attr($PlayerSettings['width']); ?>" maxlength="20" />
				<?php echo __('width of the player. e.g. 290 (290 pixels) or 100%', 'powerpress'); ?>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Right-to-Left', 'powerpress'); ?>
		</th>
		<td>
			<select style="width: 102px;" id="rtl" name="Player[rtl]" class="other_field"> 
<?php
			$options = array( 'yes'=>__('Yes', 'powerpress'), 'no'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['rtl']);
?>
          </select>			<?php echo __('switches the layout to animate from the right to the left', 'powerpress'); ?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Loading Bar Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="loader" name="Player[loader]" class="color_field" value="<?php echo esc_attr($PlayerSettings['loader']); ?>" maxlength="20" />
				<img id="loader_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['loader']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Text Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
                <input type="text" style="width: 100px;" id="text" name="Player[text]" class="color_field" value="<?php echo esc_attr($PlayerSettings['text']); ?>" maxlength="20" />
						<img id="text_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['text']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Text In Player', 'powerpress'); ?> 
		</th>
		<td>
          <div><input type="text" style="width: 60%;" id="titles" name="Player[titles]" class="other_field" value="<?php echo esc_attr($PlayerSettings['titles']); ?>" maxlength="100" /></div>
				<small><?php echo sprintf(__('Enter \'%s\' to display track name from mp3. Only works if media is hosted on same server as blog.', 'powerpress'), __('TRACK', 'powerpress') ); ?></small>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Play Animation', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
<select style="width: 102px;" id="animation" name="Player[animation]" class="other_field"> 
<?php
			$options = array( 'yes'=>__('Yes', 'powerpress'), 'no'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['animation']);
?>
                                </select>			<?php echo __('if no, player is always open', 'powerpress'); ?></div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Display Remaining Time', 'powerpress'); ?> 
		</th>
		<td>
			<div class="color_control">
<select style="width: 102px;" id="remaining" name="Player[remaining]" class="other_field">
<?php
			$options = array( 'yes'=>__('Yes', 'powerpress'), 'no'=>__('No', 'powerpress') );
			powerpress_print_options( $options, $PlayerSettings['remaining']);
?>
                                </select>			<?php echo __('if yes, shows remaining track time rather than elapsed time (default: no)', 'powerpress'); ?></div>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Buffering Time', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
<select style="width: 200px;" id="buffer" name="Player[buffer]" class="other_field"> 
<?php
			$options = array('0'=>__('No buffering', 'powerpress'), ''=>__('Default (5 seconds)', 'powerpress'),'10'=>__('10 seconds', 'powerpress'),'15'=>__('15 seconds', 'powerpress'),'20'=>__('20 seconds', 'powerpress'),'30'=>__('30 seconds', 'powerpress'),'60'=>__('60 seconds', 'powerpress'));
			powerpress_print_options( $options, $PlayerSettings['buffer']);
?>
                                </select>		<?php echo __('buffering time in seconds', 'powerpress'); ?></div>
		</td>
	</tr>
	
	
</table>
</div>

 <div id="tab_progress" class="powerpress_tab">
	<h3><?php echo __('Progress Bar', 'powerpress'); ?></h3>
<table class="form-table">
        <tr valign="top">
		<th scope="row">
			<?php echo __('Progress Bar Background', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
										<input type="text" style="width: 100px;" id="track" name="Player[track]" class="color_field" value="<?php echo esc_attr($PlayerSettings['track']); ?>" maxlength="20" />
										<img id="track_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['track']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Progress Bar Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
                            <input type="text" style="width: 100px;" id="tracker" name="Player[tracker]" class="color_field" value="<?php echo esc_attr($PlayerSettings['tracker']); ?>" maxlength="20" />
											<img id="tracker_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['tracker']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Progress Bar Border', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
                            <input type="text" style="width: 100px;" id="player_border" name="Player[border]" class="color_field" value="<?php echo esc_attr($PlayerSettings['border']); ?>" maxlength="20" />
											<img id="player_border_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['border']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>       
	</table>
	</div>
	
	
<div id="tab_volume" class="powerpress_tab">
	<h3><?php echo __('Volume Button Settings', 'powerpress'); ?></h3>
	<table class="form-table">	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Initial Volume', 'powerpress'); ?> 
		</th>
		<td>
			<select style="width: 100px;" id="initialvolume" name="Player[initialvolume]" class="other_field">
<?php
			
			for($x = 0; $x <= 100; $x +=5 )
			{
				echo '<option value="'. $x .'"'. ($PlayerSettings['initialvolume'] == $x?' selected':'') .'>'. $x .'%</option>';
			}
?>
			</select> <?php echo __('initial volume level (default: 60)', 'powerpress'); ?>
		</td>
	</tr>
				
	<tr valign="top">
		<th scope="row">
			<?php echo __('Volumn Background Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="leftbg" name="Player[leftbg]" class="color_field" value="<?php echo esc_attr($PlayerSettings['leftbg']); ?>" maxlength="20" />
				<img id="leftbg_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['leftbg']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Speaker Icon Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="lefticon" name="Player[lefticon]" class="color_field" value="<?php echo esc_attr($PlayerSettings['lefticon']); ?>" maxlength="20" />
				<img id="lefticon_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['lefticon']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Volume Icon Background', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="voltrack" name="Player[voltrack]" class="color_field" value="<?php echo esc_attr($PlayerSettings['voltrack']); ?>" maxlength="20" />
				<img id="voltrack_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['voltrack']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Volume Slider Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="volslider" name="Player[volslider]" class="color_field" value="<?php echo esc_attr($PlayerSettings['volslider']); ?>" maxlength="20" />
				<img id="volslider_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['volslider']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
</table>
</div>

<div id="tab_play" class="powerpress_tab">
	<h3><?php echo __('Play / Pause Button Settings', 'powerpress'); ?></h3>
	<table class="form-table">	
        <tr valign="top">
		<th scope="row">
			<?php echo __('Play/Pause Background Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="rightbg" name="Player[rightbg]" class="color_field" value="<?php echo esc_attr($PlayerSettings['rightbg']); ?>" maxlength="20" />
				<img id="rightbg_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['rightbg']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Play/Pause Hover Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="rightbghover" name="Player[rightbghover]" class="color_field" value="<?php echo esc_attr($PlayerSettings['rightbghover']); ?>" maxlength="20" />
				<img id="rightbghover_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['rightbghover']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Play/Pause Icon Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="righticon" name="Player[righticon]" class="color_field" value="<?php echo esc_attr($PlayerSettings['righticon']); ?>" maxlength="20" />
				<img id="righticon_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['righticon']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>
        <tr valign="top">
		<th scope="row">
			<?php echo __('Play/Pause Icon Hover Color', 'powerpress'); ?>
		</th>
		<td>
			<div class="color_control">
				<input type="text" style="width: 100px;" id="righticonhover" name="Player[righticonhover]" class="color_field" value="<?php echo esc_attr($PlayerSettings['righticonhover']); ?>" maxlength="20" />
				<img id="righticonhover_prev" src="<?php echo powerpress_get_root_url(); ?>images/color_preview.gif" width="14" height="14" style="background-color: <?php echo $PlayerSettings['righticonhover']; ?>;" class="color_preview" />
			</div>
		</td>
	</tr>

</table>
</div> <!-- end tab -->
</div> <!-- end tab wrapper -->

<?php
			}; break;

						
			case 'html5audio': {
				$SupportUploads = powerpressadmin_support_uploads();
?>
<p><?php echo __('Configure HTML5 Audio Player', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<p>
<?php
			echo powerpressplayer_build_html5audio( $Audio['html5audio'] );
?>
			</p>
		</td>
	</tr>

	
	<tr>
	<th scope="row">
	<?php echo __('Play Icon', 'powerpress'); ?></th>
	<td>

	<input type="text" id="audio_custom_play_button" name="General[audio_custom_play_button]" style="width: 60%;" value="<?php echo esc_attr($General['audio_custom_play_button']); ?>" maxlength="255" />
	<a href="#" onclick="javascript: window.open( document.getElementById('audio_custom_play_button').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

	<p><?php echo __('Place the URL to the play icon above.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/audio_play_icon.jpg<br /><br />
	<?php echo __('Leave blank to use default play icon image.', 'powerpress'); ?></p>

	<?php if( $SupportUploads ) { ?>
	<p><input name="audio_custom_play_button_checkbox" type="checkbox" onchange="powerpress_show_field('audio_custom_play_button_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?> </p>
	<div style="display:none" id="audio_custom_play_button_upload">
		<label for="audio_custom_play_button_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="audio_custom_play_button_file"  />
	</div>
	<?php } ?>
	</td>
	</tr>
</table>

<?php
			}; break;
		
		case 'blubrryaudio' : {
		
?>
<p><?php echo __('Configure Blubrry Audio Player', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?>
		</th>
		<td>
			<?php print_blubrry_player_demo(); ?>
		</td>
	</tr>

</table>  

<?php

		}; break;
		case 'mediaelement-audio': {
				$SupportUploads = powerpressadmin_support_uploads();
				
				
				if( !isset($General['audio_player_max_width']) )
					$General['audio_player_max_width'] = '';
?>
<p><?php echo __('Configure MediaElement.js Audio Player', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?>
		</th>
		<td><p>
		<?php
		// TODO
			echo powerpressplayer_build_mediaelementaudio($Audio['mediaelement-audio']);
		?>
			</p>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			<?php echo __('Max Width', 'powerpress'); ?>   
		</th>
		<td valign="top">
				<input type="text" style="width: 50px;" id="audio_player_max_width" name="General[audio_player_max_width]" class="player-width" value="<?php echo esc_attr($General['audio_player_max_width']); ?>" maxlength="4" />
			<?php echo __('Width of Audio mp3 player (leave blank for max width)', 'powerpress'); ?>
			<?php powerpresspartner_clammr_info(); ?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row">
			&nbsp;
		</th>
		<td>
			<p><?php echo __('MediaElement.js Player has no additional settings at this time.', 'powerpress'); ?></p>
		</td>
	</tr>
</table>  

<?php
			}; break;
			// TODO:
			default: {
			
				if( empty($General['player_width_audio']) )
					$General['player_width_audio'] = '';
			
?>

<h2><?php echo __('General Settings', 'powerpress'); ?></h2>
	<table class="form-table">
        <tr valign="top">
		<th scope="row">
			<?php echo __('Width', 'powerpress'); ?>   
		</th>
		<td valign="top">
				<input type="text" style="width: 50px;" id="player_width" name="General[player_width_audio]" class="player-width" value="<?php echo esc_attr($General['player_width_audio']); ?>" maxlength="4" />
			<?php echo __('Width of Audio mp3 player (leave blank for 320 default)', 'powerpress'); ?>
		</td>
	</tr>
</table>
<?php
			} break;
		}
	 }
	 else if( $type == 'video' )
	 {
			$player_to_configure = (!empty($General['video_player'])?$General['video_player']:'');
			switch( $player_to_configure )
			{
				case 'html5':
				case 'html5video': {
				
					echo '<p>'. __('Configure HTML5 Video Player', 'powerpress') . '</p>'; 
					?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
		<?php
			if( $type == 'mobile' )
			{
				echo '<p>' . __('Audio:', 'powerpress') .' ';
				echo powerpressplayer_build_html5audio( $Audio['html5audio'] );
				echo '</p>';
			}
		?>
			<p>
<?php
				if( $type == 'mobile' )
					echo  __('Video:', 'powerpress') .' ';
				echo powerpressplayer_build_html5video( $Video['html5video'] );
?>
			</p>
		</td>
	</tr>
</table>

					<?php
				}; break;
				case 'videojs-html5-video-player-for-wordpress': {
					?>
					<p><?php echo __('Configure VideoJS', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<p>
<?php
				echo powerpressplayer_build_videojs( $Video['videojs-html5-video-player-for-wordpress'] );
?>
			</p>
		</td>
	</tr>
</table>
<h3><?php echo __('VideoJS Settings', 'powerpress'); ?></h3>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php echo __('VideoJS CSS Class', 'powerpress'); ?>
</th>
<td>
<p>
<input type="text" name="General[videojs_css_class]" style="width: 150px;" value="<?php echo ( empty($General['videojs_css_class']) ?'':esc_attr($General['videojs_css_class']) ); ?>" /> 
<?php echo __('Apply specific CSS styling to your Video JS player.', 'powerpress'); ?>
</p>
</td>
</tr>
</table>
					<?php
				}; break;
				case 'mejs': // $player_to_configure
				case 'mediaelement-video':
				default: {
					?>
					<p><?php echo __('Configure MediaElement.js Player', 'powerpress'); ?></p>
<table class="form-table">
	<tr valign="top">
		<th scope="row">
			<?php echo __('Preview of Player', 'powerpress'); ?> 
		</th>
		<td>
			<p>
			<?php
			if( $type == 'mobile' )
			{
				echo '<p>' . __('Audio:', 'powerpress') .' ';
				echo powerpressplayer_build_mediaelementaudio( $Audio['mediaelement-audio'] );
				echo '</p>';
			}
					?>
			</p>
			<div style="max-width: 70%;">
				<div class="powerpressadmin-mejs-video">
<?php
				if( $type == 'mobile' )
					echo  __('Video:', 'powerpress') .' ';
				echo powerpressplayer_build_mediaelementvideo( $Video['mediaelement-video'] );
?>
				</div>
			</div>
		</td>
	</tr>
</table>

					<?php
				}; break;
			}
			
			if( !isset($General['poster_play_image']) )
				$General['poster_play_image'] = 1;
			if( !isset($General['poster_image_audio']) )
				$General['poster_image_audio'] = 0;
			if( !isset($General['player_width']) )
				$General['player_width'] = '';
			if( !isset($General['player_height']) )
				$General['player_height'] = '';
			if( !isset($General['poster_image']) )
				$General['poster_image'] = '';
			if( !isset($General['video_player_max_width']) )
				$General['video_player_max_width'] = '';
			if( !isset($General['video_player_max_height']) )
				$General['video_player_max_height'] = '';
			
			if( !isset($General['video_custom_play_button']) )
				$General['video_custom_play_button'] = '';

?>
<!-- Global Video Player settings (Appy to all video players -->
<input type="hidden" name="action" value="powerpress-save-videocommon" />
<h3><?php echo __('Common Video Settings', 'powerpress'); ?></h3>

<p><?php echo __('The following video settings apply to the video player above as well as to classic video &lt;embed&gt; formats such as Microsoft Windows Media (.wmv), QuickTime (.mov) and RealPlayer.', 'powerpress'); ?></p>
<table class="form-table">
<?php
	if( $player_to_configure == 'mediaelement-video' || $player_to_configure == 'mejs' ) 
	{
?>
<tr valign="top">
<th scope="row">
<?php echo __('Player Width', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[player_width]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9%]/g, '');" value="<?php echo esc_attr($General['player_width']); ?>" maxlength="4" />
<?php echo __('Width of player (leave blank for default width)', 'powerpress'); ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Player Height', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[player_height]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9%]/g, '');" value="<?php echo esc_attr($General['player_height']); ?>" maxlength="4" />
<?php echo __('Height of player (leave blank for default height)', 'powerpress'); ?>
</td>
</tr>
<?php
	}
	else
	{
?>
<tr valign="top">
<th scope="row">
<?php echo __('Player Width', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[player_width]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9%]/g, '');" value="<?php echo esc_attr($General['player_width']); ?>" maxlength="4" />
<?php echo __('Width of player (leave blank for 400 default)', 'powerpress'); ?>
</td>
</tr>

<tr valign="top">
<th scope="row">
<?php echo __('Player Height', 'powerpress'); ?>
</th>
<td>
<input type="text" name="General[player_height]" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9%]/g, '');" value="<?php echo esc_attr($General['player_height']); ?>" maxlength="4" />
<?php echo __('Height of player (leave blank for 225 default)', 'powerpress'); ?>
</td>
</tr>
<?php
	}
		$SupportUploads = powerpressadmin_support_uploads();
		
?>
<tr>
<th scope="row">
<?php echo __('Default Poster Image', 'powerpress'); ?></th>
<td>

<input type="text" id="poster_image" name="General[poster_image]" style="width: 60%;" value="<?php echo esc_attr($General['poster_image']); ?>" maxlength="255" />
<a href="#" onclick="javascript: window.open( document.getElementById('poster_image').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('Place the URL to the poster image above.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/poster.jpg<br /><br />
<?php echo __('Image should be at minimum the same width/height as the player above. Leave blank to use default black background image.', 'powerpress'); ?></p>

<?php if( $SupportUploads ) { ?>
<p><input name="poster_image_checkbox" type="checkbox" onchange="powerpress_show_field('poster_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?> </p>
<div style="display:none" id="poster_image_upload">
	<label for="poster_image_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="poster_image_file"  />
</div>
<?php } ?>
<?php
		if( in_array($General['video_player'], array('html5video') ) )
		{
?>
<p><input name="General[poster_play_image]" type="checkbox" value="1" <?php echo ($General['poster_play_image']?'checked':''); ?> /> <?php echo __('Include play icon over poster image when applicable', 'powerpress'); ?> </p>
	<?php if( $type == 'video'  ) { ?>
<p><input name="General[poster_image_audio]" type="checkbox" value="1" <?php echo ($General['poster_image_audio']?'checked':''); ?> /> <?php echo __('Use poster image, player width and height above for audio (Flow Player only)', 'powerpress'); ?> </p>
	<?php } ?>
<?php } ?>
</td>
</tr>

<?php
		// Play icon, only applicable to HTML5/FlowPlayerClassic
		if( in_array($General['video_player'], array('html5video') ) )
		{
?>
<tr>
<th scope="row">
<?php echo __('Video Play Icon', 'powerpress'); ?></th>
<td>

<input type="text" id="video_custom_play_button" name="General[video_custom_play_button]" style="width: 60%;" value="<?php echo esc_attr($General['video_custom_play_button']); ?>" maxlength="255" />
<a href="#" onclick="javascript: window.open( document.getElementById('video_custom_play_button').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('Place the URL to the play icon above.', 'powerpress'); ?> <?php echo __('Example', 'powerpress'); ?>: http://example.com/images/video_play_icon.jpg<br /><br />
<?php echo __('Image should 60 pixels by 60 pixels. Leave blank to use default play icon image.', 'powerpress'); ?></p>

<?php if( $SupportUploads ) { ?>
<p><input name="video_custom_play_button_checkbox" type="checkbox" onchange="powerpress_show_field('video_custom_play_button_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?> </p>
<div style="display:none" id="video_custom_play_button_upload">
	<label for="video_custom_play_button_file"><?php echo __('Choose file', 'powerpress'); ?>:</label><input type="file" name="video_custom_play_button_file"  />
</div>
<?php } ?>
</td>
</tr>
<?php
		}
?>
</table>
<?php
	 }
?>

<?php
	}
}

function print_blubrry_player_demo()
{
?>
		<p>
			<?php echo __('Note: The Blubrry Audio Player is only available to Blubrry Hosting Customers.', 'powerpress'); ?>
		</p>
			<div style="border: 1px solid #000000; height: 138px; box-shadow: inset 0 0 10px black, 0 0 6px black; margin: 20px 0;">
			<?php
			echo powerpressplayer_build_blubrryaudio_by_id(12559710); // Special episdoe where we talk about the new player
			?></div>
			<p>
				<?php echo __('Modern podcast audio player complete with subscribe and share tools.', 'powerpress'); ?>
			</p>
			<p style="margin-top: 10px;">
				<?php echo __('Shownotes and Download options are not displayed initially.', 'powerpress'); ?>
			</p>
<?php
}

?>