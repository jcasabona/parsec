<?php
	// PowerPress Player administration
	
	
// Handle post processing here for the players page.
function powerpress_admin_players_init()
{
	wp_enqueue_style('wp-mediaelement');
	wp_enqueue_script( 'wp-mediaelement' );
	
	$Settings = false; // Important, never remove this
	$Step = 1;
	
	$action = (isset($_GET['action'])?$_GET['action']: (isset($_POST['action'])?$_POST['action']:false) );
	//$type = (isset($_GET['type'])?$_GET['type']: (isset($_POST['type'])?$_POST['type']:'audio') );
	
	if( !$action )
		return;
		
	switch($action)
	{
		case 'powerpress-select-player': {
			
			$SaveSettings = array();
			//$SaveSettings = $_POST['Player'];
			if( isset($_POST['Player']) )
				$SaveSettings = $_POST['Player'];
			if( isset($_POST['VideoPlayer']) )
				$SaveSettings += $_POST['VideoPlayer'];
			powerpress_save_settings($SaveSettings, 'powerpress_general');
			powerpress_page_message_add_notice( __('Player activated successfully.', 'powerpress') );
			
		}; break;
		case 'powerpress-audio-player': {
		
			$SaveSettings = $_POST['Player'];
			powerpress_save_settings($SaveSettings, 'powerpress_audio-player');
			powerpress_page_message_add_notice( __('Audio Player settings saved successfully.', 'powerpress') );
		
		}; break;
	}
}
