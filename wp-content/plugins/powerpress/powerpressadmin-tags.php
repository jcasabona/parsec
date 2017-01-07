<?php
// powerpressadmin-tags.php

function powerpress_admin_tags()
{
	$General = powerpress_get_settings('powerpress_general');
	$TagSettings = powerpress_default_settings($General, 'tags');
?>
<script language="javascript"><!--
function ToggleID3Tags(Obj)
{
	document.getElementById('edit_id3_tags').style.display=(Obj.checked?'block':'none');
}
//-->
</script>
<input type="hidden" name="action" value="powerpress-save-tags" />
<h2><?php echo __('MP3 Tags', 'powerpress'); ?></h2>

<p><?PHP echo __('Blubrry Hosting users can configure how to have the service write their MP3 ID3 Tags before publishing episodes.', 'powerpress'); ?></p>

<p style="margin-bottom: 0;">
<?php
		echo __('ID3 tags contain useful information (title, artist, album, year, etc...) about your podcast as well as an image for display during playback in most media players.', 'powerpress');
		echo ' ';
		echo sprintf( __('Please visit the %s section of the %s to learn more about MP3 ID3 tags.', 'powerpress'),
				'<a href="http://create.blubrry.com/manual/creating-podcast-media/audio/id3-tags-media-meta-data/" target="_blank">'. __('ID3 Tags and media meta data', 'powerpress') .'</a>',
				'<a href="http://create.blubrry.com/manual/" target="_blank">' .  __('Podcasting Manual', 'powerpress') .'</a>'	);

?>
</p>
<?php
	if( empty($General['blubrry_hosting']) || $General['blubrry_hosting'] === 'false' )
	{
?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Write Tags', 'powerpress'); ?></th> 
<td>
	<p>
		<input name="NotAvailable" type="checkbox" value="1" onchange="alert('<?php echo __('You must configure your Blubrry Services Account in the Blubrry PowerPress > Basic Settings page in order to utilize this feature.', 'powerpress'); ?>'); this.checked=false; return false;" /> 
		<?php echo __('Use Blubrry Hosting services to write MP3 ID3 tags to your media.', 'powerpress'); ?>
	</p>
</td>
</tr>
</table>

<?php
	}
	else
	{
?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Write Tags', 'powerpress'); ?></th> 
<td>
	<p>
		<input name="General[write_tags]" type="checkbox" value="1" <?php if( !empty($General['write_tags']) ) echo 'checked '; ?> onchange="ToggleID3Tags(this);" /> 
		<?php echo __('Use Blubrry Hosting services to write MP3 ID3 tags to your media.', 'powerpress'); ?>
	</p>
</td>
</tr>
</table>
<?php } ?>
<table class="form-table" id="edit_id3_tags" style="display:<?php echo ( !empty($General['blubrry_hosting'])&& $General['blubrry_hosting']!=='false' ?( !empty($General['write_tags'])?'block':'none'):'block'); ?>;">

<?php
	if( empty($General['tag_title']) )
		$General['tag_title'] = '';
	if( empty($General['tag_artist']) )
		$General['tag_artist'] = '';
	if( empty($General['tag_album']) )
		$General['tag_album'] = '';
	if( empty($General['tag_genre']) )
		$General['tag_genre'] = '';
	if( empty($General['tag_year']) )
		$General['tag_year'] = '';
	if( empty($General['tag_track']) )
		$General['tag_track'] = '';
	if( empty($General['tag_composer']) )
		$General['tag_composer'] = '';
	if( empty($General['tag_copyright']) )
		$General['tag_copyright'] = '';
	if( empty($General['tag_url']) )
		$General['tag_url'] = '';
	if( empty($General['tag_coverart']) )
		$General['tag_coverart'] = '';
	
	powerpressadmin_tag_option('tag_title', $General['tag_title'], __('Title Tag', 'powerpress'), __('Use blog post title', 'powerpress') );
	powerpressadmin_tag_option('tag_artist', $General['tag_artist'], __('Artist Tag', 'powerpress'), __('Use Feed Talent Name', 'powerpress') );
	powerpressadmin_tag_option('tag_album', $General['tag_album'], __('Album Tag', 'powerpress'), __('Use blog title', 'powerpress') .': '.  get_bloginfo('name') .'' );
	powerpressadmin_tag_option('tag_genre', $General['tag_genre'], __('Genre Tag', 'powerpress'), __('Use genre \'Podcast\'', 'powerpress') );
	powerpressadmin_tag_option('tag_year', $General['tag_year'], __('Year Tag', 'powerpress'), __('Use current year', 'powerpress') );
	powerpressadmin_tag_option('tag_track', $General['tag_track'], __('Track Tag', 'powerpress'), __('Do not specify track number', 'powerpress') );
	powerpressadmin_tag_option('tag_composer', $General['tag_composer'], __('Composer Tag', 'powerpress'), __('Use Feed Talent Name', 'powerpress') );
	powerpressadmin_tag_option('tag_copyright', $General['tag_copyright'], __('Copyright Tag', 'powerpress'), __('Use &copy; Talent Name', 'powerpress') );
	powerpressadmin_tag_option('tag_url', $General['tag_url'], __('URL Tag', 'powerpress'), __('Use main blog URL', 'powerpress') .': '.  get_bloginfo('url') .'' );
	powerpressadmin_tag_option('tag_coverart', $General['tag_coverart'], __('Coverart Tag', 'powerpress'), '' );
	
?>

</table>
<?php
} // End powerpress_admin_appearance()


function powerpressadmin_tag_option($tag, $value, $label, $default_desc )
{
	$file = false;
	$other = false;
	$track = false;
	switch( $tag )
	{
		case 'tag_title': {
			$other = false;
		}; break;
		case 'tag_track': {
			$track = true;
		}; break;
		case 'tag_coverart': {
			$other = false;
			$file = true;
		}; break;
		default: {
			$other = true;
		}
	}
?>
<tr valign="top">
<th scope="row">
<?php echo $label; ?>
</th>
<td>
<?php
	if( !$file )
	{
?>
<input type="radio" name="General[<?php echo $tag; ?>]" value="0" <?php if( $value == '' ) echo 'checked'; ?> />
<?php
		echo $default_desc;
	}
	
	if( $file )
	{
		$FeedSettings = get_option('powerpress_feed');
		$SupportUploads = false;
		$UploadArray = wp_upload_dir();
		if( false === $UploadArray['error'] )
		{
			$upload_path =  $UploadArray['basedir'].'/powerpress/';
			
			if( !file_exists($upload_path) )
				$SupportUploads = @wp_mkdir_p( rtrim($upload_path, '/') );
			else
				$SupportUploads = true;
		}
?>
<input type="radio" name="General[<?php echo $tag; ?>]" value="0" <?php if( $value == '' ) echo 'checked'; ?> />
<?php echo __('Do not add a coverart image.', 'powerpress'); ?><br />
<input type="radio" id="<?php echo $tag; ?>_specify" name="General[<?php echo $tag; ?>]" value="1" <?php if( $value != '' ) echo 'checked'; ?> />

<input type="text" id="coverart_image" name="TagValues[<?php echo $tag; ?>]" style="width: 50%;" value="<?php echo esc_attr($value); ?>" maxlength="250" />
<a href="#" onclick="javascript: window.open( document.getElementById('coverart_image').value ); return false;"><?php echo __('preview', 'powerpress'); ?></a>

<p><?php echo __('Place the URL to the Coverart image above. e.g. http://mysite.com/images/coverart.jpg', 'powerpress'); ?></P>
<P><?php echo __('Coverart images may be saved as either .gif, .jpg or .png images of any size, though 300 x 300 or 600 x 600 in either png or jpg format is recommended.', 'powerpress'); ?>
</p>
<p>
<?php if( $FeedSettings['itunes_image'] ) { ?>
<input type="hidden" name="NULL[current_coverart_image]" id="current_coverart_image" value="<?php echo htmlspecialchars($FeedSettings['itunes_image']); ?>" />
<a href="#" title="" onclick="document.getElementById('coverart_image').value=document.getElementById('current_coverart_image').value;document.getElementById('tag_coverart_specify').checked=true;return false;"><?php echo __('Click here to use your current iTunes image.', 'powerpress'); ?></a>

<?php } ?>
</p>
<?php if( $SupportUploads ) { ?>
<p><input name="coverart_image_checkbox" type="checkbox" onchange="powerpress_show_field('coverart_image_upload', this.checked)" value="1" /> <?php echo __('Upload new image', 'powerpress'); ?> </p>
<div style="display:none" id="coverart_image_upload">
	<label for="coverart_image_file"><?php echo __('Choose file', 'powerpress'); ?>:</label> <input type="file" name="coverart_image_file" />
</div>
<?php } ?>

<?php
	}
	
	if( $track )
	{
		$PowerPressTrackNumber = get_option('powerpress_track_number');
?><br />
<input type="radio" name="General[<?php echo $tag; ?>]" value="1" <?php if( !empty($value) ) echo 'checked'; ?> /> <?php echo __('Specify', 'powerpress'); ?>: 
<input type="text" name="PowerPressTrackNumber" style="width: 50px;" onkeyup="javascript:this.value=this.value.replace(/[^0-9]/g, '');" value="<?php echo esc_attr( !empty($PowerPressTrackNumber) ?$PowerPressTrackNumber:'1'); ?>" maxlength="5" />
<?php
		echo __('(value entered increments every episode)', 'powerpress');
	}
	
	if( $other )
	{
?><br />
<input type="radio" name="General[<?php echo $tag; ?>]" value="1" <?php if( $value != '' ) echo 'checked'; ?> /> <?php echo __('Specify', 'powerpress'); ?>: 
<input type="text" name="TagValues[<?php echo $tag; ?>]" style="width: 300px" value="<?php echo esc_attr($value); ?>" maxlength="250" />
<?php
	}
	
?>
</td>
</tr>
<?php
}

?>