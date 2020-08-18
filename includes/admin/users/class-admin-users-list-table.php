<?php
/**
 * HTMLineMembership_Users_List_Table
 *
 * Class for displaying registered HTMLine Membership users
 * in a WordPress-like Admin Table with row actions to perform user operations
 *
 * @author		Nir Goldberg
 * @package		includes/admin/users
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
	 * This function will prepare the list of items for displaying
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

		// filter views
		$this->views();

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
	 * This function will return an array of columns
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public function get_columns() {

		$table_columns = array(
			'cb'				=> '<input type="checkbox" />',
			'user_email'		=> __( 'User Email', 'hmembership' ),
			'user_registered'	=> __( 'Registered On', 'hmembership' ),
			'user_info'			=> __( 'User Info', 'hmembership' ),
			'user_status'		=> __( 'Status', 'hmembership' ),
		);

		// return
		return apply_filters( 'hmembership_users_list_table_columns', $table_columns );

	}

	/**
	 * get_sortable_columns
	 *
	 * This function will return an array of sortable columns
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
		 * actual sorting still needs to be done by prepare_items
		 * specify which columns should have the sort icon
		 */
		$sortable_columns = array (
			'user_email'		=> 'user_email',
			'user_registered'	=> 'user_registered',
			'user_status'		=> 'user_status',
		);

		// return
		return apply_filters( 'hmembership_users_list_table_sortable_columns', $sortable_columns );

	}

	/**
	 * no_items
	 *
	 * This function will echo text displayed when no user data is available
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function no_items() {

		_e( 'No users avaliable.', 'hmembership' );

	}

	/**
	 * get_views
	 *
	 * This function will get the list of views available on this table
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	protected function get_views() {

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$admin_page_url	= admin_url( 'admin.php' );
		$page			= wp_unslash( $_REQUEST[ 'page' ] );
		$status			= isset( $_REQUEST[ 'status' ] ) ? $_REQUEST[ 'status' ] : 'all';
		$views			= array();

		// get status counts
		$sql			= "SELECT user_status, count(*) as count FROM $users_table GROUP BY user_status";
		$counts			= $wpdb->get_results( $wpdb->prepare( $sql ), ARRAY_A );
		$counts_arr		= array();

		if ( ! $counts || ! is_array( $counts ) )
			return;

		foreach ( $counts as $count ) {
			$counts_arr[ $count[ 'user_status' ] ] = $count[ 'count' ];
		}

		// all
		$query_args_all = array(
			'page'		=> $page,
		);
		$class = 'all' == $status ? 'class="current"' : '';
		$all_url = esc_url( add_query_arg( $query_args_all, $admin_page_url ) );
		$views[ 'all' ] =	'<a href="' . $all_url . '" ' . $class . '>' . __( 'All', 'hmembership' ) .
								'<span class="count"> (' . array_sum( $counts_arr ) . ')</span>' .
							'</a>';


		// pending
		if ( array_key_exists( '0', $counts_arr ) ) {

			$query_args_pending = array(
				'page'		=> $page,
				'status'	=> '0',
			);
			$class = '0' == $status ? 'class="current"' : '';
			$pending_url = esc_url( add_query_arg( $query_args_pending, $admin_page_url ) );
			$views[ 'pending' ] =	'<a href="' . $pending_url . '" ' . $class . '>' . __( 'Pending', 'hmembership' ) .
										'<span class="count"> (' . $counts_arr['0'] . ')</span>' .
									'</a>';

		}

		// approved
		if ( array_key_exists( '1', $counts_arr ) ) {

			$query_args_approved = array(
				'page'		=> $page,
				'status'	=> '1',
			);
			$class = '1' == $status ? 'class="current"' : '';
			$approved_url = esc_url( add_query_arg( $query_args_approved, $admin_page_url ) );
			$views[ 'approved' ] =	'<a href="' . $approved_url . '" ' . $class . '>' . __( 'Approved', 'hmembership' ) .
										'<span class="count"> (' . $counts_arr['1'] . ')</span>' .
									'</a>';

		}

		// declined
		if ( array_key_exists( '2', $counts_arr ) ) {

			$query_args_declined = array(
				'page'		=> $page,
				'status'	=> '2',
			);
			$class = '2' == $status ? 'class="current"' : '';
			$declined_url = esc_url( add_query_arg( $query_args_declined, $admin_page_url ) );
			$views[ 'declined' ] =	'<a href="' . $declined_url . '" ' . $class . '>' . __( 'Declined', 'hmembership' ) .
										'<span class="count"> (' . $counts_arr['2'] . ')</span>' .
									'</a>';

		}

		// deleted
		if ( array_key_exists( '3', $counts_arr ) ) {

			$query_args_deleted = array(
				'page'		=> $page,
				'status'	=> '3',
			);
			$class = '3' == $status ? 'class="current"' : '';
			$deleted_url = esc_url( add_query_arg( $query_args_deleted, $admin_page_url ) );
			$views[ 'deleted' ] =	'<a href="' . $deleted_url . '" ' . $class . '>' . __( 'Deleted', 'hmembership' ) .
										'<span class="count"> (' . $counts_arr['3'] . ')</span>' .
									'</a>';

		}

		// return
		return apply_filters( 'hmembership_users_list_table_views', $views );

	}

	/**
	 * fetch_table_data
	 *
	 * This function will fetch table data from the WordPress database
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public function fetch_table_data() {

		// vars
		global $wpdb;
		$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$where			= ( isset( $_GET[ 'status' ] ) ) ? 'WHERE user_status = ' . $_GET[ 'status' ] : '';
		$orderby		= ( isset( $_GET[ 'orderby' ] ) ) ? esc_sql( $_GET[ 'orderby' ] ) : 'user_registered';
		$order			= ( isset( $_GET[ 'order' ] ) ) ? esc_sql( $_GET[ 'order' ] ) : 'DESC';

		$sql =
			"SELECT ID, user_email, user_registered, user_info, user_status
			FROM $users_table
			$where ORDER BY $orderby $order";

		// query output_type will be an associative array with ARRAY_A.
		$results = $wpdb->get_results( $sql, ARRAY_A );

		// return
		return apply_filters( 'hmembership_users_list_table_data', $results );

	}

	/**
	 * filter_table_data
	 *
	 * This function will filter the table data based on the user search key
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
		return apply_filters( 'hmembership_users_list_table_filtered_data', $filtered_table_data, $table_data, $search_key );

	}

	/**
	 * column_default
	 *
	 * This function will render a column when no column specific method exists
	 *
	 * @since		1.0.0
	 * @param		$item (array)
	 * @param		$column_name (string)
	 * @return		(mixed)
	 */
	protected function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'user_registered':
				return $item[$column_name];

			default:
				return $item[$column_name];

		}

	}

	/**
	 * column_cb
	 *
	 * This function will render the cb column
	 *
	 * @since		1.0.0
	 * @param		$item (object) Row's data
	 * @return		(string)
	 */
	protected function column_cb( $item ) {

		// return
		return sprintf(
			'<label class="screen-reader-text" for="hmembership_user_' . $item[ 'ID' ] . '">' . sprintf( __( 'Select %s' ), $item[ 'user_email' ] ) . '</label>' .
			"<input type='checkbox' name='hmembership_users[]' id='hmembership_user_{$item[ 'ID' ]}' value='{$item[ 'ID' ]}' />"
		);

	}

	/**
	 * column_user_email
	 *
	 * This function will render the user_email column
	 * Adds row action links to the user_email column
	 *
	 * @since		1.0.0
	 * @param		$item (object) Row's data
	 * @return		(string)
	 */
	protected function column_user_email( $item ) {

		// vars
		$admin_page_url = admin_url( 'admin.php' );
		$page			= wp_unslash( $_REQUEST[ 'page' ] );
		$actions		= array();

		// approve action
		$query_args_approve_user = array(
			'page'					=> $page,
			'action'				=> 'approve_user',
			'hmembership_user_id'	=> absint( $item[ 'ID' ] ),
			'_wpnonce'				=> wp_create_nonce( 'approve_user_nonce' ),
		);
		$approve_user_link = esc_url( add_query_arg( $query_args_approve_user, $admin_page_url ) );
		$actions[ 'approve_user' ] = '<a href="' . $approve_user_link . '">' . __( 'Approve', 'hmembership' ) . '</a>';

		// decline action
		$query_args_decline_user = array(
			'page'					=> $page,
			'action'				=> 'decline_user',
			'hmembership_user_id'	=> absint( $item[ 'ID' ] ),
			'_wpnonce'				=> wp_create_nonce( 'decline_user_nonce' ),
		);
		$decline_user_link = esc_url( add_query_arg( $query_args_decline_user, $admin_page_url ) );
		$actions[ 'decline_user' ] = '<a href="' . $decline_user_link . '">' . __( 'Decline', 'hmembership' ) . '</a>';

		// delete action
		$query_args_delete_user = array(
			'page'					=> $page,
			'action'				=> 'delete_user',
			'hmembership_user_id'	=> absint( $item[ 'ID' ] ),
			'_wpnonce'				=> wp_create_nonce( 'delete_user_nonce' ),
		);
		$delete_user_link = esc_url( add_query_arg( $query_args_delete_user, $admin_page_url ) );
		$actions[ 'delete_user' ] = '<a href="' . $delete_user_link . '">' . __( 'Delete', 'hmembership' ) . '</a>';

		$row_value = '<strong><a href="mailto:' . $item[ 'user_email' ] . '">' . $item[ 'user_email' ] . '</a></strong>';

		// return
		return apply_filters( 'hmembership_users_list_table_column_user_email', $row_value, $item ) . $this->row_actions( $actions );

	}

	/**
	 * column_user_info
	 *
	 * This function will render the user_info column
	 *
	 * @since		1.0.0
	 * @param		$item (object) Row's data
	 * @return		(string)
	 */
	protected function column_user_info( $item ) {

		// vars
		$fields			= HTMLineMembership_Form::get_fields();
		$info			= unserialize( $item[ 'user_info' ] );
		$current_info	= array();
		$old_info		= array();
		$output			= '';

		// exit if no expected info
		if ( ! is_array( $info ) )
			return $item[ 'user_info' ];

		// exit if no user custom fields defined
		if ( ! is_array( $fields ) )
			return $item[ 'user_info' ];

		// loop
		foreach ( $info as $key => $value ) {

			// check if is a current or old info
			$current	= false !== array_search( $key, array_column( $fields, 'id' ) );
			$li			= array();

			if ( in_array( $value[ 'type' ], array( 'radio', 'checkbox' ) ) ) {

				// radio/checkbox
				if ( is_array( $value[ 'value' ] ) ) {

					foreach ( $value[ 'value' ] as $val ) {
						$li[] = '<li><b>' . $val . '</b></li>';
					}

				}

			} else {

				// other
				$li[] = '<li>' . $value[ 'label' ] . ': <b>' . $value[ 'value' ] . '</b></li>';

			}

			if ( $li ) {
				if ( $current ) {
					$current_info = array_merge( $current_info, $li );
				} else {
					$old_info = array_merge( $old_info, $li );
				}
			}

		}

		if ( $current_info ) {
			$output .= '<ul class="hmembership-users-list-table-current-info-col">' . implode( '', $current_info ) . '</ul>';
		}

		if ( $old_info ) {

			$output .= '<h4>' . __( 'Old User Info', 'hmembership' ) . '</h4>';
			$output .= '<ul class="hmembership-users-list-table-old-info-col">' . implode( '', $old_info ) . '</ul>';

		}

		if ( $current_info || $old_info ) {

			$output =	'<div class="expand">
							<span class="dashicons dashicons-plus-alt2 open"></span>
							<span class="dashicons dashicons-minus"></span>
						</div>
						<div class="content">' . $output . '</div>';

		}

		// return
		return apply_filters( 'hmembership_users_list_table_column_user_info', $output, $item );

	}

	/**
	 * column_user_status
	 *
	 * This function renders the user_status column
	 *
	 * @since		1.0.0
	 * @param		$item (object) Row's data
	 * @return		(string)
	 */
	protected function column_user_status( $item ) {

		// vars
		$output = str_replace(

			array( '0', '1', '2', '3' ),
			array(
				'<span class="pending">' . __( 'Pending', 'hmembership' ) . '</span>',
				'<span class="approved">' . __( 'Approved', 'hmembership' ) . '</span>',
				'<span class="declined">' . __( 'Declined', 'hmembership' ) . '</span>',
				'<span class="deleted">' . __( 'Deleted', 'hmembership' ) . '</span>',
			),
			$item[ 'user_status' ]

		);

		// return
		return apply_filters( 'hmembership_users_list_table_column_user_status', $output, $item );

	}

	/**
	 * get_bulk_actions
	 *
	 * This function will return an associative array containing the bulk actions
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	protected function get_bulk_actions() {

		/**
		 * on hitting apply in bulk actions the url params are set as
		 * ?action=bulk-approve&paged=1&action2=-1
		 *
		 * action and action2 are set based on the triggers above or below the table
		 */
		$actions = array(
			'bulk-approve'	=> __( 'Approve', 'hmembership' ),
			'bulk-decline'	=> __( 'Decline', 'hmembership' ),
			'bulk-delete'	=> __( 'Delete', 'hmembership' ),
		);

		// return
		return apply_filters( 'hmembership_users_list_table_bulk_actions', $actions );

	}

	/**
	 * handle_table_actions
	 *
	 * This function will process actions triggered by the user
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

		if ( 'delete_user' === $the_table_action ) {

			$nonce = wp_unslash( $_REQUEST[ '_wpnonce' ] );

			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'delete_user_nonce' ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->page_delete_user( absint( $_REQUEST[ 'hmembership_user_id' ] ) );
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

		if ( ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'bulk-delete' ) || ( isset( $_REQUEST[ 'action2' ] ) && $_REQUEST[ 'action2' ] === 'bulk-delete' ) ) {

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
				$this->page_bulk_delete( $_REQUEST[ 'hmembership_users' ] );
				$this->graceful_exit();
			}

		}

	}

	/**
	 * page_approve_user
	 *
	 * @since		1.0.0
	 * @param		$user_id (int) HTMLine Membership user ID
	 * @return		N/A
	 */
	public function page_approve_user( $user_id ) {}

	/**
	 * page_decline_user
	 *
	 * @since		1.0.0
	 * @param		$user_id (int) HTMLine Membership user ID
	 * @return		N/A
	 */
	public function page_decline_user( $user_id ) {}

	/**
	 * page_delete_user
	 *
	 * @since		1.0.0
	 * @param		$user_id (int) HTMLine Membership user ID
	 * @return		N/A
	 */
	public function page_delete_user( $user_id ) {}

	/**
	 * page_bulk_approve
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array) HTMLine Membership user IDs
	 * @return		N/A
	 */
	public function page_bulk_approve( $user_id ) {}

	/**
	 * page_bulk_decline
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array) HTMLine Membership user IDs
	 * @return		N/A
	 */
	public function page_bulk_decline( $user_id ) {}

	/**
	 * page_bulk_delete
	 *
	 * @since		1.0.0
	 * @param		$user_ids (array) HTMLine Membership user IDs
	 * @return		N/A
	 */
	public function page_bulk_delete( $user_id ) {}

	/**
	 * graceful_exit
	 *
	 * This function will stop execution and exit
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
	 * This function will die when the nonce check fails
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