<?php

class Parsec_Projects extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		load_plugin_textdomain( 'parsec', false, basename( dirname( __FILE__ ) ) . '/languages' );

		parent::__construct(
			'parsec-recent-post-widget',
			'Parsec Recent Post',
			array( 'description' => __( 'Display the latest post in a widget', 'parsec' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		$title = ! empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : __( 'The Latest', 'dcp' );

		$format =  '%1$s%2$s%3$s';

		printf( $format,
			$args['before_title'],
			$title,
			$args['after_title']
		);

    parsec_latest_post();

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		// Set defaults
		if( ! isset( $instance['title'] ) ) { $instance['title'] = 'The Latest'; }

		$title = $instance[ 'title' ];
		?>
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:', 'dcp' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;

	}

}
