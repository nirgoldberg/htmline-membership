<?php
/**
 * Admin settings textarea field
 *
 * @author		Nir Goldberg
 * @package		includes/admin/fields
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMLineMembership_Admin_Textarea_Field extends HTMLineMembership_Admin_Field {

	/**
	 * display_field
	 *
	 * This function will display a textarea field
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

		printf( '<textarea name="%2$s" id="%1$s" placeholder="%3$s" rows="5" cols="50">%4$s</textarea>',
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

		// sanitize textarea
		if ( is_array( $value ) ) {

			// dynamic section setting
			// vars
			$output = array();

			foreach ( $value as $key => $val ) {
				$output[ $key ] = sanitize_textarea_field( $val );
			}

		} elseif ( ! ( empty( $value ) && '0' !== $value ) ) {

			$output = sanitize_textarea_field( $value );

		}

		// return
		return apply_filters( 'hmembership_field_' . $this->field[ 'type' ] . '/sanitize', $output, $value );

	}

}