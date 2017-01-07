// powerpress-subscribe-widget.js

function powerpress_subscribe_widget_change(event) {
	switch( jQuery(event).val() )
	{
		case 'channel': {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').show();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').hide();
		}; break;
		case 'post_type': {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').show();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').show();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').hide();
		}; break;
		case 'category': {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').show();
		}; break;
		default: {
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-post_type').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-channel').hide();
			jQuery(event).closest('.widget-content').find('.pp-sub-widget-p-category').hide();
		}; break;
	}
}