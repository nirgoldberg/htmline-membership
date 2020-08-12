<?php
/**
 * Dynamic settings section functions
 *
 * @author		Nir Goldberg
 * @package		includes/api
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * hmembership_dynamic_section_count
 *
 * @since		1.0.0
 * @param		N/A
 * @return		N/A
 */
function hmembership_dynamic_section_count() {

	/**
	 * Variables
	 */
	$options_group_id	= $_REQUEST[ 'options_group_id' ];
	$nonce				= $_REQUEST[ 'nonce' ];
	$option				= $_REQUEST[ 'option' ];
	$count				= $_REQUEST[ 'count' ];

	// verify nonce
	if ( ! $options_group_id || ! $nonce || ! $option || ! $count || ! wp_verify_nonce( $nonce, $options_group_id . '-options' ) )
		exit;

	// update section count
	update_option( $option, $count );

	// die
	die();

}
add_action( 'wp_ajax_dynamic_section_count', 'hmembership_dynamic_section_count' );