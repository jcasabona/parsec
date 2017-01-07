<?php

if( !function_exists('add_action') )
	die("access denied.");
	
function powerpress_admin_posttypefeeds_columns($data=array())
{
	$data['name'] = __('Feed Title', 'powerpress');
	$data['post-type'] = __('Post Type', 'powerpress');
	$data['feed-slug'] = __('Slug', 'powerpress');
	$data['url'] = __('Feed URL', 'powerpress');
	return $data;
}

add_filter('manage_powerpressadmin_posttypefeeds_columns', 'powerpress_admin_posttypefeeds_columns');

function powerpress_admin_posttypefeeds()
{
	$General = powerpress_get_settings('powerpress_general');
	$post_types = powerpress_admin_get_post_types(false);

?>
<h2><?php echo __('Post Type Podcasting', 'powerpress'); ?></h2>
<p>
	<?php echo __('Post Type Podcasting adds custom podcast settings to specific Post Type feeds.', 'powerpress'); ?>
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
.column-post-type {
	width: 15%;
}
.column-episode-count {
	width: 15%;
}
.category-list {
	width: 100%;
}
.form-field select {
	width: 95%;
}
</style>
<div id="col-container">

<div id="col-right">
<table class="widefat fixed" cellspacing="0">
	<thead>
	<tr>
<?php 
		print_column_headers('powerpressadmin_posttypefeeds');
?>
	</tr>
	</thead>

	<tfoot>
	<tr>
<?php
		print_column_headers('powerpressadmin_posttypefeeds', false);
?>
	</tr>
	</tfoot>
	<tbody>
<?php

	$count = 0;
	while( list($null, $post_type) = each($post_types) )
	{
		$PostTypeSettingsArray = get_option('powerpress_posttype_'. $post_type );
		if( !$PostTypeSettingsArray )
			continue;
		
		while( list($feed_slug, $PostTypeSettings) = each($PostTypeSettingsArray) )
		{
			$feed_title = ( !empty($PostTypeSettings['title']) ? $PostTypeSettings['title'] : '(blank)');
			// $post_type
			// $feed_slug
			
		
			//global $wpdb;

		 //	var_dump($term_info);
			
			//$category = get_category_to_edit($cat_ID);
			
			
			$columns = powerpress_admin_posttypefeeds_columns();
			$hidden = array();

			if( $count % 2 == 0 )
				echo '<tr valign="middle" class="alternate">';
			else
				echo '<tr valign="middle">';
				
			$edit_link = admin_url('admin.php?page='. powerpress_admin_get_page() .'&amp;action=powerpress-editposttypefeed&amp;feed_slug='. $feed_slug .'&podcast_post_type='.$post_type) ;
			
			$url = get_post_type_archive_feed_link($post_type, $feed_slug);
			if( empty($url) ) {
				$url = '';
				$short_url = '';
			} else {
				$short_url = str_replace('http://', '', $url);
				$short_url = str_replace('www.', '', $short_url);
				if (strlen($short_url) > 35)
					$short_url = substr($short_url, 0, 32).'...';
			}
			foreach($columns as $column_name=>$column_display_name) {
				$class = "class=\"column-$column_name\"";

				switch($column_name) {
					case 'feed-slug': {
						
						echo "<td $class>{$feed_slug}";
						echo "</td>";
						
					}; break;
					case 'name': {

						echo '<td '.$class.'><strong><a class="row-title" href="'.$edit_link.'" title="' . esc_attr(sprintf(__('Edit "%s"', 'powerpress'), $feed_title)) . '">'.esc_attr($feed_title).'</a></strong><br />';
						$actions = array();
						$actions['edit'] = '<a href="' . $edit_link . '">' . __('Edit', 'powerpress') . '</a>';
						$actions['remove'] = "<a class='submitdelete' href='". admin_url() . wp_nonce_url("admin.php?page=". powerpress_admin_get_page() ."&amp;action=powerpress-delete-posttype-feed&amp;podcast_post_type={$post_type}&amp;feed_slug={$feed_slug}", 'powerpress-delete-posttype-feed-'.$post_type .'_'.$feed_slug) . "' onclick=\"if ( confirm('" . esc_js(sprintf( __("You are about to remove podcast settings for Post Type '%s'\n  'Cancel' to stop, 'OK' to delete.", 'powerpress'), esc_attr($feed_title) )) . "') ) { return true;}return false;\">" . __('Remove', 'powerpress') . "</a>";
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
					
						echo "<td $class><a href='$url' title='". esc_attr(sprintf(__('Visit %s', 'powerpress'), $feed_title))."' target=\"_blank\">$short_url</a>";
							echo '<div class="row-actions">';
							if( defined('POWERPRESS_FEEDVALIDATOR_URL') ) { // http://www.feedvalidator.org/check.cgi?url=
								echo '<span class="'.$action .'"><a href="'. POWERPRESS_FEEDVALIDATOR_URL . urlencode( str_replace('&amp;', '&', $url) ) .'" target="_blank">' . __('Validate Feed', 'powerpress') . '</a></span>';
							}
							echo '</div>';
						echo "</td>";
						
					};	break;
						
					case 'episode-count': {
					
						echo "<td $class>$episode_total";
						echo "</td>";
						
					}; break;
					case 'post-type': {
						echo "<td $class>$post_type";
						echo "</td>";
					}; break;
					default: {
					
					};	break;
				}
			}
			echo "\n    </tr>\n";
			$count++;
		}
	}
?>
	</tbody>
</table>
</div> <!-- col-right -->

<div id="col-left">
<div class="col-wrap">
<div class="form-wrap">
<h3><?php echo __('Add Podcasting to a custom Post Type', 'powerpress'); ?></h3>
<input type="hidden" name="action" value="powerpress-addposttypefeed" />


<div class="form-field form-required">
<label  for="powerpress_post_type_select"><?php echo __('Post Type', 'powerpress'); ?></label>
<select id="powerpress_post_type_select" name="podcast_post_type" style="width: 95%;">
	<option value=""><?php echo __('Select Post Type', 'powerpress'); ?></option>
<?php



reset($post_types);
while( list($null,$post_type) = each($post_types) ) {
	if( $post_type == 'post' )
		continue;
	
	$post_type = htmlspecialchars($post_type);
	
	echo "\t<option value=\"$post_type\">$post_type</option>\n";
}
?>
</select>
</div>

<div class="form-field form-required">
	<label for="feed_title"><?php echo __('Feed Title', 'powerpress') ?></label>
	<input name="feed_title" id="feed_title" type="text" value="" size="100" />
</div>

<div class="form-field">
	<label for="feed_slug"><?php echo __('Feed Slug', 'powerpress') ?></label>
	<input name="feed_slug" id="feed_slug" type="text" value="" size="40" />
    <p><?php echo __('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'powerpress'); ?></p>
</div>
<?php
	wp_nonce_field('powerpress-add-posttype-feed');
?>
<p class="submit"><input type="submit" class="button" name="add_podcasting" value="<?php echo __('Add Podcasting to Post Type', 'powerpress'); ?>" />  </p>


</div>
</div>

</div> <!-- col-left -->

</div> <!-- col-container -->

<?php
	}
?>