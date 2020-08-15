<?php
/**
 * Dynamic setting sections functions
 *
 * @author		Nir Goldberg
 * @package		includes/api
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * hmembership_add_dynamic_section_option
 *
 * Hook after dynamic section settings option has been added
 *
 * @since		1.0.0
 * @param		$option (string)
 * @param		$value (mixed)
 * @return		N/A
 */
function hmembership_add_dynamic_section_option( $option, $value ) {

	hmembership_dynamic_section_count( $option, $value );

}

/**
 * hmembership_update_dynamic_section_option
 *
 * Hook after dynamic section settings option has been updated
 *
 * @since		1.0.0
 * @param		$old_value (mixed)
 * @param		$value (mixed)
 * @param		$option (string)
 * @return		N/A
 */
function hmembership_update_dynamic_section_option( $old_value, $value, $option ) {

	hmembership_dynamic_section_count( $option, $value );

}

/**
 * hmembership_dynamic_section_count
 *
 * @since		1.0.0
 * @param		$option (string)
 * @param		$value (mixed)
 * @return		N/A
 */
function hmembership_dynamic_section_count( $option, $value ) {

	// get field args associated with this option
	$field = HTMLineMembership_Admin_Field::get_field( $option );

	if ( ! $field || ! isset( $field[ 'section' ] ) )
		return;

	// get the number of dynamic sections were updated with this settings field
	$count = is_array( $value ) ? count( $value ) : 0;

	// update dynamic section count
	update_option( 'hmembership_section_' . $field[ 'section' ], $count );

}