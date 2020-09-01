<?php
/**
 * Admin users page filters, actions, variables and includes
 *
 * @author		Nir Goldberg
 * @package		includes/admin/pages
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
	 * Users update type
	 *
	 * @var (string)
	 */
	private $users_update_type;

	/**
	 * Users update in DB result
	 *
	 * @var (mixed)
	 */
	private $users_effected;

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

		if ( isset( $_REQUEST[ 'action' ] ) && in_array( $_REQUEST[ 'action' ], array( 'do_approve', 'do_decline', 'do_delete' ) ) ) {
			// update users
			$this->update_users( $_REQUEST[ 'action' ] );
		}

		// remove query args
		add_filter( 'removable_query_args', array( $this, 'removable_query_args' ) );

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

		if ( isset( $_GET[ 'update' ] ) && method_exists( $this, 'admin_notice_' . $_GET[ 'update' ] ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_' . $_GET[ 'update' ] ) );
		}

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

	/**
	 * update_users
	 *
	 * This function will update users after user action is fired
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @return		N/A
	 */
	private function update_users( $action ) {

		// vars
		$nonce		= isset( $_REQUEST[ '_wpnonce' ] ) && $_REQUEST[ '_wpnonce' ] ? $_REQUEST[ '_wpnonce' ] : '';
		$user_ids	= isset( $_REQUEST[ 'hmembership_user_ids' ] ) && $_REQUEST[ 'hmembership_user_ids' ] ? $_REQUEST[ 'hmembership_user_ids' ] : array();

		if ( ! $nonce || ! $user_ids )
			return;

		// verify nonce
		if ( ! wp_verify_nonce( $nonce, 'hmembership_update_users_' . $action . '_nonce' ) ) {
			$this->invalid_nonce_redirect();
		} else {
			$this->$action( $user_ids );
		}

	}

	/**
	 * do_approve
	 *
	 * This function will initiate users approval after user action is fired
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array)
	 * @return		N/A
	 */
	private function do_approve( $user_ids ) {

		if ( ! $user_ids || ! is_array( $user_ids ) )
			return;

		// create WordPress users
		$users = (array) hmembership_users_create_wp_users( $user_ids );

		// approve
		$this->users_update_type	= 'approve';
		$this->users_effected		= hmembership_users_set_users_wp_user( $users );

		// get approved users data
		$users = hmembership_users_get_users_by_id( array_keys( $users ) );

		foreach ( $users as $user ) {

			// send approval notification to user
			hmembership_approval_notification_to_user( $user[ 'wp_user_id' ], $user[ 'user_email' ], $user[ 'user_info' ] );

		}

		// redirect after action processed
		$this->complete_update_users();

	}

	/**
	 * do_decline
	 *
	 * This function will initiate users rejection after user action is fired
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array)
	 * @return		N/A
	 */
	private function do_decline( $user_ids ) {

		if ( ! $user_ids || ! is_array( $user_ids ) )
			return;

		// decline
		$this->users_update_type	= 'decline';
		$this->users_effected		= hmembership_users_update_users_status( $user_ids, 'declined' );

		// get declined users data
		$users = hmembership_users_get_users_by_id( $user_ids );

		foreach ( $users as $user ) {

			// send rejection notification to user
			hmembership_rejection_notification_to_user( $user[ 'user_email' ], $user[ 'user_info' ] );

		}

		// redirect after action processed
		$this->complete_update_users();

	}

	/**
	 * do_delete
	 *
	 * This function will initiate users removal after user action is fired
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array)
	 * @return		N/A
	 */
	private function do_delete( $user_ids ) {

		if ( ! $user_ids || ! is_array( $user_ids ) )
			return;

		// delete
		$this->users_update_type	= 'delete';
		$this->users_effected		= hmembership_users_delete_users( $user_ids );

		// redirect after action processed
		$this->complete_update_users();

	}

	/**
	 * complete_update_users
	 *
	 * This function will redirect after action processed
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function complete_update_users() {

		// vars
		$admin_page_url = admin_url( 'admin.php' );
		$page			= wp_unslash( $this->settings[ 'menu_slug' ] );

		// query args
		$query_args = array(
			'page' => $page,
		);

		if ( $this->users_update_type && $this->users_effected ) {

			// query args
			$query_args[ 'update' ]	= $this->users_update_type;
			$query_args[ 'total' ]	= $this->users_effected;

		}

		// add query args
		$url = add_query_arg( $query_args, $admin_page_url );

		// redirect after action processed
		wp_redirect( $url );

		//exit
		exit;

	}

	/**
	 * admin_notice_approve
	 *
	 * This function will add an admin updated notice after users have been successfully approved
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function admin_notice_approve() {

		// vars
		$users_effected = isset( $_GET[ 'total' ] ) && $_GET[ 'total' ] ? intval( $_GET[ 'total' ] ) : '';

		if ( $users_effected ) {
			$msg = sprintf( _n( '%s user approved successfully', '%s users approved successfully', $users_effected, 'hmembership' ), number_format_i18n( $users_effected ) );
		} else {
			$msg = __( 'User approved successfully', 'hmembership' );
		}

		$this->admin_notice( 'updated', $msg );

	}

	/**
	 * admin_notice_decline
	 *
	 * This function will add an admin updated notice after users have been successfully declined
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function admin_notice_decline() {

		// vars
		$users_effected = isset( $_GET[ 'total' ] ) && $_GET[ 'total' ] ? intval( $_GET[ 'total' ] ) : '';

		if ( $users_effected ) {
			$msg = sprintf( _n( '%s user declined successfully', '%s users declined successfully', $users_effected, 'hmembership' ), number_format_i18n( $users_effected ) );
		} else {
			$msg = __( 'User declined successfully', 'hmembership' );
		}

		$this->admin_notice( 'updated', $msg );

	}

	/**
	 * admin_notice_delete
	 *
	 * This function will add an admin updated notice after users have been successfully deleted from DB
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function admin_notice_delete() {

		// vars
		$users_effected = isset( $_GET[ 'total' ] ) && $_GET[ 'total' ] ? intval( $_GET[ 'total' ] ) : '';

		if ( $users_effected ) {
			$msg = sprintf( _n( '%s user deleted successfully', '%s users deleted successfully', $users_effected, 'hmembership' ), number_format_i18n( $users_effected ) );
		} else {
			$msg = __( 'User deleted successfully', 'hmembership' );
		}

		$this->admin_notice( 'updated', $msg );

	}

	/**
	 * removable_query_args
	 *
	 * This function will add query args to WordPress removable query args
	 *
	 * @since		1.0.0
	 * @param		$removable_query_args (array)
	 * @return		(array)
	 */
	public function removable_query_args( $removable_query_args ) {

		$removable_query_args[] = 'total';

		// return
		return $removable_query_args;

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