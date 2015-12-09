<?php
add_action( 'admin_menu', 'parsec_create_menu' );
function parsec_create_menu() {

	//create new top-level menu
	add_menu_page( __( 'Personal Info', 'parsec' ), __( 'Personal Info', 'parsec' ), 'administrator', 'parsec-theme-settings', 'parsec_settings_page', 'dashicons-megaphone' );
}

/**
* Step 2: Create settings fields.
*/
add_action( 'admin_init', 'parsec_register_settings' );
function parsec_register_settings() {
	register_setting( 'wedding-info', 'wedding_date' );
  register_setting( 'wedding-info', 'wedding_hashtag' );
  register_setting( 'wedding-info', 'wedding_time' );
  register_setting( 'wedding-info', 'wedding_reception_time' );
  register_setting( 'wedding-info', 'wedding_local' );
  register_setting( 'wedding-info', 'wedding_reception_local' );
  register_setting( 'wedding-info', 'wedding_local_address' );
  register_setting( 'wedding-info', 'wedding_reception_local_address' );
  register_setting( 'wedding-info', 'wedding_story_page' );
  register_setting( 'wedding-info', 'wedding_rsvp_page' );
  register_setting( 'wedding-info', 'wedding_gifts_page' );
}

/**
* Step 3: Create the markup for the options page
*/
function parsec_settings_page() {

  $wedding_options = array( 'Date' => 'wedding_date',
			'Time' => 'wedding_time',
			'Reception Time' => 'wedding_reception_time',
			'Ceremony Location' => 'wedding_local',
			'Ceremony Address' => 'wedding_local_address',
			'Reception Location' => 'wedding_reception_local',
			'Reception Address' => 'wedding_reception_local_address',
			'Hashtag' => 'wedding_hashtag',
			'Our Story Page' => 'wedding_story_page',
			'RSVP Page' => 'wedding_rsvp_page',
			'Gfits Page' => 'wedding_gifts_page',
		);

?>

<div class="wrap">
<h2><?php _e( 'Personal Info', 'parsec' ); ?></h2>

<form method="post" action="options.php">

	<?php if ( isset( $_GET['settings-updated'] ) ) { ?>
	<div class="updated">
        <p><?php _e( 'Info updated successfully', $textdomain ); ?></p>
    </div>
	<?php } ?>

    <table class="form-table">
    <?php foreach ( $wedding_options as $label => $option ) : ?>
  		<tr valign="top">
  			<th scope="row">
          <?php _e( $label, 'parsec' ); ?>
        </th>
  			<td>
  				<input type="text" name="<?php echo $option; ?>" value="<?php echo get_option( $option ); ?>" />
  			</td>
  		</tr>
    <?php endforeach; ?>

		<?php settings_fields( 'wedding-info' ); ?>
		<?php do_settings_sections( 'wedding-info' ); ?>
    </table>

    <?php submit_button(); ?>
</form>
</div>
<?php
  }
