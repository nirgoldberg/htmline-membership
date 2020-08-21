<?php
/**
 * HTMLineMembership_Status
 *
 * @author		Nir Goldberg
 * @package		includes/classes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Status' ) ) :

class HTMLineMembership_Status {

	/**
	 * Statuses
	 *
	 * @var (array)
	 */
	private $statuses;

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
	 * The real constructor to initialize HTMLineMembership_Status
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function initialize() {

		$this->statuses = array(
			'pending'		=> array(
				'code'			=> 0,
				'label'			=> __( 'Pending', 'hmembership' ),
				'actions'		=> array( 'approve', 'decline', 'delete' ),
			),
			'approved'		=> array(
				'code'			=> 1,
				'label'			=> __( 'Approved', 'hmembership' ),
				'actions'		=> array(),
			),
			'declined'		=> array(
				'code'			=> 2,
				'label'			=> __( 'Declined', 'hmembership' ),
				'actions'		=> array( 'approve', 'delete' ),
			),
			'unassigned'	=> array(
				'code'			=> 3,
				'label'			=> __( 'Unassigned', 'hmembership' ),
				'actions'		=> array(),
			),
			'deleted'		=> array(
				'code'			=> 4,
				'label'			=> __( 'Deleted', 'hmembership' ),
				'actions'		=> array( 'delete' ),
			),
		);

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
		do_action( 'hmembership_status/init' );

	}

	/**
	 * statuses
	 *
	 * This function will return statuses array
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public function statuses() {

		// return
		return $this->statuses;

	}

	/**
	 * get
	 *
	 * This function will return status setting
	 *
	 * @since		1.0.0
	 * @param		$status (string)
	 * @param		$setting (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get( $status, $setting, $default = null ) {

		// return
		return	isset( $this->statuses[ $status ] ) && isset( $this->statuses[ $status ][ $setting ] ) ?
				$this->statuses[ $status ][ $setting ] :
				$default;

	}

	/**
	 * get_code
	 *
	 * This function will return status code
	 *
	 * @since		1.0.0
	 * @param		$status (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_code( $status, $default = null ) {

		// return
		return	isset( $this->statuses[ $status ] ) && $this->statuses[ $status ][ 'code' ] ?
				$this->statuses[ $status ][ 'code' ] :
				$default;

	}

	/**
	 * get_label
	 *
	 * This function will return status label
	 *
	 * @since		1.0.0
	 * @param		$status (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_label( $status, $default = null ) {

		// return
		return	isset( $this->statuses[ $status ] ) && $this->statuses[ $status ][ 'label' ] ?
				$this->statuses[ $status ][ 'label' ] :
				$default;

	}

	/**
	 * get_actions
	 *
	 * This function will return status available actions
	 *
	 * @since		1.0.0
	 * @param		$status (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_actions( $status, $default = null ) {

		// return
		return	isset( $this->statuses[ $status ] ) && $this->statuses[ $status ][ 'actions' ] ?
				$this->statuses[ $status ][ 'actions' ] :
				$default;

	}

	/**
	 * can
	 *
	 * This function will return array of status keys which can provide an action
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @return		(array)
	 */
	public function can( $action ) {

		// vars
		$statuses		= $this->statuses;
		$status_keys	= array();

		foreach ( $statuses as $key => $status ) {
			if ( in_array( $action, $status[ 'actions' ] ) ) {
				$status_keys[] = $key;
			}
		}

		// return
		return $status_keys;

	}

	/**
	 * get_status_by_code
	 *
	 * This function will return status key by code
	 *
	 * @since		1.0.0
	 * @param		$code (int)
	 * @return		(mixed) Status key or false in case of error
	 */
	public function get_status_by_code( $code ) {

		// vars
		$code = intval( $code );

		foreach ( $this->statuses as $key => $status ) {
			if ( $code == $status[ 'code' ] ) {
				// return
				return $key;
			}
		}

		// return
		return false;

	}

	/**
	 * get_label_by_code
	 *
	 * This function will return status label by code
	 *
	 * @since		1.0.0
	 * @param		$code (int)
	 * @return		(mixed) Status label or false in case of error
	 */
	public function get_label_by_code( $code ) {

		// vars
		$code = intval( $code );

		$labels = array_column( $this->statuses, 'label', 'code' );

		// return
		return false !== $code && isset( $labels[ $code ] ) ? $labels[ $code ] : false;

	}

}

/**
 * hmembership_status
 *
 * The main function responsible for returning the one true instance
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(object)
 */
function hmembership_status() {

	// globals
	global $hmembership_status;

	// initialize
	if( ! isset( $hmembership_status ) ) {

		$hmembership_status = new HTMLineMembership_Status();
		$hmembership_status->initialize();

	}

	// return
	return $hmembership_status;

}

// initialize
hmembership_status();

endif; // class_exists check