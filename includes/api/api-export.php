<?php
/**
 * Export functions
 *
 * @author		Nir Goldberg
 * @package		includes/api
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * hmembership_export_users
 *
 * @since		1.0.0
 * @param		N/A
 * @return		N/A
 */
function hmembership_export_users() {

	// verify nonce
	if ( ! wp_verify_nonce( $_REQUEST[ 'nonce' ], 'hmembership_export_users_nonce' ) ) {
		exit();
	}

	// export users
	new HTMLineMembership_Export();

	// die
	die();

}
add_action( 'wp_ajax_hmembership_export_users', 'hmembership_export_users' );