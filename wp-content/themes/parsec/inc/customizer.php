<?php
/**
 * Parsec Theme Customizer
 *
 * @package Parsec
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function parsec_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	$wp_customize->add_section( 'parsec_settings' , array(
		'title'    =>  'Parsec Settings',
		'priority' => 10,
	) );

	$wp_customize->add_setting( 'parsec_custom_logo' );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'parsec_custom_logo', array(
		'label'    => __( 'Logo', 'tep' ),
		'section'  => 'parsec_settings',
		'settings' => 'parsec_custom_logo',
		)
	));

	$wp_customize->add_setting( 'parsec_custom_header_bg' );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'parsec_custom_header_bg', array(
		'label'    => __( 'Header Background', 'tep' ),
		'section'  => 'parsec_settings',
		'settings' => 'parsec_custom_header_bg',
		)
	));

	$wp_customize->add_setting(
		'parsec_accent_color', array(
		'default' => '#10607E',
		'type' => 'option',
		'capability' => 'edit_theme_options',
	));

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'parsec_accent_color',
			array(
				'label' => 'Accent Color',
				'section' => 'parsec_settings',
				'settings' => 'parsec_accent_color',
			)
		)
	);

	$wp_customize->add_setting( 'parsec_ga_code' );

	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'parsec_ga_code', array(
		'label'    => __( 'Google Analytics UA Code', 'tep' ),
		'section'  => 'parsec_settings',
		'settings' => 'parsec_ga_code',
		)
	));
}
add_action( 'customize_register', 'parsec_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function parsec_customize_preview_js() {
	wp_enqueue_script( 'parsec_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'parsec_customize_preview_js' );

/**
 * Output customized CSS to head
 */
function parsec_customize_css() {
	$logo = get_theme_mod( 'parsec_custom_logo' );
	$header_bg = get_theme_mod( 'parsec_custom_header_bg' );
	$accent_color = get_option( 'parsec_accent_color' );
?>
	<style type="text/css">

<?php
	if ( ! empty( $logo ) ) :
?>
		.site-title a {
			background: url('<?php echo esc_url( $logo ); ?>') top center no-repeat;
			text-indent: -999999px;
		}

<?php
	endif;

	if ( ! empty( $header_bg ) ) :
?>
		.site-header {
			background-image: url('<?php echo esc_url( $header_bg ); ?>');
		}

<?php
	endif;

	if ( ! empty( $accent_color ) ) :
?>

<?php	endif; ?>

</style>

<?php
}

add_action( 'wp_head', 'parsec_customize_css' );
