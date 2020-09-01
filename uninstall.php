<?php
/**
 * HTMLine Membership uninstall
 *
 * @author		Nir Goldberg
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit; // Exit if accessed directly

// exit if is large network
if ( wp_is_large_network() ) exit;

// vars
$sites		= array();
$options	= array();

// get sites
$sites		= get_sites( array( 'fields' => 'ids' ) );

if ( $sites ) {

	// set options
	$options = array(
		// users
		'hmembership_user_registration_form_status',
		'hmembership_user_email_field_label',
		'hmembership_user_custom_field_label',
		'hmembership_user_custom_field_type',
		'hmembership_user_custom_field_options',
		'hmembership_user_custom_field_default',
		'hmembership_user_custom_field_required',
		// emails
		'hmembership_user_registration_email_to_user_subject',
		'hmembership_user_registration_email_to_user_message',
		'hmembership_user_registration_email_to_admin_subject',
		'hmembership_user_registration_email_to_admin_message',
		'hmembership_user_approval_email_to_user_subject',
		'hmembership_user_approval_email_to_user_message',
		'hmembership_user_rejection_email_to_user_subject',
		'hmembership_user_rejection_email_to_user_message',
		// permissions
		'hmembership_delete_users',
		'hmembership_export_users',
		// general
		'hmembership_user_role_display_name',
		'hmembership_admin_email',
		'hmembership_email_from_name',
		'hmembership_email_from_address',
		// uninstall
		'hmembership_uninstall_remove_data',
	);

	foreach ( $sites as $site_id ) {

		$remove_data = get_blog_option( $site_id, 'hmembership_uninstall_remove_data' );

		if ( $remove_data && in_array( 'true', $remove_data ) ) {

			// remove plugin data
			hmembership_remove_data( $site_id, $options );

		}

	}

}

/**
 * hmembership_remove_data
 *
 * This function will remove options and database plugin data
 *
 * @since		1.0.0
 * @param		$site_id (int) site ID
 * @param		$options (array) plugin options
 * @return		N/A
 */
function hmembership_remove_data( $site_id, $options = array() ) {

	// remove plugin options
	hmembership_remove_options_data( $site_id, $options );

	// remove database plugin data
	hmembership_remove_db_data( $site_id );

}

/**
 * hmembership_remove_options_data
 *
 * This function will remove plugin options
 *
 * @since		1.0.0
 * @param		$site_id (int) site ID
 * @param		&$options (array) plugin options
 * @return		N/A
 */
function hmembership_remove_options_data( $site_id, &$options = array() ) {

	foreach ( $options as $option ) {
		delete_blog_option( $site_id, $option );
	}

}

/**
 * hmembership_remove_db_data
 *
 * This function will remove database plugin data
 *
 * @since		1.0.0
 * @param		$site_id (int) site ID
 * @return		N/A
 */
function hmembership_remove_db_data( $site_id ) {

	switch_to_blog( $site_id );

	// remove the HTMLine Membership user role
	hmembership_users_remove_user_role();

	// drop HTMLine Membership users DB table
	hmembership_users_drop_db_table();

	restore_current_blog();

}