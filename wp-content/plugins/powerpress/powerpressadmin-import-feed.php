<?php



function powerpress_admin_import_feed()
{
	?>
<h2><?php echo __('Import Podcast', 'powerpress'); ?></h2>
<p><?php echo __('Import your podcast including episodes, media files and settings.', 'powerpress'); ?></p>

<ul><li>
	<h4><?php echo __('Import from specific service', 'powerpress'); ?></h4>
	<ul>
		<li><strong><a href="<?php echo admin_url("admin.php?import=powerpress-soundcloud-rss-podcast"); ?>"><?php echo __('Podcast from SoundCloud', 'powerpress'); ?></a></strong></li>
		<li><strong><a href="<?php echo admin_url("admin.php?import=powerpress-libsyn-rss-podcast"); ?>"><?php echo __('Podcast from LibSyn', 'powerpress'); ?></a></strong></li>
		<li><strong><a href="<?php echo admin_url("admin.php?import=powerpress-podbean-rss-podcast"); ?>"><?php echo __('Podcast from PodBean', 'powerpress'); ?></a></strong></li>
		<li><strong><a href="<?php echo admin_url("admin.php?import=powerpress-squarespace-rss-podcast"); ?>"><?php echo __('Podcast from Squarespace', 'powerpress'); ?></a></strong></li>
	</ul>
	<h4><?php echo __('Import from anywhere else', 'powerpress'); ?></h4>
	<ul>
		<li><strong><a href="<?php echo admin_url("admin.php?import=powerpress-rss-podcast"); ?>"><?php echo __('Podcast RSS Feed', 'powerpress'); ?></a></strong></li>
	</ul>
</li></ul>
<!--
<p><?php echo sprintf(__('Importing your feed does not migrate your media files. Please use the %s tool to migrate your media once your feed is imported.', 'powerpress'), '<strong><a href="'.admin_url('admin.php?page=powerpress/powerpressadmin_migrate.php') .'">'. __('Migrate Media', 'powerpress') .'</a></strong>'); ?></p> -->
<br />

<h2><?php echo __('Migrate Media to your Blubrry Podcast Media Hosting Account', 'powerpress'); ?></h2>
<dl>
	<dt><strong><a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_migrate.php"); ?>"><?php echo __('Migrate Media', 'powerpress'); ?></a></strong></dt>
	<dd><?php echo __('Migrate all of your media with only a few clicks.', 'powerpress'); ?></dd>
</dl>
<br />

<h2><?php echo __('Import settings from another plugin', 'powerpress'); ?></h2>
<dl>
	<dt><strong><a href="<?php echo admin_url() . wp_nonce_url("admin.php?page=powerpress/powerpressadmin_tools.php&amp;action=powerpress-podpress-settings", 'powerpress-podpress-settings'); ?>" 
			onclick="return confirm('<?php echo __('Import PodPress settings, are you sure?\n\nExisting PowerPress settings will be overwritten.', 'powerpress'); ?>');"><?php echo __('Import PodPress Settings', 'powerpress'); ?></a></strong></dt>
	<dd><?php echo __('Import settings from PodPress into PowerPress.', 'powerpress'); ?></dd>
	
	<dt><strong><a href="<?php echo admin_url() . wp_nonce_url("admin.php?page=powerpress/powerpressadmin_tools.php&amp;action=powerpress-podcasting-settings", 'powerpress-podcasting-settings'); ?>" 
			onclick="return confirm('<?php echo __('Import Podcasting plugin settings, are you sure?', 'powerpress') .'\n\n'. __('Existing PowerPress settings will be overwritten.', 'powerpress'); ?>');"><?php echo htmlspecialchars(__('Import TSG\'s Podcasting Plugin Settings', 'powerpress')); ?></a></strong></dt>
	<dd><?php echo esc_html(__('Import settings from the plugin "Podcasting Plugin by TSG" into PowerPress.', 'powerpress')); ?></dd>
</dl>
<br />

<h2><?php echo __('Import Episodes from another plugin', 'powerpress'); ?></h2>
<dl>
	<dt><strong><a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_tools.php&amp;action=powerpress-podpress-epiosdes"); ?>"><?php echo __('Import PodPress Episodes', 'powerpress'); ?></a></strong></dt>
	<dd><?php echo __('Import PodPress created episodes to PowerPress.', 'powerpress'); ?></dd>
	
	<dt><strong><?php echo __('Podcasting Plugin by TSG', 'powerpress'); ?></strong></dt>
	<dd><?php echo esc_html(__('Note: Episodes created using the plugin "Podcasting Plugin by TSG" do not require importing.', 'powerpress')); ?></dd>
</dl>
<br />

<h2><?php echo __('Import Episodes from previous feed import', 'powerpress'); ?></h2>
<dl>
	<dt><strong><a href="<?php echo admin_url("admin.php?page=powerpress/powerpressadmin_tools.php&amp;action=powerpress-mt-epiosdes"); ?>"><?php echo __('Import from other Blogging Platform', 'powerpress'); ?></a></strong> <?php echo __('(media linked in blog posts)', 'powerpress'); ?></dt>
	<dd><?php echo __('Import from podcast episodes from blogging platforms such as Movable Type/Blogger/Joomla/TypePad (and most other blogging systems) to PowerPress.', 'powerpress'); ?></dd>
</dl>
<br />
<?php
}

// eof