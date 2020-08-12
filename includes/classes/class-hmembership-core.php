<?php
/**
 * HTMLineMembership_Core
 *
 * @author		Nir Goldberg
 * @package		includes/classes
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Core' ) ) :

class HTMLineMembership_Core {

	/**
	 * Main site ID
	 *
	 * @var (int)
	 */
	private $main_site_id;

	/**
	 * Main site wpml active
	 *
	 * @var (boolean)
	 */
	private $main_site_wpml_active;

	/**
	 * Local site wpml active
	 *
	 * @var (boolean)
	 */
	private $local_site_wpml_active;

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
	 * The real constructor to initialize HTMLineMembership_Core
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function initialize() {

		$this->main_site_id = get_main_site_id();

		// wpml
		$this->main_site_wpml_active	= $this->is_wpml_active( $this->main_site_id );
		$this->local_site_wpml_active	= $this->is_wpml_active();

		// actions
		add_action( 'init',	array( $this, 'init' ) );

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
		do_action( 'hmembership_core/init' );

	}

	/**
	 * get_main_site_id
	 *
	 * This function will return main site id
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(int)
	 */
	public function get_main_site_id() {

		// return
		return $this->main_site_id;

	}

	/**
	 * is_wpml_active
	 *
	 * This function will return true if WPML is active for specified site ID
	 * If site ID is not set, current site will be checked
	 *
	 * @since		1.0.0
	 * @param		$site_id (int)
	 * @return		(boolean)
	 */
	private function is_wpml_active( $site_id = false ) {

		// vars
		$wpml = false;

		if ( $site_id ) {
			switch_to_blog ( $site_id );
		}

		$wpml = is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' );

		if ( $site_id ) {
			restore_current_blog();
		}

		// return
		return $wpml;

	}

	/**
	 * is_main_site_wpml_active
	 *
	 * This function will return true if WPML is active for main site
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(boolean)
	 */
	public function is_main_site_wpml_active() {

		// return
		return $this->main_site_wpml_active;

	}

	/**
	 * is_local_site_wpml_active
	 *
	 * This function will return true if WPML is active for current site
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(boolean)
	 */
	public function is_local_site_wpml_active() {

		// return
		return $this->local_site_wpml_active;

	}

}

/**
 * hmembership_core
 *
 * The main function responsible for returning the one true instance
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(object)
 */
function hmembership_core() {

	// globals
	global $hmembership_core;

	// initialize
	if( ! isset( $hmembership_core ) ) {

		$hmembership_core = new HTMLineMembership_Core();
		$hmembership_core->initialize();

	}

	// return
	return $hmembership_core;

}

// initialize
hmembership_core();

endif; // class_exists check