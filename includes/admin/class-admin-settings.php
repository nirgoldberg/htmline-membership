<?php
/**
 * Admin settings filters, actions, variables and includes
 *
 * @author		Nir Goldberg
 * @package		includes/admin
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
				'display'			=> array(
					'title'				=> __( 'Display', 'hmembership' ),
					'sections'			=> array(
						'ui'			=> array(
							'title'			=> __( 'User Interface Settings', 'hmembership' ),
							'description'	=> '',
						),
					),
				),
				'permissions'		=> array(
					'title'				=> __( 'Permissions', 'hmembership' ),
					'sections'			=> array(
						'edit'			=> array(
							'title'			=> __( 'Edit Settings', 'hmembership' ),
							'description'	=> '',
						),
						'export'		=> array(
							'title'			=> __( 'Export Settings', 'hmembership' ),
							'description'	=> '',
						),
					),
				),
				'uninstall'			=> array(
					'title'				=> __( 'Uninstall', 'hmembership' ),
					'sections'			=> array(
						'uninstall'		=> array(
							'title'			=> __( 'Uninstall Settings', 'hmembership' ),
							'description'	=> '',
						),
					),
				),
			),
			'active_tab'			=> 'display',

			// sections
			'sections'				=> array(),

			// fields
			'fields'				=> array(
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