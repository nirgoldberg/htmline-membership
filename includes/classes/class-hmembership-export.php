<?php
/**
 * HTMLineMembership_Export
 *
 * @author		Nir Goldberg
 * @package		includes/classes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Export' ) ) :

class HTMLineMembership_Export {

	/**
	 * Filename
	 *
	 * @var (string)
	 */
	private $filename;

	/**
	 * User custom columns
	 *
	 * @var (array)
	 */
	private $custom_columns;

	/**
	 * Columns
	 *
	 * @var (array)
	 */
	private $columns;

	/**
	 * Users data
	 *
	 * @var (array)
	 */
	private $users_data;

	/**
	 * __construct
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function __construct() {

		// initialize
		$this->initialize();

		// generate export data
		$this->create_export_data();

	}

	/**
	 * initialize
	 *
	 * This function will initialize HTMLineMembership_Export
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function initialize() {

		$this->filename			= 'hmembership-users-' . current_time('Ymd-His') . '.csv';
		$this->custom_columns	= $this->get_custom_columns();
		$this->columns			= $this->get_columns();
		$this->users_data		= $this->get_users_data();

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

		// action for 3rd party
		do_action( 'hmembership_export/init' );

	}

	/**
	 * get_custom_columns
	 *
	 * This function will return user custom columns
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	private function get_custom_columns() {

		// vars
		$fields			= hmembership_form()::get_fields();
		$custom_columns	= array();

		if ( ! $fields )
			return $custom_columns;

		foreach ( $fields as $field ) {

			if ( ! $field[ 'id' ] )
				continue;

			$custom_columns[ $field[ 'id' ] ] = isset( $field[ 'label' ] ) ? $field[ 'label' ] : $field[ 'id' ];

		}

		// return
		return $custom_columns;

	}

	/**
	 * get_columns
	 *
	 * This function will generate and return columns to be used by exported data
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	private function get_columns() {

		// vars
		$columns = array(
			'ID'				=> 'ID',
			'user_email'		=> 'User Email',
			'user_registered'	=> 'User Registered',
			'user_status'		=> 'Status',
		);

		// add custom columns
		$columns = array_merge(
			array_slice( $columns, 0, 3 ),
			$this->custom_columns,
			array_slice( $columns, 3, 1 )
		);

		// return
		return $columns;

	}

	/**
	 * get_users_data
	 *
	 * This function will return users data from database
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	private function get_users_data() {

		// vars
		global $wpdb;
		$users_table		= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
		$status_codes		= hmembership_status()->statuses();
		$select_columns		= array();
		$select_statuses	= array();

		// build select clouse custom columns
		if ( $this->custom_columns ) {
			foreach ( $this->custom_columns as $key => $column ) {
				$select_columns[] = "JSON_UNQUOTE(JSON_EXTRACT(user_info, '$.\"" . $key . "\".value')) AS \"" . $key . "\"";
			}
		}
		$select_columns = $select_columns ? ', ' . implode( ', ', $select_columns ) : '';

		// build status labels
		if ( $status_codes ) {
			foreach ( $status_codes as $status ) {
				$select_statuses[] = "WHEN user_status = '{$status['code']}' THEN '{$status['label']}'";
			}
		}
		$select_statuses = $select_statuses ? 'case ' . implode( ' ', $select_statuses ) . ' END AS user_status' : 'user_status';

		$sql =
			"SELECT ID, user_email, user_registered {$select_columns}, {$select_statuses}
			FROM $users_table
			ORDER BY user_registered DESC";

		// query
		$users_data = $wpdb->get_results( $wpdb->prepare( $sql ), ARRAY_A );

		// return
		return $users_data;

	}

	/**
	 * create_export_data
	 *
	 * This function will generate the export data
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function create_export_data() {

		$columns	= $this->columns;
		$users_data	= $this->users_data;

		if ( ! $users_data )
			return;

		// open
		$csv = fopen( 'php://output', 'w' );

		$this->send_headers();

		// insert $columns
		fputcsv( $csv, $columns );

		// insert $users_data
		foreach ( $users_data as $row ) {

			$row_edited = array();

			foreach ( $row as $value ) {
				$arr = (array) json_decode( $value );

				if ( json_last_error() === JSON_ERROR_NONE ) {
					$row_edited[] = htmlspecialchars_decode( stripslashes( implode( '; ', $arr ) ), ENT_QUOTES );
				}
				else {
					$row_edited[] = htmlspecialchars_decode( stripslashes( $value ), ENT_QUOTES );
				}
			}

			fputcsv( $csv, $row_edited );

		}

		// die
		die();

	}

	/**
	 * send_headers
	 *
	 * This function will send export file headers
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function send_headers() {

		// vars
		$filename = $this->filename;

		// Content-Type
		header( "Content-Type: text/csv; charset=utf-8" );

		// disposition / encoding on response body
		header( "Content-Disposition: attachment;filename={$filename}" );

	}

}

endif; // class_exists check