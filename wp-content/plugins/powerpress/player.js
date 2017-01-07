/** 
 * jsMediaPlayer 1.5.0 for Blubrry PowerPress
 * 
 * http://www.blubrry.com/powepress/
 *
 * Copyright (c) 2008-2015 Angelo Mandato (angelo [at] mandato {period} com)
 *
 * Released under Aoache 2 license:
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * version 1.5.0	- 04/23/2016 - Removed pp_embed_quicktime function (Preventive measure due to security issues with Quicktime) and removed pp_embed_swf, and show embed function enhanced to toggle.
 * version 1.4.0 - 09/08/2015 - Removed the pp_flashembed function (we are no longer using flash for fallback).
 * version 1.3.0 - 02/18/2011 - Adding HTML5 audio/video tags if format possibly supported around default video embed.
 * version 1.2.0 - 07/20/2009 - Major rewrite, we're now replying less upon this javascript to make way for flexibility for adding future players.
 * version 1.1.3 - 03/23/2009 - Added code to support FlowPlayer v3.
 * version 1.1.2 - 03/04/2009 - Added options to set the width for audio, width and height for video.
 * version 1.1.1 - 12/22/20008 - Minor change to support Windows Media in Firefox. Includes link to preferred Firefox Windows Media Player plugin.
 * version 1.1.0 - 11/25/20008 - Major re-write, object now stored in this include file, auto play is no longer a member variable and is determined by function call.
 * version 1.0.3 - 11/02/2008 - Added option for playing quicktime files in an intermediate fashion with an image to click to play.
 * version 1.0.2 - 07/26/2008 - Fixed pop up player bug caused by v 1.0.1
 * version 1.0.1 - 07/28/2008 - fixed flow player looping playback, flash player no longer loops.
 * version 1.0.0 - 07/26/2008 - initial release
 * Minified with https://javascript-minifier.com/
 */


function powerpress_show_embed(id)
{
	if( document.getElementById('powerpress_embed_'+id) ) {
		if( document.getElementById('powerpress_embed_'+id).style.display == 'block' ) {
			document.getElementById('powerpress_embed_'+id).style.display = 'none';
		} else {
			document.getElementById('powerpress_embed_'+id).style.display = 'block';
			document.getElementById('powerpress_embed_'+id +'_t').select();
		}
	}
	return false;
}

/**
	Insert embed for H.264 mp4 video, with fallback to WebM
	
	@div - specific div to insert embed into
	@media_url - URL of media file to play
	@width - width of player
	@height - height of player
	@webm_media_url - Alternative WebM media URL
*/
function powerpress_embed_html5v(id,media_url,width,height,webm_media_url)
{
	if( document.getElementById('powerpress_player_'+id) )
	{
		var poster = '';
		if( document.getElementById('powerpress_player_'+id).getElementsByTagName ) {
			var images = document.getElementById('powerpress_player_'+id).getElementsByTagName('img');
			if( images.length && images[0].src )
				poster = images[0].src;
		}
		
		var contentType = 'video/mp4'; // Default content type
		if( media_url.indexOf('.webm') > -1 )
			contentType = 'video/webm';
		if( media_url.indexOf('.ogg') > -1 || media_url.indexOf('.ogv') > -1 )
			contentType = 'video/ogg';
		
		var v = document.createElement("video");
		var html5 = false;
		if( !!v.canPlayType ) {
			var status = v.canPlayType(contentType);
			if( status == 'probably' || status == 'maybe' ) {
				html5 = true;
			}
			else if( webm_media_url )
			{
				status = v.canPlayType('video/webm');
				if( status == 'probably' || status == 'maybe' ) {
					html5 = true;
				}
			}
		}
		
		if( html5 ) {
			var s = document.createElement('source');
			v.width = width; v.height = height; v.controls = true;
			if( poster ) v.poster = poster;
			s.src = media_url; s.type = contentType;
			v.appendChild(s);
			if( webm_media_url ) {
				var s_webm = document.createElement('source');
				s_webm.src = webm_media_url; s_webm.type = 'video/webm; codecs="vp8, vorbis"';
				v.appendChild(s_webm);
			}
			
			document.getElementById('powerpress_player_'+id).innerHTML = '';
			document.getElementById('powerpress_player_'+id).appendChild(v);
			v.play();
	
			if( window.powerpress_resize_player )
				powerpress_resize_player();
			
			return false; // stop the default link from proceeding
		}
	}
	
	return true; // let the default link to the media open...
}

/**
	Insert embed for audio, with fallback to flash (m4a/mp3/ogg)
	
	@div - specific div to insert embed into
	@media_url - URL of media file to play
	@width - width of player
	@height - height of player
	@webm_media_url - Alternative WebM media URL
*/
function powerpress_embed_html5a(id,media_url)
{
	if( document.getElementById('powerpress_player_'+id) )
	{
		var poster = '';
		if( document.getElementById('powerpress_player_'+id).getElementsByTagName ) {
			var images = document.getElementById('powerpress_player_'+id).getElementsByTagName('img');
			if( images.length && images[0].src )
				poster = images[0].src;
		}
		
		var contentType = 'audio/mpeg'; // Default content type
		if( media_url.indexOf('.m4a') > -1 )
			contentType = 'audio/x-m4a';
		if( media_url.indexOf('.ogg') > -1 || media_url.indexOf('.oga') > -1 )
			contentType = 'audio/ogg';
		
		var a = document.createElement("audio");
		var html5 = false;
		if( !!a.canPlayType ) {
			var status = a.canPlayType(contentType);
			if( status == 'probably' || status == 'maybe' ) {
				html5 = true;
			}
		}
		
		if( html5 ) {
			var s = document.createElement('source');
			a.controls = true;
			s.src = media_url; s.type = contentType;
			a.appendChild(s);
			
			document.getElementById('powerpress_player_'+id).innerHTML = '';
			document.getElementById('powerpress_player_'+id).appendChild(a);
			a.play();
			return false; // stop the default link from proceeding
		}
	}
	
	return true; // let the default link to the media open...
}

/**
	Insert embed for windows media within specified div
	
	@div - specific div to insert embed into
	@media_url - URL of media file to play
	@width - width of player
	@height - height of player
*/
function powerpress_embed_winplayer(div,media_url,width,height)
{
	if( document.getElementById(div) )
	{
		var Html = '';
		Html += '<object id="winplayer" classid="clsid:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'+ width +'" height="'+ height +'" standby="loading..." type="application/x-oleobject">\n';
		Html += '	<param name="url" value="'+ media_url +'" />\n';
		Html += '	<param name="AutoStart" value="true" />\n';
		Html += '	<param name="AutoSize" value="true" />\n';
		Html += '	<param name="AllowChangeDisplaySize" value="true" />\n';
		Html += '	<param name="standby" value="Media is loading..." />\n';
		Html += '	<param name="AnimationAtStart" value="true" />\n';
		Html += '	<param name="scale" value="aspect" />\n';
		Html += '	<param name="ShowControls" value="true" />\n';
		Html += '	<param name="ShowCaptioning" value="false" />\n';
		Html += '	<param name="ShowDisplay" value="false" />\n';
		Html += '	<param name="ShowStatusBar" value="false" />\n';
		Html += '	<embed type="application/x-mplayer2" src="'+ media_url +'" width="'+ width +'" height="'+ height +'" scale="aspect" AutoStart="true" ShowDisplay="0" ShowStatusBar="0" AutoSize="1" AnimationAtStart="1" AllowChangeDisplaySize="1" ShowControls="1"></embed>\n';
		Html += '</object>\n';
		document.getElementById(div).innerHTML = Html;
		return false; // stop the default link from proceeding
	}
	return true; // let the default link to the media open...
}

