<?php
	// powerpressadmin-metamarks.php

	function powerpress_metabox_save($post_ID)
	{
		$MetaMarks = ( !empty($_POST['MetaMarks']) ? $_POST['MetaMarks'] : false);
		$Episodes = ( !empty($_POST['Powerpress']) ? $_POST['Powerpress'] : false);
		if( $Episodes )
		{
			while( list($feed_slug,$Powerpress) = each($Episodes) )
			{
				$field = '_'.$feed_slug.':metamarks';
				
				if( !empty($Powerpress['remove_podcast']) )
				{
					delete_post_meta( $post_ID, $field);
				}
				else if( !empty($Powerpress['change_podcast']) || !empty($Powerpress['new_podcast']) )
				{
					// No URL specified, then it's not really a podcast to save
					if( $Powerpress['url'] == '' )
						continue; // go to the next media file
						
					if( !empty($MetaMarks[ $feed_slug ]) )
					{
						$MetaMarkData = $MetaMarks[ $feed_slug ];
						// Loop through, and convert position and duration to seconds, if specified with 00:00:00
						while( list($index,$row) = each($MetaMarkData) )
						{
							$MetaMarkData[ $index ]['position'] = powerpress_raw_duration( $row['position'] );
							$MetaMarkData[ $index ]['duration'] = powerpress_raw_duration( $row['duration'] );
						}
						reset($MetaMarkData);
						
						while( list($index,$row) = each($MetaMarkData) )
						{
							if( empty($MetaMarkData[ $index ]['type']) && empty($MetaMarkData[ $index ]['position']) && empty($MetaMarkData[ $index ]['duration']) && empty($MetaMarkData[ $index ]['link']) && empty($MetaMarkData[ $index ]['value']) )
							{
								unset($MetaMarkData[ $index ]);
							}
						}
						reset($MetaMarkData);
						
						if( count($MetaMarkData) > 0 )
						{
							if( !empty($Powerpress['new_podcast']) )
							{
								add_post_meta($post_ID, $field, $MetaMarkData, true);
							}
							else
							{
								update_post_meta($post_ID, $field, $MetaMarkData);
							}
						}
						else // Delete them from the database...
						{
							delete_post_meta($post_ID, $field );
						}
					}
				}
			} // Loop through posted episodes...
		}
		return $post_ID;
	}
	
	function powerpress_metamarks_get($post_id, $feed_slug)
	{
		$return = array();
		if( $post_id )
		{
			$return = get_post_meta($post_id, '_'. $feed_slug .':metamarks', true);
			if( $return == false )
				$return  = array();
		}
		
		return $return;
	}
	
	function powerpress_metabox_metamarks($value, $object, $feed_slug)
	{
		$MetaRecords = powerpress_metamarks_get($object->ID, $feed_slug );
		
		$html = '<div class="powerpress_row">';
		$html .= '<label for "Powerpress['. $feed_slug .'][metamarks]">'. __('Meta Marks', 'powerpress') .'</label>';
		$html .= '	<div class="powerpress_row_content">';
		$html .= '<input type="hidden" name="Null[powerpress_metamarks_counter_'. $feed_slug .']" id="powerpress_metamarks_counter_'. $feed_slug .'" value="'. count($MetaRecords) .'" />';
		
		$html .= '<div class="powerpress_metamarks_block" id="powerpress_metamarks_block_' . $feed_slug .'">';
			$index = 0;
			while( list($key,$row) = each($MetaRecords) )
			{
				$html .= powerpress_metamarks_editrow_html($feed_slug, $index, $row);
				$index++;
			}
			
		$html .= '</div>';
		$html .= '<input style="cursor:pointer;" type="button" id="powerpress_check_'. $feed_slug .'_button" name="powerpress_check_'. $feed_slug .'_button" value="'. __('Add Meta Mark', 'powerpress') .'" onclick="powerpress_metamarks_addrow(\''. $feed_slug .'\');" class="button" />';
		$html .= '	</div>';
		$html .= '</div>';
		return $value . $html;
	}
	add_filter('powerpress_metabox', 'powerpress_metabox_metamarks', 10, 4);
	
	function powerpress_metabox_admin_head($null)
	{
?>
<script language="javascript"><!--

jQuery(document).ready(function($) {

});

function powerpress_metamarks_addrow(FeedSlug)
{
	var NextRow = 0;
	if( jQuery('#powerpress_metamarks_counter_'+FeedSlug).length > 0 ) {
		NextRow = jQuery('#powerpress_metamarks_counter_'+FeedSlug).val();
	} else {
		alert('<?php echo __('An error occurred.', 'powerpress'); ?>');
		return; 
	}
	NextRow++;
	jQuery('#powerpress_metamarks_counter_'+FeedSlug).val( NextRow );
	
	jQuery.ajax( {
				type: 'POST',
				url: '<?php echo admin_url(); ?>admin-ajax.php', 
				data: { action: 'powerpress_metamarks_addrow', next_row : NextRow, feed_slug : encodeURIComponent(FeedSlug) },
				timeout: (10 * 1000),
				success: function(response) {
					<?php
					if( defined('POWERPRESS_AJAX_DEBUG') )
						echo "\t\t\t\talert(response);\n";
					?>
					jQuery('#powerpress_metamarks_block_'+ FeedSlug ).append( response );
				},
				error: function(objAJAXRequest, strError) {
					
					var errorMsg = "HTTP " +objAJAXRequest.statusText;
					if ( objAJAXRequest.responseText ) {
						errorMsg += ', '+ objAJAXRequest.responseText.replace( /<.[^<>]*?>/g, '' );
					}
					
					jQuery('#powerpress_check_'+FeedSlug).css("display", 'none');
					if( strError == 'timeout' )
						jQuery( '#powerpress_warning_'+FeedSlug ).text( '<?php echo __('Operation timed out.', 'powerpress'); ?>' );
					else if( errorMsg )
						jQuery( '#powerpress_warning_'+FeedSlug ).text( '<?php echo __('AJAX Error', 'powerpress') .': '; ?>'+errorMsg );
					else if( strError != null )
						jQuery( '#powerpress_warning_'+FeedSlug ).text( '<?php echo __('AJAX Error', 'powerpress') .': '; ?>'+strError );
					else 
						jQuery( '#powerpress_warning_'+FeedSlug ).text( '<?php echo __('AJAX Error', 'powerpress') .': '. __('Unknown', 'powerpress'); ?>' );
					jQuery( '#powerpress_warning_'+FeedSlug ).css('display', 'block');
				}
			});
}

function powerpress_metamarks_deleterow(div)
{
	if( confirm('<?php echo __('Delete row, are you sure?', 'powerpress'); ?>') ) {
		jQuery('#'+div).remove();
	}
	return false;
}
// -->
</script>
<style type="text/css">
.pp-metamarks-row {
	position: relative;
	width: 100%;
}
.pp-metamarks-row input {
	vertical-align:top;
}
.pp=metamarks-row input.pp-metamark-link {
}
.pp-metamarks-row select {
	vertical-align:top;
}
.pp-metamarks-row textarea {
	height: 50px;
}
.pp-metamark-delete a {
	margin: 4px 0pt 0pt -2px;
	cursor: pointer;
	width: 10px;
	height: 10px;
	display: block;
	text-indent: -9999px;
	overflow: hidden;
	position: absolute;
	top: 4px;
	left: -14px;
}
.pp-metamark-delete a {
	background: url(<?php echo esc_url( admin_url( 'images/xit.gif' ) ); ?>) no-repeat;
}
.pp-metamark-delete a:hover {
	background: url(<?php echo esc_url( admin_url( 'images/xit.gif' ) ); ?>) no-repeat -10px 0;
}
</style>
<?php
	}
	add_action('admin_head', 'powerpress_metabox_admin_head', 11);
	
	function powerpress_metamarks_addrow() // Called by AJAX call
	{
		$feed_slug = $_POST['feed_slug'];
		$next_row = $_POST['next_row'];
		$html = powerpress_metamarks_editrow_html($feed_slug, $next_row);
		echo $html;
		exit;
	}
	
	function powerpress_metamarks_get_types()
	{
		$types = array();
		$types['audio'] = 'Audio';
		$types['video'] = 'Video';
		$types['image'] = 'Image';
		$types['comment'] = 'Comment';
		$types['tag'] = 'Tag';
		$types['ad'] = 'Advertisement';
		$types['lowerthird'] = 'Lower Third';
		return $types;
	}
	
	function powerpress_metamarks_editrow_html($feed_slug, $next_row, $data = null)
	{
		$feed_slug = esc_attr($feed_slug);
		$MarkTypes = powerpress_metamarks_get_types();
		$html = '<div class="pp-metamarks-row" id="powerpress_metamarks_row_'. $feed_slug .'_'. $next_row .'">';
		if( !is_array($data) )
		{
			$data = array();
			$data['type'] = '';
			$data['position'] = '';
			$data['duration'] = '';
			$data['link'] = '';
			$data['value'] = '';
		}
		
		$data['position'] = powerpress_readable_duration($data['position']);
		$data['duration'] = powerpress_readable_duration($data['duration']);
		if( $data['position'] == '0:00' )
			$data['position'] = '';
		if( $data['duration'] == '0:00' )
			$data['duration'] = '';
		
			$html .= '<select class="pp-metamark-type" type="text" name="MetaMarks['.$feed_slug.']['.$next_row.'][type]" style="width: 18%;">';
			$html .= powerpress_print_options( array(''=>'Select Type')+ $MarkTypes, $data['type'], true);
			$html .= '</select>';
			$html .= '<input class="pp-metamark-position" type="text" name="MetaMarks['.$feed_slug.']['.$next_row.'][position]" value="' .htmlspecialchars($data['position']) .'" placeholder="'. htmlspecialchars(__('Position', 'powerpress'))  .'" style="width: 10%;" />';
			$html .= '<input class="pp-metamark-duration" type="text" name="MetaMarks['.$feed_slug.']['.$next_row.'][duration]" value="' .htmlspecialchars($data['duration']) .'" placeholder="'. htmlspecialchars(__('Duration', 'powerpress'))  .'" style="width: 10%;" />';
			$html .= '<input class="pp-metamark-link" type="text" name="MetaMarks['.$feed_slug.']['.$next_row.'][link]" value="' .htmlspecialchars($data['link']) .'" placeholder="'. htmlspecialchars(__('Link', 'powerpress'))  .'" style="width: 25%;" />';
			$html .= '<textarea class="pp-metamark-value" name="MetaMarks['.$feed_slug.']['.$next_row.'][value]" style="width: 35%;" placeholder="'. htmlspecialchars(__('Value', 'powerpress'))  .'">' .htmlspecialchars($data['value']) .'</textarea>';
			
			$html .= '<div class="pp-metamark-delete"><a href="#" onclick="return powerpress_metamarks_deleterow(\'powerpress_metamarks_row_'. $feed_slug .'_'. $next_row .'\');" title="'. __('Delete', 'powerpress') .'">';
			$html .= '</a></div>';
		
		$html .= '</div>';
		$html .= "\n";
		return $html;
	}
	
	function powerpress_metamarks_print_rss2($episode_data)
	{
		$MetaRecords = powerpress_metamarks_get($episode_data['id'], $episode_data['feed'] );
		while( list($index,$MetaMark) = each($MetaRecords) )
		{
			echo "\t\t";
			echo '<rawvoice:metamark type="'. esc_attr($MetaMark['type']) .'"';
			if( !empty($MetaMark['duration']) )
				echo ' duration="'. esc_attr($MetaMark['duration']) .'"';
			if( !empty($MetaMark['position']) )
				echo ' position="'. esc_attr($MetaMark['position']) .'"';
			if( !empty($MetaMark['link']) )
				echo ' link="'. esc_attr($MetaMark['link']) .'"';
				
			$value = trim($MetaMark['value']);
			if( $value == '' ) {
				echo ' />';
			} else {
				echo '>';
				echo htmlspecialchars($value);
				echo '</rawvoice:metamark>';
			}
			echo PHP_EOL;
		}
	}
	
?>