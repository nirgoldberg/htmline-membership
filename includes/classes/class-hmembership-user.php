<?php
/**
 * HTMLineMembership_User
 *
 * @author		Nir Goldberg
 * @package		includes/classes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_User' ) ) :

class HTMLineMembership_User {

	/**
	 * __construct
	 *
	 * A dummy constructor to ensure is only initialized once
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function __construct() {

		/* Do nothing here */

	}

	/**
	 * initialize
	 *
	 * The real constructor to initialize HTMLineMembership_User
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function initialize() {

		// constants
		hmembership_define( 'HTMLineMembership_USERS_TABLE', 'hmembership_users' );

		// api
		hmembership_include( 'includes/api/api-users.php' );

		// actions
		add_action( 'init', array( $this, 'init' ) );

	}

	/**
	 * init
	 *
	 * This function will run after all plugins and theme functions have been included
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function init() {

		// exit if called too early
		if ( ! did_action( 'plugins_loaded' ) )
			return;

		// admin
		if ( is_admin() ) {

			// classes
			hmembership_include( 'includes/admin/class-admin-wp-list-table.php' );
			hmembership_include( 'includes/admin/class-admin-users-list-table.php' );

		}

		// action for 3rd party
		do_action( 'hmembership_user/init' );

	}

	/**
	 * create_users_table
	 *
	 * This function will create HTMLine Membership users DB table
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function create_users_table() {

		/**
		 * Variables
		 */
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$wpdb_collate	= $wpdb->collate;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql =
			"CREATE TABLE IF NOT EXISTS {$users_table} (
				ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				user_login varchar(60) NOT NULL DEFAULT '',
				user_email varchar(100) NOT NULL DEFAULT '',
				user_registered datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				user_status int(11) NOT NULL DEFAULT '0',
				user_info longtext NULL DEFAULT NULL,
				PRIMARY KEY  (ID)
			)
			COLLATE {$wpdb_collate}";

		// execute query
		dbDelta( $sql );

	}

	/**
	 * get_users
	 *
	 * This function will return HTMLine Membership users data
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public function get_users() {

		/**
		 * Variables
		 */
		global $wpdb;
		$users_table = $wpdb->prefix . HTMLineMembership_USERS_TABLE;

		// return
		return $wpdb->get_results( "SELECT * FROM $users_table" );

	}

}

/**
 * hmembership_user
 *
 * The main function responsible for returning the one true instance
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(object)
 */
function hmembership_user() {

	// globals
	global $hmembership_user;

	// initialize
	if( ! isset( $hmembership_user ) ) {

		$hmembership_user = new HTMLineMembership_User();
		$hmembership_user->initialize();

	}

	// return
	return $hmembership_user;

}

// initialize
hmembership_user();

endif; // class_exists check