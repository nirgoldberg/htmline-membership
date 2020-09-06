<?php
/**
* Plugin Name: HTMLine Membership
* Plugin URI: http://www.htmline.com/
* Description: Handles membership WordPress sites including user registration custom form, registration process control, content restriction, etc.
* Version: 1.0.0
* Author: Nir Goldberg
* Author URI: http://www.htmline.com/
* License: GPLv3
* Text Domain: hmembership
* Domain Path: /lang
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership' ) ) :

class HTMLineMembership {

	/**
	 * Plugin version
	 *
	 * @var (string)
	 */
	private $version;

	/**
	 * Required plugins must be active for HTMLineMembership
	 *
	 * @var (array)
	 */
	private $required_plugins;

	/**
	 * __construct
	 *
	 * A dummy constructor to ensure HTMLineMembership is only initialized once
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function __construct() {

		$this->version				= '1.0.0';
		$this->required_plugins		= array();

		/* Do nothing here */

	}

	/**
	 * initialize
	 *
	 * The real constructor to initialize HTMLineMembership
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function initialize() {

		// vars
		$basename	= plugin_basename( __FILE__ );
		$path		= plugin_dir_path( __FILE__ );
		$url		= plugin_dir_url( __FILE__ );
		$slug		= dirname( $basename );

		// settings
		$this->settings = array(

			// basic
			'name'				=> __( 'HTMLine Membership', 'hmembership' ),
			'version'			=> $this->version,

			// urls
			'basename'			=> $basename,
			'path'				=> $path,		// with trailing slash
			'url'				=> $url,		// with trailing slash
			'slug'				=> $slug,

			// options
			'show_admin'		=> true,
			'capability'		=> 'manage_options',
			'debug'				=> false,

		);

		if ( ! $this->check_required_plugins() )
			return;

		// helpers
		include_once( $path . 'includes/api/api-helpers.php' );

		// constants
		hmembership_define( 'HTMLineMembership',			true );
		hmembership_define( 'HTMLineMembership_VERSION',	$this->version );
		hmembership_define( 'HTMLineMembership_PATH',		$path );

		// classes
		hmembership_include( 'includes/classes/class-hmembership-status.php' );
		hmembership_include( 'includes/classes/class-hmembership-action.php' );
		hmembership_include( 'includes/classes/class-hmembership-user.php' );
		hmembership_include( 'includes/classes/class-hmembership-form.php' );
		hmembership_include( 'includes/classes/class-hmembership-email.php' );
		hmembership_include( 'includes/classes/class-hmembership-content.php' );
		hmembership_include( 'includes/classes/class-hmembership-export.php' );

		// actions
		add_action( 'init',	array( $this, 'init' ) );
		add_action( 'init',	array( $this, 'register_assets' ) );

		if ( is_admin() ) {

			// admin actions
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts') );

		} else {

			// front actions
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts') );

		}

		// plugin activation / deactivation
		register_activation_hook	( __FILE__,	array( $this, 'hmembership_activate' ) );
		register_deactivation_hook	( __FILE__,	array( $this, 'hmembership_deactivate' ) );

		// plugin activation for new network site activation
		add_action( 'wpmu_new_blog', array( $this, 'hmembership_activate_new_site' ) );

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

		// exit if already init
		if ( hmembership_get_setting( 'init' ) )
			return;

		// only run once
		hmembership_update_setting( 'init', true );

		// update url - allow another plugin to modify dir
		hmembership_update_setting( 'url', plugin_dir_url( __FILE__ ) );

		// set textdomain
		$this->load_plugin_textdomain();

		// admin
		if ( is_admin() ) {

			// admin pages
			hmembership_include( 'includes/admin/pages/class-admin.php' );
			hmembership_include( 'includes/admin/pages/class-admin-page.php' );
			hmembership_include( 'includes/admin/pages/class-admin-users-page.php' );
			hmembership_include( 'includes/admin/pages/class-admin-settings-page.php' );
			hmembership_include( 'includes/admin/pages/class-admin-settings.php' );
			hmembership_include( 'includes/admin/pages/class-admin-tools-page.php' );

		}

		// action for 3rd party
		do_action( 'hmembership/init' );

	}

	/**
	 * register_assets
	 *
	 * This function will register scripts and styles
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function register_assets() {

		// append styles
		$styles = array(
			'hmembership'			=> array(
				'src'	=> hmembership_get_url( 'assets/css/hmembership-style.css' ),
				'deps'	=> false,
			),
			'hmembership-rtl'		=> array(
				'src'	=> hmembership_get_url( 'assets/css/hmembership-style-rtl.css' ),
				'deps'	=> array( 'hmembership' ),
			),
			'hmembership-front'		=> array(
				'src'	=> hmembership_get_url( 'assets/css/hmembership-front-style.css' ),
				'deps'	=> false,
			),
			'hmembership-front-rtl'	=> array(
				'src'	=> hmembership_get_url( 'assets/css/hmembership-front-style-rtl.css' ),
				'deps'	=> array( 'hmembership' ),
			),
			'jquery-ui'				=> array(
				'src'	=> '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
				'deps'	=> false,
			),
		);

		// append scripts
		$scripts = array(
			'jquery-ui'			=> array(
				'src'	=> 'https://code.jquery.com/ui/1.12.1/jquery-ui.js',
				'deps'	=> array( 'jquery' ),
			),
			'hmembership'		=> array(
				'src'	=> hmembership_get_url( 'assets/js/min/hmembership.min.js' ),
				'deps'	=> array( 'jquery-ui' ),
			),
			'hmembership-front'	=> array(
				'src'	=> hmembership_get_url( 'assets/js/min/hmembership-front.min.js' ),
				'deps'	=> array( 'jquery' ),
			),
		);

		// register styles
		foreach( $styles as $handle => $style ) {
			wp_register_style( $handle, $style[ 'src' ], $style[ 'deps' ], HTMLineMembership_VERSION );
		}

		// register scripts
		foreach( $scripts as $handle => $script ) {
			wp_register_script( $handle, $script[ 'src' ], $script[ 'deps' ], HTMLineMembership_VERSION, true );
		}

	}

	/**
	 * wp_enqueue_scripts
	 *
	 * This function will enque scripts and styles
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function wp_enqueue_scripts() {

		// enqueue styles
		wp_enqueue_style( 'hmembership-front' );

		// rtl
		if ( is_rtl() ) {

			wp_enqueue_style( 'hmembership-front-rtl' );

		}

		// localize hmembership-front
		$translation_arr	= array(
			'settings'		=> array(),
			'strings'		=> array(
				'error'			=> __( 'Error', 'hmembership' ),
				'success'		=> __( 'Request was sent successfuly for user:', 'hmembership' ),
				'failed'		=> __( 'Request failed', 'hmembership' ),
			),
			'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'hmembership-front', '_hmembership_front', $translation_arr );

		// Enqueued script with localized data.
		wp_enqueue_script( 'hmembership-front' );

	}

	/**
	 * admin_enqueue_scripts
	 *
	 * This function will enque admin scripts and styles
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function admin_enqueue_scripts() {

		// enqueue styles
		wp_enqueue_style( 'hmembership' );
		wp_enqueue_style( 'jquery-ui' );

		// rtl
		if ( is_rtl() ) {

			wp_enqueue_style( 'hmembership-rtl' );

		}

		// enqueue scripts
		wp_enqueue_script( 'jquery-ui' );

		// localize hmembership
		$translation_arr	= array(
			'settings'		=> array(
				'export_users'				=> get_option( 'hmembership_export_users', array( 'true' ) ),
			),
			'strings'		=> array(
				'failed_export'				=> __( 'Export process has been failed', 'hmembership' ),
			),
			'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
		);
		wp_localize_script( 'hmembership', '_hmembership', $translation_arr );

		// Enqueued script with localized data.
		wp_enqueue_script( 'hmembership' );

	}

	/**
	 * define
	 *
	 * This function will safely define a constant
	 *
	 * @since		1.0.0
	 * @param		$name (string)
	 * @param		$value (mixed)
	 * @return		N/A
	 */
	public function define( $name, $value = true ) {

		if ( ! defined( $name ) ) {
			define( $name, $value );
		}

	}

	/**
	 * load_plugin_textdomain
	 *
	 * This function will load the textdomain file
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function load_plugin_textdomain() {

		// vars
		$domain = 'hmembership';
		$locale = apply_filters( 'plugin_locale', hmembership_get_locale(), $domain );
		$mofile = $domain . '-' . $locale . '.mo';

		// load from the languages directory first
		load_textdomain( $domain, WP_LANG_DIR . '/plugins/' . $mofile );

		// load from plugin lang folder
		load_textdomain( $domain, hmembership_get_path( 'lang/' . $mofile ) );

	}

	/**
	 * has_setting
	 *
	 * This function will return true if has setting
	 *
	 * @since		1.0.0
	 * @param		$name (string)
	 * @return		(boolean)
	 */
	public function has_setting( $name ) {

		// return
		return isset( $this->settings[ $name ] );

	}

	/**
	 * get_setting
	 *
	 * This function will return a setting value
	 *
	 * @since		1.0.0
	 * @param		$name (string)
	 * @return		(mixed)
	 */
	public function get_setting( $name ) {

		// return
		return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : null;

	}

	/**
	 * update_setting
	 *
	 * This function will update a setting value
	 *
	 * @since		1.0.0
	 * @param		$name (string)
	 * @param		$value (mixed)
	 * @return		N/A
	 */
	public function update_setting( $name, $value ) {

		if ( $name && $value ) {
			$this->settings[ $name ] = $value;
		}

		// return
		return true;

	}

	/**
	 * hmembership_activate
	 *
	 * Actions perform on activation of plugin
	 *
	 * @since		1.0.0
	 * @param		$network_wide (bool)
	 * @return		N/A
	 */
	public function hmembership_activate( $network_wide ) {

		if ( is_multisite() && $network_wide ) {

			// get sites
			$sites = get_sites( array( 'fields' => 'ids' ) );

			if ( $sites ) {
				foreach ( $sites as $site_id ) {

					switch_to_blog( $site_id );

					$this->hmembership_activate_single_site();

					restore_current_blog();

				}
			}

		} else {

			$this->hmembership_activate_single_site();

		}

	}

	/**
	 * hmembership_activate_new_site
	 *
	 * Actions perform on registration of new multisite site
	 *
	 * @since		1.0.0
	 * @param		$site_id (int)
	 * @return		N/A
	 */
	public function hmembership_activate_new_site( $site_id ) {

		if ( is_plugin_active_for_network( $this->settings[ 'basename' ] ) ) {

			switch_to_blog( $site_id );

			$this->hmembership_activate_single_site();

			restore_current_blog();

		}

	}

	/**
	 * hmembership_activate_single_site
	 *
	 * Actions perform on activation of plugin per single site
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function hmembership_activate_single_site() {

		// add the HTMLine Membership user role
		hmembership_users_add_user_role();

		// create HTMLine Membership users DB table
		hmembership_users_create_db_table();

	}

	/**
	 * hmembership_deactivate
	 *
	 * Actions perform on deactivation of plugin
	 *
	 * @since		1.0.0
	 * @param		$network_wide (bool)
	 * @return		N/A
	 */
	public function hmembership_deactivate( $network_wide ) {

		if ( is_multisite() && $network_wide ) {

			// get sites
			$sites		= get_sites( array( 'fields' => 'ids' ) );
			$basename	= $this->settings[ 'basename' ];

			if ( $sites ) {
				foreach ( $sites as $site_id ) {

					switch_to_blog( $site_id );

					// is plugin activated manually
					$active_plugins = get_option( 'active_plugins', array() );

					$key = array_search( $basename, $active_plugins, true );

					if ( false !== $key ) {
						unset( $active_plugins[ $key ] );

						update_option( 'active_plugins', $active_plugins );
					}

					$this->hmembership_deactivate_single_site();

					restore_current_blog();

				}
			}

		} else {

			$this->hmembership_deactivate_single_site();

		}

	}

	/**
	 * hmembership_deactivate_single_site
	 *
	 * Actions perform on deactivation of plugin per single site
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function hmembership_deactivate_single_site() {}

	/**
	 * check_required_plugins
	 *
	 * This function will check if required plugins are activated.
	 * A backup sanity check, in case the plugin is activated in a weird way,
	 * or one of required plugins has been deactivated
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(boolean)
	 */
	private function check_required_plugins() {

		// vars
		$basename = $this->settings[ 'basename' ];

		if ( ! $this->has_required_plugins() ) {

			if ( is_plugin_active( $basename ) ) {

				deactivate_plugins( $basename );
				add_action( 'admin_notices', array( $this, 'admin_required_plugins_notices_error' ) );

				if ( isset( $_GET[ 'activate' ] ) ) {
					unset( $_GET[ 'activate' ] );
				}

			}

			// return
			return false;

		}

		// return
		return true;

	}

	/**
	 * has_required_plugins
	 *
	 * This function will check if required plugins are activated
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(boolean)
	 */
	private function has_required_plugins() {

		// vars
		$required = $this->required_plugins;

		if ( empty( $required ) )
			// return
			return true;

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		foreach ( $required as $key => $plugin ) {

			$plugin = ( ! is_numeric( $key ) ) ? "{$key}/{$plugin}.php" : "{$plugin}/{$plugin}.php";

			if ( ! in_array( $plugin, $active_plugins ) && ! array_key_exists( $plugin, $active_plugins ) )
				// return
				return false;

		}

		// return
		return true;

	}

	/**
	 * admin_required_plugins_notices_error
	 *
	 * This function will add admin error notice
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	private function admin_required_plugins_notices_error() {

		// vars
		$required	= $this->required_plugins;
		$msg		= sprintf( __( "<strong>%s</strong> plugin can't be activated.<br />The following plugins should be installed and activated first:<br />", 'hmembership' ), $this->settings[ 'name' ] );

		foreach ( $required as $key => $plugin ) {

			$path = ( ! is_numeric( $key ) ) ? "{$key}/{$plugin}.php" : "{$plugin}/{$plugin}.php";

			if ( file_exists( plugin_dir_path( __DIR__ ) . $path ) ) {
				$name = get_plugin_data( plugin_dir_path( __DIR__ ) . $path )[ 'Name' ];
			} else {
				$name = $plugin;
			}

			$msg .= "<br />&bull; {$name}";

		}

		$this->admin_notices_error( $msg );

	}

	/**
	 * admin_notices_error
	 *
	 * This function will display admin error notice
	 *
	 * @since		1.0.0
	 * @param		$msg (string)
	 * @return		N/A
	 */
	private function admin_notices_error( $msg ) {

		if ( ! $msg )
			return;

		// vars
		$class = 'notice notice-error is-dismissible';

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $msg );

	}

}

/**
 * hmembership
 *
 * The main function responsible for returning the one true HTMLineMembership instance
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(object)
 */
function hmembership() {

	// globals
	global $hmembership;

	// initialize
	if( ! isset( $hmembership ) ) {

		$hmembership = new HTMLineMembership();
		$hmembership->initialize();

	}

	// return
	return $hmembership;

}

// initialize
hmembership();

endif; // class_exists check