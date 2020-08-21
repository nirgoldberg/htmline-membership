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
 * hmembership_users_add_user_role
 *
 * Alias of hmembership_user()->add_user_role()
 *
 * @since		1.0.0
 * @param		N/A
 * @return		N/A
 */
function hmembership_users_add_user_role() {

	hmembership_user()->add_user_role();

}

/**
 * hmembership_users_remove_user_role
 *
 * Alias of hmembership_user()->remove_user_role()
 *
 * @since		1.0.0
 * @param		N/A
 * @return		N/A
 */
function hmembership_users_remove_user_role() {

	hmembership_user()->remove_user_role();

}

/**
 * hmembership_users_create_wp_users
 *
 * Alias of hmembership_user()->create_wp_users()
 *
 * @since		1.0.0
 * @param		$user_ids (array)
 * @return		(array) Associative array in the following format: user_id => wp_user_id
 */
function hmembership_users_create_wp_users( $user_ids ) {

	return hmembership_user()->create_wp_users( $user_ids );

}

/**
 * hmembership_users_create_db_table
 *
 * Alias of hmembership_user()->create_users_table()
 *
 * @since		1.0.0
 * @param		N/A
 * @return		N/A
 */
function hmembership_users_create_db_table() {

	hmembership_user()->create_users_table();

}

/**
 * hmembership_users_drop_db_table
 *
 * Alias of hmembership_user()->drop_users_table()
 *
 * @since		1.0.0
 * @param		N/A
 * @return		N/A
 */
function hmembership_users_drop_db_table() {

	hmembership_user()->drop_users_table();

}

/**
 * hmembership_users_insert_user
 *
 * Alias of hmembership_user()->insert_user()
 *
 * @since		1.0.0
 * @param		$user_email (string)
 * @param		$user_info (string)
 * @return		(mixed) Number of rows inserted, or false on error
 */
function hmembership_users_insert_user( $user_email, $user_info ) {

	// return
	return hmembership_user()->insert_user( $user_email, $user_info );

}

/**
 * hmembership_users_set_users_wp_user
 *
 * Alias of hmembership_user()->set_users_wp_user()
 *
 * @since		1.0.0
 * @param		$users (array) Associative array in the following format: user_id => wp_user_id
 * @return		(mixed) Number of users updated, or false on error
 */
function hmembership_users_set_users_wp_user( $users ) {

	// return
	return hmembership_user()->set_users_wp_user( $users );

}

/**
 * hmembership_users_unset_users_wp_user
 *
 * Alias of hmembership_user()->unset_users_wp_user()
 *
 * @since		1.0.0
 * @param		$user_ids (array)
 * @return		(mixed) Number of users updated, or false on error
 */
function hmembership_users_unset_users_wp_user( $user_ids ) {

	// return
	return hmembership_user()->unset_users_wp_user( $user_ids );

}

/**
 * hmembership_users_update_users_status
 *
 * Alias of hmembership_user()->update_users_status()
 *
 * @since		1.0.0
 * @param		$user_ids (array)
 * @param		$user_status (string)
 * @return		(mixed) Number of rows updated, or false on error
 */
function hmembership_users_update_users_status( $user_ids, $user_status ) {

	// return
	return hmembership_user()->update_users_status( $user_ids, $user_status );

}

/**
 * hmembership_users_delete_user
 *
 * Alias of hmembership_user()->delete_user()
 *
 * @since		1.0.0
 * @param		$user_id (int)
 * @return		(mixed) Number of rows deleted, or false on error
 */
function hmembership_users_delete_user( $user_id ) {

	// return
	return hmembership_user()->delete_user( $user_id );

}

/**
 * hmembership_users_delete_users
 *
 * Alias of hmembership_user()->delete_users()
 *
 * @since		1.0.0
 * @param		$user_ids (array)
 * @return		(mixed) Number of rows deleted, or false on error
 */
function hmembership_users_delete_users( $user_ids ) {

	// return
	return hmembership_user()->delete_users( $user_ids );

}

/**
 * hmembership_users_get_users_by_id
 *
 * Alias of hmembership_user()->get_users_by_id()
 *
 * @since		1.0.0
 * @param		user_ids (array)
 * @return		(mixed)
 */
function hmembership_users_get_users_by_id( $user_ids ) {

	// return
	return hmembership_user()->get_users_by_id( $user_ids );

}

/**
 * hmembership_users_get_user_by_wp_user_id
 *
 * Alias of hmembership_user()->get_user_by_email()
 *
 * @since		1.0.0
 * @param		$wp_user_id (int)
 * @return		(mixed)
 */
function hmembership_users_get_user_by_wp_user_id( $wp_user_id ) {

	// return
	return hmembership_user()->get_user_by_wp_user_id( $wp_user_id );

}

/**
 * hmembership_users_get_user_by_email
 *
 * Alias of hmembership_user()->get_user_by_email()
 *
 * @since		1.0.0
 * @param		$user_email
 * @return		(mixed)
 */
function hmembership_users_get_user_by_email( $user_email ) {

	// return
	return hmembership_user()->get_user_by_email( $user_email );

}

/**
 * hmembership_users_get_list_table
 *
 * This function will return the HTMLineMembership_Users_List_Table object found in the hmembership_admin_users object
 *
 * @since		1.0.0
 * @param		$default (string)
 * @return		(mixed)
 */
function hmembership_users_get_list_table( $default = null ) {

	// vars
	$users_list_table = hmembership_admin_users()->get_users_list_table();

	// return
	return $users_list_table ? $users_list_table : $default;

}