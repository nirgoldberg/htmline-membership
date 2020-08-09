<?php
/**
 * HTMLineMembership_Users_List_Table
 *
 * Class for displaying registered HTMLine Membership users
 * in a WordPress-like Admin Table with row actions to perform user meta operations
 *
 * @author		Nir Goldberg
 * @package		includes/admin
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Users_List_Table' ) ) :

class HTMLineMembership_Users_List_Table extends HTMLineMembership_WP_List_Table {

	/**
	* __construct
	*
	* @since		1.0.0
	* @param		N/A
	* @return		N/A
	*/
	public function __construct() {

		parent::__construct( array(
			'plural'	=> 'hmembership-users',		// plural value used for labels and the objects being listed
			'singular'	=> 'hmembership-user',		// singular label for an object being listed
			'ajax'		=> false,					// if true, the parent class will call the _js_vars() method in the footer
		) );

	}

	/**
	 * prepare_items
	 *
	 * This function prepares the list of items for displaying
	 * Query, filter data, handle sorting, pagination and any other data manipulation required prior to rendering
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function prepare_items() {

		// check if a search was performed
		$user_search_key = isset( $_REQUEST[ 's' ] ) ? wp_unslash( trim( $_REQUEST[ 's' ] ) ) : '';

		$this->_column_headers = $this->get_column_info();

		// check and process any actions such as bulk actions
		$this->handle_table_actions();

		// fetch table data
		$table_data = $this->fetch_table_data();

		// filter the data in case of a search
		if( $user_search_key ) {
			$table_data = $this->filter_table_data( $table_data, $user_search_key );
		}

		// required for pagination
		$users_per_page = $this->get_items_per_page( 'hmembership_users_per_page' );
		$table_page = $this->get_pagenum();

		// provide the ordered data to the List Table
		// we need to manually slice the data based on the current pagination
		$this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $users_per_page ), $users_per_page );

		// set the pagination arguments
		$total_users = count( $table_data );
		$this->set_pagination_args( array (
			'total_items'	=> $total_users,
			'per_page'		=> $users_per_page,
			'total_pages'	=> ceil( $total_users/$users_per_page )
		) );

	}

	/**
	 * get_columns
	 *
	 * This function returns an array of columns
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public function get_columns() {

		$table_columns = array(
			'cb'				=> '<input type="checkbox" />',
			'user_login'		=> __( 'Username', 'hmembership' ),
			'user_email'		=> __( 'Email', 'hmembership' ),
			'user_registered'	=> _x( 'Registered On', 'column name', 'hmembership' ),
			'user_status'		=> __( 'Status', 'hmembership' ),
		);

		// return
		return apply_filters( 'hmembership_user_list_table_columns', $table_columns );

	}

	/**
	 * get_sortable_columns
	 *
	 * This function returns an array of sortable columns
	 *
	 * The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	protected function get_sortable_columns() {

		/**
		 * actual sorting still needs to be done by prepare_items.
		 * specify which columns should have the sort icon.
		 *
		 * key => value
		 * column name_in_list_table => columnname in the db
		 */
		$sortable_columns = array (
			'user_login'		=> 'user_login',
			'user_email'		=> 'user_email',
			'user_registered'	=> 'user_registered',
			'user_status'		=> 'user_status',
		);

		// return
		return apply_filters( 'hmembership_user_list_table_sortable_columns', $sortable_columns );

	}

	/**
	 * no_items
	 *
	 * Text displayed when no user data is available
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function no_items() {

		_e( 'No users avaliable.', 'hmembership' );

	}

	/**
	 * fetch_table_data
	 *
	 * This function fetches table data from the WordPress database
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public function fetch_table_data() {

		/**
		 * Variables
		 */
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$orderby		= ( isset( $_GET[ 'orderby' ] ) ) ? esc_sql( $_GET[ 'orderby' ] ) : 'user_login';
		$order			= ( isset( $_GET[ 'order' ] ) ) ? esc_sql( $_GET[ 'order' ] ) : 'ASC';

		$sql =
			"SELECT ID, user_login, user_email, user_registered, user_status
			FROM $users_table ORDER BY $orderby $order";

		// query output_type will be an associative array with ARRAY_A.
		$results = $wpdb->get_results( $sql, ARRAY_A );

		// return result array to prepare_items.
		return apply_filters( 'hmembership_user_list_table_data', $results );

	}

	/**
	 * filter_table_data
	 *
	 * This function filters the table data based on the user search key
	 *
	 * @since		1.0.0
	 * @param		$table_data (array)
	 * @param		$search_key (string)
	 * @return		(array)
	 */
	public function filter_table_data( $table_data, $search_key ) {

		$filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {

			foreach ( $row as $row_val ) {
				if ( stripos( $row_val, $search_key ) !== false ) {
					return true;
				}
			}

		} ) );

		// return
		return $filtered_table_data;

	}

	/**
	 * column_default
	 *
	 * This function renders a column when no column specific method exists
	 *
	 * @since		1.0.0
	 * @param		$item (array)
	 * @param		$column_name (string)
	 * @return		(mixed)
	 */
	public function column_default( $item, $column_name ) {

		// return
		return $item[ $column_name ];

	}

	/**
	 * column_cb
	 *
	 * This function gets value for checkbox column
	 *
	 * @since		1.0.0
	 * @param		$item (object) A row's data
	 * @return		(string)
	 */
	protected function column_cb( $item ) {

		// return
		return sprintf(
			'<label class="screen-reader-text" for="hmembership_user_' . $item[ 'ID' ] . '">' . sprintf( __( 'Select %s' ), $item[ 'user_login' ] ) . '</label>' .
			"<input type='checkbox' name='hmembership_users[]' id='hmembership_user_{$item[ 'ID' ]}' value='{$item[ 'ID' ]}' />"
		);

	}

	/**
	 * column_user_login
	 *
	 * This function renders the user_login column
	 * Adds row action links to the user_login column
	 *
	 * @since		1.0.0
	 * @param		$item (object) A singular item (one full row's worth of data)
	 * @return		(string)
	 */
	protected function column_user_login( $item ) {

		/**
		 * Build usermeta row actions
		 *
		 * e.g. /admin.php?page=hmembership-users&action=approve_user&hmembership_user_id=1&_wpnonce=1984253e5e
		 */

		$admin_page_url = admin_url( 'admin.php' );

		// row action to approve user
		$query_args_approve_user = array(
			'page'					=>  wp_unslash( $_REQUEST[ 'page' ] ),
			'action'				=> 'approve_user',
			'hmembership_user_id'	=> absint( $item[ 'ID' ] ),
			'_wpnonce'				=> wp_create_nonce( 'approve_user_nonce' ),
		);
		$approve_user_link = esc_url( add_query_arg( $query_args_approve_user, $admin_page_url ) );
		$actions[ 'approve_user' ] = '<a href="' . $approve_user_link . '">' . __( 'Approve', 'hmembership' ) . '</a>';

		// row action to decline user
		$query_args_decline_user = array(
			'page'					=>  wp_unslash( $_REQUEST[ 'page' ] ),
			'action'				=> 'decline_user',
			'hmembership_user_id'	=> absint( $item[ 'ID' ] ),
			'_wpnonce'				=> wp_create_nonce( 'decline_user_nonce' ),
		);
		$decline_user_link = esc_url( add_query_arg( $query_args_decline_user, $admin_page_url ) );
		$actions[ 'decline_user' ] = '<a href="' . $decline_user_link . '">' . __( 'Decline', 'hmembership' ) . '</a>';

		$row_value = '<strong>' . $item[ 'user_login' ] . '</strong>';

		// return
		return $row_value . $this->row_actions( $actions );

	}

	/**
	 * get_bulk_actions
	 *
	 * This function returns an associative array containing the bulk action
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public function get_bulk_actions() {

		/**
		 * on hitting apply in bulk actions the url params are set as
		 * ?action=bulk-approve&paged=1&action2=-1
		 *
		 * action and action2 are set based on the triggers above or below the table
		 */
		$actions = array(
			'bulk-approve'	=> __( 'Approve', 'hmembership' ),
			'bulk-decline'	=> __( 'Decline', 'hmembership' ),
		);

		// return
		return $actions;

	}

	/**
	 * handle_table_actions
	 *
	 * This function processes actions triggered by the user
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function handle_table_actions() {

		/*
		 * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
		 *
		 * action - is set if checkbox from top-most select-all is set, otherwise returns -1
		 * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
		 */

		// check for individual row actions
		$the_table_action = $this->current_action();

		if ( 'approve_user' === $the_table_action ) {

			$nonce = wp_unslash( $_REQUEST[ '_wpnonce' ] );

			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'approve_user_nonce' ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->page_approve_user( absint( $_REQUEST[ 'hmembership_user_id' ] ) );
				$this->graceful_exit();
			}

		}

		if ( 'decline_user' === $the_table_action ) {

			$nonce = wp_unslash( $_REQUEST[ '_wpnonce' ] );

			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'decline_user_nonce' ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->page_decline_user( absint( $_REQUEST[ 'hmembership_user_id' ] ) );
				$this->graceful_exit();
			}

		}

		// check for table bulk actions
		if ( ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'bulk-approve' ) || ( isset( $_REQUEST[ 'action2' ] ) && $_REQUEST[ 'action2' ] === 'bulk-approve' ) ) {

			$nonce = wp_unslash( $_REQUEST[ '_wpnonce' ] );

			// verify the nonce.
			/**
			 * Note: the nonce field is set by the parent class
			 * wp_nonce_field( 'bulk-' . $this->_args[ 'plural' ] );
			 */
			if ( ! wp_verify_nonce( $nonce, 'bulk-' . $this->_args[ 'plural' ] ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->page_bulk_approve( $_REQUEST[ 'hmembership_users' ] );
				$this->graceful_exit();
			}

		}

		if ( ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'bulk-decline' ) || ( isset( $_REQUEST[ 'action2' ] ) && $_REQUEST[ 'action2' ] === 'bulk-decline' ) ) {

			$nonce = wp_unslash( $_REQUEST[ '_wpnonce' ] );

			// verify the nonce.
			/**
			 * Note: the nonce field is set by the parent class
			 * wp_nonce_field( 'bulk-' . $this->_args[ 'plural' ] );
			 */
			if ( ! wp_verify_nonce( $nonce, 'bulk-' . $this->_args[ 'plural' ] ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->page_bulk_decline( $_REQUEST[ 'hmembership_users' ] );
				$this->graceful_exit();
			}

		}

	}

	/**
	 * page_approve_user
	 *
	 * This function 
	 *
	 * @since		1.0.0
	 * @param		$user_id (int)
	 * @return		N/A
	 */

	/**
	 * View a user's meta information.
	 *
	 * @since   1.0.0
	 *
	 * @param int $user_id  user's ID
	 */
	public function page_view_usermeta( $user_id ) {

		$user = get_user_by( 'id', $user_id );
		include_once( 'views/partials-wp-list-table-demo-view-usermeta.php' );

	}

	/**
	 * Add a meta information for a user.
	 *
	 * @since   1.0.0
	 *
	 * @param int $user_id  user's ID
	 */

	public function page_add_usermeta( $user_id ) {

		$user = get_user_by( 'id', $user_id );
		include_once( 'views/partials-wp-list-table-demo-add-usermeta.php' );

	}

	/**
	 * Bulk process users.
	 *
	 * @since   1.0.0
	 *
	 * @param array $bulk_user_ids
	 */
	public function page_bulk_download( $bulk_user_ids ) {

		include_once( 'views/partials-wp-list-table-demo-bulk-download.php' );

	}

	/**
	 * graceful_exit
	 *
	 * This function stops execution and exits
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	 public function graceful_exit() {

		exit;

	 }

	/**
	 * invalid_nonce_redirect
	 *
	 * This function dies when the nonce check fails
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	 public function invalid_nonce_redirect() {

		wp_die( __( 'Invalid Nonce', 'hmembership' ),
				__( 'Error', 'hmembership' ),
				array(
					'response'	=> 403,
					'back_link'	=> esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST[ 'page' ] ) ), admin_url( 'admin.php' ) ) ),
				)
		);

	 }

}

endif; // class_exists check