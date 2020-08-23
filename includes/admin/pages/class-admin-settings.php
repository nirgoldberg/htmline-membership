<?php
/**
 * Admin settings filters, actions, variables and includes
 *
 * @author		Nir Goldberg
 * @package		includes/admin/pages
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Admin_Settings' ) ) :

class HTMLineMembership_Admin_Settings extends HTMLineMembership_Admin_Settings_Page {

	/**
	 * initialize
	 *
	 * This function will initialize the admin settings page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	protected function initialize() {

		// settings
		$this->settings = array(

			// slugs
			'parent_slug'			=> 'hmembership-users',
			'menu_slug'				=> 'hmembership-settings',

			// titles
			'page_title'			=> __( 'HTMLine Membership Settings', 'hmembership' ),
			'menu_title'			=> __( 'Settings', 'hmembership' ),

			// tabs
			'tabs'					=> array(
				'users'					=> array(
					'title'				=> __( 'Users', 'hmembership' ),
					'sections'			=> array(
						'registration_form'		=> array(
							'type'				=> 'static',
							'title'				=> __( 'Registration Form', 'hmembership' ),
							'description'		=> __( 'Place the following shortcode in order to display registration form on screen: ', 'hmembership' ) . '[hmembership-form]',
						),
						'user_custom_fields'	=> array(
							'type'				=> 'dynamic',
							'title'				=> __( 'User Custom Fields', 'hmembership' ),
							'description'		=> __( 'Custom fields for user registration form', 'hmembership' ),
						),
					),
				),
				'emails'				=> array(
					'title'				=> __( 'Emails', 'hmembership' ),
					'sections'			=> array(
						'registration'			=> array(
							'type'				=> 'static',
							'title'				=> __( 'User Registration', 'hmembership' ),
							'description'		=> '',
						),
						'approval'				=> array(
							'type'				=> 'static',
							'title'				=> __( 'User Approval', 'hmembership' ),
							'description'		=> '',
						),
						'rejection'				=> array(
							'type'				=> 'static',
							'title'				=> __( 'User Rejection', 'hmembership' ),
							'description'		=> '',
						),
					),
				),
				'permissions'			=> array(
					'title'				=> __( 'Permissions', 'hmembership' ),
					'sections'			=> array(
						'users_management'		=> array(
							'type'				=> 'static',
							'title'				=> __( 'Users Management Settings', 'hmembership' ),
							'description'		=> '',
						),
						'export'				=> array(
							'type'				=> 'static',
							'title'				=> __( 'Export Settings', 'hmembership' ),
							'description'		=> '',
						),
					),
				),
				'general'				=> array(
					'title'				=> __( 'General', 'hmembership' ),
					'sections'			=> array(
						'general'				=> array(
							'type'				=> 'static',
							'title'				=> __( 'General Settings', 'hmembership' ),
							'description'		=> '',
						),
					),
				),
				'uninstall'				=> array(
					'title'				=> __( 'Uninstall', 'hmembership' ),
					'sections'			=> array(
						'uninstall'				=> array(
							'type'				=> 'static',
							'title'				=> __( 'Uninstall Settings', 'hmembership' ),
							'description'		=> '',
						),
					),
				),
			),
			'active_tab'			=> 'users',

			// sections
			'sections'				=> array(),

			// fields
			'fields'				=> array(

				// users

				array(
					'uid'				=> 'hmembership_user_registration_form_status',
					'label'				=> __( 'Status', 'hmembership' ),
					'label_for'			=> 'hmembership_user_registration_form_status',
					'tab'				=> 'users',
					'section'			=> 'registration_form',
					'type'				=> 'checkbox',
					'options'			=> array(
						'true'			=> 'Active',
					),
					'default'			=> array( 'true' ),
					'helper'			=> sprintf( __( '(Default: %s)', 'hmembership' ), __( 'active', 'hmembership' ) ),
				),
				array(
					'uid'				=> 'hmembership_user_email_field_label',
					'label'				=> __( 'User Email Field Label', 'hmembership' ),
					'label_for'			=> 'hmembership_user_email_field_label',
					'tab'				=> 'users',
					'section'			=> 'registration_form',
					'type'				=> 'text',
					'placeholder'		=> __( 'Email Address', 'hmembership' ),
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), __( 'Email Address', 'hmembership' ) ),
				),
				array(
					'uid'				=> 'hmembership_user_custom_field_label',
					'label'				=> __( 'Field Label', 'hmembership' ),
					'label_for'			=> 'hmembership_user_custom_field_label',
					'tab'				=> 'users',
					'section'			=> 'user_custom_fields',
					'type'				=> 'text',
				),
				array(
					'uid'				=> 'hmembership_user_custom_field_type',
					'label'				=> __( 'Field Type', 'hmembership' ),
					'label_for'			=> 'hmembership_user_custom_field_type',
					'tab'				=> 'users',
					'section'			=> 'user_custom_fields',
					'type'				=> 'select',
					'options'			=> array(
						'text'			=> __( 'Text', 'hmembership' ),
						'email'			=> __( 'Email', 'hmembership' ),
						'textarea'		=> __( 'Textarea', 'hmembership' ),
						'select'		=> __( 'Select', 'hmembership' ),
						'multiselect'	=> __( 'Multiselect', 'hmembership' ),
						'radio'			=> __( 'Radio Button', 'hmembership' ),
						'checkbox'		=> __( 'Checkbox', 'hmembership' ),
					),
				),
				array(
					'uid'				=> 'hmembership_user_custom_field_options',
					'label'				=> __( 'Field Options', 'hmembership' ),
					'label_for'			=> 'hmembership_user_custom_field_options',
					'tab'				=> 'users',
					'section'			=> 'user_custom_fields',
					'type'				=> 'textarea',
					'supplimental'		=> __( 'Each option per row', 'hmembership' ),
				),
				array(
					'uid'				=> 'hmembership_user_custom_field_default',
					'label'				=> __( 'Default Option', 'hmembership' ),
					'label_for'			=> 'hmembership_user_custom_field_default',
					'tab'				=> 'users',
					'section'			=> 'user_custom_fields',
					'type'				=> 'text',
					'supplimental'		=> __( 'One option name from above options', 'hmembership' ),
				),
				array(
					'uid'				=> 'hmembership_user_custom_field_required',
					'label'				=> __( 'Required Field', 'hmembership' ),
					'label_for'			=> 'hmembership_user_custom_field_required',
					'tab'				=> 'users',
					'section'			=> 'user_custom_fields',
					'type'				=> 'checkbox',
					'options'			=> array(
						'true'			=> '',
					),
				),

				// emails

				array(
					'uid'				=> 'hmembership_user_registration_email_to_user_subject',
					'label'				=> __( 'Email Subject Sent to User', 'hmembership' ),
					'label_for'			=> 'hmembership_user_registration_email_to_user_subject',
					'tab'				=> 'emails',
					'section'			=> 'registration',
					'type'				=> 'text',
					'placeholder'		=> sprintf( __( 'Your registration request to %s', 'hmembership' ), get_bloginfo( 'name' ) ),
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), sprintf( __( 'Your registration request to %s', 'hmembership' ), get_bloginfo( 'name' ) ) ),
				),
				array(
					'uid'				=> 'hmembership_user_registration_email_to_user_message',
					'label'				=> __( 'Email Message Sent to User', 'hmembership' ),
					'label_for'			=> 'hmembership_user_registration_email_to_user_message',
					'tab'				=> 'emails',
					'section'			=> 'registration',
					'type'				=> 'editor',
					'supplimental'		=> sprintf( __( 'Available patterns: %s', 'hmembership' ), '{user_email}' ),
				),
				array(
					'uid'				=> 'hmembership_user_registration_email_to_admin_subject',
					'label'				=> __( 'Email Subject Sent to Website Admin', 'hmembership' ),
					'label_for'			=> 'hmembership_user_registration_email_to_admin_subject',
					'tab'				=> 'emails',
					'section'			=> 'registration',
					'type'				=> 'text',
					'placeholder'		=> sprintf( __( '[%s] A new registration request', 'hmembership' ), get_bloginfo( 'name' ) ),
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), sprintf( __( '[%s] A new registration request', 'hmembership' ), get_bloginfo( 'name' ) ) ),
				),
				array(
					'uid'				=> 'hmembership_user_registration_email_to_admin_message',
					'label'				=> __( 'Email Message Sent to Website Admin', 'hmembership' ),
					'label_for'			=> 'hmembership_user_registration_email_to_admin_message',
					'tab'				=> 'emails',
					'section'			=> 'registration',
					'type'				=> 'editor',
					'supplimental'		=> sprintf( __( 'Available patterns: %s', 'hmembership' ), '{user_email}' ),
				),
				array(
					'uid'				=> 'hmembership_user_approval_email_to_user_subject',
					'label'				=> __( 'Email Subject Sent to User', 'hmembership' ),
					'label_for'			=> 'hmembership_user_approval_email_to_user_subject',
					'tab'				=> 'emails',
					'section'			=> 'approval',
					'type'				=> 'text',
					'placeholder'		=> sprintf( __( 'Your registration request to %s is approved', 'hmembership' ), get_bloginfo( 'name' ) ),
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), sprintf( __( 'Your registration request to %s is approved', 'hmembership' ), get_bloginfo( 'name' ) ) ),
				),
				array(
					'uid'				=> 'hmembership_user_approval_email_to_user_message',
					'label'				=> __( 'Email Message Sent to User', 'hmembership' ),
					'label_for'			=> 'hmembership_user_approval_email_to_user_message',
					'tab'				=> 'emails',
					'section'			=> 'approval',
					'type'				=> 'editor',
					'supplimental'		=> sprintf( __( 'Available patterns: %s', 'hmembership' ), '{user_email}' ),
				),
				array(
					'uid'				=> 'hmembership_user_rejection_email_to_user_subject',
					'label'				=> __( 'Email Subject Sent to User', 'hmembership' ),
					'label_for'			=> 'hmembership_user_rejection_email_to_user_subject',
					'tab'				=> 'emails',
					'section'			=> 'rejection',
					'type'				=> 'text',
					'placeholder'		=> sprintf( __( 'Your registration request to %s is declined', 'hmembership' ), get_bloginfo( 'name' ) ),
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), sprintf( __( 'Your registration request to %s is declined', 'hmembership' ), get_bloginfo( 'name' ) ) ),
				),
				array(
					'uid'				=> 'hmembership_user_rejection_email_to_user_message',
					'label'				=> __( 'Email Message Sent to User', 'hmembership' ),
					'label_for'			=> 'hmembership_user_rejection_email_to_user_message',
					'tab'				=> 'emails',
					'section'			=> 'rejection',
					'type'				=> 'editor',
					'supplimental'		=> sprintf( __( 'Available patterns: %s', 'hmembership' ), '{user_email}' ),
				),

				// permissions

				array(
					'uid'				=> 'hmembership_delete_users',
					'label'				=> __( 'Delete Users', 'hmembership' ),
					'label_for'			=> 'hmembership_delete_users',
					'tab'				=> 'permissions',
					'section'			=> 'users_management',
					'type'				=> 'checkbox',
					'options'			=> array(
						'true'			=> '',
					),
					'default'			=> array( 'true' ),
					'supplimental'		=> __( 'Check this option to allow deleting of users', 'hmembership' ),
					'helper'			=> sprintf( __( '(Default: %s)', 'hmembership' ), __( 'true', 'hmembership' ) ),
				),
				array(
					'uid'				=> 'hmembership_export_users',
					'label'				=> __( 'Export Users', 'hmembership' ),
					'label_for'			=> 'hmembership_export_users',
					'tab'				=> 'permissions',
					'section'			=> 'export',
					'type'				=> 'checkbox',
					'options'			=> array(
						'true'			=> '',
					),
					'default'			=> array( 'true' ),
					'supplimental'		=> __( 'Check this option to allow exporting of users', 'hmembership' ),
					'helper'			=> sprintf( __( '(Default: %s)', 'hmembership' ), __( 'true', 'hmembership' ) ),
				),

				// general

				array(
					'uid'				=> 'hmembership_user_role_display_name',
					'label'				=> __( 'User Role Display Name', 'hmembership' ),
					'label_for'			=> 'hmembership_user_role_display_name',
					'tab'				=> 'general',
					'section'			=> 'general',
					'type'				=> 'text',
					'placeholder'		=> __( 'Member', 'hmembership' ),
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), __( 'Member', 'hmembership' ) ),
				),
				array(
					'uid'				=> 'hmembership_admin_email',
					'label'				=> __( 'Admin Email Address', 'hmembership' ),
					'label_for'			=> 'hmembership_admin_email',
					'tab'				=> 'general',
					'section'			=> 'general',
					'type'				=> 'email',
					'placeholder'		=> get_option( 'admin_email' ),
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), get_option( 'admin_email' ) ),
				),
				array(
					'uid'				=> 'hmembership_email_from_name',
					'label'				=> __( 'Emails "From" Name', 'hmembership' ),
					'label_for'			=> 'hmembership_email_from_name',
					'tab'				=> 'general',
					'section'			=> 'general',
					'type'				=> 'text',
					'placeholder'		=> 'WordPress',
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), 'WordPress' ),
				),
				array(
					'uid'				=> 'hmembership_email_from_address',
					'label'				=> __( 'Emails "From" Address', 'hmembership' ),
					'label_for'			=> 'hmembership_email_from_address',
					'tab'				=> 'general',
					'section'			=> 'general',
					'type'				=> 'email',
					'placeholder'		=> hmembership_email()->get_default_from_email(),
					'supplimental'		=> sprintf( __( 'Default: %s', 'hmembership' ), hmembership_email()->get_default_from_email() ),
				),

				// uninstall

				array(
					'uid'				=> 'hmembership_uninstall_remove_data',
					'label'				=> __( 'Remove Data on Uninstall', 'hmembership' ),
					'label_for'			=> 'hmembership_uninstall_remove_data',
					'tab'				=> 'uninstall',
					'section'			=> 'uninstall',
					'type'				=> 'checkbox',
					'options'			=> array(
						'true'		=> '',
					),
					'supplimental'		=> __( 'Caution: all data will be removed without any option to restore', 'hmembership' ),
					'helper'			=> sprintf( __( '(Default: %s)', 'hmembership' ), __( 'false', 'hmembership' ) ),
				),
			),

		);

	}

}

// initialize
new HTMLineMembership_Admin_Settings();

endif; // class_exists check