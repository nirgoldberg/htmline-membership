<?php
/**
 * Admin settings editor field
 *
 * @author		Nir Goldberg
 * @package		includes/admin/fields
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMLineMembership_Admin_Editor_Field extends HTMLineMembership_Admin_Field {

	/**
	 * display_field
	 *
	 * This function will display an editor field
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function display_field() {

		// vars
		$id		= $this->field[ 'uid' ];
		$name	= $this->field[ 'uid' ];
		$value	= get_option( $this->field[ 'uid' ] );

		wp_editor( $value, $id, array(
			'textarea_name'	=> $name,
		) );

		$this->display_field_meta();

	}

	/**
	 * sanitize
	 *
	 * This function will sanitize the field value before saving to DB
	 *
	 * @since		1.0.0
	 * @param		$value (mixed)
	 * @return		(array)
	 */
	public function sanitize( $value ) {

		// vars
		$output = false;

		// sanitize email
		if ( is_array( $value ) ) {

			// dynamic section setting
			// vars
			$output = array();

			foreach ( $value as $key => $val ) {
				$output[ $key ] = wp_kses_post( $val );
			}

		} elseif ( ! empty( $value ) ) {

			$output = wp_kses_post( $value );

		}

		// return
		return apply_filters( 'hmembership_field_' . $this->field[ 'type' ] . '/sanitize', $output, $value );

	}

}