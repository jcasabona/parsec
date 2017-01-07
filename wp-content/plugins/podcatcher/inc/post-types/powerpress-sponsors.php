<?php
/**
 *  WPP_PowerPress_Sponsors registers the metabox for associating sponsors with posts
 *
 * @package wp_podcatcher
 */
class WPP_PowerPress_Sponsors {

	/**
	 * Our construct
	 */
	public function __construct() {
		 add_action( 'fm_post', array( $this, 'fm_setup' ) );
	}

	/**
	 * Define custom meta fields via Field Manager
	 */
	public function fm_setup() {

		$fm = new Fieldmanager_Autocomplete( array(
			'name' => 'wpp_episode_sponsor',
			'limit'          => 0,
			'add_more_label' => 'Add another Sponsor',
			'sortable'       => true,
			'show_edit_link' => true,
			'datasource' => new Fieldmanager_Datasource_Post( array(
				'query_args' => array( 'post_type' => 'sponsor', 'limit' => 2 ),
			) ),
		) );

		$fm->add_meta_box( 'Episode/Post Sponsor', 'post' );
	}
} // END class

new WPP_PowerPress_Sponsors();
