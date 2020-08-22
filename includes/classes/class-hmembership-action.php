<?php
/**
 * HTMLineMembership_Action
 *
 * @author		Nir Goldberg
 * @package		includes/classes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Action' ) ) :

class HTMLineMembership_Action {

	/**
	 * Actions
	 *
	 * @var (array)
	 */
	private $actions;

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
	 * The real constructor to initialize HTMLineMembership_Action
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function initialize() {

		$this->actions = array(
			'approve'		=> array(
				'page_title'	=> __( 'Approve Users', 'hmembership' ),
				'label'			=> __( 'Approve', 'hmembership' ),
				'singular'		=> 'approve-user',
				'plural'		=> 'bulk-approve',
				'table_action'	=> 'approve-users',
				'do_action'		=> 'do_approve',
				'permission'	=> array(),
				'strings'		=> array(
					'str1'			=> __( 'Approval', 'hmemberhip' ),
					'str2'			=> __( 'approval', 'hmemberhip' ),
					'str3'			=> __( 'approved', 'hmemberhip' ),
				),
			),
			'decline'		=> array(
				'page_title'	=> __( 'Decline Users', 'hmembership' ),
				'label'			=> __( 'Decline', 'hmembership' ),
				'singular'		=> 'decline-user',
				'plural'		=> 'bulk-decline',
				'table_action'	=> 'decline-users',
				'do_action'		=> 'do_decline',
				'permission'	=> array(),
				'strings'		=> array(
					'str1'			=> __( 'Rejection', 'hmemberhip' ),
					'str2'			=> __( 'rejection', 'hmemberhip' ),
					'str3'			=> __( 'declined', 'hmemberhip' ),
				),
			),
			'delete'		=> array(
				'page_title'	=> __( 'Delete Users from HTMLine Membership', 'hmembership' ),
				'label'			=> __( 'Delete', 'hmembership' ),
				'singular'		=> 'delete-user',
				'plural'		=> 'bulk-delete',
				'table_action'	=> 'delete-users',
				'do_action'		=> 'do_delete',
				'permission'	=> array(
					'key'		=> 'hmembership_delete_users',
					'default'	=> 'true',
				),
				'strings'		=> array(
					'str1'			=> __( 'Removal', 'hmemberhip' ),
					'str2'			=> __( 'removal', 'hmemberhip' ),
					'str3'			=> __( 'deleted', 'hmemberhip' ),
				),
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
		do_action( 'hmembership_action/init' );

	}

	/**
	 * actions
	 *
	 * This function will return actions array
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public function actions() {

		// return
		return $this->actions;

	}

	/**
	 * get
	 *
	 * This function will return action setting
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @param		$setting (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get( $action, $setting, $default = null ) {

		// return
		return	isset( $this->actions[ $action ] ) && isset( $this->actions[ $action ][ $setting ] ) ?
				$this->actions[ $action ][ $setting ] :
				$default;

	}

	/**
	 * get_label
	 *
	 * This function will return action label
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_label( $action, $default = null ) {

		// return
		return	isset( $this->actions[ $action ] ) && $this->actions[ $action ][ 'label' ] ?
				$this->actions[ $action ][ 'label' ] :
				$default;

	}

	/**
	 * get_singular
	 *
	 * This function will return action singular
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_singular( $action, $default = null ) {

		// return
		return	isset( $this->actions[ $action ] ) && $this->actions[ $action ][ 'singular' ] ?
				$this->actions[ $action ][ 'singular' ] :
				$default;

	}

	/**
	 * get_plural
	 *
	 * This function will return action plural
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_plural( $action, $default = null ) {

		// return
		return	isset( $this->actions[ $action ] ) && $this->actions[ $action ][ 'plural' ] ?
				$this->actions[ $action ][ 'plural' ] :
				$default;

	}

	/**
	 * get_do_action
	 *
	 * This function will return action do_action
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_do_action( $action, $default = null ) {

		// return
		return	isset( $this->actions[ $action ] ) && $this->actions[ $action ][ 'do_action' ] ?
				$this->actions[ $action ][ 'do_action' ] :
				$default;

	}

	/**
	 * get_permission
	 *
	 * This function will return action permission
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_permission( $action, $default = null ) {

		// return
		return	isset( $this->actions[ $action ] ) && $this->actions[ $action ][ 'permission' ] ?
				$this->actions[ $action ][ 'permission' ] :
				$default;

	}

	/**
	 * get_page_title
	 *
	 * This function will return action page title
	 *
	 * @since		1.0.0
	 * @param		$action (string)
	 * @param		$default (mix)
	 * @return		(mixed)
	 */
	public function get_page_title( $action, $default = null ) {

		// check singular list table action
		$titles = array_column( $this->actions, 'page_title', 'singular' );

		if ( is_array( $titles ) && isset( $titles[ $action ] ) ) {

			// return
			return $titles[ $action ];

		} else {

			// check plural list table action
			$titles = array_column( $this->actions, 'page_title', 'plural' );

			if ( is_array( $titles ) && isset( $titles[ $action ] ) ) {

				// return
				return $titles[ $action ];

			}

		}

		// return
		return $default;

	}

	/**
	 * action_allowed
	 *
	 * This function will return true if action is allowed according to permission setting
	 *
	 * @since		1.0.0
	 * @param		$action (array)
	 * @return		(bool)
	 */
	public function action_allowed( $action ) {

		// vars
		$permission			= 'true';
		$action_permission	= $action[ 'permission' ];

		if ( $action_permission ) {

			$permission	= $action_permission[ 'default' ] ? get_option( $action_permission[ 'key' ], array( $action_permission[ 'default' ] ) ) : get_option( $action_permission[ 'key' ] );
			$permission	= $action_permission[ 'default' ] ? $permission && in_array( $action_permission[ 'default' ], $permission ) : $permission;

		}

		// return
		return $permission;

	}

	/**
	 * get_action_by_singular
	 *
	 * This function will return associative action array by singular
	 *
	 * @since		1.0.0
	 * @param		$singular (int)
	 * @return		(mixed) Action array or false in case of error
	 */
	public function get_action_by_singular( $singular ) {

		foreach ( $this->actions as $key => $action ) {
			if ( $singular == $action[ 'singular' ] ) {
				// return
				return array( $key => $action );
			}
		}

		// return
		return false;

	}

	/**
	 * get_action_by_plural
	 *
	 * This function will return associative action array by plural
	 *
	 * @since		1.0.0
	 * @param		$plural (int)
	 * @return		(mixed) Action array or false in case of error
	 */
	public function get_action_by_plural( $plural ) {

		foreach ( $this->actions as $key => $action ) {
			if ( $plural == $action[ 'plural' ] ) {
				// return
				return array( $key => $action );
			}
		}

		// return
		return false;

	}

}

/**
 * hmembership_action
 *
 * The main function responsible for returning the one true instance
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(object)
 */
function hmembership_action() {

	// globals
	global $hmembership_action;

	// initialize
	if( ! isset( $hmembership_action ) ) {

		$hmembership_action = new HTMLineMembership_Action();
		$hmembership_action->initialize();

	}

	// return
	return $hmembership_action;

}

// initialize
hmembership_action();

endif; // class_exists check