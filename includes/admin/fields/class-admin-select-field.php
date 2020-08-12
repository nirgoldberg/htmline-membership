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
		$id			= $this->field[ 'uid' ] . ( $this->dynamic ? '_' . $index : '' );
		$name		= $this->field[ 'uid' ] . ( $this->dynamic ? '[' . ($index-1) . ']' : '' );
		$options	= $this->field[ 'options' ];
		$values		= get_option( $this->field[ 'uid' ] );
		$value		= $this->dynamic ? $values[ $index-1 ] : $values;
		$default	= isset( $this->field[ 'default' ] ) ? $this->field[ 'default' ] : false;
		$value		= $value ? $value : $default;

		if ( ! empty ( $options ) && is_array( $options ) ) {

			// vars
			$attributes		= '';
			$options_markup	= '';

			foreach ( $options as $key => $label ) {
				$options_markup .=	sprintf( '<option value="%s" %s>%s</option>',
										$key,
										( $value !== false && is_array( $value ) ) ? selected( $value[ array_search( $key, $value, true ) ], $key, false ) : '',
										$label
									);
			}

			if ( 'multiselect' === $this->field[ 'type' ] ) {
				$attributes = ' multiple="multiple" ';
			}

			printf( '<select name="%2$s[]" id="%1$s" %3$s>%4$s</select>',
				$id,
				$name,
				$attributes,
				$options_markup
			);

		}

		$this->display_field_meta();

	}

}