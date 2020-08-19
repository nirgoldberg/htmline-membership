<?php
/**
 * Admin settings page filters, actions, variables and includes
 *
 * @author		Nir Goldberg
 * @package		includes/admin/pages
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Admin_Settings_Page' ) ) :

class HTMLineMembership_Admin_Settings_Page {

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
	 * Main site indicator
	 *
	 * @var (boolean)
	 */
	protected $is_main_site;

	/**
	 * __construct
	 *
	 * This function will initialize the admin settings page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function __construct() {

		// main site indicator
		$this->is_main_site = is_main_site();

		// initialize
		$this->initialize();

		// actions
		$this->add_action( 'init',			array( $this, 'init' ), 11 );
		$this->add_action( 'admin_menu',	array( $this, 'admin_menu' ) );
		$this->add_action( 'admin_init',	array( $this, 'setup_sections' ) );
		$this->add_action( 'admin_init',	array( $this, 'setup_fields' ) );

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
	 * This function will initialize the admin settings page
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	protected function initialize() {

		// settings
		$this->$settings = array(

			// slugs
			'parent_slug'			=> '',
			'menu_slug'				=> '',

			// titles
			'page_title'			=> '',
			'menu_title'			=> '',

			// tabs
			/**
			 * tabs structure:
			 *
			 * '[tab slug]'	=> array(
			 *		'target'	=> [main/local],
			 * 		'title'		=> [tab title],
			 * 		'sections'	=> array(
			 * 			'[section slug]'	=> array(
			 *				'target'		=> [main/local],
			 * 				'type'			=> [static/dynamic],
			 * 				'title'			=> [section title],
			 * 				'description'	=> [section description],
			 * 			),
			 *			...
			 * 		),
			 * ),
			 * ...
			 */
			'tabs'					=> array(),
			'active_tab'			=> '',

			// sections
			/**
			 * sections structure:
			 *
			 * '[section slug]'	=> array(
			 *		'target'		=> [main/local],
			 *		'type'			=> [static/dynamic],
			 * 		'title'			=> [section title],
			 * 		'description'	=> [section description],
			 * ),
			 * ...
			 */
			'sections'				=> array(),

			// fields
			/**
			 * fields structure:
			 *
			 * array(
			 *		'target'		=> [main/local],
			 *		'uid'			=> [field slug],
			 *		'label'			=> [field label],
			 *		'label_for'		=> [field label_for],
			 *		'tab'			=> [tab slug],
			 *		'section'		=> [section slug],
			 *		'type'			=> [field type: text/password/number/email/textarea/editor/select/multiselect/radio/checkbox],
			 *		'placeholder'	=> [field placeholder],
			 *		'options'		=> [array of field options: slugs and labels],
			 *		'default'		=> [array of field option slug],
			 *		'supplimental'	=> [field description text],
			 *		'helper'		=> [field helper text],
			 * ),
			 * ...
			 */
			'fields'				=> array(),

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

		if ( empty( $this->settings[ 'fields' ] ) )
			return;

		// add action
		add_action( $tag, $function_to_add, $priority, $accepted_args );

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

		// api
		hmembership_include( 'includes/api/api-dynamic-setting-sections.php' );

		// classes
		hmembership_include( 'includes/admin/fields/class-admin-field.php' );
		hmembership_include( 'includes/admin/fields/class-admin-text-field.php' );
		hmembership_include( 'includes/admin/fields/class-admin-email-field.php' );
		hmembership_include( 'includes/admin/fields/class-admin-textarea-field.php' );
		hmembership_include( 'includes/admin/fields/class-admin-editor-field.php' );
		hmembership_include( 'includes/admin/fields/class-admin-select-field.php' );
		hmembership_include( 'includes/admin/fields/class-admin-radio-field.php' );

	}

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
		add_submenu_page(
			$this->settings[ 'parent_slug' ],
			$this->settings[ 'page_title' ],
			$this->settings[ 'menu_title' ],
			$capability,
			$this->settings[ 'menu_slug' ],
			array( $this, 'html' )
		);

	}

	/**
	 * html
	 *
	 * This function will display the admin settings page content
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function html() {

		// vars
		$view = array(

			'parent_slug'	=> $this->settings[ 'parent_slug' ],
			'menu_slug'		=> $this->settings[ 'menu_slug' ],
			'page_title'	=> $this->settings[ 'page_title' ],
			'tabs'			=> $this->settings[ 'tabs' ],
			'active_tab'	=> $this->settings[ 'active_tab' ],
			'sections'		=> $this->settings[ 'sections' ],
			'is_main_site'	=> $this->is_main_site,

		);

		// set active tab
		if ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] != '' && array_key_exists( $_GET[ 'tab' ], $view[ 'tabs' ] ) ) {
			$view[ 'active_tab' ] = $_GET[ 'tab' ];
		}

		// load view
		hmembership_get_view( 'hmembership-settings', $view );

	}

	/**
	 * setup_sections
	 *
	 * This function will setup admin settings page sections
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function setup_sections() {

		// vars
		$menu_slug	= $this->settings[ 'menu_slug' ];
		$tabs		= $this->settings[ 'tabs' ];
		$sections	= $this->settings[ 'sections' ];

		// setup sections
		if ( ! empty( $tabs ) ) {
			// tabs
			foreach ( $tabs as $tab_slug => $tab ) {

				foreach ( $tab[ 'sections' ] as $section_slug => $section ) {

					// vars
					$options_group_id	= $menu_slug . '-' . $tab_slug;
					$section_id			= $tab_slug . '-' . $section_slug;

					// add settings section
					$this->setup_section( $section_id, $options_group_id, $section_slug );

				}

			}
		} elseif ( ! empty( $sections ) ) {
			// no tabs, only sections
			foreach ( $sections as $section_slug => $section ) {

				// vars
				$options_group_id	= $menu_slug;
				$section_id			= $section_slug;

				// add settings section
				$this->setup_section( $section_id, $options_group_id, $section_slug );

			}
		}

	}

	/**
	 * setup_section
	 *
	 * This function will setup admin settings page section
	 *
	 * @since		1.0.0
	 * @param		$section_id (string)
	 * @param		$options_group_id (string)
	 * @param		$section_slug (string)
	 * @return		N/A
	 */
	protected function setup_section( $section_id, $options_group_id, $section_slug ) {

		// is dynamic section
		$dynamic = $this->is_dynamic_section( $section_slug );

		if ( $dynamic ) {

			// get section count
			$count = (int) get_option( 'hmembership_section_' . $section_slug, 1 );
			$count = $count ? $count : 1;

			// setup dynamic sections
			for ( $i=1 ; $i<=$count ; $i++ ) {

				// setup section
				$this->add_settings_section( $section_id . '_' . $i, $options_group_id );

			}

		} else {

			// setup section
			$this->add_settings_section( $section_id, $options_group_id );

		}

	}

	/**
	 * add_settings_section
	 *
	 * This function will setup admin settings page section
	 *
	 * @since		1.0.0
	 * @param		$section_id (string)
	 * @param		$options_group_id (string)
	 * @return		N/A
	 */
	protected function add_settings_section( $section_id, $options_group_id ) {

		// add settings section
		add_settings_section(
			$section_id,
			false,
			false,
			$options_group_id
		);

	}

	/**
	 * setup_fields
	 *
	 * This function will setup admin settings page fields
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function setup_fields() {

		// vars
		$menu_slug	= $this->settings[ 'menu_slug' ];
		$tabs		= $this->settings[ 'tabs' ];
		$sections	= $this->settings[ 'sections' ];
		$fields		= $this->settings[ 'fields' ];

		// setup fields
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {

				// verify field target
				if ( isset( $field[ 'target' ] ) && ( ( $this->is_main_site && 'local' == $field[ 'target' ] ) || ( ! $this->is_main_site && 'main' == $field[ 'target' ] ) ) )
					continue;

				// vars
				if ( ! empty( $tabs ) ) {

					// tabs
					$options_group_id	= $menu_slug . '-' . $field[ 'tab' ];
					$section_id			= $field[ 'tab' ] . '-' . $field[ 'section' ];

				} elseif ( ! empty( $sections ) ) {

					// no tabs, only sections
					$options_group_id	= $menu_slug;
					$section_id			= $field[ 'section' ];

				}

				// is dynamic section
				$dynamic = $this->is_dynamic_section( $field[ 'section' ] );

				if ( $dynamic ) {

					// get section count
					$count = (int) get_option( 'hmembership_section_' . $field[ 'section' ], 1 );
					$count = $count ? $count : 1;

					for ( $i=1 ; $i<=$count ; $i++ ) {

						// field index
						$field[ 'index' ] = $i;
						$field[ 'label_for' ] = $field[ 'uid' ] . '_' . $i;

						// add settings field
						$this->setup_field( $options_group_id, $section_id . '_' . $i, $field );

						// add actions to update dynamic section count
						add_action( 'add_option_' . $field[ 'uid' ], 'hmembership_add_dynamic_section_option', 10, 2 );
						add_action( 'update_option_' . $field[ 'uid' ], 'hmembership_update_dynamic_section_option', 10, 3 );

					}

				} else {

					// add settings field
					$this->setup_field( $options_group_id, $section_id, $field );

				}

			}
		}

	}

	/**
	 * setup_field
	 *
	 * This function will setup admin settings page field
	 *
	 * @since		1.0.0
	 * @param		$options_group_id (string)
	 * @param		$section_id (string)
	 * @param		$field_args (array)
	 * @return		N/A
	 */
	protected function setup_field( $options_group_id, $section_id, $field_args ) {

		// create field instance
		switch ( $field_args[ 'type' ] ) {

			case 'text':
			case 'password':
			case 'number':
				$field = new HTMLineMembership_Admin_Text_Field( $field_args );
				break;

			case 'email':
				$field = new HTMLineMembership_Admin_Email_Field( $field_args );
				break;

			case 'textarea':
				$field = new HTMLineMembership_Admin_Textarea_Field( $field_args );
				break;

			case 'editor':
				$field = new HTMLineMembership_Admin_Editor_Field( $field_args );
				break;

			case 'select':
			case 'multiselect':
				$field = new HTMLineMembership_Admin_Select_Field( $field_args );
				break;

			case 'radio':
			case 'checkbox':
				$field = new HTMLineMembership_Admin_Radio_Field( $field_args );
				break;

		}

		// add settings field
		add_settings_field(
			$field_args[ 'uid' ],
			$field_args[ 'label' ],
			array( $field, 'display_field' ),
			$options_group_id,
			$section_id,
			array(
				'label_for' => $field_args[ 'label_for' ],
			)
		);

		// register setting
		register_setting(
			$options_group_id,
			$field_args[ 'uid' ],
			array(
				'sanitize_callback' => array( $field, 'sanitize' ),
			)
		);

	}

	/**
	 * is_dynamic_section
	 *
	 * This function will check whether a section is dynamic
	 *
	 * @since		1.0.0
	 * @param		$section_slug (string)
	 * @return		(bool)
	 */
	protected function is_dynamic_section( $section_slug ) {

		// vars
		$settings = $this->settings;

		if ( isset( $settings[ 'tabs' ] ) && $settings[ 'tabs' ] ) {
			foreach ( $settings[ 'tabs' ] as $tab ) {
				if ( isset( $tab[ 'sections' ] ) && isset( $tab[ 'sections' ][ $section_slug ] ) && 'dynamic' == $tab[ 'sections' ][ $section_slug ][ 'type' ] ) {
					// return
					return true;
				}
			}
		}

		if ( isset( $settings[ 'sections' ] ) && isset( $settings[ 'sections' ][ $section_slug ] ) && 'dynamic' == $settings[ 'sections' ][ $section_slug ][ 'type' ] ) {
			// return
			return true;
		}

		// return
		return false;

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