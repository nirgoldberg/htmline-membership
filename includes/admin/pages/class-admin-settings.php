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
						'user_custom_fields'	=> array(
							'type'				=> 'dynamic',
							'title'				=> __( 'User Custom fields', 'hmembership' ),
							'description'		=> __( 'Custom fields for user registration form', 'hmembership' ),
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
					'uid'				=> 'hmembership_export_users',
					'label'				=> __( 'Export users', 'hmembership' ),
					'label_for'			=> 'hmembership_export_users',
					'tab'				=> 'permissions',
					'section'			=> 'export',
					'type'				=> 'checkbox',
					'options'			=> array(
						'can'			=> '',
					),
					'default'			=> array( 'can' ),
					'supplimental'		=> __( 'Check this option to allow exporting of users', 'hmembership' ),
					'helper'			=> __( '(Default: true)', 'hmembership' ),
				),
				array(
					'uid'				=> 'hmembership_admin_email',
					'label'				=> __( 'Admin Email Address', 'hmembership' ),
					'label_for'			=> 'hmembership_admin_email',
					'tab'				=> 'general',
					'section'			=> 'general',
					'type'				=> 'email',
					'supplimental'		=> __( 'Default: ' . get_option( 'admin_email' ), 'hmembership' ),
				),
				array(
					'uid'				=> 'hmembership_uninstall_remove_data',
					'label'				=> __( 'Remove data on uninstall', 'hmembership' ),
					'label_for'			=> 'hmembership_uninstall_remove_data',
					'tab'				=> 'uninstall',
					'section'			=> 'uninstall',
					'type'				=> 'checkbox',
					'options'			=> array(
						'remove'		=> '',
					),
					'supplimental'		=> __( 'Caution: all data will be removed without any option to restore', 'hmembership' ),
					'helper'			=> __( '(Default: false)', 'hmembership' ),
				),
			),

		);

	}

}

// initialize
new HTMLineMembership_Admin_Settings();

endif; // class_exists check