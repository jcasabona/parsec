<?php
	// settings_tab_destinations.php
	$cat_ID = '';
	if( !empty($FeedAttribs['category_id']) )
		$cat_ID = $FeedAttribs['category_id'];
	if( empty($FeedAttribs['type']) )
		$FeedAttribs['type'] = '';
	
	$feed_url = '';
	switch( $FeedAttribs['type'] )
	{
		case 'ttid': {
			$feed_url = get_term_feed_link($FeedAttribs['term_taxonomy_id'], $FeedAttribs['taxonomy_type'], 'rss2' );
		}; break;
		case 'category': {
			if( !empty($General['cat_casting_podcast_feeds']) )
				$feed_url = get_category_feed_link($cat_ID, 'podcast');
			else
				$feed_url = get_category_feed_link($cat_ID);
		}; break;
		case 'channel': {
			$feed_url = get_feed_link($FeedAttribs['feed_slug']);
		}; break;
		case 'post_type': {
			$feed_url = get_post_type_archive_feed_link($FeedAttribs['post_type'], $FeedAttribs['feed_slug']);
		}; break;
		case 'general':
		default: {
			$feed_url = get_feed_link('podcast');
		}
	}
	
	if( empty($FeedSettings['itunes_url']) )
		$FeedSettings['itunes_url'] = '';
	if( empty($FeedSettings['googleplay_url']) )
		$FeedSettings['googleplay_url'] = '';
	if( empty($FeedSettings['blubrry_url']) )
		$FeedSettings['blubrry_url'] = '';
	if( empty($FeedSettings['stitcher_url']) )
		$FeedSettings['stitcher_url'] = '';
	if( empty($FeedSettings['tunein_url']) )
		$FeedSettings['tunein_url'] = '';	
?>



<h2><?php echo __('Destinations', 'powerpress'); ?></h2>
<p><?php echo __('Podcast directories and applications to syndicate your podcast.', 'powerpress'); ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row">&nbsp;</th> 
<td>
<p><?php echo __('For your reference, your podcast feed URL is...', 'powerpress'); ?></p>
<input type="text" style="width: 80%;" name="NULL[feed_url]" value="<?php echo esc_attr($feed_url); ?>" maxlength="1024" onclick="javascript: this.select();" onfocus="javascript: this.select();" />
</td>
</tr>
</table>
<br />

<h3><?php echo __('Podcast Directories', 'powerpress'); ?></h3>
<p><?php echo __('Listing URLs are used by player subscribe links, subscribe sidebar widgets and subscribe to podcast page shortcodes.', 'powerpress'); ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('iTunes', 'powerpress'); ?></th> 
<td>
	<p><strong><a href="http://create.blubrry.com/manual/podcast-promotion/submit-podcast-to-itunes/?podcast-feed=<?php echo urlencode($feed_url); ?>" target="_blank"><?php echo __('Submit podcast to iTunes', 'powerpress'); ?></a></strong></p>
	<label for="itunes_url" style="font-size: 120%; display: block; font-weight: bold;"><?php echo __('iTunes Subscription URL', 'powerpress'); ?></label>
	<input type="text" style="width: 80%;" id="itunes_url" name="Feed[itunes_url]" value="<?php echo esc_attr($FeedSettings['itunes_url']); ?>" maxlength="255" />
	<p class="description"><?php echo sprintf(__('e.g. %s', 'powerpress'), 'http://itunes.apple.com/podcast/title-of-podcast/id<strong>000000000</strong>'); ?></p>
	<p><?php echo __('iTunes will email your Subscription URL to your <em>iTunes Email</em> when your podcast is accepted into the iTunes Directory.', 'powerpress'); ?></p>
</td>
</tr>
</table>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Google Play Music', 'powerpress'); ?></th>
<td>
	<p><strong><a href="http://create.blubrry.com/manual/podcast-promotion/publish-podcast-google-play-music-podcast-portal/?podcast-feed=<?php echo urlencode($feed_url); ?>" target="_blank"><?php echo  __('Submit podcast to Google Play Music', 'powerpress'); ?></a></strong></p>
	<label for="googleplay_url" style="font-size: 120%; display: block; font-weight: bold;"><?php echo __('Google Play Music Listing URL', 'powerpress'); ?></label>
	<input type="text" class="bpp-input-normal" id="googleplay_url" name="Feed[googleplay_url]" value="<?php echo esc_attr($FeedSettings['googleplay_url']); ?>" maxlength="255" />
</td>
</tr>
</table>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Blubrry Podcast Directory', 'powerpress'); ?></th>
<td>
	<p><strong><a href="https://www.blubrry.com/addpodcast.php?feed=<?php echo urlencode($feed_url); ?>" target="_blank"><?php echo  __('Submit podcast to Blubrry Podcast Directory', 'powerpress'); ?></a></strong></p>
	<p>
		<?php echo __('The largest podcast directory in the World!', 'powerpress'); ?>
	</p><p>
		<?php echo sprintf(__('Once listed, %s to expand your podcast distribution to Blubrry\'s SmartTV Apps (e.g. Roku) and apply to be on Spotify.', 'powerpress'), '<a href="http://create.blubrry.com/resources/blubrry-podcast-directory/get-featured-on-blubrry/" target="_blank">'. __('Get Featured', 'powerpress').'</a>' ); ?>
	</p>
	<label for="blubrry_url" style="font-size: 120%; display: block; font-weight: bold;"><?php echo __('Blubrry Listing URL', 'powerpress'); ?></label>
	<input type="text" class="bpp-input-normal" id="blubrry_url" name="Feed[blubrry_url]" value="<?php echo esc_attr($FeedSettings['blubrry_url']); ?>" maxlength="255" />
	<p class="description"><?php echo sprintf(__('e.g. %s', 'powerpress'), 'https://www.blubrry.com/title_of_podcast/'); ?></p>
</td>
</tr>
</table>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('Stitcher Podcast Radio', 'powerpress'); ?></th>
<td>
	<p><strong><a href="http://create.blubrry.com/manual/podcast-promotion/publish-podcast-stitcher/?podcast-feed=<?php echo urlencode($feed_url); ?>" target="_blank"><?php echo  __('Submit podcast to Stitcher', 'powerpress'); ?></a></strong></p>
	<label for="stitcher_url" style="font-size: 120%; display: block; font-weight: bold;"><?php echo __('Stitcher Listing URL', 'powerpress'); ?></label>
	<input type="text" class="bpp-input-normal" id="stitcher_url" name="Feed[stitcher_url]" value="<?php echo esc_attr($FeedSettings['stitcher_url']); ?>" maxlength="255" />
	<p class="description"><?php echo sprintf(__('e.g. %s', 'powerpress'), 'http://www.stitcher.com/podcast/your/listing-url/'); ?></p>
</td>
</tr>
</table>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php echo __('TuneIn', 'powerpress'); ?></th>
<td>
	<p><strong><a href="http://create.blubrry.com/manual/podcast-promotion/publish-podcast-tunein/?podcast-feed=<?php echo urlencode($feed_url); ?>" target="_blank"><?php echo  __('Submit podcast to TuneIn', 'powerpress'); ?></a></strong></p>
	<label for="tunein_url" style="font-size: 120%; display: block; font-weight: bold;"><?php echo __('TuneIn Listing URL', 'powerpress'); ?></label>
	<input type="text" class="bpp-input-normal" id="tunein_url" name="Feed[tunein_url]" value="<?php echo esc_attr($FeedSettings['tunein_url']); ?>" maxlength="255" />
	<p class="description"><?php echo sprintf(__('e.g. %s', 'powerpress'), 'http://tunein.com/radio/your-podcast-p000000/'); ?></p>
	
</td>
</tr>
</table>

<br />




<h3 style="margin-bottom: 15px;"><?php echo __('Want your own iOS and Android podcast apps?', 'powerpress'); ?></h3>
<p style="margin: 0  0 0 220px; font-size: 120%;">
	<?php echo __('Blubrry has partnered with Reactor by AppPresser to provide iOS and Android apps for PowerPress powered podcasts. With Reactor, you are able to build, design and retain control of your app to highlight your podcast content, and provide access to value-add content from your website.', 'powerpress'); ?>
</p>
<p style="margin: 15px 0 0 220px; font-size: 120%;">
	<strong><?php echo '<a href="http://create.blubrry.com/resources/partners/reactor-ios-android-podcast-apps-powerpress/" target="_blank">'. __('Learn More about Reactor iOS and Android podcast apps for PowerPress', 'powerpress') .'</a>'; ?></strong>
</p>
<br />
