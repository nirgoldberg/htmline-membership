<?php
/**
 * Admin settings radio/checkbox field
 *
 * @author		Nir Goldberg
 * @package		includes/admin/fields
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMLineMembership_Admin_Radio_Field extends HTMLineMembership_Admin_Field {

	/**
	 * display_field
	 *
	 * This function will display a radio/checkbox field
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function display_field() {

		// vars
		$index		= $this->field[ 'index' ];
		$id			= $this->field[ 'uid' ] . ( $index ? '_' . $index : '' );
		$name		= $this->field[ 'uid' ] . ( $index ? '[' . ($index-1) . ']' : '' );
		$options	= $this->field[ 'options' ];
		$values		= get_option( $this->field[ 'uid' ] );
		$value		= $index ? $values[ $index-1 ] : $values;
		$default	= isset( $this->field[ 'default' ] ) ? $this->field[ 'default' ] : false;
		$value		= $value ? $value : $default;

		if ( ! empty ( $options ) && is_array( $options ) ) {

			// vars
			$options_markup	= '';
			$iterator		= 0;

			// use a hidden input in order do get an indication for empty selection
			$options_markup .= '<input type="hidden" name="' . $name . '[]" value="" />';

			foreach ( $options as $key => $label ) {

				$iterator++;
				$options_markup .=	sprintf( '<label for="%1$s_%7$s"><input id="%1$s_%7$s" name="%2$s[]" type="%3$s" value="%4$s" %5$s /> %6$s</label><br/>',
					$id,
					$name,
					$this->field[ 'type' ],
					$key,
					( $value !== false && is_array( $value ) ) ? checked( $value[ array_search( $key, $value, true ) ], $key, false ) : '',
					$label,
					$iterator
				);

			}

			printf( '<fieldset>%s</fieldset>', $options_markup );

		}

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

		// sanitize radio/checkbox
		// assume select value as string
		if ( ! empty( $value ) ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $key => $val ) {

					if ( is_array( $val ) ) {

						// dynamic section setting
						foreach ( $val as $k => $v) {
							$output[ $key ][ $k ] = filter_var( $v, FILTER_SANITIZE_STRING );
						}

					} else {

						$output[ $key ] = filter_var( $val, FILTER_SANITIZE_STRING );

					}

				}
			}
		}

		// return
		return apply_filters( $this->field[ 'type' ] . '/sanitize', $output, $value );

	}

}