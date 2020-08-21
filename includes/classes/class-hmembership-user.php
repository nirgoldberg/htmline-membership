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
	 * User role
	 *
	 * @var (string)
	 */
	private $role;

	/**
	 * User role display name
	 *
	 * @var (string)
	 */
	private $role_display_name;

	/**
	 * WordPress user data
	 *
	 * @var (array)
	 */
	private $userdata;

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

		// user role
		$this->role = 'hmembership_member';

		// user role display name
		$role_display_name = get_option( 'hmembership_user_role_display_name', __( 'Member', 'hmembership' ) );
		$this->role_display_name = $role_display_name ? $role_display_name : __( 'Member', 'hmembership' );

		// userdata
		$this->userdata = array(
			'show_admin_bar_front'	=> 'false',
			'role'					=> $this->role,
		);

		// constants
		hmembership_define( 'HTMLineMembership_USERS_TABLE', 'hmembership_users' );

		// api
		hmembership_include( 'includes/api/api-users.php' );

		// actions
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'deleted_user', array( $this, 'before_delete_user' ), 10, 2 );

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

		// update user role display name if necessary
		$this->verify_role_display_name();

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
	 * add_user_role
	 *
	 * This function will add the HTMLine Membership user role
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function add_user_role() {

		add_role( $this->role, $this->role_display_name, get_role( 'subscriber' )->capabilities );

	}

	/**
	 * remove_user_role
	 *
	 * This function will remove the HTMLine Membership user role
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function remove_user_role() {

		remove_role( $this->role );

	}

	/**
	 * verify_role_display_name
	 *
	 * This function will modify user role display name in case it modified in plugin settings
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function verify_role_display_name() {

		// vars
		global $wpdb;
		$option_name	= $wpdb->prefix . 'user_roles';
		$roles			= get_option( $option_name );

		if ( isset( $roles[ $this->role ] ) && $this->role_display_name != $roles[ $this->role ][ 'name' ] ) {

			$roles[ $this->role ][ 'name' ] = $this->role_display_name;
			update_option( $option_name, $roles );

		}

	}

	/**
	 * create_wp_users
	 *
	 * This function will create WordPress users
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array)
	 * @return		(array) Associative array in the following format: user_id => wp_user_id
	 */
	public function create_wp_users( $user_ids ) {

		if ( ! $user_ids || ! is_array( $user_ids ) )
			return;

		// vars
		$result = array();

		$users = $this->get_users_by_id( $user_ids );

		if ( ! $users )
			return $result;

		foreach ( $users as $user ) {

			$userdata = array(
				'user_login'	=> $user[ 'user_email' ],
				'user_email'	=> $user[ 'user_email' ],
			);

			$userdata = apply_filters( 'hmembership_user/userdata', array_merge( $userdata, $this->userdata ), $user );

			// check if already exists (in case $userdata[ 'user_login' ] is hooked)
			if ( isset( $userdata[ 'user_login' ] ) && username_exists( $userdata[ 'user_login' ] ) ) {
				$userdata[ 'user_login' ] = $userdata[ 'user_email' ];
			}

			// create user
			$wp_user_id = wp_insert_user( $userdata );

			if ( ! is_wp_error( $wp_user_id ) ) {

				// update $result
				$result[ $user[ 'ID' ] ] = $wp_user_id;

				// send user notificcation
				wp_new_user_notification( $wp_user_id, null, 'user' );

			}

		}

		// return
		return $result;

	}

	/**
	 * before_delete_user
	 *
	 * This function will unlink HTMLine Membership user from his WordPress user ID and update his user status accordingly
	 *
	 * @since		1.0.0
	 * @param		$user_id (int)
	 * @param		$reassign (int|null)
	 * @return		N/A
	 */
	public function before_delete_user( $user_id, $reassign ) {

		// verify it's an HTMLine Membership user
		$user = $this->get_user_by_wp_user_id( $user_id );

		if ( $user ) {

			$this->unset_users_wp_user( array( $user[ 'ID' ] ) );

		}

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
				wp_user_id bigint(20) unsigned NOT NULL DEFAULT 0,
				user_email varchar(100) NOT NULL DEFAULT '',
				user_registered datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				user_status int(11) NOT NULL DEFAULT 0,
				user_info longtext NULL DEFAULT NULL,
				PRIMARY KEY  (ID)
			)
			COLLATE {$wpdb_collate}";

		// execute query
		dbDelta( $sql );

	}

	/**
	 * drop_users_table
	 *
	 * This function will create HTMLine Membership users DB table
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function drop_users_table() {

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;

		$sql = "DROP TABLE IF EXISTS $users_table";

		$wpdb->query( $wpdb->prepare( $sql ) );

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
	 * set_users_wp_user
	 *
	 * This function will:
	 * 1. Associate HTMLine Membership users with their WordPress user IDs
	 * 2. Update users status
	 *
	 * @since		1.0.0
	 * @param		$users (array) Associative array in the following format: user_id => wp_user_id
	 * @return		(mixed) Number of users updated, or false on error
	 */
	public function set_users_wp_user( $users ) {

		if ( ! $users || ! is_array( $users ) )
			return;

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$status			= hmembership_status()->get_code( 'approved' );
		$result			= 0;

		foreach ( $users as $user_id => $wp_user_id ) {

			$sql =	"UPDATE $users_table
					SET	wp_user_id = $wp_user_id,
						user_status = $status
					WHERE ID = $user_id";

			$update = $wpdb->query( $wpdb->prepare( $sql ) );

			// count
			$result += $update ? $update : 0;

		}

		// return
		return $result;

	}

	/**
	 * unset_users_wp_user
	 *
	 * This function will:
	 * 1. Unlink HTMLine Membership users from their WordPress user IDs
	 * 2. Update users status
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array)
	 * @return		(mixed) Number of users updated, or false on error
	 */
	public function unset_users_wp_user( $user_ids ) {

		if ( ! $user_ids || ! is_array( $user_ids ) )
			return;

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$user_ids		= array_map( 'intval', $user_ids );
		$status			= hmembership_status()->get_code( 'deleted' );
		$result			= 0;

		foreach ( $user_ids as $user_id ) {

			$sql =	"UPDATE $users_table
					SET	wp_user_id = 0,
						user_status = $status
					WHERE ID = $user_id";

			$update = $wpdb->query( $wpdb->prepare( $sql ) );

			// count
			$result += $update ? $update : 0;

		}

		// return
		return $result;

	}

	/**
	 * update_users_status
	 *
	 * This function will update HTMLine Membership users status in DB table
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array)
	 * @param		$user_status (string)
	 * @return		(mixed) Number of rows updated, or false on error
	 */
	public function update_users_status( $user_ids, $user_status ) {

		if ( ! $user_ids || ! is_array( $user_ids ) || ! $user_status )
			return false;

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$user_ids		= implode( ',', array_map( 'intval', $user_ids ) );
		$status			= hmembership_status()->get_code( $user_status );

		$sql =	"UPDATE $users_table
				SET user_status = $status
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
						'ID' => intval( $user_id ),
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

		if ( ! $user_ids || ! is_array( $user_ids) )
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
	 * get_user_by_wp_user_id
	 *
	 * This function will return HTMLine Membership user data by wp_user_id
	 *
	 * @since		1.0.0
	 * @param		$wp_user_id (int)
	 * @return		(mixed)
	 */
	public function get_user_by_wp_user_id( $wp_user_id ) {

		if ( ! $wp_user_id )
			return false;

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$wp_user_id		= intval( $wp_user_id );

		$sql =	"SELECT * FROM $users_table
				WHERE wp_user_id = $wp_user_id";

		// return
		return $wpdb->get_row( $wpdb->prepare( $sql ), ARRAY_A );

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