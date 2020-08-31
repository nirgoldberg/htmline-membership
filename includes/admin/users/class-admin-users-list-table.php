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
		$current		= isset( $_REQUEST[ 'status' ] ) ? $_REQUEST[ 'status' ] : 'all';
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
		$class = 'all' == $current ? 'class="current"' : '';
		$all_url = esc_url( add_query_arg( $query_args_all, $admin_page_url ) );
		$views[ 'all' ] =	'<a href="' . $all_url . '" ' . $class . '>' . __( 'All', 'hmembership' ) .
								'<span class="count"> (' . array_sum( $counts_arr ) . ')</span>' .
							'</a>';

		foreach ( hmembership_status()->statuses() as $key => $status ) {

			$code = strval( $status[ 'code' ] );

			if ( array_key_exists( $code, $counts_arr ) ) {

				$query_args = array(
					'page'		=> $page,
					'status'	=> $code,
				);
				$class = $code == $current ? 'class="current"' : '';
				$url = esc_url( add_query_arg( $query_args, $admin_page_url ) );
				$views[ $key ] =	'<a href="' . $url . '" ' . $class . '>' . $status[ 'label' ] .
												'<span class="count"> (' . $counts_arr[ $code ] . ')</span>' .
											'</a>';

			}

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
	private function fetch_table_data() {

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
		$results = $wpdb->get_results( $wpdb->prepare( $sql ), ARRAY_A );

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
	private function filter_table_data( $table_data, $search_key ) {

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
			"<input type='checkbox' name='hmembership_user_ids[]' id='hmembership_user_id_{$item[ 'ID' ]}' value='{$item[ 'ID' ]}' />"
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
		$admin_page_url 	= admin_url( 'admin.php' );
		$page				= wp_unslash( $_REQUEST[ 'page' ] );
		$available_actions	= hmembership_action()->actions();
		$actions			= array();

		foreach ( $available_actions as $key => $action ) {

			// vars
			$user_status		= hmembership_status()->get_status_by_code( $item[ 'user_status' ] );
			$allowed_actions	= hmembership_status()->can( $key );
			$permission 		= hmembership_action()->action_allowed( $action );

			if ( $permission && in_array( $user_status, $allowed_actions ) ) {

				// vars
				$action_name	= $action[ 'singular' ];
				$action_label	= $action[ 'label' ];

				$query_args = array(
					'page'					=> $page,
					'action'				=> $action_name,
					'hmembership_user_id'	=> absint( $item[ 'ID' ] ),
					'_wpnonce'				=> wp_create_nonce( 'hmembership_' . $key . '_user_nonce' ),
				);
				$url = esc_url( add_query_arg( $query_args, $admin_page_url ) );
				$actions[ $action_name ] = '<a href="' . $url . '">' . $action_label . '</a>';

			}

		}

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
						$li[] = '<li><b>' . stripslashes( $val ) . '</b></li>';
					}

				}

			} else {

				// other
				$li[] = '<li>' . $value[ 'label' ] . ': <b>' . stripslashes( $value[ 'value' ] ) . '</b></li>';

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
		$class	= hmembership_status()->get_status_by_code( $item[ 'user_status' ] );
		$label	= hmembership_status()->get_label_by_code( $item[ 'user_status' ] );
		$output	= '';

		if ( $class && $label )
			$output =	"<span class='$class'>$label</span>";

		// return
		return apply_filters( 'hmembership_users_list_table_column_user_status', $output, $item );

	}

	/**
	 * This function generates the required HTML for a list of row action links
	 * Copied just to maintain same height for no embedded actions row
	 *
	 * @since		1.0.0
	 * @param		$actions (array) Array of action links
	 * @param		$always_visible (bool) Whether the actions should be always visible
	 * @return		(string)
	 */
	protected function row_actions( $actions, $always_visible = false ) {

		/// vars
		$action_count = count( $actions );

		if ( ! $action_count ) {
			return '<div class="row-actions"><span style="visibility: hidden;">&nbsp;</span></div>';
		}

		$mode = get_user_setting( 'posts_list_mode', 'list' );

		if ( 'excerpt' === $mode ) {
			$always_visible = true;
		}

		$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';

		$i = 0;

		foreach ( $actions as $action => $link ) {
			++$i;

			$sep = ( $i < $action_count ) ? ' | ' : '';

			$out .= "<span class='$action'>$link$sep</span>";
		}

		$out .= '</div>';

		$out .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details' ) . '</span></button>';

		// return
		return $out;

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

		// vars
		$available_actions	= hmembership_action()->actions();
		$actions			= array();

		foreach ( $available_actions as $key => $action ) {

			// vars
			$action_name	= $action[ 'plural' ];
			$label			= $action[ 'label' ];
			$permission 	= hmembership_action()->action_allowed( $action );

			if ( $permission ) {
				$actions[ $action_name ] = $label;
			}

		}

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
	private function handle_table_actions() {

		/*
		 * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
		 *
		 * action - is set if checkbox from top-most select-all is set, otherwise returns -1
		 * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
		 */

		// check for individual row actions
		$the_table_action = $this->current_action();

		// singular action
		$action = hmembership_action()->get_action_by_singular( $the_table_action );

		if ( $action ) {

			$nonce = wp_unslash( $_REQUEST[ '_wpnonce' ] );

			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'hmembership_' . key( $action ) . '_user_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->do_action( $action, absint( $_REQUEST[ 'hmembership_user_id' ] ) );
			}

		} else {

			// plural action
			$action = hmembership_action()->get_action_by_plural( $the_table_action );

			if ( $action ) {

				$nonce = wp_unslash( $_REQUEST[ '_wpnonce' ] );

				// verify the nonce.
				/**
				 * Note: the nonce field is set by the parent class
				 * wp_nonce_field( 'bulk-' . $this->_args[ 'plural' ] );
				 */
				if ( ! wp_verify_nonce( $nonce, 'bulk-' . $this->_args[ 'plural' ] ) ) {
					$this->invalid_nonce_redirect();
				} else {
					$this->do_action( $action, $_REQUEST[ 'hmembership_user_ids' ] );
				}

			}

		}

	}

	/**
	 * do_action
	 *
	 * This function will display an action page
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @param		$user_ids (int|array) HTMLine Membership user ID or array of user IDs
	 * @return		N/A
	 */
	private function do_action( $action, $user_ids ) {

		// load action view
		hmembership_get_view( 'hmembership-users-list-table-action', array(
			'action'	=> $action,
			'user_ids'	=> $user_ids,
		) );

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
	private function invalid_nonce_redirect() {

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