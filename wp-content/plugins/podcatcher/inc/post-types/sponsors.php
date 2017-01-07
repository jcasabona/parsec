<?php
/**
 *  WPP_Sponsors registers the episode post type
 *
 * @package wp_podcatcher
 */

/**
 * WPP_Sponsors registers the episode post type
 *
 * @package wp_podcatcher
 **/
class WPP_Sponsors extends WP_Podcatcher {

	/**
	 * Our construct
	 */
	public function __construct() {
		$this->name = 'sponsor';
		$this->plural_name = 'sponsors';
		$this->icon .= 'id';
		parent::__construct();
	}

	/**
	 * Define custom meta fields via Field Manager
	 */
	public function fm_setup() {
		$fm = new Fieldmanager_Link( array(
			'name' => 'wpp_sponsor_link',
		) );
		$fm->add_meta_box( 'Sponsor Link', array( $this->name ) );
	}
} // END class

new WPP_Sponsors();

/** Todo list...

 @TODO: Re-evaluate checkbox for "Current"

 'wpp_current' => new Fieldmanager_Checkbox( array(
	 'name' => 'sponsor_current',
	 'label' => 'Checkbox Label',
 ) ),
*/
