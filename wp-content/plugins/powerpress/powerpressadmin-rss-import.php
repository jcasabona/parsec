<?php

// Load Importer API
require_once( ABSPATH . 'wp-admin/includes/import.php');

if ( !class_exists( 'WP_Importer' ) ) {
	if ( file_exists( ABSPATH . 'wp-admin/includes/class-wp-importer.php' ) )
		require_once( ABSPATH . 'wp-admin/includes/class-wp-importer.php' );
}

/**
 * PowerPress RSS Podcast Importer
 *
 * originally based on the rss importer, significantly modified specifically for podcasting
 */

/**
 * PowerPress RSS Podcast Importer
 *
 * Will process a Podcast RSS feed for importing posts into WordPress. 
 *
 */
if ( class_exists( 'WP_Importer' ) ) {
class PowerPress_RSS_Podcast_Import extends WP_Importer {

	var $m_content = '';
	var $m_item_pos = 0;
	var $m_item_inserted_count = 0;
	var $m_item_skipped_count = 0;
	var $m_item_migrate_count = 0;
	var $m_step = 0;
	var $m_errors = array();
	
	function migrateCount() {
		return $this->m_item_migrate_count;
	}
	
	function importCount() {
		return $this->m_item_inserted_count;
	}
	
	function skippedCount() {
		return $this->m_item_skipped_count;
	}
	
	function errorsExist() {
		return ( count($this->m_errors) > 0 );
	}
	
	function getErrors() {
		return $this->m_errors;
	}
	
	function addError($msg) {
		$this->m_errors[] = $msg;
	}
	

	function header() {
		echo '<div class="wrap" style="padding-left: 5%">';
		screen_icon();
		
		if( !empty($_GET['import']) )
		{
			switch($_GET['import'] )
			{
				case 'powerpress-soundcloud-rss-podcast': echo '<h2>'.__('Import Podcast from SoundCloud', 'powerpress').'</h2>'; break;
				case 'powerpress-libsyn-rss-podcast': echo '<h2>'.__('Import Podcast from LibSyn', 'powerpress').'</h2>'; break;
				case 'powerpress-podbean-rss-podcast': echo '<h2>'.__('Import Podcast from PodBean', 'powerpress').'</h2>'; break;
				case 'powerpress-squarespace-rss-podcast': echo '<h2>'.__('Import Podcast from Squarespace', 'powerpress').'</h2>'; break;
				case 'powerpress-rss-podcast': 
				default: echo '<h2>'.__('Import Podcast RSS Feed', 'powerpress').'</h2>'; break;
			}
		}
		else
		{
			echo '<h2>'.__('Podcast RSS Import', 'powerpress').'</h2>';
		}
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		$General = powerpress_get_settings('powerpress_general');
?>
<div class="wrap">

<p><?php echo __('The following tool will import your podcast episodes to this website.', 'powerpress'); ?></p>

<form enctype="multipart/form-data" action="" method="post" name="blogroll">
<?php wp_nonce_field('import-powerpress-rss') ?>

<div style="width: 70%; margin: auto; height: 8em;">
<input type="hidden" name="step" value="1" />
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo wp_max_upload_size(); ?>" />
<div style="width: 48%;" class="alignleft">
<h3><label for="podcast_feed_url"><?php _e('Podcast Feed URL:', 'powerpress'); ?></label></h3>
<?php
	$placeholder = 'https://example.com/feed.xml';
	switch($_GET['import']) {
		case 'powerpress-soundcloud-rss-podcast': $placeholder = 'http://feeds.soundcloud.com/users/soundcloud:users:00000000/sounds.rss'; break;
		case 'powerpress-libsyn-rss-podcast': $placeholder = 'http://yourshow.libsyn.com/rss'; break;
		case 'powerpress-podbean-rss-podcast': $placeholder = 'http://yourshow.podbean.com/feed/'; break;
		case 'powerpress-squarespace-rss-podcast': $placeholder = 'http://example.com/podcast/?format=rss'; break;
	}
?>
<input type="text" name="podcast_feed_url" id="podcast_feed_url" size="50" class="code" style="width: 90%;" placeholder="<?php echo esc_attr($placeholder); ?>" />
</div>

<div style="width: 48%;" class="alignleft">
<h3><label for="podcast_feed_file"><?php _e('Or choose from your local disk:', 'powerpress'); ?></label></h3>
<input id="podcast_feed_file" name="podcast_feed_file" type="file" />
</div>

</div>
<!--
<p><?php echo sprintf(__('Importing your feed does not migrate your media files. Please use the %s tool to migrate your media once your feed is imported.', 'powerpress'), '<strong><a href="'.admin_url('admin.php?page=powerpress/powerpressadmin_migrate.php') .'">'. __('Migrate Media', 'powerpress') .'</a></strong>'); ?></p>
-->
<style>
.ppi-option {
	margin: 15px 0;
	font-size: 14px;
}
.ppi-option > p,
.ppi-option > label {
	font-size: 16px;
	
}

</style>
<div class="submit">
	
	<div class="ppi-option">
		<h3><?php echo __('Import Podcast To', 'powerpress'); ?></h3>
	</div>
	<div style="">
		<div class="ppi-option">
			<label><input type="radio" name="import_to" id="import_to_default" value="default" checked /> <?php echo __('Default podcast feed', 'powerpress'); ?></label><br />
			<div class="import-to" id="import-to-default" style="display: none;">
				<div style="margin: 10px 0 10px 24px;">
					<label><input type="checkbox" name="import_overwrite_program_info" value="1"> <?php echo __('Import program information', 'powerpress'); ?></label>
				</div>
				<div style="margin: 10px 0 10px 24px;">
					<label><input type="checkbox" name="import_itunes_image" value="1"> <?php echo __('Import iTunes artwork', 'powerpress'); ?></label>
				</div>
			</div>
		</div>
		<div class="ppi-option">
			<label><input type="radio" name="import_to" id="import_to_category" value="category" /> <?php echo __('Podcast Category feed', 'powerpress'); ?></label>
			<div class="import-to" id="import-to-category" style="display: none;">
				<div style="margin: 10px 0 10px 24px;">
					<label for="category"><?php echo __('Category', 'powerpress'); ?></label> &nbsp; <?php
					wp_dropdown_categories(array('show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '', 'hide_empty' => 0, 'id'=>'category', 'name' => 'category', 'orderby' => 'name', 'selected' => '', 'hierarchical' => true));
					?>
				</div>
				<div style="margin: 10px 0 10px 24px;">
					<label><input type="checkbox" name="import_overwrite_program_info_category" value="1"> <?php echo __('Import program information', 'powerpress'); ?></label>
				</div>
				<div style="margin: 10px 0 10px 24px;">
					<label><input type="checkbox" name="import_itunes_image_category" value="1"> <?php echo __('Import iTunes artwork', 'powerpress'); ?></label>
				</div>
			</div>
		</div>
		
		<!-- coming soon -->
		<!--
		<div class="ppi-option">
			<label><input type="radio" name="import_to" id="import_to_channel" value="channel" /> <?php echo __('Podcast Channel feed', 'powerpress'); ?></label>
			<div style="margin: 10px 0 10px 24px;">
				<label for="channel"><?php echo __('Channel', 'powerpress'); ?></label> &nbsp; <?php
				wp_dropdown_categories(array('show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '', 'hide_empty' => 0, 'id'=>'category', 'name' => 'category', 'orderby' => 'name', 'selected' => '', 'hierarchical' => true));
				?>
			</div>
			<div style="margin: 10px 0 10px 24px;">
				<label><input type="checkbox" name="import_overwrite_program_info_channel" value="1"> <?php echo __('Import program information', 'powerpress'); ?></label>
			</div>
			<div style="margin: 10px 0 10px 24px;">
				<label><input type="checkbox" name="import_itunes_image_channel" value="1"> <?php echo __('Import iTunes artwork', 'powerpress'); ?></label>
			</div>
		</div>
		-->
	</div>
	
<div>
<?php 
	//$Settings = get_option('powerpress_general');
	/*
	if( !empty($Settings['blubrry_auth']) ) { ?>
	<label><input type="checkbox" name="migrate_to_blubrry" value="1" checked /> <?php echo __('Migrate Media files to Blubrry Podcast Hosting account', 'powerpress'); ?></label>
<?php } else { ?>
	<label><input type="checkbox" name="migrate_to_blubrry" value="1" /> <?php echo __('Migrate Media files to Blubrry Podcast Hosting account', 'powerpress'); ?></label> <a title="<?php echo esc_attr(__('Blubrry Podcast Hosting', 'powerpress')); ?>" href="<?php echo admin_url('admin.php'); ?>?action=powerpress-jquery-hosting&amp;KeepThis=true&amp;TB_iframe=true&amp;modal=false&amp;width=800&amp;height=400" target="_blank" class="thickbox"><?php echo __('Learn More', 'powerpress'); ?></a>
<?php }
	*/
?>
</div>
<div class="ppi-option">
	<h3><?php echo __('Blubrry Podcast Media Hosting', 'powerpress'); ?></h3>
<?php
		if( empty($General['blubrry_hosting']) || $General['blubrry_hosting'] === 'false' ) {
?>
	<div class="ppi-option">
		<label><input type="checkbox" name="NULL" value="1" disabled> <?php echo __('Migrate media to your Blubrry hosting account', 'powerpress'); ?></label>
	</div>
	<p><a title="<?php echo esc_attr(__('Blubrry Podcast Hosting', 'powerpress')); ?>" href="<?php echo admin_url('admin.php'); ?>?action=powerpress-jquery-hosting&amp;KeepThis=true&amp;TB_iframe=true&amp;modal=false&amp;width=900&amp;height=600" target="_blank" class="thickbox"><?php echo __('Don\'t have a blubrry podcast hosting account?', 'powerpress'); ?></a></p>
<?php
		} else { ?>
	<div class="ppi-option">
		<label><input type="checkbox" name="migrate_to_blubrry" value="1" checked> <?php echo __('Migrate media to your Blubrry hosting account', 'powerpress'); ?></label>
	</div>
<?php
		}
?>
</div>
<link rel="stylesheet" href="<?php echo powerpress_get_root_url(); ?>css/admin.css" type="text/css" media="screen" />
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

<div class="ppi-option">
	
</div>

<h3><a href="#" class="pp-expand-section"><?php echo __('Advanced Options', 'powerpress'); ?></a></h3>

<div style="margin-left: 24px; display: none;">
	<div class="ppi-option">
		<label><input type="checkbox" name="NULL" value="1" checked disabled> <?php echo __('Match episode by GUID (required)', 'powerpress'); ?></label>
	</div>
	<div class="ppi-option">
		<label><input type="checkbox" name="match_filename" value="1" checked> <?php echo __('Match episode by filename (recommended)', 'powerpress'); ?></label>
	</div>
	<div class="ppi-option">
		<label><input type="checkbox" name="match_title" value="1"> <?php echo __('Match episode by post title', 'powerpress'); ?></label>
	</div>
	<div class="ppi-option">
		<label><input type="checkbox" name="match_date" value="1"> <?php echo __('Match episode by exact post date and time', 'powerpress'); ?></label>
	</div>
	<div class="ppi-option">
		<label><input type="checkbox" name="import_blog_posts" value="1" > <?php echo __('Include blog posts', 'powerpress'); ?></label>
	</div>
	<div class="ppi-option">
		<label for="import_item_limit"><?php echo __('Item limit', 'powerpress'); ?></label> &nbsp;
		<input type="text" name="import_item_limit" id="import_item_limit" class="small-text" value="" /> (<?php echo __('leave blank for no limit', 'powerpress'); ?>)
	</div>
	
	
</div>
<?php submit_button( __('Import Podcast', 'powerpress' ), 'button-blubrry'); ?>
</div>
</form>
</div>
<script>
jQuery(document).ready( function() {
	
	var import_type = jQuery("input[name='import_to']:checked").val()
	jQuery('#import-to-'+import_type).show();
	//alert(import_type);
	jQuery("input[name='import_to']").change( function(e) {
		jQuery('.import-to').hide(400);
		jQuery('#import-to-'+jQuery(this).val() ).show(400);
	});
});
</script>
<?php
	return;
		echo '<div class="narrow">';
		
		echo '<h2>'.__('Import saved Feed', 'powerpress') .'</h2>';
		wp_import_upload_form("admin.php?import=rss-podcast&amp;step=1");
		echo '</div>';
	}

	function _normalize_tag( $matches ) {
		return '<' . strtolower( $matches[1] );
	}
	
	function import_program_info($channel, $overwrite=false, $download_itunes_image=false, $category_id = '', $feed_slug ='') {
		$Feed = get_option('powerpress_feed_podcast' );
		if( empty($Feed) )
			$Feed = get_option('powerpress_feed');
		
		$NewSettings = array();
		
		$matches = array();
		$program_title = false;
		if( preg_match('|<title>(.*?)</title>|is', $channel, $matches) ) {
			$program_title = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['title']) )
				$NewSettings['title'] = $program_title;
		}
		
		// language
		$language = false;
		if( preg_match('|<language>(.*?)</language>|is', $channel, $matches) ) {
			$language = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['rss_language']) )
				$NewSettings['rss_language'] = $language;
		}
		
		// copyright
		$copyright = false;
		if( preg_match('|<copyright>(.*?)</copyright>|is', $channel, $matches) ) {
			$copyright = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['copyright']) )
				$NewSettings['copyright'] = $copyright;
		}
		
		// description
		$description = false;
		if( preg_match('|<description>(.*?)</description>|is', $channel, $matches) ) {
			$description = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['description']) )
				$NewSettings['description'] = $description;
		}
		
		// itunes:subtitle
		$itunes_subtitle = false;
		if( preg_match('|<itunes:subtitle>(.*?)</itunes:subtitle>|is', $channel, $matches) ) {
			$itunes_subtitle = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['itunes_subtitle']) )
				$NewSettings['itunes_subtitle'] = $itunes_subtitle;
		}
		
		// itunes:summary
		$itunes_summary = false;
		if( preg_match('|<itunes:summary>(.*?)</itunes:summary>|is', $channel, $matches) ) {
			$itunes_summary = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['itunes_summary']) )
				$NewSettings['itunes_summary'] = $itunes_summary;
		}
		
		// itunes:email
		$itunes_email = false;
		if( preg_match('|<itunes:email>(.*?)</itunes:email>|is', $channel, $matches) ) {
			$itunes_email = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['email']) )
				$NewSettings['email'] = $itunes_email;
		}
		
		// itunes:author
		$itunes_talent_name = false;
		if( preg_match('|<itunes:author>(.*?)</itunes:author>|is', $channel, $matches) ) {
			$itunes_talent_name = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['itunes_talent_name']) )
				$NewSettings['itunes_talent_name'] = $itunes_talent_name;
		}
		
		// itunes:explicit
		if( preg_match('|<itunes:explicit>(.*?)</itunes:explicit>|is', $channel, $explicit) )
		{
			$explicit_array = array('yes'=>1, 'clean'=>2); // No need to save 'no'
			$value = strtolower( trim( $explicit[1] ) );
			if( !empty($explicit_array[ $value ]) )
			{
				if( $overwrite || empty($Feed['itunes_explicit']) ) {
					$NewSettings['itunes_explicit'] = $explicit_array[ $value ];
				}
			}
		}

		// itunes:image
		$itunes_image = '';
		if( preg_match('/<itunes:image.*href="(.*?)".*(\/>|>.*<\/itunes:image>)/i', $channel, $image) )
		{
			$itunes_image = html_entity_decode( trim( $image[1] ) ); // Now we need to download and save the image locally...
			
			// download the image then save it locally...
			if( $download_itunes_image ) {
				
				$upload_path = false;
				$upload_url = false;
				$UploadArray = wp_upload_dir();
				if( false === $UploadArray['error'] )
				{
					$upload_path =  $UploadArray['basedir'].'/powerpress/';
					$upload_url =  $UploadArray['baseurl'].'/powerpress/';
					$filename = str_replace(" ", "_", basename($itunes_image) );
					
					if( file_exists($upload_path . $filename ) )
					{
						$filenameParts = pathinfo($filename);
						if( !empty($filenameParts['extension']) ) {
							do {
								$filename_no_ext = substr($filenameParts['basename'], 0, (strlen($filenameParts['extension'])+1) * -1 );
								$filename = sprintf('%s-%03d.%s', $filename_no_ext, rand(0, 999), $filenameParts['extension'] );
							} while( file_exists($upload_path . $filename ) );
						}
					}
					
					$options = array();
					$options['user-agent'] = 'Blubrry PowerPress/'.POWERPRESS_VERSION;
					if( !empty($_GET['import']) && $_GET['import'] == 'powerpress-squarespace-rss-podcast' )
						$options['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36';
					$options['timeout'] = 10;
					
					$image_data = '';
					$response = wp_safe_remote_get($itunes_image, $options);
					if ( !is_wp_error( $response ) ) {
						$image_data = wp_remote_retrieve_body( $response );
						$this->addError( __('Error downloading itunes image.', 'powerpress') );
					}
		
					if( !empty($image_data) ) {
						file_put_contents($upload_path.$filename, $image_data);
						$NewSettings['itunes_image'] = $upload_url . $filename;
						$NewSettings['rss2_image'] = $itunes_image;
					}
				}
			} else if( $overwrite || empty($Feed['itunes_image']) ) {
				$NewSettings['itunes_image'] = $itunes_image;
				$NewSettings['rss2_image'] = $itunes_image;
			}
		}
		
			
		if( preg_match('|<itunes:author>(.*?)</itunes:author>|is', $channel, $matches) ) {
			$itunes_talent_name = $this->_sanatize_tag_value( $matches[1] );
			
			if( $overwrite || empty($Feed['itunes_talent_name']) )
				$NewSettings['itunes_talent_name'] = $itunes_talent_name;
		}
		
		// itunes:category (up to 3)
		$itunes_categories  = false;
		if( preg_match_all('|<itunes:category.*text="(.*?)"|is', $channel, $matches) ) {
			$pos = 1;
			$itunes_categories = $matches[1];
			$Categories = powerpress_itunes_categories();
			$Categories = array_map('strtolower', $Categories);
			$cats_by_title = array_flip( $Categories );
			
			$FoundCategories = array();
			while( list($index,$category) = each($itunes_categories) )
			{
				$category = str_replace('&amp;', '&', $category);
				$category = strtolower($category);
				if( !empty($cats_by_title[ $category ] ) )
					$FoundCategories[] = $cats_by_title[ $category ];
			}
			
			// Now walk trouigh found categories and stack them correctly...
			// this logic rebuilds the categorires in the correct order no matter what method the service stacked them
			$FinalCats = array(1=>'', 2=>'', 3=>'');
			$last_category_index = 1;
			while( list($index,$cat_id) = each($FoundCategories) )
			{
				if( !empty($FinalCats[$last_category_index]) ) // Do we need to increment to the next category position
				{
					if( intval(substr($FinalCats[$last_category_index], 3)) > 0 )
					{
						$last_category_index++;
					}
					else if( intval(substr($FinalCats[$last_category_index],0, 2)) != intval(substr($cat_id,0, 2)) )
					{
						$last_category_index++;
					}
					// else we can overwrite this category with subcategory
				}
				
				if( $last_category_index > 3 )
					break; // We are at the max cats available...
				
				$FinalCats[ $last_category_index ] = $cat_id;
			}
			
			while( list($field_no, $cat_id) = each($FinalCats) ) {
				if( empty( $cat_id) )
					continue;
				$field = sprintf('itunes_cat_%d', $field_no);
				
				if( $overwrite || empty($Feed[ $field  ]) ) {
					$NewSettings[ $field ] = $cat_id;
				}
			}
		}
		
		if( !empty($NewSettings) )
		{
			if( empty($category_id) && empty($feed_slug) ) {
				// Save here..
				if( get_option('powerpress_feed_podcast') ) { // If the settings were moved to the podcast channels feature...
					powerpress_save_settings($NewSettings, 'powerpress_feed_podcast' ); // save a copy here if that is the case.
				} else {
					powerpress_save_settings($NewSettings, 'powerpress_feed' );
				}
			} else if( !empty($category_id) ) {
				
				// First save the new settings into the specified options row...
				powerpress_save_settings($NewSettings, 'powerpress_cat_feed_'.$category_id ); // save a copy here if that is the case.
				
				// Then add the category id to the global array...
				$CurrentSettings = powerpress_get_settings('powerpress_general');
				if( !in_array($category_id, $CurrentSettings['custom_cat_feeds']) )
				{
					$NewSettings = array();
					$NewSettings['custom_cat_feeds'][] = $category_id;
					if( empty($CurrentSettings['cat_casting']) ) {
						$NewSettings['cat_casting'] = 1; // Turn on category podcasting if not enabled
						$NewSettings['cat_casting_podcast_feeds'] = 1;
						$NewSettings['cat_casting_strict'] = 1;
					}
					
					powerpress_save_settings($NewSettings);
				}
			} else if ( !empty($feed_slug) ) {
				
				powerpress_save_settings($NewSettings, 'powerpress_feed_'.$feed_slug ); // save a copy here if that is the case.
			}
			
			
			
			echo '<hr />';
			echo '<p><strong>'. __('Program information imported', 'powerpress') .'</strong></p>';
			echo '<ul class="ul-disc">';
			while( list($field,$value) = each($NewSettings) )
			{
				if( $field == 'rss2_image' )
					continue;
				
				echo '<li>';
				switch( $field )
				{
					case 'title': echo __('Feed Title (Show Title)', 'powerpress'); break;
					case 'rss_language': echo __(' Feed Language', 'powerpress'); break;
					case 'description': echo __('Feed Description', 'powerpress'); break;
					case 'copyright': echo __('Copyright', 'powerpress'); break;
					case 'itunes_talent_name': echo __('iTunes Author Name', 'powerpress'); break;
					case 'itunes_summary': echo __('iTunes Program Summary', 'powerpress'); break;
					case 'itunes_subtitle': echo __('iTunes Program Subtitle', 'powerpress'); break;
					case 'itunes_image': echo __('iTunes Image', 'powerpress'); break;
					case 'itunes_explicit': echo __('iTunes Explicit', 'powerpress'); break;
					case 'email': echo __('iTunes Email', 'powerpress'); break;
					case 'itunes_cat_1': echo __('iTunes Category', 'powerpress'); break;
					case 'itunes_cat_2': echo __('iTunes Category 2', 'powerpress'); break;
					case 'itunes_cat_3': echo __('iTunes Category 3', 'powerpress'); break;
					default: {
						if( defined('POWERPRESS_DEBUG') ) {
							if( is_string($value) )
								echo $field  . ': '.htmlspecialchars($value);
							else if( is_array($value) )
								echo $field .': {'. print_r($value, true) .'}';
						}
					}; break;
				}
				echo '</li>';
			}
			echo '</ul>';
		}
	}
	
	function import_item($post, $MatchFilter, $import_blog_posts=false, $category_strict='', $feed_slug='') {	
		global $wpdb;
		$this->m_item_pos++;
		
		$matches = array();
		$post_title = false;
		if( !preg_match('|<title>(.*?)</title>|is', $post, $matches) ) {
			echo  sprintf(__('Empty episode title for item %d', 'powerpress'), $this->m_item_pos);
			$this->m_item_skipped_count++;
			return false;
		}
		$post_title = $this->_sanatize_tag_value($matches[1]);
			
		// Look for an enclosure, if not found skip it...
		$enclosure_data = false;
		if( !preg_match('|<enclosure(.*?)/>|is', $post, $enclosure_data) ) {
			echo sprintf(__('No Media found for item %d', 'powerpress'), $this->m_item_pos);
			if( empty($import_blog_posts) ) {
				$this->m_item_skipped_count++;
				return false;
			}
			
			echo ' - ';
		}
		if( !empty($enclosure_data[1]) ) {
			$enclosure = $this->_parse_enclosure( '<enclosure '.$enclosure_data[1].' />', $post, $category_strict );
			if( empty($enclosure) ) {
				if( empty($import_blog_posts) ) {
					echo sprintf(__('No Media found for item %d', 'powerpress'), $this->m_item_pos);
					$this->m_item_skipped_count++;
					return false;
				}
			}
		}
		
		// GUID has to be last, as we will use the media URL as the guid as a last resort
		
		$guid = false;
		if( preg_match('|<guid.*?>(.*?)</guid>|is', $post, $matches) )
			$guid = $this->_sanatize_tag_value( $matches[1] );
		else if( !empty($enclosure['url']) )
			$guid = $enclosure['url'];
		
		$media_url = '';
		if( !empty($enclosure['url']) )
			$media_url = $enclosure['url'];
			
		$post_date_gmt = false;
		if( preg_match('|<pubdate>(.*?)</pubdate>|is', $post, $matches) ) {
			$post_date_gmt = strtotime($matches[1]);
		} else {
			// if we don't already have something from pubDate
			if( preg_match('|<dc:date>(.*?)</dc:date>|is', $post, $matches) )
			{
				$post_date_gmt = preg_replace('|([-+])([0-9]+):([0-9]+)$|', '\1\2\3', $matches[1]);
				$post_date_gmt = str_replace('T', ' ', $post_date_gmt);
				$post_date_gmt = strtotime($post_date_gmt);
			}
		}

		$post_date_gmt = gmdate('Y-m-d H:i:s', $post_date_gmt);
		$post_date = get_date_from_gmt( $post_date_gmt );
		
		// Before we go any further, lets see if we have imported this one already...
		$exists = $this->_find_post(
			(empty($MatchFilter['match_guid'])?'':$guid),
			(empty($MatchFilter['match_title'])?'':$post_title),
			(empty($MatchFilter['match_date'])?'':$post_date),
			(empty($MatchFilter['match_filename'])?'':$media_url),
			$feed_slug
			);
		
		if( !empty($exists) )
		{
			echo sprintf(__('<i>%s</i> already imported.', 'powerpress'), htmlspecialchars($post_title) );
			$this->m_item_skipped_count++;
			return false;
		}
		
		// Okay awesome, lets dig through the rest...
		$categories = array();
		if( preg_match_all('|<category>(.*?)</category>|is', $post, $matches) )
			$categories = $matches[1];

		if ( empty($categories) ) {
			if( preg_match_all('|<dc:subject>(.*?)</dc:subject>|is', $post, $matches) )
				$categories = $matches[1];
		}
		
		$cat_index = 0;
		foreach ($categories as $category) {
			$categories[$cat_index] = $this->_sanatize_tag_value( $category );
			$cat_index++;
		}
		
		$post_content = '';
		if( preg_match('|<content:encoded>(.*?)</content:encoded>|is', $post, $matches) )
			$post_content = $this->_sanatize_tag_value( $matches[1] );

		if ( empty($post_content) ) {
			// This is for feeds that put content in description
			if( preg_match('|<description>(.*?)</description>|is', $post, $matches) )
				$post_content = $this->_sanatize_tag_value( $matches[1] );
		}
		
		if ( empty($post_content) && !empty($enclosure['summary']) ) { // Last case situation lets used the itunes:summary if no description was available
			$post_content = $enclosure['summary'];
		}

		// Clean up content
		$post_content = preg_replace_callback('|<(/?[A-Z]+)|', array( &$this, '_normalize_tag' ), $post_content);
		$post_content = str_replace('<br>', '<br />', $post_content);
		$post_content = str_replace('<hr>', '<hr />', $post_content);

		$post_author = 1;
		$post_status = 'publish';
		
		// Save this episode to the database...
		$post_to_save = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_status', 'guid', 'categories', 'enclosure');
		$this->m_item_inserted_count++;
		echo '<span style="color: green; font-weight: bold;">';
		echo sprintf(__('<i>%s</i> imported.', 'powerpress'), htmlspecialchars($post_title) );
		echo '</span>';
		$post_id = $this->_import_post_to_db($post_to_save);
		
		// Category strict
		if( !empty($category_strict) )
		{
			wp_set_post_categories( $post_id, array($category_strict), true );
		}
		
		return ( $post_id > 0 );
	}
	
	function _sanatize_tag_value($value)
	{
		if( !is_string($value) )
			return '';
		
		$value = trim($value);
		if( preg_match('/^<!\[CDATA\[(.*)\]\]>$/is', $value, $matches) ) {
			$value = $matches[1];
		} else {
			$value = html_entity_decode($value);
		}
		
		return $value;
	}

	function import_episodes($MatchFilter, $import_blog_posts=false, $import_item_limit=0, $category='', $feed_slug='') {
		global $wpdb;
		@set_time_limit(60*15); // Give it 15 minutes
		$this->m_item_pos = 0;
		
		$item_count = substr_count( $this->m_content, '<item>');
		$item_count += substr_count( $this->m_content, '<ITEM>');
		
		echo '<hr />';
		echo '<p><strong>';
		echo __('Importing Episodes...', 'powerpress');
		echo '</strong></p>';
		
		echo '<p>';
		echo sprintf( __('Items Found: %d', 'powerpress'), $item_count);
		echo '</p>';
		@flush();

		echo '<ol>';
		
		$item_start_pos = 0;
		$item_start_pos = (function_exists('mb_stripos')?mb_stripos($this->m_content, '<item>', $item_start_pos):stripos($this->m_content, '<item>', $item_start_pos) );
		$item_end_pos = $item_start_pos;
		$item_end_pos = (function_exists('mb_stripos')?mb_stripos($this->m_content, '</item>', $item_end_pos):stripos($this->m_content, '</item>', $item_end_pos) );
		
		$count = 0;
		while( $item_start_pos !== false && $item_end_pos !== false ) // If one were to return false, we stap!
		{
			// check item limit at the beginning of each iteration
			if( $import_item_limit > 0 && $this->m_item_pos >= $import_item_limit ) {
				break; // Item limit reached, stop!
			}
			
			echo '<li>';
			$new_start = $item_start_pos + mb_strlen('<item>');
			$item_content = mb_substr($this->m_content, $new_start, $item_end_pos - $new_start);
			$this->import_item($item_content, $MatchFilter, $import_blog_posts, $category, $feed_slug);
			echo '</li>';
			
			// Extra stop just in case...
			if( $count > 3000 )
				break;
				
			if( $count % 25 == 0 )
				@flush();
			
			$item_start_pos = (function_exists('mb_stripos')?mb_stripos($this->m_content, '<item>', $item_end_pos):stripos($this->m_content, '<item>', $item_end_pos) );
			$item_end_pos = (function_exists('mb_stripos')?mb_stripos($this->m_content, '</item>', $item_start_pos):stripos($this->m_content, '</item>', $item_start_pos) );
		}
		echo '</ol>';
	}

	function import() {
?>	
<div class="wrap">
<h3><?php _e('Importing Podcast', 'powerpress') ?></h3>
<?php
		
		$result = false;
		if ( empty($_POST['podcast_feed_url']) ) {
			?><p><?php _e('From Uploaded file...', 'powerpress'); ?></p><?php
			$result = $this->_import_handle_upload();
		}
		else
		{
			?><p><?php _e('From URL', 'powerpress'); ?> <?php echo esc_html($_POST['podcast_feed_url']); ?></p><?php
			$result = $this->_import_handle_url();
		}
		
		if( $result == false ) {
			$this->addError( __('Error occurred importing podcast.', 'powerpress') );
			return;
		}
		
		// Match posts by:
		$MatchFilter = array('match_guid'=>true);
		$MatchFilter['match_date'] = (!empty($_POST['match_date'])?true:false);
		$MatchFilter['match_title'] = (!empty($_POST['match_title'])?true:false);
		$MatchFilter['match_filename'] = (!empty($_POST['match_filename'])?true:false);
		
		$import_blog_posts = (!empty($_POST['import_blog_posts'])?true:false);
		$import_item_limit  = (!empty($_POST['import_item_limit'])?intval($_POST['import_item_limit']):0);
		$category  = (!empty($_POST['category'])?intval($_POST['category']):0);
		$feed_slug  = (!empty($_POST['feed_slug'])?intval($_POST['feed_slug']):0);
		$import_  = (!empty($_POST['import_item_limit'])?intval($_POST['import_item_limit']):0);
		$import_to = 'default';
		if( !empty($_POST['import_to']) && $_POST['import_to'] != 'default' )
			$import_to = $_POST['import_to'];
		
		
		// First import program info...
		if( preg_match('/^(.*)<item>/is', $this->m_content, $matches) )
		{
			if( $import_to == 'default' ) {
				$overwrite_program_info = (!empty($_POST['import_overwrite_program_info'])?true:false);
				$import_itunes_image = (!empty($_POST['import_itunes_image'])?true:false);
				if( $overwrite_program_info || $import_itunes_image )
					$this->import_program_info($matches[1], $overwrite_program_info, $import_itunes_image);
			} else if( $import_to == 'category' ) {
				$overwrite_program_info = (!empty($_POST['import_overwrite_program_info_category'])?true:false);
				$import_itunes_image = (!empty($_POST['import_itunes_image_category'])?true:false);
				if( $overwrite_program_info || $import_itunes_image )
					$this->import_program_info($matches[1], $overwrite_program_info, $import_itunes_image, $category);
			} else if( $import_to == 'channel' ) {
				$overwrite_program_info = (!empty($_POST['import_overwrite_program_info_channel'])?true:false);
				$import_itunes_image = (!empty($_POST['import_itunes_image_channel'])?true:false);
				if( $overwrite_program_info || $import_itunes_image )
					$this->import_program_info($matches[1], $overwrite_program_info, $import_itunes_image, false, $feed_slug);
			}
		}
		
		$this->import_episodes($MatchFilter, $import_blog_posts, $import_item_limit, $category, $feed_slug);
		
		$migrated_to_blubrry = false;
		if( !empty($_POST['migrate_to_blubrry'])  && !empty($GLOBALS['pp_migrate_media_urls']) ) {
			require_once( POWERPRESS_ABSPATH .'/powerpressadmin-migrate.php');
			$migrated_to_blubrry = true;
			
			$update_option = true;
			$QueuedFiles = get_option('powerpress_migrate_queued');
			if( !is_array($QueuedFiles) ) {
				$QueuedFiles = array();
				$update_option = false;
			}
			
			$add_urls = '';
			while( list($meta_id, $url) = each($GLOBALS['pp_migrate_media_urls']) )
			{
				if( empty($QueuedFiles[ $meta_id ]) ) { // Add to the array if not already added
					$QueuedFiles[ $meta_id ] = $url;
					if( !empty($add_urls) ) {
						$add_urls .= "\n";
					}
					$this->m_item_migrate_count++;
					$add_urls .= $url;
				}
			}
			
			echo '<h3>';
			echo __('Migration request...', 'powerpress');
			echo '</h3>';
			echo '<pre style="border: 1px solid #333; background-color: #FFFFFF; padding: 4px 8px;">';
			echo $add_urls;
			echo '</pre>';
			
			$UpdateResults = powepress_admin_migrate_add_urls($add_urls);
			if( !empty($UpdateResults) )
			{
				echo '<p>Migration queued successfully.</p>';
				// Queued ok...
				if( $update_option )
					update_option('powerpress_migrate_queued', $QueuedFiles);
				else
					add_option('powerpress_migrate_queued', $QueuedFiles, '', 'no');
			}
			else
			{
				echo '<p>Failed to request migration.</p>';
			}
		}
		
		powerpress_page_message_print();

		echo '<h3>';
		echo __('Import Completed!', 'powerpress');
		echo '</h3>';
		echo '<p>'. sprintf(__('Items Skipped: %d', 'powerpress'), $this->m_item_skipped_count).'</p>';
		echo '<p>'. sprintf(__('Items Inserted: %d', 'powerpress'), $this->m_item_inserted_count).'</p>';
		if( !empty( $this->m_item_migrate_count ) )
			echo '<p>'. sprintf(__('Media files queued for migration: %d', 'powerpress'), $this->m_item_migrate_count).'</p>';
		
		echo '';
		if( $migrated_to_blubrry ) {
			echo '<p>'. sprintf(__('Visit %s to monitor the migration process.','powerpress'), '<strong><a href="'.admin_url('admin.php?page=powerpress/powerpressadmin_migrate.php') .'">'. __('Migrate Media', 'powerpress') .'</a></strong>' ). '</p>';
		} else {
			echo '<p>'. sprintf(__('You may now migrate your media manually or use the %s tool.','powerpress'), '<strong><a href="'.admin_url('admin.php?page=powerpress/powerpressadmin_migrate.php') .'">'. __('Migrate Media', 'powerpress') .'</a></strong>' ). '</p>';
		}
	}

	function dispatch() {
		
		$this->m_step = 0;
		if( !empty($_POST['step']) )
			$this->m_step = intval($_POST['step']);
		else if( !empty($_GET['step']) )
			$this->m_step = intval($_GET['step']);
			
		
		// Drop back down a step if not setup for hosting...
		if( !empty($_POST['migrate_to_blubrry']) ) {
			$Settings = get_option('powerpress_general');
			if( empty($Settings['blubrry_auth']) ) {
				echo '<div class="notice is-dismissible updated"><p>'. sprintf(__('You must have a blubrry Podcast Hosting account to continue.', 'powerpress')) .' '. '<a href="http://create.blubrry.com/resources/podcast-media-hosting/" target="_blank">'. __('Learn More', 'powerpress') .'</a>'. '</p></div>';
				$this->m_step = 0; // Drop back a step
			}
		}
		
		$this->header();

		switch ($this->m_step) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				check_admin_referer('import-powerpress-rss');
				$result = $this->import();
				if ( is_wp_error( $result ) )
					echo $result->get_error_message();
				break;
		}

		$this->footer();
	}

	function get_step() {

		return $this->m_step;
	}
	
	function _find_post_by_guid($guid)
	{
		global $wpdb;

		$post_guid = wp_unslash( sanitize_post_field( 'guid', $guid, 0, 'db' ) );

		$query = "SELECT ID FROM $wpdb->posts WHERE 1=1 ";
		$args = array();

		if ( !empty ( $post_guid ) ) {
			$query .= 'AND guid = %s';
			$args[] = $post_guid;
		}

		if ( !empty ( $args ) ) {
			$found = intval( $wpdb->get_var( $wpdb->prepare($query, $args) ) );
			if( $found > 0 )
				return $found;
		}
		
		return 0;
	}
	
	function _find_post_by_title($title)
	{
		global $wpdb;

		$post_guid = wp_unslash( sanitize_post_field( 'post_title', $title, 0, 'db' ) );

		$query = "SELECT ID FROM $wpdb->posts WHERE 1=1 ";
		$args = array();

		if ( !empty ( $post_guid ) ) {
			$query .= 'AND post_title = %s';
			$args[] = $title;
		}

		if ( !empty ( $args ) ) {
			$found = intval( $wpdb->get_var( $wpdb->prepare($query, $args) ) );
			if( $found > 0 )
				return $found;
		}
		
		return 0;
	}
	
	function _find_post_by_date($date)
	{
		global $wpdb;

		$post_guid = wp_unslash( sanitize_post_field( 'post_date', $date, 0, 'db' ) );

		$query = "SELECT ID FROM $wpdb->posts WHERE 1=1 ";
		$args = array();

		if ( !empty ( $post_guid ) ) {
			$query .= 'AND post_date = %s';
			$args[] = $date;
		}

		if ( !empty ( $args ) ) {
			$found = intval( $wpdb->get_var( $wpdb->prepare($query, $args) ) );
			if( $found > 0 )
				return $found;
		}
		
		return 0;
	}
	
	function _find_post_by_enclosure_filename($filename, $feed_slug = '')
	{
		global $wpdb;
		
		$meta_key = 'podcast';
		if( !empty($feed_slug) && $feed_slug != 'podcast' )
			$meta_key = '_'. $feed_slug .':enclosure';
		
		$meta_value = $filename;
		
		$query = "SELECT p.ID ";
		$query .= "FROM {$wpdb->posts} AS p ";
		$query .= "INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id ";
		$query .= "WHERE pm.meta_key = %s ";
		$query .= "AND pm.meta_value LIKE '%%%s%%' ";
		$query .= "AND p.post_type != 'revision' ";
		$query .= "GROUP BY p.ID ";
		$query .= "ORDER BY p.post_date DESC LIMIT 1 "; // Make sure we use the oldest date
		$query = $wpdb->prepare($query, $meta_key, $meta_value );
		
		$results = $wpdb->get_results($query, ARRAY_A);
		if( !empty($results) )
		{
			if( list($null,$row) = each($results) )
			{
				return $row['ID'];
			}
		}
		
		return 0;
	}
	
	function _find_post($guid = '', $title = '', $date = '', $media_url = '', $feed_slug='') {
		global $wpdb;
		
		if( !empty($guid) )
		{
			$found = $this->_find_post_by_guid($guid);
			if( $found )
				return $found;
		}
		
		if( !empty($media_url) )
		{
			$filename = basename($media_url);
			if( !empty($filename) ) {
				$found = $this->_find_post_by_enclosure_filename($filename, $feed_slug);
				if( $found )
					return $found;
			}
		}
		
		if( !empty($title) )
		{
			$found = $this->_find_post_by_title($title);
			if( $found )
				return $found;
		}
		
		if( !empty($date) )
		{
			$found = $this->_find_post_by_date($date);
				return $found;
		}

		return 0;
	}
	
	function _import_post_to_db($post)
	{
		extract($post);
		$post_id = wp_insert_post($post);
		if ( is_wp_error( $post_id ) )
			return $post_id;
		if (!$post_id) {
			_e('Couldn&#8217;t get post ID', 'powerpress');
			return false;
		}

		if (0 != count($categories))
			wp_create_categories($categories, $post_id);
					
		if( !empty($enclosure['url']) )
		{
			$encstring = $enclosure['url'] . "\n" . $enclosure['length'] . "\n" . $enclosure['type'];
			$serialize = array();
			if( !empty($enclosure['duration']) && function_exists('powerpress_raw_duration') )
				$serialize['duration'] = powerpress_raw_duration($enclosure['duration']);
			if( !empty($enclosure['keywords']) )
				$serialize['keywords'] = $enclosure['keywords'];
			if( !empty($enclosure['summary']) )
				$serialize['summary'] = $enclosure['summary'];
			if( !empty($enclosure['subtitle']) )
				$serialize['subtitle'] = $enclosure['subtitle'];
			if( !empty($enclosure['author']) )
				$serialize['author'] = $enclosure['author'];
			if( !empty($enclosure['itunes_image']) )
				$serialize['itunes_image'] = $enclosure['itunes_image'];
			if( !empty($enclosure['block']) )
				$serialize['block'] = $enclosure['block'];
			if( !empty($enclosure['cc']) )
				$serialize['cc'] = $enclosure['cc'];
			if( !empty($enclosure['order']) )
				$serialize['order'] = $enclosure['order'];
			if( !empty($enclosure['explicit']) )
				$serialize['explicit'] = $enclosure['explicit'];
			if( !empty($enclosure['category']) )
				$serialize['category'] = $enclosure['category'];
				
			if( !empty($serialize) )
				$encstring .= "\n". serialize( $serialize );
			$meta_id = add_post_meta($post_id, 'enclosure', $encstring, true);
			if( $meta_id ) {
				if( empty($GLOBALS['pp_migrate_media_urls']) )
					$GLOBALS['pp_migrate_media_urls'] = array();
				$GLOBALS['pp_migrate_media_urls'][ $meta_id ] = $enclosure['url'];
			}
		}
		return $post_id;
	}
	
	function _parse_enclosure($string, $post, $category_strict='')
	{
		global $wpdb;
		$p = xml_parser_create();
		xml_parse_into_struct($p, $string, $vals, $index);
		xml_parser_free($p);

		if( !empty($vals[0]['attributes']['URL']) )
		{
			$enclosure = array('url'=>trim($vals[0]['attributes']['URL']),'length'=>1, 'type'=>'');
			if(  !empty($vals[0]['attributes']['LENGTH']) )
				$enclosure['length'] = trim($vals[0]['attributes']['LENGTH']);
			if(  !empty($vals[0]['attributes']['TYPE']) )
				$enclosure['type'] = trim($vals[0]['attributes']['TYPE']);
			if( empty($enclosure['type']) )
				$enclosure['type'] = powerpress_get_contenttype($enclosure['url']);
			$matches = array();
			if( preg_match('|<itunes:duration>(.*?)</itunes:duration>|i', $post, $matches) )
			{
				$enclosure['duration'] = $this->_sanatize_tag_value( $matches[1] );
			}
			
			// keywords No longer supported by iTunes:
			if( preg_match('|<itunes:keywords>(.*?)</itunes:keywords>|i', $post, $matches) )
			{
				$enclosure['keywords'] = $this->_sanatize_tag_value( $matches[1] );
			}
			
			if( preg_match('|<itunes:summary>(.*?)</itunes:summary>|is', $post, $matches) )
			{
				$enclosure['summary'] = $this->_sanatize_tag_value( $matches[1] );
			}
			
			if( preg_match('|<itunes:subtitle>(.*?)</itunes:subtitle>|i', $post, $matches) )
			{
				$enclosure['subtitle'] = $this->_sanatize_tag_value( $matches[1] );
			}
			
			if( preg_match('|<itunes:author>(.*?)</itunes:author>|i', $post, $matches) )
			{
				$enclosure['author'] = $this->_sanatize_tag_value(  $matches[1] );
			}
			
			if( preg_match('|<itunes:block>(.*?)</itunes:block>|i', $post, $matches) )
			{
				$value = strtolower(trim( $matches[1] ));
				if( $value == 'yes' )
					$enclosure['block'] = 1;
			}
			
			// <itunes:image href="http://example.com/podcasts/everything/AllAboutEverything.jpg" />
			if( preg_match('/<itunes:image[^h]*href="(.*?)".*(\/>|>.*<\/itunes:image>)/i', $post, $matches) )
			{
				$enclosure['itunes_image'] = html_entity_decode( trim( $matches[1] ) );
			}
			
			if( preg_match('|<itunes:isClosedCaptioned>(.*?)</itunes:isClosedCaptioned>|i', $post, $matches) )
			{
				$value = strtolower(trim( $matches[1] ));
				if( $value == 'yes' )
					$enclosure['cc'] = 1;
			}
			
			if( preg_match('|<itunes:order>(.*?)</itunes:order>|i', $post, $matches) )
			{
				$value = trim( $matches[1] );
				if( !empty($value) )
					$enclosure['order'] = intval($value);
			}
			
			if( preg_match('|<itunes:explicit>(.*?)</itunes:explicit>|i', $post, $matches) )
			{
				$explicit_array = array('yes'=>1, 'clean'=>2); // No need to save 'no'
				$value = strtolower( trim( $matches[1] ) );
				if( !empty($explicit_array[ $value ]) )
					$enclosure['explicit'] = $explicit_array[ $value ];
			}
			
			if( !empty($category_strict) )
			{
				$enclosure['category'] = $category_strict;
			}
				
			return $enclosure;
		}
		
		return '';
	}
	
	function _import_handle_url() {
		
		if( empty($_POST['podcast_feed_url']) ) {
			echo '<p>'.	__( 'URL is empty.', 'powerpress' ) .'<p>';
			return false;
		}
		
		$options = array();
		$options['user-agent'] = 'Blubrry PowerPress/'.POWERPRESS_VERSION;
		if( !empty($_GET['import']) && $_GET['import'] == 'powerpress-squarespace-rss-podcast' )
			$options['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36';
		else if( !empty($_GET['import']) && $_GET['import'] == 'powerpress-podbean-rss-podcast' )
			$options['user-agent'] = 'iTunes/12.2.2 (Macintosh; OS X 10.10.5) AppleWebKit/600.8.9';  // Common user agent
		// 'gPodder/3.8.4 (+http://gpodder.org/)';
		$options['timeout'] = 10;
		
		$response = wp_safe_remote_get($_POST['podcast_feed_url'], $options);
		if ( is_wp_error( $response ) ) {
			echo '<p>'.	$response->get_error_message() .'<p>';
			return false;
		}
		
		$this->m_content = wp_remote_retrieve_body( $response );
		return true;
	}
	
	function _import_handle_upload() {
		if ( ! isset( $_FILES['podcast_feed_file'] )  || empty($_FILES['podcast_feed_file']['tmp_name']) ) {
			echo '<p>'.	__( 'Upload failed.', 'powerpress' ).'<p>';
			return false;
		}
		
		$this->m_content = file_get_contents($_FILES['podcast_feed_file']['tmp_name']);
		return true;
	}
} // end PowerPress_RSS_Podcast_Import class

	$powerpress_rss_podcast_import = new PowerPress_RSS_Podcast_Import();

	register_importer('powerpress-soundcloud-rss-podcast', __('Podcast from SoundCloud', 'powerpress'), __('Import episodes from a SoundCloud podcast feed.', 'powerpress'), array ($powerpress_rss_podcast_import, 'dispatch'));
	register_importer('powerpress-libsyn-rss-podcast', __('Podcast from LibSyn', 'powerpress'), __('Import episodes from a LibSyn podcast feed.', 'powerpress'), array ($powerpress_rss_podcast_import, 'dispatch'));
	register_importer('powerpress-podbean-rss-podcast', __('Podcast from PodBean ', 'powerpress'), __('Import episodes from a PodBean podcast feed.', 'powerpress'), array ($powerpress_rss_podcast_import, 'dispatch'));
	register_importer('powerpress-squarespace-rss-podcast', __('Podcast from Squarespace', 'powerpress'), __('Import episodes from a Squarespace podcast feed.', 'powerpress'), array ($powerpress_rss_podcast_import, 'dispatch'));
	register_importer('powerpress-rss-podcast', __('Podcast RSS Feed', 'powerpress'), __('Import episodes from a RSS podcast feed.', 'powerpress'), array ($powerpress_rss_podcast_import, 'dispatch'));
	
}; // end if WP_Importer exists

// eof