<?php
/**
 * Parsec functions and definitions
 *
 * @package Parsec
 */


/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'parsec_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function parsec_setup() {

	load_theme_textdomain( 'parsec', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'title-tag' );


	add_theme_support( 'post-thumbnails' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'parsec' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'parsec_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	/**
	* Global Variables
	*/

	global $p_svg_path;
	$p_svg_path = esc_url( get_stylesheet_directory_uri() .'/assets/icons.svg' );
}
endif; // parsec_setup
add_action( 'after_setup_theme', 'parsec_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function parsec_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'parsec' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	//quick and dirty thing to make the title a hyperlink.
	register_sidebar( array(
		'name'          => __( 'Event Info Widgets', 'parsec' ),
		'id'            => 'event-info',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Widgets', 'parsec' ),
		'id'            => 'footer-widgets',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'parsec_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function parsec_scripts() {
	if ( ! is_admin() ) {
      wp_deregister_script( 'jquery' );
      wp_register_script( 'jquery', '/wp-includes/js/jquery/jquery.js', false, '1.11.0', true );
      wp_enqueue_script( 'jquery' );
	}

	wp_enqueue_style( 'google-web-fonts', '//fonts.googleapis.com/css?family=Source+Sans+Pro:200,400,700|Pathway+Gothic+One' );
	wp_enqueue_style( 'parsec-style', get_stylesheet_uri(), array( 'google-web-fonts' ) );

	wp_enqueue_script( 'parsec-navigation', get_template_directory_uri() . '/js/navigation.js', array( 'jquery' ), '20120206', true );

	wp_enqueue_script( 'parsec-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( WP_DEBUG ) {
		wp_enqueue_script( 'picturefill', get_template_directory_uri() . '/js/picturefill.js', false, '20150912', true );
	} else {
		wp_enqueue_script( 'picturefillmin', get_template_directory_uri() . '/js/picturefill.min.js', false,  '20150912', true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'parsec_scripts' );


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/parsec.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/theme-options.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
