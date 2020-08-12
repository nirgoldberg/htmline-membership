<?php
/**
 * Admin menu page filters, actions, variables and includes
 *
 * @author		Nir Goldberg
 * @package		includes/admin
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Admin' ) ) :

class HTMLineMembership_Admin {

	/**
	 * Settings array
	 *
	 * @var (array)
	 */
	private $settings;

	/**
	 * __construct
	 *
	 * This function will initialize the admin menu page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function __construct() {

		// initialize
		$this->initialize();

		// actions
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	/**
	 * initialize
	 *
	 * This function will initialize the admin menu page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function initialize() {

		$this->settings = array(

			// slug
			'menu_slug'		=> 'hmembership-users',

			// titles
			'page_title'	=> __( 'HTMLine Membership', 'hmembership' ),
			'menu_title'	=> __( 'Membership', 'hmembership' ),

			// icon
			'icon_url'		=> 'dashicons-groups',

			// position
			'position'		=> 71,

		);

	}

	/**
	 * admin_menu
	 *
	 * This function will add HTMLine Membership menu item to the WP admin
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function admin_menu() {

		// exit if no show_admin
		if ( ! hmembership_get_setting( 'show_admin' ) )
			return;

		// vars
		$capability = hmembership_get_setting( 'capability' );

		// add menu page
		add_menu_page(
			$this->settings[ 'page_title' ],
			$this->settings[ 'menu_title' ],
			$capability,
			$this->settings[ 'menu_slug' ],
			'',
			$this->settings[ 'icon_url' ],
			$this->settings[ 'position' ]
		);

	}

}

// initialize
new HTMLineMembership_Admin();

endif; // class_exists check