<?php
/**
 * HTMLineMembership_Email
 *
 * @author		Nir Goldberg
 * @package		includes/classes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Email' ) ) :

class HTMLineMembership_Email {

	/**
	 * From email name
	 *
	 * @var (string)
	 */
	private $from_name;

	/**
	 * Default from email adddress
	 *
	 * @var (string)
	 */
	private $default_from_email;

	/**
	 * From email adddress
	 *
	 * @var (string)
	 */
	private $from_email;

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
	 * The real constructor to initialize HTMLineMembership_Email
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function initialize() {

		$from_name					= get_option( 'hmembership_email_from_name', 'WordPress' );
		$this->from_name			= $from_name ? $from_name : 'WordPress';
		$this->default_from_email	= $this->set_default_from_email();
		$this->from_email			= $this->set_from_email();

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
		do_action( 'hmembership_email/init' );

	}

	/**
	 * set_default_from_email
	 *
	 * This function will generate the default from email address
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(string)
	 */
	private function set_default_from_email() {

		// get the site domain and get rid of www
		$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );

		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}

		// return
		return 'wordpress@' . $sitename;

	}

	/**
	 * set_from_email
	 *
	 * This function will set from email address
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function set_from_email() {

		// vars
		$from_email = get_option( 'hmembership_email_from_address' );

		// return
		return $from_email ? $from_email : $this->default_from_email;

	}

	/**
	 * get_default_from_email
	 *
	 * This function will return the default from email address
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(string)
	 */
	public function get_default_from_email() {

		// return
		return $this->default_from_email;

	}

	/**
	 * send
	 *
	 * This function will send an email
	 *
	 * @since		1.0.0
	 * @param		$subject (string)
	 * @param		$to (string)
	 * @param		$message (string)
	 * @return		(bool)
	 */
	public function send( $subject, $to, $message ) {

		// vars
		$headers = array(
			'From: ' . $this->from_name . ' <' . $this->from_email . '>',
			'Content-Type: text/html; charset=UTF-8',
		);

		// return
		return wp_mail(
			$to,
			$subject,
			$message,
			$headers
		);

	}

}

/**
 * hmembership_email
 *
 * The main function responsible for returning the one true instance
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(object)
 */
function hmembership_email() {

	// globals
	global $hmembership_email;

	// initialize
	if( ! isset( $hmembership_email ) ) {

		$hmembership_email = new HTMLineMembership_Email();
		$hmembership_email->initialize();

	}

	// return
	return $hmembership_email;

}

// initialize
hmembership_email();

endif; // class_exists check