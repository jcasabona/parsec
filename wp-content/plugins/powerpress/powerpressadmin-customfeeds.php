<?php

if( !function_exists('add_action') )
	die("access denied.");
	
function powerpress_admin_customfeeds_columns($data=array())
{
	$data['name'] = __('Name', 'powerpress');
	$data['feed-slug'] = __('Slug', 'powerpress');
	$data['episode-count'] = __('Episodes', 'powerpress');
	$data['url'] = __('URL', 'powerpress');
	return $data;
}

add_filter('manage_powerpressadmin_customfeeds_columns', 'powerpress_admin_customfeeds_columns');

function powerpress_admin_customfeeds()
{
	$General = powerpress_get_settings('powerpress_general');
	
	
?>
<h2><?php echo __('Custom Podcast Channels', 'powerpress'); ?></h2>
<p>
	<?php echo __('Custom podcast Channels allow you to associate multiple media files and/or formats to one blog post.', 'powerpress'); ?>
</p>

<style type="text/css">

.column-url {
	width: 40%;
}
.column-name {
	width: 30%;
}
.column-feed-slug {
	width: 15%;
}
.column-episode-count {
	width: 15%;
}
</style>
<div id="col-container">

<div id="col-right">
<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
<?php
		print_column_headers('powerpressadmin_customfeeds');
?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php
		print_column_headers('powerpressadmin_customfeeds', false);
?>
	</tr>
	</tfoot>
	<tbody>
<?php
	
	
	$Feeds = array('podcast'=>__('Podcast', 'powerpress') );
	if( isset($General['custom_feeds']['podcast']) )
		$Feeds = $General['custom_feeds'];
	else if( is_array($General['custom_feeds']) )
		$Feeds += $General['custom_feeds'];
		
	asort($Feeds, SORT_STRING); // Sort feeds 
	
	$count = 0;
	while( list($feed_slug, $feed_title) = each($Feeds	) )
	{
		$feed_slug = esc_attr($feed_slug); // Precaution
		$episode_total = powerpress_admin_episodes_per_feed($feed_slug);
		$columns = powerpress_admin_customfeeds_columns();
		$hidden = array();
		if( $feed_slug == 'podcast' )
			$feed_title = __('Podcast', 'powerpress');
		if( $count % 2 == 0 )
			echo '<tr valign="middle" class="alternate">';
		else
			echo '<tr valign="middle">';

		foreach($columns as $column_name=>$column_display_name) {
			$class = "class=\"column-$column_name\"";
			
			$edit_link = admin_url('admin.php?page='. powerpress_admin_get_page() .'&amp;action=powerpress-editfeed&amp;feed_slug=') . $feed_slug;
			
			$url = get_feed_link($feed_slug);
			$short_url = str_replace('http://', '', $url);
			$short_url = str_replace('www.', '', $short_url);
			//if ('/' == substr($short_url, -1))
			//	$short_url = substr($short_url, 0, -1);
			if (strlen($short_url) > 35)
				$short_url = substr($short_url, 0, 32).'...';
			
			//$short_url = '';
			
			switch($column_name) {
				case 'feed-slug': {
					
					echo "<td $class>$feed_slug";
					echo "</td>";
					
				}; break;
				case 'name': {

					echo '<td '.$class.'><strong><a class="row-title" href="'.$edit_link.'" title="' . esc_attr(sprintf(__('Edit "%s"', 'powerpress'), $feed_title)) . '">'. esc_html($feed_title) .'</a></strong>'. ( $feed_slug == 'podcast' ?' ('. __('default channel', 'powerpress') .')':'').'<br />';
					$actions = array();
					$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit', 'powerpress') . '</a>';
					$actions['delete'] = "<a class='submitdelete' href='". admin_url() . wp_nonce_url("admin.php?page=". powerpress_admin_get_page() ."&amp;action=powerpress-delete-feed&amp;feed_slug=$feed_slug", 'powerpress-delete-feed-' . $feed_slug) . "' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to delete feed '%s'\n  'Cancel' to stop, 'OK' to delete.", 'powerpress'), esc_attr($feed_title) )) . "') ) { return true;}return false;\">" . __('Delete', 'powerpress') . "</a>";
					if( !isset($General['custom_feeds'][ $feed_slug ]) )
					{
						unset($actions['delete']);
					}
					$action_count = count($actions);
					$i = 0;
					echo '<div class="row-actions">';
					foreach ( $actions as $action => $linkaction ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo '<span class="'.$action.'">'.$linkaction.$sep .'</span>';
					}
					echo '</div>';
					echo '</td>';
					
				};	break;
					
				case 'url': {
				
					echo "<td $class><a href='$url' title='". esc_attr(sprintf(__('Visit %s', 'powerpress'), $feed_title))."' target=\"_blank\">". esc_html($short_url) ."</a>";
						echo '<div class="row-actions">';
						if( defined('POWERPRESS_FEEDVALIDATOR_URL') ) {
							echo '<span class="'.$action .'"><a href="'. POWERPRESS_FEEDVALIDATOR_URL . urlencode($url) .'" target="_blank">' . __('Validate Feed', 'powerpress') . '</a></span>';
						}
						echo '</div>';
					echo "</td>";
					
				};	break;
					
				case 'episode-count': {
				
					echo "<td $class>$episode_total";
					echo "</td>";
					
				}; break;
				default: {
				
				};	break;
			}
		}
		echo "\n    </tr>\n";
		$count++;
	}
?>
	</tbody>
</table>
<?php if( !isset($General['custom_feeds'][ $feed_slug ]) ) { ?>
<p><?php echo sprintf( __('Note: The default channel "Podcast" is currently using global PowerPress settings. Click %s to customize the default "Podcast" channel.', 'powerpress'), 
	'<a href="'. admin_url('admin.php?page='. powerpress_admin_get_page() .'&amp;action=powerpress-editfeed&amp;feed_slug=podcast') .'">'. __('Edit', 'powerpress') .'</a>'); ?></p>
<?php } ?>
</div> <!-- col-right -->

<div id="col-left">
<div class="col-wrap">
<div class="form-wrap">
<h3><?php echo __('Add Podcast Channel', 'powerpress'); ?></h3>
<div id="ajax-response"></div>
<input type="hidden" name="action" value="powerpress-addfeed" />
<?php
	//wp_original_referer_field(true, 'previous'); 
	//wp_nonce_field('powerpress-add-feed');
?>

<div class="form-field form-required">
	<label for="feed_name"><?php echo __('Feed Name', 'powerpress') ?></label>
	<input name="feed_name" id="feed_name" type="text" value="" size="40" />
    <p><?php echo __('The name is used for use within the administration area only.', 'powerpress'); ?></p>
</div>

<div class="form-field">
	<label for="feed_slug"><?php echo __('Feed Slug', 'powerpress') ?></label>
	<input name="feed_slug" id="feed_slug" type="text" value="" size="40" />
    <p><?php echo __('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'powerpress'); ?></p>
</div>

<p class="submit"><input type="submit" class="button" name="submit" value="<?php echo __('Add Podcast Channel', 'powerpress'); ?>" /></p>

</div>
</div>

</div> <!-- col-left -->

</div> <!-- col-container -->

<h3><?php echo __('Example Usage', 'powerpress'); ?></h3>
<p>
	<?php echo __('Example 1: You want to distribute both an mp3 and an ogg version of your podcast. Use the default podcast channel for your mp3 media and create a custom channel for your ogg media.', 'powerpress'); ?>
</p>
<p>
	<?php echo __('Example 2: You have a video podcast with multiple file formats. Use the default podcast channel for the main media that you want to appear on your blog (e.g. m4v). Create additional channels for the remaining formats (e.g. wmv, mov, mpeg).', 'powerpress'); ?>
</p>
<p>
	<?php echo __('Example 3: You create two versions of your podcast, a 20 minute summary and a full 2 hour episode. Use the default channel for your 20 minute summary episodes and create a new custom channel for your full length episodes.', 'powerpress'); ?>
</p>

<?php
	}
?>