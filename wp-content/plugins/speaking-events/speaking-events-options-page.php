<?php
// create custom plugin settings menu
add_action('admin_menu', 'readlist_create_menu');

function readlist_create_menu() {

	//create new top-level menu
	add_submenu_page( 'edit.php?post_type=reading-list', 'Reading List Settings', 'Settings', 'administrator', __FILE__, 'readlist_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'readlist-settings-group', 'readlist_orderby' );
	register_setting( 'readlist-settings-group', 'readlist_show_review' );
	register_setting( 'readlist-settings-group', 'readlist_color_code' );
}

function readlist_settings_page() {

?>
<div class="wrap">
<h2>Reading List Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'readlist-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Order Booklist By:</th>
        <td>
        	<select name="readlist_orderby">
        		<option value="status" <?php if(get_option('readlist_orderby') == "status"){ print "selected"; } ?>>Status (default)</option>
        		<option value="title" <?php if(get_option('readlist_orderby') == "title"){ print "selected"; } ?>>Title</option>
        		<option value="author" <?php if(get_option('readlist_orderby') == "author"){ print "selected"; } ?>>Author</option>
        		<option value="rating" <?php if(get_option('readlist_orderby') == "rating"){ print "selected"; } ?>>Rating</option>
        		<option value="priority" <?php if(get_option('readlist_orderby') == "priority"){ print "selected"; } ?>>Priority</option>
        	</select>
        </td>
        </tr>
        <tr valign="top">
        <th scope="row">Show Review Link on Reading List:</th>
        <td>
        	<input type="checkbox" name="readlist_show_review" <?php if(get_option('readlist_show_review') == true){ print "checked"; } ?>/>
        </td>
        </tr>
         <tr valign="top">
        <th scope="row">Color Code Books by Status:</th>
        <td>
        	<input type="checkbox" name="readlist_color_code" <?php if(get_option('readlist_color_code') == true){ print "checked"; } ?>/>
        </td>
        </tr>
        
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } ?>