<?php
class CSE_Next_Event_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			 	'cse_next_event',
				'Upcoming Event', 
				array( 'description' => __( 'Displays the next Event'))
			);
		}

	public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
		
			return $instance;
		}
		
		public function form( $instance ) {
			$title = (isset( $instance[ 'title' ])) ? $instance[ 'title' ] : 'Next Speaking Event';
		?>
			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
		<?php 
		}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
			print cse_print_events(1);
		echo $after_widget; 
	}
}


function cse_register_widgets() {
	register_widget( 'CSE_Next_Event_Widget' );
}

add_action( 'widgets_init', 'CSE_register_widgets' );

?>
