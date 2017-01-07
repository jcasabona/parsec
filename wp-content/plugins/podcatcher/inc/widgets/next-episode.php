<?php
/**
 * Show the upcoming episode widget.
 *
 * @package wp_podcatcher
 */

/**
 *  WPP_Upcoming_Episode gets the next scheduled episode.
 *
 * @package wp_podcatcher
 */
class WPP_Upcoming_Episode extends WP_Widget {

	/**
	 * Sets up the widgets name and description
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'wpp_upcoming_episode',
			'description' => esc_html__( 'Display the next scheduled episode', 'wp-podcatcher' ),
		);
		parent::__construct( 'wpp_upcoming_episode', 'Next Episode', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args widget options.
	 * @param array $instance current widget instance.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		$title = ! empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : __( 'Next Episode', 'wp-podcatcher' );

		$format = '%1$s%2$s%3$s';

		printf( $format,
			$args['before_title'],
			esc_attr( $title ),
			$args['after_title']
		);

		wpp_print_upcoming_episodes();

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {
		// Set defaults.
		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = 'Current Sponsors';
		}

		$title = $instance['title'];
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-podcatcher' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
		// Processes widget options to be saved.
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'WPP_Upcoming_Episode' );
});
