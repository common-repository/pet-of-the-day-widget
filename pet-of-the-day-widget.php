<?php

/*
Plugin Name: Pet of the Day Widget
Plugin URI: https://github.com/ChrisHardie/pet-of-the-day-widget
Description: Display an Adopt-a-Pet.com pet of the day in a widget
Version: 1.0
Author: Chris Hardie
Author URI: https://chrishardie.com/
License: GPL2
*/

/*

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

defined( 'ABSPATH' ) || die( "Please don't try to run this file directly." );

class Pet_of_the_Day_Widget extends WP_Widget {
	/**
	 * Register the widget with WordPress
	 */
	public function __construct() {
		parent::__construct(
			'pet_of_the_day_widget', // base id
			__( 'Pet of the Day Widget', 'pet_of_the_day_widget_domain' ), // name
			array(
				'description' => __( 'A widget to display a pet of the day from Adopt-a-Pet.com.', 'pet_of_the_day_widget_domain' ),
			)
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

		if ( empty( $instance['title'] ) || empty( $instance['postal_code'] ) ) {
			return '';
		}

		// Output the widget content
		echo wp_kses_post( $args['before_widget'] );

		// Title
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'] );
		}

		// US or Canada postal code
		if ( ! empty( $instance['postal_code'] ) ) {
			$postal_code = esc_html( $instance['postal_code'] );
		}

		if ( preg_match( '/^[A-Za-z][0-9][A-Za-z] ?[0-9][A-Za-z][0-9]$/', $postal_code ) ) {
			$country_code = 'CA';
		} else {
			$country_code = 'US';
		}

		// Adapted from https://www.adoptapet.com/public/searchtools/pet-of-the-day
		// In future versions we could allow some customization to match the theme.
		echo '<div class="pet_of_the_day_widget_main" style="text-align: center;">
			<iframe width="199" height="361" frameborder="0" marginwidth="0" marginheight="0" 
				scrolling="0" allowtransparency="true"
				src="https://searchtools.adoptapet.com/public/searchtools/display/pet-of-the-day?postal_code='
			. esc_attr( $postal_code )
			. '&country_code='
			. esc_attr( $country_code )
			. '&background=tan&size=tall"></iframe>';

		echo '</div>';

		echo wp_kses_post( $args['after_widget'] );
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
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		if ( ! empty( $new_instance['postal_code'] ) ) {
			if ( preg_match( '/^[A-Za-z][0-9][A-Za-z] ?[0-9][A-Za-z][0-9]$/', $new_instance['postal_code'] )
			|| preg_match( '/^(\d{5})$/', $new_instance['postal_code'] ) ) {
				$instance['postal_code'] = $new_instance['postal_code'];
			} else {
				$instance['postal_code'] = '';
			}
		}

		return $instance;

	}

	/**
	 * Back-end form to manage a widget's options in wp-admin
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 * @return string
	 */
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'A Local Adoptable Pet', 'pet_of_the_day_widget_domain' );
		}
		$postal_code = isset( $instance['postal_code'] ) ? esc_html( $instance['postal_code'] ) : '';

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
			   value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'postal_code' ) ); ?>"><?php esc_attr_e( 'Postal Code:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'postal_code' ) ); ?>"
			   name="<?php echo esc_attr( $this->get_field_name( 'postal_code' ) ); ?>" type="text"
			   value="<?php echo esc_attr( $postal_code ); ?>">
		</p>
	<?php

	}

}

add_action('widgets_init', function() {
	register_widget( 'Pet_of_the_Day_Widget' );
} );

?>
