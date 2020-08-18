<?php
/**
 * Admin settings select/multiselect field
 *
 * @author		Nir Goldberg
 * @package		includes/admin/fields
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMLineMembership_Admin_Select_Field extends HTMLineMembership_Admin_Field {

	/**
	 * display_field
	 *
	 * This function will display a select/multiselect field
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
			$attributes				= '';
			$options_markup			= '';
			$hidden_option_markup	= '';

			foreach ( $options as $key => $label ) {
				$options_markup .=	sprintf( '<option value="%s" %s>%s</option>',
										$key,
										( $value !== false && is_array( $value ) ) ? selected( $value[ array_search( $key, $value, true ) ], $key, false ) : '',
										$label
									);
			}

			if ( 'multiselect' === $this->field[ 'type' ] ) {
				$attributes = ' multiple="multiple" ';

				// use a hidden input in order do get an indication for empty selection
				$hidden_option_markup .= '<input type="hidden" name="' . $name . '[]" value="" />';
			}

			printf( '%5$s<select name="%2$s[]" id="%1$s" %3$s>%4$s</select>',
				$id,
				$name,
				$attributes,
				$options_markup,
				$hidden_option_markup
			);

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

		// sanitize select/multiselect
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
		return apply_filters( 'hmembership_field_' . $this->field[ 'type' ] . '/sanitize', $output, $value );

	}

}