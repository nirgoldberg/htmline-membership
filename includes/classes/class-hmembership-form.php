<?php
/**
 * HTMLineMembership_Form
 *
 * @author		Nir Goldberg
 * @package		includes/classes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Form' ) ) :

class HTMLineMembership_Form {

	/**
	 * Form shortcode
	 *
	 * @var (string)
	 */
	private $shortcode;

	/**
	 * Form fields
	 *
	 * @var (array)
	 */
	private $fields;

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
	 * The real constructor to initialize HTMLineMembership_Form
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function initialize() {

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
		do_action( 'hmembership_form/init' );

	}

}

/**
 * hmembership_form
 *
 * The main function responsible for returning the one true instance
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(object)
 */
function hmembership_form() {

	// globals
	global $hmembership_form;

	// initialize
	if( ! isset( $hmembership_form ) ) {

		$hmembership_form = new HTMLineMembership_Form();
		$hmembership_form->initialize();

	}

	// return
	return $hmembership_form;

}

// initialize
hmembership_form();

endif; // class_exists check