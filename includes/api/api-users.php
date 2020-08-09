<?php
/**
 * Users functions
 *
 * @author		Nir Goldberg
 * @package		includes/api
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * hmembership_get_users_list_table
 *
 * This function will return the HTMLineMembership_Users_List_Table object found in the hmembership_admin_users object
 *
 * @since		1.0.0
 * @param		$default (string)
 * @return		(mixed)
 */
function hmembership_get_users_list_table( $default = null ) {

	// vars
	$users_list_table = hmembership_admin_users()->get_users_list_table();

	// return
	return $users_list_table ? $users_list_table : $default;

}