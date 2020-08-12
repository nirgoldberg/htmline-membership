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
		$id		= $this->field[ 'uid' ] . ( $this->dynamic ? '_' . $index : '' );
		$name	= $this->field[ 'uid' ] . ( $this->dynamic ? '[]' : '' );
		$values	= get_option( $this->field[ 'uid' ] );
		$value	= $this->dynamic ? $values[ $index-1 ] : $values;

		printf( '<textarea name="%2$s" id="%1$s" placeholder="%3$s" rows="5" cols="50">%4$s</textarea>',
			$id,
			$name,
			$this->field[ 'placeholder' ],
			( $value !== false ) ? $value : ''
		);

		$this->display_field_meta();

	}

}