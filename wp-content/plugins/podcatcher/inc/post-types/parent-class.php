<?php
/**
 *  WP_Podcatcher creates a skeleton class
 *
 * @package wp_podcatcher
 */

/**
 * WPP_Sponsors creates a skeleton class
 *
 * @package wp_podcatcher
 **/
class WP_Podcatcher {

	/**
	 * Slug of our CPT
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Plural Slug of our CPT
	 *
	 * @var string
	 */
	public $plural_name;

	/**
	 * Dashicon id
	 *
	 * @var string
	 */
	public $icon = 'dashicons-';

	/**
	 * Our construct
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'fm_post_' . $this->name, array( $this, 'fm_setup' ) );
		add_action( 'admin_init', array( $this, 'register_tax' ), 15 );
	}


	/**
	 * Wrapper for register_post_type
	 */
	public function register_cpt() {

		$single_label = ucfirst( $this->name );
		$plural_label = ucfirst( $this->plural_name );

		/**
		 * Registers a new post type
		 *
		 * @uses $wp_post_types Inserts new post type object into the list
		 *
		 * @param string  Post type key, must not exceed 20 characters
		 * @param array|string  See optional args description above.
		 * @return object|WP_Error the registered post type object, or an error object
		 */
		register_post_type( $this->name, array(
			'labels'              => array(
				'name'                => __( $plural_label, 'wp-podcatcher' ),
				'singular_name'       => __( $single_label, 'wp-podcatcher' ),
				'add_new'             => __( 'Add a New ' . $single_label, 'wp-podcatcher' ),
				'add_new_item'        => __( 'Add a New ' . $single_label, 'wp-podcatcher' ),
				'edit_item'           => __( 'Edit ' . $single_label, 'wp-podcatcher' ),
				'new_item'            => __( 'New ' . $single_label, 'wp-podcatcher' ),
				'view_item'           => __( 'View ' . $single_label, 'wp-podcatcher' ),
				'search_items'        => __( 'Search ' . $plural_label, 'wp-podcatcher' ),
				'not_found'           => __( 'No '. $plural_label .' found', 'wp-podcatcher' ),
				'not_found_in_trash'  => __( 'No '. $plural_label .' found in Trash', 'wp-podcatcher' ),
				'parent_item_colon'   => __( 'Parent '. $single_label .':', 'wp-podcatcher' ),
				'menu_name'           => __( $plural_label, 'wp-podcatcher' ),
			),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => $this->icon,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', 'comments' ),
		) );

	}

	/**
	 * Define custom meta fields via Field Manager
	 */
	public function fm_setup() {
		// Slug for the time being.
		return;
	}

	/**
	 * Register a default custom Category
	 */
	public function register_tax() {

		$tax_name = $this->name . '-categories';
		$tax_label = ucfirst( $this->name );

		register_taxonomy( $tax_name, array( $this->name ), array(
			'labels' => array(
				'name'					=> __( $tax_label . ' Categories', 'wp-podcatcher' ),
				'singular_name'			=> __( $tax_label . ' Category', 'wp-podcatcher' ),
				'search_items'			=> __( 'Search ' . $tax_label . 's Categories', 'wp-podcatcher' ),
				'popular_items'			=> __( 'Popular ' . $tax_label . 's Categories', 'wp-podcatcher' ),
				'all_items'				=> __( 'All ' . $tax_label . 's Categories', 'wp-podcatcher' ),
				'parent_item'			=> __( 'Parent ' . $tax_label . ' Category', 'wp-podcatcher' ),
				'parent_item_colon'		=> __( 'Parent ' . $tax_label . ' Category', 'wp-podcatcher' ),
				'edit_item'				=> __( 'Edit ' . $tax_label . ' Category', 'wp-podcatcher' ),
				'update_item'			=> __( 'Update ' . $tax_label . ' Category', 'wp-podcatcher' ),
				'add_new_item'			=> __( 'Add New ' . $tax_label . ' Category', 'wp-podcatcher' ),
				'new_item_name'			=> __( 'New ' . $tax_label . ' Category Name', 'wp-podcatcher' ),
				'add_or_remove_items'	=> __( 'Add or remove ' . $tax_label . 's Categories', 'wp-podcatcher' ),
				'choose_from_most_used'	=> __( 'Choose from most used wp-podcatcher', 'wp-podcatcher' ),
				'menu_name'				=> __( $tax_label . ' Category', 'wp-podcatcher' ),
			),
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'show_tagcloud'     => false,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
		) );
	}
} // END class

//require_once( 'episodes.php' );
require_once( 'sponsors.php' );
require_once( 'powerpress-sponsors.php' );
