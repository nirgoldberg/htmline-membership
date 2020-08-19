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
			hmembership_include( 'includes/admin/lib/class-admin-wp-list-table.php' );
			hmembership_include( 'includes/admin/users/class-admin-users-list-table.php' );

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

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$wpdb_collate	= $wpdb->collate;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql =
			"CREATE TABLE IF NOT EXISTS {$users_table} (
				ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
	 * insert_user
	 *
	 * This function will insert HTMLine Membership user to DB table
	 *
	 * @since		1.0.0
	 * @param		$user_email (string)
	 * @param		$user_info (string)
	 * @return		(mixed) Number of rows inserted, or false on error
	 */
	public function insert_user( $user_email, $user_info ) {

		if ( ! $user_email || ! $user_info )
			return false;

		// vars
		global $wpdb;
		$users_table = $wpdb->prefix . HTMLineMembership_USERS_TABLE;

		$insert = 	$wpdb->insert( $users_table, array(
						'user_email'		=> $user_email,
						'user_registered'	=> current_time( 'mysql' ),
						'user_info'			=> $user_info,
					));

		// return
		return $insert;

	}

	/**
	 * hmembership_users_update_users_status
	 *
	 * This function will update HTMLine Membership users status in DB table
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array)
	 * @param		$user_status (int)
	 * @return		(mixed) Number of rows updated, or false on error
	 */
	public function update_users_status( $user_ids, $user_status ) {

		if ( ! $user_ids )
			return false;

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$user_ids		= implode( ',', array_map( 'intval', $user_ids ) );

		$sql =	"UPDATE $users_table
				SET user_status = $user_status
				WHERE ID IN ($user_ids)";

		$updated = $wpdb->query( $wpdb->prepare( $sql ) );

		// return
		return $updated;

	}

	/**
	 * delete_user
	 *
	 * This function will delete HTMLine Membership user from DB table
	 *
	 * @since		1.0.0
	 * @param		$user_id (int)
	 * @return		(mixed) Number of rows deleted, or false on error
	 */
	public function delete_user( $user_id ) {

		if ( ! $user_id )
			return false;

		// vars
		global $wpdb;
		$users_table = $wpdb->prefix . HTMLineMembership_USERS_TABLE;

		$delete = 	$wpdb->delete( $users_table, array(
						'ID' => $user_id,
					));

		// return
		return $delete;

	}

	/**
	 * delete_users
	 *
	 * This function will delete HTMLine Membership users from DB table
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array)
	 * @return		(mixed) Number of rows deleted, or false on error
	 */
	public function delete_users( $user_ids ) {

		if ( ! $user_ids )
			return false;

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$user_ids		= implode( ',', array_map( 'intval', $user_ids ) );

		$sql =	"DELETE FROM $users_table
				WHERE ID IN ($user_ids)";

		$delete = $wpdb->query( $wpdb->prepare( $sql ) );

		// return
		return $delete;

	}

	/**
	 * get_users_by_id
	 *
	 * This function will return HTMLine Membership users data by their IDs
	 *
	 * @since		1.0.0
	 * @param		user_ids (array)
	 * @return		(mixed)
	 */
	public function get_users_by_id( $user_ids ) {

		if ( ! $user_ids || ! is_array( $user_ids ) )
			return false;

		// vars
		global $wpdb;
		$users_table 	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$user_ids		= implode( ',', array_map( 'intval', $user_ids ) );

		$sql =	"SELECT * FROM $users_table
				WHERE ID IN ($user_ids)";

		// return
		return $wpdb->get_results( $wpdb->prepare( $sql ), ARRAY_A );

	}

	/**
	 * get_user_by_email
	 *
	 * This function will return HTMLine Membership user data by email
	 *
	 * @since		1.0.0
	 * @param		$user_email (string)
	 * @return		(mixed)
	 */
	public function get_user_by_email( $user_email ) {

		if ( ! $user_email )
			return false;

		// vars
		global $wpdb;
		$users_table = $wpdb->prefix . HTMLineMembership_USERS_TABLE;

		$sql =	"SELECT * FROM $users_table
				WHERE user_email = '$user_email'";

		// return
		return $wpdb->get_row( $wpdb->prepare( $sql ), ARRAY_A );

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