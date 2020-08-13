<?php
/**
 * Admin settings text/password/number field
 *
 * @author		Nir Goldberg
 * @package		includes/admin/fields
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMLineMembership_Admin_Text_Field extends HTMLineMembership_Admin_Field {

	/**
	 * display_field
	 *
	 * This function will display a text/password/number field
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

		printf( '<input name="%2$s" id="%1$s" type="%3$s" placeholder="%4$s" value="%5$s" />',
			$id,
			$name,
			$this->field[ 'type' ],
			$this->field[ 'placeholder' ],
			( $value !== false ) ? $value : ''
		);

		$this->display_field_meta();

	}

}