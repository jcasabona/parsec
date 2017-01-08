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

	$wp_customize->add_section( 'parsec_settings' , array(
		'title'    =>  'Parsec Settings',
		'priority' => 10,
	) );

	$wp_customize->add_setting( 'parsec_custom_logo' );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'parsec_custom_logo', array(
		'label'    => __( 'Logo', 'parsec' ),
		'section'  => 'parsec_settings',
		'settings' => 'parsec_custom_logo',
		)
	));

	$wp_customize->add_setting( 'parsec_custom_header_bg' );

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'parsec_custom_header_bg', array(
		'label'    => __( 'Header Background', 'parsec' ),
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
		'label'    => __( 'Google Analytics UA Code', 'parsec' ),
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
		list($width, $height) = getimagesize( $logo );
		if ( $height > 90 ) {
			$ratio = $height / 90;
			$height = 90;
			$width = $width / $ratio;
		}

?>
		.site-title a {
			background: url('<?php echo esc_url( $logo ); ?>') top center no-repeat;
			background-size: <?php echo $width; ?>px <?php echo $height; ?>px;
			display: inline-block;
			height: <?php echo $height; ?>px;
			text-indent: -999999px;
			min-width: <?php echo $width; ?>px;
		}

<?php
	endif;

	if ( ! empty( $header_bg ) ) :
?>
		.site-header {
			background-image: url('<?php echo esc_url( $header_bg ); ?>');
			background-position: bottom;
			background-size: cover;
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
