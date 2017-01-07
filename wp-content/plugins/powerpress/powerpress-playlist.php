<?php
	// powerpress-playlist.php
	
	
function powerpress_get_term_by_ttid($ttid, $output = OBJECT, $filter = 'raw')
{
	global $wpdb;

	$value = intval($ttid);
	$field = 'tt.term_taxonomy_id';
	
	$term = $wpdb->get_row( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE $field = %s LIMIT 1", $value) );
	if ( !$term )
		return false;
	
	$taxonomy = $term->taxonomy;
	wp_cache_add($term->term_id, $term, $taxonomy);

	/** This filter is documented in wp-includes/taxonomy.php */
	$term = apply_filters( 'get_term', $term, $taxonomy );

	/** This filter is documented in wp-includes/taxonomy.php */
	$term = apply_filters( "get_$taxonomy", $term, $taxonomy );

	$term = sanitize_term($term, $taxonomy, $filter);

	if ( $output == OBJECT ) {
		return $term;
	} elseif ( $output == ARRAY_A ) {
		return get_object_vars($term);
	} elseif ( $output == ARRAY_N ) {
		return array_values(get_object_vars($term));
	} else {
		return $term;
	}
}
	
function powepress_get_program_title_by_term_taxonomy_id($ttid)
{
	$FeedSettings = powerpress_get_settings('powerpress_taxonomy_'.$ttid);
	if( !empty($FeedSettings['title']) )
		return $FeedSettings['title'];
	return;
}
	
function powerpress_get_program_title_by_taxonomy($term_id, $taxonomy = 'category')
{
	$General = get_option('powerpress_general');
	// Efficiently get the taxonomy program titles from WordPress
	if( !isset($GLOBALS['powerpress'][$taxonomy]) )
	{
		$GLOBALS['powerpress'][$taxonomy] = array();
		
		// SELECT all the caegory podcasting / taxonomy podasting feeds...
		
		if( $taxonomy == 'category' && isset($General['custom_cat_feeds'])  )
		{
			$Feeds = $General['custom_cat_feeds'];
			// Walk through the categories...
			while( list($null, $cat_id) = each($Feeds) )
			{
				$FeedSettings = get_option('powerpress_cat_feed_'.$cat_id);
				if( !empty($FeedSettings['title']) )
					$GLOBALS['powerpress'][$taxonomy][ $cat_id ] = $FeedSettings['title'];
			}
		}
		else
		{
			$PowerPressTaxonomies = get_option('powerpress_taxonomy_podcasting');
			if( !empty($PowerPressTaxonomies) )
			{
				$query_in = '';
				while( list($tt_id, $null) = each($PowerPressTaxonomies) )
				{
					if( !empty($query_in) )
							$query_in .= ',';
						$query_in .= $tt_id;
				}
				
				if( !empty($query_in) )
				{
					$terms = $wpdb->get_results("SELECT term_taxonomy_id, term_id, taxonomy FROM {$wpdb->term_taxonomy} WHERE term_taxonomy_id IN ($query_in)",  ARRAY_A);
				
					while( list($index,$term_info) = each($terms) )
					{
						// TODO: We need to get term by the term_id and taxonomy
						$FeedSettings = powerpress_get_settings('powerpress_taxonomy_'.$term_info['term_taxonomy_id']);
						if( !empty($FeedSettings['title']) )
							$GLOBALS['powerpress'][ $term_info['taxonomy'] ][ $term_info['term_id'] ] = $FeedSettings['title'];
					}
				}
			}
		}
	}
	
	if( !empty($GLOBALS['powerpress'][$taxonomy][$term_id]) )
		return $GLOBALS['powerpress'][$taxonomy][$term_id];
	if( !empty($General['program_title']) )
		return $General['program_title']; // Get the default podcast title
	return ''; // REturn the blog title last resort
}

function powerpress_get_title_by_post_type($post_type, $slug='podcast')
{
	
}

function powerpress_playlist_episodes($args)
{
	global $wpdb;
	$return = array();
	
	$defaults = array(
		'limit' => 10,
		'slug' => 'podcast',
		'post_type'=>'post',
		'category'=>'',
		'taxonomy'=>'',
		'tax_term'=>'',
		'term_taxonomy_id'=>'',
		'ids'=>''
	);
	$args = wp_parse_args( $args, $defaults );
	
	/* handle taxonomy and post id's*/
	if( empty($args['taxonomy']) && empty($args['tax_term']) && !empty($args['category']) )
	{
		$args['taxonomy'] = 'category';
		$args['tax_term'] = $args['category'];
	}
	
	$TaxonomyObj = false;
	if( !empty($args['term_taxonomy_id']) )
	{
		$TaxonomyObj = powerpress_get_term_by_ttid( $args['term_taxonomy_id'] );
	}
	
	if( empty($TaxonomyObj) && !empty($args['taxonomy']) && !empty($args['tax_term']) )
	{
		if( preg_match('/^[0-9]*$/', $args['tax_term']) ) // If it is a numeric ID, lets try finding it by ID first...
			$TaxonomyObj = get_term_by('id', $args['tax_term'], $args['taxonomy']);
		if( empty($TaxonomyObj) )
			$TaxonomyObj = get_term_by('name', $args['tax_term'], $args['taxonomy']);
		if( empty($TaxonomyObj) )
			$TaxonomyObj = get_term_by('slug', $args['tax_term'], $args['taxonomy']);
	}
	
	// Start the SQL query
	$query = "SELECT p.ID, p.post_title, p.post_date, pm.meta_value ";
	$query .= "FROM {$wpdb->posts} AS p ";
	$query .= "INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id ";
	
	if( !empty($TaxonomyObj->term_taxonomy_id) )
		$query .= "INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id ";
	
	$query .= "WHERE (pm.meta_key = %s) ";
	$query .= "AND p.post_type = %s ";
	$query .= "AND p.post_status = 'publish' ";
	if( !empty($TaxonomyObj->term_taxonomy_id) ) {
		$query .= "AND tr.term_taxonomy_id = '". $TaxonomyObj->term_taxonomy_id ."' ";
	}
	
	if( !empty( $args['ids'] ) ) {
		// First santity check make sure we are only working with numbers....
		if( preg_match('/^[0-9,\s]*$/', $args['ids']) ) {
			$ids	= explode(',', preg_replace('/(\s)/', '', $args['ids']) );
			$for_query = '';
			while( list($index,$id) = each($ids) ) {
				if( empty($id) )	
					continue;
				if( !empty($for_query) )
					$for_query .= ', ';
				$for_query .= $id;
			}
			
			if( !empty($for_query) ) {
				$query .= "AND p.ID IN ($for_query) ";
			}
		}	
	}
	
	$query .= "GROUP BY p.ID ";
	$query .= "ORDER BY p.post_date DESC ";
	$query .= "LIMIT 0, %d";
	
	$query = $wpdb->prepare($query, ($args['slug'] == 'podcast'?'enclosure': '_'.$args['slug'].':enclosure'), $args['post_type'], $args['limit'] );
	$results_data = $wpdb->get_results($query, ARRAY_A);
	if( $results_data )
	{
		while( list($null,$row) = each($results_data) )
		{
			if( empty($row['meta_value']) )
				continue;
			
			$EnclosureData = powerpress_get_enclosure_data($row['ID'], $args['slug'], $row['meta_value']);
			$return[ $row['ID'] ] = array();
			$return[ $row['ID'] ]['ID'] = $row['ID'];
			$return[ $row['ID'] ]['post_title'] = $row['post_title'];
			$return[ $row['ID'] ]['post_date'] = $row['post_date'];
			$return[ $row['ID'] ]['enclosure'] = $EnclosureData;
		}
	}
	return $return;
}

/**
 * Output the templates used by playlists.
 *
 */
function powerpress_underscore_playlist_templates() {
?>
<script type="text/html" id="tmpl-wp-playlist-current-item">
	<# if ( data.poster ) { #>
			<img src="{{ data.poster.src }}" />
	<# } #>
	<div class="wp-playlist-caption">
		<# if ( data.meta.program_title ) { #>
			<span class="wp-playlist-item-meta wp-playlist-item-title">{{ data.meta.program_title }}</span>
		<# } #>
		<# if ( data.meta.link ) { #>
		<span class="wp-playlist-item-meta wp-playlist-item-title"><a href="{{ data.meta.link }}" rel="nofollow">{{ data.title }}</a></span>
		<# } else { #>
		<span class="wp-playlist-item-meta wp-playlist-item-title">{{ data.title }}</span>
		<# } #>
		<# if ( data.meta.date ) { #><span class="wp-playlist-item-meta wp-playlist-item-artist">{{ data.meta.date }}</span><# } #>
		<# if ( data.meta.itunes_subtitle ) { #><span class="wp-playlist-item-meta wp-playlist-item-album">{{ data.meta.itunes_subtitle }}</span><# } #>
	</div>
</script>
<script type="text/html" id="tmpl-wp-playlist-item">
	<div class="wp-playlist-item">
		<a class="wp-playlist-caption" href="{{ data.src }}">
				<!-- <span class="wp-playlist-item-title">&#8220;{{{ data.title }}}&#8221;</span> -->
				<span class="wp-playlist-item-title">{{ data.title }}</span>
				<# if ( data.meta.date ) { #>
				<span class="wp-playlist-item-artist"> &mdash; {{ data.meta.date }}</span>
				<# } #>
		</a>
		<# if ( data.meta.length_formatted ) { #>
		<div class="wp-playlist-item-length">{{ data.meta.length_formatted }}</div>
		<# } #>
	</div>
</script>
<?php
}

/**
 * Output and enqueue default scripts and styles for playlists.
 *
 * @since 6.0
 *
 * @param string $type Type of playlist. Accepts 'audio' or 'video'.
 */
function powerpress_playlist_scripts( $type ) {
	wp_enqueue_style( 'wp-mediaelement' ); // Use the playlist built into WordPress
	wp_enqueue_script( 'wp-playlist' ); // Use the playlist built into WordPress
?>
<!--[if lt IE 9]><script>document.createElement('<?php echo esc_js( $type ) ?>');</script><![endif]-->
<?php
	add_action( 'wp_footer', 'powerpress_underscore_playlist_templates', 0 );
	add_action( 'admin_footer', 'powerpress_underscore_playlist_templates', 0 );
}
add_action( 'powerpress_playlist_scripts', 'powerpress_playlist_scripts' );

/**
 * The playlist shortcode.
 *
 * This implements the functionality of the playlist shortcode for displaying
 * a collection of podcast episodes in a post.
 *
 * @since 6.0
 *
 * @param array $attr Playlist shortcode attributes.
 * @return string Playlist output. Empty string if the passed type is unsupported.
 */
function powerpress_playlist_shortcode( $attr ) {
	global $content_width;
	
	if ( is_feed() ) {
		return '';
	}

	static $instance = 0;
	$instance++;
	
	/*
	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) ) {
			$attr['orderby'] = 'post__in';
		}
		$attr['include'] = $attr['ids'];
	}
	*/

	
	extract( shortcode_atts( array(
		'type'		=> 'audio', // Already defined by the first episode type
		'style'		=> 'light', /* */
		'tracklist' => true, /* always true */
		'tracknumbers' => true, /* always false for podcasting */
		'images'	=> true, // Used for PowerPress Playlist
		'image'	=> '', // Used for PowerPress Playlist (specific image URL for default poster artwork
		'artists'	=> true, // display the artist / author / talent name (Future use)
		'itunes_subtitle'=>false,
		'category'=>'', // Used for PowerPress Playlist (specify category ID, name or slug)
		'term_id'=>'', // Used for PowerPress Playlist (specify term ID, name or slug)
		'taxonomy'=>'', // Used for PowerPress Playlist (specify taxonomy name)
		'term_taxonomy_id'=>'', // Used for PowerPress Playlist (specify term_taxonomy_id)
		'program_titles_by_taxonomy'=>'', // e.g. category
		'date'	=> true,  // Display the date
		'title'	=> true, // Dislay the title of program
		'links'=>true, // Link to episode page
		'slug' => '', // Used for PowerPress Playlist
		'feed' => '', // Used for PowerPress Playlist
		'channel'=>'', // Used for PowerPress Playlist
		'post_type' => 'post', // Used for PowerPress Playlist
		'limit'=>10, // Used for PowerPress Playlist
		'ids'=>'' // Used to specify specific post ids to assemble a player with specific episodes
	), $attr, 'powerpressplaylist' ) );
	
			
	if ( $type !== 'audio' ) {
		$type = 'video';
	}


	$tracknumbers = false;
	//$images = true;
	$artists = true; // Program title
	
	$images = filter_var( $images, FILTER_VALIDATE_BOOLEAN );
	$links = filter_var( $links, FILTER_VALIDATE_BOOLEAN );
	$itunes_subtitle = filter_var( $itunes_subtitle, FILTER_VALIDATE_BOOLEAN );
	$date = filter_var( $date, FILTER_VALIDATE_BOOLEAN );
	
	if( empty($slug) && !empty($feed) ) 
		$slug = $feed;
	if( empty($slug) && !empty($channel) ) 
		$slug = $channel;
	if( empty($slug) )
		$slug = 'podcast';

	$args = array(
		'limit' => $limit,
		'slug' => $slug,
		'post_type'=>$post_type,
		'category'=>$category,
		'term_id'=>'',
		'taxonomy'=>'',
		'term_taxonomy_id'=>$term_taxonomy_id,
		'ids'=>$ids
	);
	
	$episodes = powerpress_playlist_episodes( $args );
	
	if ( empty( $episodes ) ) {
		return '';
	}
	
	$ProgramSettings = false;
	// Get Podcast Settings...ssss
	if( !empty($post_type) )
	{
		$PostTypeSettingsArray = get_option('powerpress_posttype_'.$post_type);
		if( isset($PostTypeSettingsArray[ $slug ]) && is_array($PostTypeSettingsArray[ $slug ] ) )
		{
			$ProgramSettings = $PostTypeSettingsArray[ $slug ];
		}
	}
	if( !empty($slug) && !$ProgramSettings )
		$ProgramSettings = get_option('powerpress_feed_'.$slug);
	if( !empty($term_taxonomy_id) && !$ProgramSettings )
		$ProgramSettings = get_option('powerpress_taxonomy_'.$term_taxonomy_id);
	if( empty($ProgramSettings) )
		$ProgramSettings = get_option('powerpress_general');
	
	$outer = 22; // default padding and border of wrapper

	$default_width = 640;
	$default_height = 360;

	$theme_width = empty( $content_width ) ? $default_width : ( $content_width - $outer );
	$theme_height = empty( $content_width ) ? $default_height : round( ( $default_height * $theme_width ) / $default_width );

	$data = compact( 'type' );
	
	if( !empty($images) && empty($image) ) // If they specified images but did not specify a specific image in the shortcode...
	{
		if( !empty($ProgramSettings['rss2_image']) )
			$image = $ProgramSettings['rss2_image'];
		else if( !empty($ProgramSettings['itunes_image']) )
			$image = $ProgramSettings['itunes_image'];
	}

	// don't pass strings to JSON, will be truthy in JS
	// foreach ( array( 'tracklist', 'tracknumbers', 'images', 'artists' ) as $key ) {
	foreach ( array( 'tracklist', 'tracknumbers', 'images', 'artists', 'date', 'itunes_subtitle' ) as $key ) {
		$data[$key] = filter_var( $$key, FILTER_VALIDATE_BOOLEAN );
	}
	
	// Set a global poster image
	if( !empty($image) ) {
		$data['poster'] = array( 'src'=>$image, 'width'=>'144', 'height'=>'144' );
	}
	
	$tracks = array();
	foreach ( $episodes as $episode ) {
		//$url = wp_get_attachment_url( $attachment->ID );
		$url = $episode['enclosure']['url'];
		//$ftype = wp_check_filetype( $url, wp_get_mime_types() );
		$track = array(
			'src' => $url,
			'type' => $episode['enclosure']['type'],
			'title' => $episode['post_title'],
			'caption' => $episode['post_title'],
			'description' => $episode['post_title']
		);
		
		//$image = false;
		$episode_image = $image;
		if( $images && !empty($episode['enclosure']['image']) )
		{
			$episode_image = $episode['enclosure']['image'];
		}
		else if( $images && !empty($episode['enclosure']['itunes_image']) )
		{
			$episode_image = $episode['enclosure']['itunes_image'];
		}
		// enclosure

		$track['meta'] = array();
		
		$track['meta']['artist'] = ('Talent Name'); // TODO
		$track['meta']['album'] = ('Podcast Title here'); // TODO
		if( $program_titles_by_taxonomy )
		{
			$ObjectTerms = wp_get_object_terms( $episode['ID'], $program_titles_by_taxonomy);
			if(!empty($ObjectTerms) && !is_wp_error( $ObjectTerms ) && count($ObjectTerms) == 1 )
			{
				$track['meta']['program_title'] = powerpress_get_program_title_by_taxonomy($ObjectTerms[0]->term_id, $program_titles_by_taxonomy);
			}
		}
		$track['meta']['title'] = $episode['post_title'];
		if( !empty($itunes_subtitle) && !empty($episode['enclosure']['subtitle']) )
			$track['meta']['itunes_subtitle'] = $episode['enclosure']['subtitle'];
		$track['meta']['genre'] = 'Podcast';
		$track['meta']['year'] = mysql2date( 'Y', $episode['post_date'] ); // Episode year
		if( !empty($date) )
			$track['meta']['date'] = mysql2date( get_option( 'date_format' ), $episode['post_date'] );// Get episode date
		$track['meta']['length_formatted'] = powerpress_readable_duration($episode['enclosure']['duration']); // $episode['enclosure']['duration'];
		if( $track['meta']['length_formatted'] == '0:00' )
			$track['meta']['length_formatted'] = ''; // Make it empty
		if( !empty($links) )
			$track['meta']['link'] = get_permalink( $episode['ID'] ); // 'http://www.google.com/';
		
		if( $type == 'video' )
		{
			$track['dimensions'] = array(
					'original' => array('width'=>'100%', 'height'=>'100%' ),
					'resized' => array('width'=>'100%', 'height'=>'100%' )
						);
		}
		/*
		//$meta = wp_get_attachment_metadata( $attachment->ID );
		$meta = false;
		if ( ! empty( $meta ) ) {

			foreach ( wp_get_attachment_id3_keys( $attachment ) as $key => $label ) {
				if ( ! empty( $meta[ $key ] ) ) {
					$track['meta'][ $key ] = $meta[ $key ];
				}
			}

			if ( 'video' === $type ) {
				if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
					$width = $meta['width'];
					$height = $meta['height'];
					$theme_height = round( ( $height * $theme_width ) / $width );
				} else {
					$width = $default_width;
					$height = $default_height;
				}

				$track['dimensions'] = array(
					'original' => compact( 'width', 'height' ),
					'resized' => array(
						'width' => $theme_width,
						'height' => $theme_height
					)
				);
			}
		}
		*/
		
		if( !empty($episode_image) ) // !empty($image) )
		{
			$src = $episode_image;
			$width = 144;
			$height = 144;
			if( $type == 'video' ) // image onlyl used for video
				$track['image'] = array('src'=>$src, 'width'=>'100%', 'height'=>'100%');
			//$track['thumb'] = compact( 'src', 'width', 'height' );
			$track['poster'] = compact( 'src', 'width', 'height' );
		}

		$tracks[] = $track;
	}
	$data['tracks'] = $tracks;

	$safe_type = esc_attr( $type );
	$safe_style = esc_attr( $style );

	ob_start();

	if ( 1 === $instance ) {
		do_action( 'powerpress_playlist_scripts', $type, $style );
	} ?>
<div class="wp-playlist wp-<?php echo $safe_type ?>-playlist wp-playlist-<?php echo $safe_style ?>">
	<?php if ( 'audio' === $type ): ?>
	<div class="wp-playlist-current-item"></div>
	<?php endif ?>
	<<?php echo $safe_type ?> controls="controls" preload="none" width="<?php
		echo (int) $theme_width;
	?>"<?php if ( 'video' === $safe_type ):
		echo ' height="', (int) $theme_height, '"';
	endif; ?>></<?php echo $safe_type ?>>
	<div class="wp-playlist-next"></div>
	<div class="wp-playlist-prev"></div>
	<noscript>
	<ol><?php // TODO
	//foreach ( $attachments as $att_id => $attachment ) {
	//	printf( '<li>%s</li>', wp_get_attachment_link( $att_id ) );
	//}
	?></ol>
	</noscript>
	<script type="application/json" class="wp-playlist-script"><?php echo json_encode( $data ) ?></script>
</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'podcastlist', 'powerpress_playlist_shortcode' );
add_shortcode( 'podcastplaylist', 'powerpress_playlist_shortcode' );
add_shortcode( 'powerpressplaylist', 'powerpress_playlist_shortcode' );
add_shortcode( 'powerpress_playlist', 'powerpress_playlist_shortcode' );
