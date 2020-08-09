<?php
/**
 * Admin users page filters, actions, variables and includes
 *
 * @author		Nir Goldberg
 * @package		includes/admin
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Admin_Users' ) ) :

class HTMLineMembership_Admin_Users extends HTMLineMembership_Admin_Page {

	/**
	 * Screen ID
	 *
	 * @var (string)
	 */
	private $screen;

	/**
	 * Screen option name
	 *
	 * @var (string)
	 */
	private $option_name;

	/**
	 * Screen option default per page
	 *
	 * @var (int)
	 */
	private $default_per_page;

	/**
	 * HTMLineMembership_Users_List_Table object
	 *
	 * @var (object)
	 */
	private $users_list_table;

	/**
	 * initialize
	 *
	 * This function will initialize the users submenu page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	protected function initialize() {

		$this->settings = array(

			// slugs
			'parent_slug'		=> 'hmembership-users',
			'menu_slug'			=> 'hmembership-users',

			// titles
			'page_title'		=> __( 'HTMLine Membership Users', 'hmembership' ),
			'menu_title'		=> __( 'Users', 'hmembership' ),

			// tabs
			'tabs'				=> array(),
			'active_tab'		=> '',

		);

		$this->screen			= 'toplevel_page_' . $this->settings[ 'menu_slug' ];
		$this->option_name		= 'hmembership_users_per_page';
		$this->default_per_page	= 20;

	}

	/**
	 * init
	 *
	 * This function will run after all plugins and theme functions have been included
	 * This function will add screen options
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function init() {

		// screen options
		new HTMLineMembership_Admin_Page_Screen_Options( $this->screen, $this->option_name, $this->default_per_page );

	}

	/**
	 * load_page
	 *
	 * This function will be triggered when menu page is loaded
	 * This function will load the users List Table
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function load_page() {

		// users List Table
		$this->users_list_table = new HTMLineMembership_Users_List_Table();

	}

	/**
	 * get_users_list_table
	 *
	 * This function will return the HTMLineMembership_Users_List_Table object
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(object)
	 */
	public function get_users_list_table() {

		// return
		return $this->users_list_table;

	}

}

/**
 * hmembership_admin_users
 *
 * The main function responsible for returning the one true HTMLineMembership_Admin_Users instance
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(object)
 */
function hmembership_admin_users() {

	// globals
	global $hmembership_admin_users;

	// initialize
	if( ! isset( $hmembership_admin_users ) ) {

		$hmembership_admin_users = new HTMLineMembership_Admin_Users();

	}

	// return
	return $hmembership_admin_users;

}

// initialize
hmembership_admin_users();

endif; // class_exists check