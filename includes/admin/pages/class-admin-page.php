<?php
/**
 * Admin page filters, actions, variables and includes
 *
 * @author		Nir Goldberg
 * @package		includes/admin/pages
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Admin_Page' ) ) :

class HTMLineMembership_Admin_Page {

	/**
	 * Instances array
	 *
	 * @var (array)
	 */
	protected static $_instances = array();

	/**
	 * Settings array
	 *
	 * @var (array)
	 */
	protected $settings;

	/**
	 * Page hook
	 *
	 * @var (mixed)
	 */
	protected $page_hook;

	/**
	 * __construct
	 *
	 * This function will initialize the admin submenu page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function __construct() {

		// initialize
		$this->initialize();

		// actions
		add_action( 'init', array( $this, 'screen_options' ),	11 );
		add_action( 'init', array( $this, 'init' ),				11 );
		$this->add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// store instance
		self::$_instances[] = $this;

	}

	/**
	 * __destruct
	 *
	 * This function will unset the stored instance
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function __destruct() {

		// unset stored instance
		unset( self::$_instances[ array_search( $this, self::$_instances, true ) ] );

	}

	/**
	 * initialize
	 *
	 * This function will initialize the admin submenu page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	protected function initialize() {

		$this->settings = array(

			// slugs
			'parent_slug'	=> '',
			'menu_slug'		=> '',

			// titles
			'page_title'	=> '',
			'menu_title'	=> '',

			// tabs
			/**
			 * tabs structure:
			 *
			 * '[tab slug]'	=> array(
			 * 		'title'			=> [tab title],
			 * 		'permission'	=> array(),
			 * ),
			 * ...
			 */
			'tabs'			=> array(),
			'active_tab'	=> '',

		);

	}

	/**
	 * add_action
	 *
	 * This function will check page settings validity before adding the action
	 *
	 * @since		1.0.0
	 * @param		$tag (string)
	 * @param		$function_to_add (string)
	 * @param		$priority (int)
	 * @param		$accepted_args (int)
	 * @return		N/A
	 */
	protected function add_action( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

		if ( empty( $this->settings[ 'parent_slug' ] ) || empty( $this->settings[ 'menu_slug' ] ) || empty( $this->settings[ 'page_title' ] ) || empty( $this->settings[ 'menu_title' ] ) )
			return;

		// add action
		add_action( $tag, $function_to_add, $priority, $accepted_args );

	}

	/**
	 * screen_options
	 *
	 * This function will handle page screen options
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function screen_options() {

		// classes
		hmembership_include( 'includes/admin/pages/class-admin-page-screen-options.php' );

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
	public function init() {}

	/**
	 * admin_menu
	 *
	 * This function will add HTMLine Membership submenu item to the WP admin
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function admin_menu() {

		// exit if no show_admin
		if ( ! hmembership_get_setting( 'show_admin' ) )
			return;

		// vars
		$capability = hmembership_get_setting( 'capability' );

		// add submenu page
		$this->page_hook = add_submenu_page(
			$this->settings[ 'parent_slug' ],
			$this->settings[ 'page_title' ],
			$this->settings[ 'menu_title' ],
			$capability,
			$this->settings[ 'menu_slug' ],
			array( $this, 'html' )
		);

		// load-$this->page_hook action
		add_action( 'load-' . $this->page_hook, array( $this, 'load_page' ) );

	}

	/**
	 * html
	 *
	 * This function will display the page content
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function html() {

		// vars
		$view = array(

			'version'		=> hmembership_get_setting( 'version' ),
			'menu_slug'		=> $this->settings[ 'menu_slug' ],
			'page_title'	=> $this->settings[ 'page_title' ],
			'tabs'			=> $this->settings[ 'tabs' ],
			'active_tab'	=> $this->settings[ 'active_tab' ],

		);

		// set active tab
		if ( ! empty( $_GET[ 'tab' ] ) && array_key_exists( $_GET[ 'tab' ], $view[ 'tabs' ] ) ) {
			$view[ 'active_tab' ] = $_GET[ 'tab' ];
		}

		// load view
		hmembership_get_view( $this->settings[ 'menu_slug' ], $view );

	}

	/**
	 * load_page
	 *
	 * This function will be triggered when menu page is loaded
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function load_page() {}

	/**
	 * admin_notice
	 *
	 * This function will display admin notice
	 *
	 * @since		1.0.0
	 * @param		$type (string)
	 * @param		$msg (string)
	 * @return		N/A
	 */
	protected function admin_notice( $type, $msg ) {

		// vars
		$class = "notice $type is-dismissible";

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $msg );

	}

	/**
	 * invalid_nonce_redirect
	 *
	 * This function will die when nonce check fails
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	protected function invalid_nonce_redirect() {

		wp_die( __( 'Invalid Nonce', 'hmembership' ),
				__( 'Error', 'hmembership' ),
				array(
					'response'	=> 403,
					'back_link'	=> esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST[ 'page' ] ) ), admin_url( 'admin.php' ) ) ),
				)
		);

	}

	/**
	 * get_instances
	 *
	 * This function will return all class instances
	 *
	 * @since		1.0.0
	 * @param		$include_subclasses (boolean) Optionally include subclasses in returned set
	 * @return		(array)
	 */
	protected static function get_instances( $include_subclasses = false ) {

		// vars
		$instances = array();

		foreach ( self::$_instances as $instance ) {

			// vars
			$class = get_class( $instance );

			if ( $instance instanceof $class ) {
				if ( $include_subclasses || ( get_class( $instance ) === $class ) ) {
					$instances[] = $instance;
				}
			}

		}

		// return
		return $instances;

	}

}

endif; // class_exists check