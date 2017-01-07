<?php
// powerpressadmin-search.php

function powerpress_admin_search()
{
	$General = powerpress_get_settings('powerpress_general');
	if( empty($General['seo_feed_title']) )
		$General['seo_feed_title'] = '';
	
?>
<script language="javascript"><!--

jQuery(document).ready(function() {
	
<?php  
	
	if( empty($General['seo_append_show_title']) )
		echo "	jQuery('#powerpress_example_show_title').hide();\n";
	if( !empty($General['seo_feed_title'])  && $General['seo_feed_title'] == 1 )
		echo "	jQuery('#powerpress_example_post_title').hide();\n";
?>
	jQuery('#seo_feed_title').change( function() {
		if( this.checked )
			jQuery('#powerpress_seo_feed_title_1').prop('checked', true);
		else
			jQuery('.powerpress_seo_feed_title').prop('checked', false);
	});
	jQuery('#seo_append_show_title').change( function() {
		if( jQuery(this).prop('checked') )
			jQuery('#powerpress_example_show_title').show();
		else
			jQuery('#powerpress_example_show_title').hide();
	});
	jQuery('.powerpress_seo_feed_title').change( function() {
		
		jQuery('#seo_feed_title').prop('checked', true);
		switch( this.value )
		{
			case '1':
			case 1: {
				jQuery('#powerpress_example_post_title').hide();
			}; break;
			case '2':
			case 2: {
				jQuery('#powerpress_example_post_title').show();
				
				var p_title_html = jQuery('#powerpress_example_post_title')[0].outerHTML;
				var e_title_html = jQuery('#powerpress_example_episode_title')[0].outerHTML;
				jQuery('#powerpress_example_post_episode_title').html( e_title_html + p_title_html);
			}; break;
			case '3':
			case 3: {
				jQuery('#powerpress_example_post_title').show();
				
				var p_title_html = jQuery('#powerpress_example_post_title')[0].outerHTML;
				var e_title_html = jQuery('#powerpress_example_episode_title')[0].outerHTML;
				jQuery('#powerpress_example_post_episode_title').html( p_title_html + e_title_html);
			}; break;
			default: {
				
			}
		}
	});
});
//-->
</script>
<input type="hidden" name="action" value="powerpress-save-search" />
<h2><?php echo __('Podcasting SEO', 'powerpress'); ?></h2>

<p><?php echo __('Enable features to help with podcasting search engine optimization (SEO). The following options can assist your web and podcasting SEO strategies.', 'powerpress'); ?></p>
<p>
	<a href="http://create.blubrry.com/resources/powerpress/advanced-tools-and-options/podcasting-seo-settings/"  target="_blank"><?php echo __('Learn More', 'powerpress'); ?></a>
</p>


<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Episode Titles', 'powerpress'); ?></th> 
<td>
	<p>
		<label for="seo_feed_title">
		<input name="PowerPressSearchToggle[seo_feed_title]" type="hidden" value="0" />
		<input id="seo_feed_title" name="PowerPressSearchToggle[seo_feed_title]" type="checkbox" value="1" <?php if( !empty($General['seo_feed_title']) ) echo 'checked '; ?> /> 
		<?php echo __('Specify custom episode titles for podcast feeds.', 'powerpress'); ?></label>
	</p>
	<div style="margin-left: 40px;">
		<p><label style="display: block;"><input type="radio" class="powerpress_seo_feed_title" id="powerpress_seo_feed_title_1" name="General[seo_feed_title]" value="1" <?php if( $General['seo_feed_title'] == 1 ) echo 'checked'; ?> />
			<?php echo __('Feed episode title replaces post title (default)', 'powerpress'); ?></label></p>
		<p><label style="display: block;"><input type="radio" class="powerpress_seo_feed_title" id="powerpress_seo_feed_title_2" name="General[seo_feed_title]" value="2" <?php if( $General['seo_feed_title'] == 2 ) echo 'checked'; ?> /> 
			<?php echo __('Feed episode title prefixes post title', 'powerpress'); ?></label></p>
		<p><label style="display: block;"><input type="radio" class="powerpress_seo_feed_title" id="powerpress_seo_feed_title_3" name="General[seo_feed_title]" value="3" <?php if( $General['seo_feed_title'] == 3 ) echo 'checked'; ?> /> 
			<?php echo __('Feed episode title appended to post title', 'powerpress'); ?></label></p>
	</div>
	<p>
		<label for="seo_append_show_title">
		<input name="General[seo_append_show_title]" type="hidden" value="0" />
		<input id="seo_append_show_title" name="General[seo_append_show_title]" type="checkbox" value="1" <?php if( !empty($General['seo_append_show_title']) ) echo 'checked '; ?> /> 
		<?php echo __('Append show title to episode titles.', 'powerpress'); ?></label>
	</p>
	<p style="margin: 10px 0 0 40px;">
		<strong><?php echo __('Example based on options selected above:', 'powerpress'); ?></strong><br /><i>
		<span id="powerpress_example_post_episode_title">
			<span id="powerpress_example_post_title" style="margin: 0 5px;"> <?php echo __('Blog Post Title', 'powerpress'); ?> </span>
			<span id="powerpress_example_episode_title" style="margin: 0 5px;"> <?php echo __('Custom Episode Title', 'powerpress'); ?> </span>
		</span>
		<span id="powerpress_example_show_title"> - <span style="margin: 0 5px;"><?php echo __('Show Title', 'powerpress'); ?></span></span>
		</i>
	</p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php echo __('AudioObjects', 'powerpress'); ?></th> 
<td>
	<p>
		<input name="General[seo_audio_objects]" type="hidden" value="0" />
		<input name="General[seo_audio_objects]" type="checkbox" value="1" <?php if( !empty($General['seo_audio_objects']) ) echo 'checked '; ?> /> 
		<?php echo __('Schema.org audio objects in microdata format.', 'powerpress'); ?>
	</p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php echo __('VideoObjects', 'powerpress'); ?></th> 
<td>
	<p>
		<input name="General[seo_video_objects]" type="hidden" value="0" />
		<input name="General[seo_video_objects]" type="checkbox" value="1" <?php if( !empty($General['seo_video_objects']) ) echo 'checked '; ?> /> 
		<?php echo __('Schema.org video objects in microdata format.', 'powerpress'); ?>
	</p>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php echo __('Podcast Directory SEO Guidance', 'powerpress'); ?></th> 
<td>
	<p>
		<input name="General[seo_itunes]" type="hidden" value="0" />
		<input name="General[seo_itunes]" type="checkbox" value="1" <?php if( !empty($General['seo_itunes']) ) echo 'checked '; ?> /> 
		<?php echo __('Enable and highlight features that help with Podcast Directory Search Engine Optimization.', 'powerpress'); ?>
	</p>
	<p>
	<ul>
			<li>
		<ul>
			<li>
				<?php echo __('Highlight fields for Podcasting SEO', 'powerpress'); ?>
			</li>
			<li>
				<?php echo __('Enables iTunes Subtitle field', 'powerpress'); ?>
			</li>
			<li>
				<?php echo __('Enables iTunes Author field', 'powerpress'); ?>
			</li>
			<li>
				<?php echo __('Enables Enhanced iTunes Summary feature', 'powerpress'); ?>
			</li>
		</ul>
			</li>
		</ul>
	</p>
</td>
</tr>
</table>

<?php

?>

<?php
} // End powerpress_admin_search()

