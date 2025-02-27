<?php
defined( 'ABSPATH' ) || exit;

/**
 * Inventory_Presser_KBB_Widget
 *
 * This class creates the Kelley Blue Book widget.
 */
class Inventory_Presser_KBB_Widget extends WP_Widget {


	const ID_BASE = '_invp_kbb';

	/**
	 * Creates an array of available images, but only contains one so far
	 *
	 * @return array
	 */
	protected function images() {
		return array(
			'default' => array(
				'text' => __( 'Bordered Rectangle', 'inventory-presser' ),
				'img'  => 'kelley-blue-book.jpg',
			),
		);
	}

	/**
	 * Calls the parent class' contructor and adds a hook that will delete the
	 * option that stores this widget's data when the plugin's delete all data
	 * method is run.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			self::ID_BASE,
			__( 'Kelley Blue Book Logo', 'inventory-presser' ),
			array(
				'description'           => __( 'KBB logo image linked to kbb.com', 'inventory-presser' ),
				'show_instance_in_rest' => true,
			)
		);

		add_action( 'invp_delete_all_data', array( $this, 'delete_option' ) );
	}

	/**
	 * Deletes the option that stores this widget's data.
	 *
	 * @return void
	 */
	public function delete_option() {
		delete_option( 'widget_' . self::ID_BASE );
	}

	/**
	 * Outputs the widget front-end HTML
	 *
	 * @param  array $args
	 * @param  array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$image_keys = array_keys( $this->images() );
		$image      = ( isset( $instance['image'] ) && in_array( $instance['image'], $image_keys, true ) ) ? $instance['image'] : $image_keys[0];

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		// before and after widget arguments are defined by themes.
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( isset( $instance['before_image'] ) ) {
			echo wpautop( $instance['before_image'] );
		}
		printf(
			'<a href="https://kbb.com" target="_blank"><img src="%s"></a>',
			esc_url( plugins_url( '/images/' . $this->images()[ $image ]['img'], INVP_PLUGIN_FILE_PATH ) )
		);
		if ( isset( $instance['after_image'] ) ) {
			echo wpautop( $instance['after_image'] );
		}

		echo $args['after_widget'];
	}

	/**
	 * Outputs the widget settings form that is shown in the dashboard.
	 *
	 * @param  array $instance
	 * @return void
	 */
	public function form( $instance ) {

		$image_keys = array_keys( $this->images() );

		$title        = isset( $instance['title'] ) ? $instance['title'] : '';
		$before_image = isset( $instance['before_image'] ) ? $instance['before_image'] : '';
		$image        = isset( $instance['image'] ) ? $instance['image'] : $image_keys[0];
		$after_image  = isset( $instance['after_image'] ) ? $instance['after_image'] : '';

		// Widget admin form.
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'inventory-presser' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'before_image' ) ); ?>"><?php esc_html_e( 'Text before image:', 'inventory-presser' ); ?></label>
		<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'before_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'before_image' ) ); ?>"><?php echo esc_attr( $before_image ); ?></textarea>
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Image:', 'inventory-presser' ); ?></label>

		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>">
		<?php
		foreach ( $this->images() as $key => $imginfo ) {
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $key ),
				selected( $key === $image, true, false ),
				esc_html( $imginfo['text'] )
			);
		}
		?>
		</select>

		</p>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'after_image' ) ); ?>"><?php esc_html_e( 'Text after image:', 'inventory-presser' ); ?></label>
		<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'after_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'after_image' ) ); ?>"><?php echo esc_attr( $after_image ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Saves the widget settings when a dashboard user clicks the Save button.
	 *
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array The updated array full of settings
	 */
	public function update( $new_instance, $old_instance ) {
		$image_keys               = array_keys( $this->images() );
		$instance                 = array();
		$instance['title']        = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['before_image'] = ( ! empty( $new_instance['before_image'] ) ) ? wp_strip_all_tags( $new_instance['before_image'] ) : '';
		$instance['image']        = ( ! empty( $new_instance['image'] ) ) ? wp_strip_all_tags( $new_instance['image'] ) : $image_keys[0];
		$instance['after_image']  = ( ! empty( $new_instance['after_image'] ) ) ? wp_strip_all_tags( $new_instance['after_image'] ) : '';
		return $instance;
	}
}
