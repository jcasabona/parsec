<?php
/**
 * RSS2 Podcast Feed Template for displaying RSS2 Podcast Posts feed.
 *
 * @package WordPress
 */
 
	function powerpress_get_the_content_feed($feed_type, $no_filters = false)
	{
		if( $no_filters == false )
			return get_the_content_feed($feed_type);
	
		if ( post_password_required() ) {
			return __( 'There is no content because this is a protected post.' );
		}
		
		global $post;
		$content = $post->post_content;
		$content = wptexturize($content);
		$content = convert_smilies($content);
		$content = wpautop($content);
		$content = shortcode_unautop($content);
		$content = prepend_attachment($content);
		//$content = wp_make_content_images_responsive($content);
		$content = capital_P_dangit($content);
		$content = strip_shortcodes( $content );
		$content = str_replace(']]>', ']]&gt;', $content);
		
		if( function_exists('_oembed_filter_feed_content') )
			return wp_staticize_emoji( _oembed_filter_feed_content( $content ) );
		if( function_exists('wp_staticize_emoji') )
			return wp_staticize_emoji( $content );
		return $content;
	}
 
	function powerpress_get_the_excerpt_rss($no_filters = true)
	{
		if( $no_filters == false ) {
			$output = get_the_excerpt();
			return apply_filters( 'the_excerpt_rss', $output );
		}
		
		global $post;
		
		if ( post_password_required() ) {
			return __( 'There is no excerpt because this is a protected post.' );
		}
		$output = strip_tags($post->post_excerpt);
		if ( $output == '') {
			$output = strip_shortcodes( $post->post_content );
			$output = str_replace(']]>', ']]&gt;', $output);
			$output = strip_tags($output);
		}
		
		return convert_chars( ent2ncr( $output ) );
	}
	
	function powerpress_the_generator() {
		echo '<generator>https://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) . '</generator>';
	}
	
 
	$GeneralSettings = get_option('powerpress_general');
	$iTunesOrderNumber = 0;
	$FeaturedPodcastID = 0;
	
	if( !empty($GeneralSettings['episode_box_feature_in_itunes']) ) {
		$iTunesFeatured = get_option('powerpress_itunes_featured');
		$feed_slug = get_query_var('feed');
		if( !empty($iTunesFeatured[ $feed_slug ]) )
		{
			if( get_post_type() == 'post' )
			{
				$FeaturedPodcastID = $iTunesFeatured[ $feed_slug ];
				$GLOBALS['powerpress_feed']['itunes_feature'] = true; // So any custom order value is not used when looping through the feeds.
				$iTunesOrderNumber = 2; // One reserved for featured episode
			}
		}
 }

 
header('Content-Type: application/rss+xml; charset=' . get_option('blog_charset'), true);
$more = 1;


$FeedActionHook = '';
if( !empty($GeneralSettings['feed_action_hook']) )
	$FeedActionHook = '_powerpress';
	
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'."\n"; ?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'.$FeedActionHook); ?>
>
<channel>
	<title><?php if( version_compare($GLOBALS['wp_version'], 4.4, '<' ) ) { bloginfo_rss('name'); } wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php if( !empty($FeedActionHook) ) { echo '<generator>https://wordpress.org/?v=' . get_bloginfo_rss( 'version' ) . '</generator>'. PHP_EOL; } ?>
	<?php do_action('rss2_head'.$FeedActionHook); ?>
<?php
		
		$ItemCount = 0;
	?>
<?php while( have_posts()) :
		
		//if( empty($GeneralSettings['feed_accel']) )
		the_post();
		//else
		//	$GLOBALS['post'] = $GLOBALS['wp_query']->next_post(); // Use this rather than the_post() that way we do not add additional queries to the database
?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
<?php
		if( empty($GLOBALS['powerpress_feed']['feed_maximizer_on']) ) // If feed maximizer off
		{
			
			if( empty($GeneralSettings['feed_accel']) ) {
		?>
		<comments><?php comments_link_feed(); ?></comments>
		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link(null, 'rss2') ); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number(); ?></slash:comments>
<?php } // end powerpress feed comments  

	if( empty($GeneralSettings['feed_accel']) ) {
		the_category_rss('rss2');
	}
				if (get_option('rss_use_excerpt')) { ?>
		<description><?php echo powerpress_format_itunes_value( powerpress_get_the_excerpt_rss( !empty($GeneralSettings['feed_action_hook']) ), 'description' ); ?></description>
<?php } else { // else no rss_use_excerpt ?>
		<description><?php echo powerpress_format_itunes_value( powerpress_get_the_excerpt_rss( !empty($GeneralSettings['feed_action_hook']) ), 'description' ); ?></description>
<?php $content = powerpress_get_the_content_feed('rss2', !empty($GeneralSettings['feed_action_hook']) ); ?>
<?php if ( strlen( $content ) > 0 ) { ?>
		<content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
<?php } else { //  else strlen( $post->post_content ) <= 0 ?>
		<content:encoded><![CDATA[<?php echo powerpress_get_the_excerpt_rss( !empty($GeneralSettings['feed_action_hook']) ); ?>]]></content:encoded>
<?php 		} // end else strlen( $post->post_content ) <= 0 ?>
<?php } // end else no rss_use_excerpt ?>
		<?php
		}
		else // If feed maximizer on
		{
		?>
		<description><?php echo powerpress_format_itunes_value( powerpress_get_the_excerpt_rss( !empty($GeneralSettings['feed_action_hook']) ), 'description' ); ?></description>
		<?php
		}
		?>
<?php rss_enclosure(); ?>
	<?php do_action('rss2_item'.$FeedActionHook); ?>
	<?php
	if( $iTunesOrderNumber > 0 )
	{
		echo "\t<itunes:order>";
		if( $FeaturedPodcastID == get_the_ID() )
		{
			echo '1';
			$FeaturedPodcastID = 0;
		}
		else // Print of 2, 3, ...
		{
			echo $iTunesOrderNumber;
			$iTunesOrderNumber++;
		}
		echo "</itunes:order>\n";
	}
	
	// Decide based on count if we want to flip on the feed maximizer...
	$ItemCount++;
	
	if( empty($GLOBALS['powerpress_feed']['feed_maximizer_on']) && $ItemCount >= 10 && !empty($GLOBALS['powerpress_feed']['maximize_feed']) )
	{
		$GLOBALS['powerpress_feed']['feed_maximizer_on'] = true; // All future items will be minimized in order to maximize episode count
	}
	
	?>
	</item>
<?php endwhile; ?>
<?php 
	
	if( !empty($FeaturedPodcastID) )
	{
		query_posts( array('p'=>$FeaturedPodcastID) );
		if( have_posts())
		{
			if( empty($GeneralSettings['feed_accel']) )
				the_post();
			else
				$GLOBALS['post'] = $GLOBALS['wp_query']->next_post(); // Use this rather than the_post() that way we do not add additional queries to the database
	// Featured podcast epiosde, give it the highest itunes:order value...
?>
	<item>
		<title><?php the_title_rss() ?></title>
		<link><?php the_permalink_rss() ?></link>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
		<guid isPermaLink="false"><?php the_guid(); ?></guid>
		<description><?php echo powerpress_format_itunes_value( powerpress_get_the_excerpt_rss( !empty($GeneralSettings['feed_accel']) ), 'description' ); ?></description>
<?php rss_enclosure(); ?>
	<?php do_action('rss2_item'.$FeedActionHook); ?>
	<?php
	echo "\t<itunes:order>";
	echo 1;
	echo "</itunes:order>\n";
	?>
	</item>
<?php 
		}
		wp_reset_query();
	}
?>
</channel>
</rss>
<?php

if( defined('POWERPRESS_DEBUG_QUERIES') ) {
	if( !empty($wpdb->queries) ) {
		echo "<!--\n";
		echo "SQL Queries for this feed:\n";
		print_r($wpdb->queries);
		echo "\n-->";
	}
}
?>
<?php exit; // exit feed to prevent possible notices ?>