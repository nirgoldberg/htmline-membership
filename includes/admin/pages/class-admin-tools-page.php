<?php
/**
 * Admin tools page filters, actions, variables and includes
 *
 * @author		Nir Goldberg
 * @package		includes/admin/pages
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Admin_Tools' ) ) :

class HTMLineMembership_Admin_Tools extends HTMLineMembership_Admin_Page {

	/**
	 * initialize
	 *
	 * This function will initialize the tools submenu page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	protected function initialize() {

		$this->settings = array(

			// slugs
			'parent_slug'		=> 'hmembership-users',
			'menu_slug'			=> 'hmembership-tools',

			// titles
			'page_title'		=> __( 'HTMLine Membership Tools', 'hmembership' ),
			'menu_title'		=> __( 'Tools', 'hmembership' ),

			// tabs
			'tabs'				=> array(
				'export-users'	=> array(
					'title'			=> __( 'Export Users', 'hmembership' ),
					'permission'	=> get_option( 'hmembership_export_users', array( 'true' ) ),
				),
			),
			'active_tab'		=> '',

		);

	}

}

// initialize
new HTMLineMembership_Admin_Tools();

endif; // class_exists check