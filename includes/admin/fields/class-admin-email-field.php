<?php
/**
 * Admin settings email field
 *
 * @author		Nir Goldberg
 * @package		includes/admin/fields
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMLineMembership_Admin_Email_Field extends HTMLineMembership_Admin_Field {

	/**
	 * display_field
	 *
	 * This function will display an email field
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function display_field() {

		// vars
		$index	= $this->field[ 'index' ];
		$id		= $this->field[ 'uid' ] . ( $index ? '_' . $index : '' );
		$name	= $this->field[ 'uid' ] . ( $index ? '[]' : '' );
		$values	= get_option( $this->field[ 'uid' ] );
		$value	= $index ? $values[ $index-1 ] : $values;

		printf( '<input name="%2$s" id="%1$s" type="text" placeholder="%3$s" value="%4$s" />',
			$id,
			$name,
			$this->field[ 'placeholder' ],
			( $value !== false ) ? $value : ''
		);

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
				$output[ $key ] = filter_var( $val, FILTER_SANITIZE_EMAIL );
			}

		} elseif ( ! empty( $value ) ) {

			$output = filter_var( $value, FILTER_SANITIZE_EMAIL );

		}

		// return
		return apply_filters( $this->field[ 'type' ] . '/sanitize', $output, $value );

	}

}