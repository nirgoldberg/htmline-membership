<?php
/**
 * Admin page screen options
 *
 * @author		Nir Goldberg
 * @package		includes/admin/pages
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class HTMLineMembership_Admin_Page_Screen_Options {

	/**
	 * Screen ID
	 *
	 * @var (string)
	 */
	private $screen;

	/**
	 * Screen option name
	 *
	 * @var (string)
	 */
	private $option_name;

	/**
	 * Screen default per page
	 *
	 * @var (int)
	 */
	private $default_per_page;

	/**
	 * __construct
	 *
	 * @since		1.0.0
	 * @param		$screen (string)
	 * @param		$option_name (string)
	 * @param		$default_per_page (int)
	 * @return		N/A
	 */
	public function __construct( $screen, $option_name, $default_per_page ) {

		// initialize
		$this->screen				= $screen;
		$this->option_name			= $option_name;
		$this->default_per_page		= $default_per_page;

		// hooks
		$this->add_action( 'admin_head',						array( $this, 'add_screen_options' ) );
		$this->add_filter( 'set-screen-option',					array( $this, 'set_screen_options_filter' ), 10, 3 );
		$this->add_filter( "set_screen_option_{$option_name}",	array( $this, 'set_screen_options_filter' ), 10, 3 );

	}

	/**
	 * add_action
	 *
	 * This function will check settings validity before adding the action
	 *
	 * @since		1.0.0
	 * @param		$tag (string)
	 * @param		$function_to_add (string)
	 * @param		$priority (int)
	 * @param		$accepted_args (int)
	 * @return		N/A
	 */
	private function add_action( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

		if ( empty( $this->option_name ) || empty( $this->default_per_page ) )
			return;

		// add action
		add_action( $tag, $function_to_add, $priority, $accepted_args );

	}

	/**
	 * add_filter
	 *
	 * This function will check settings validity before adding the filter
	 *
	 * @since		1.0.0
	 * @param		$tag (string)
	 * @param		$function_to_add (string)
	 * @param		$priority (int)
	 * @param		$accepted_args (int)
	 * @return		N/A
	 */
	private function add_filter( $tag = '', $function_to_add = '', $priority = 10, $accepted_args = 1 ) {

		if ( empty( $this->option_name ) || empty( $this->default_per_page ) )
			return;

		// add filter
		add_filter( $tag, $function_to_add, $priority, $accepted_args );

	}

	/**
	 * add_screen_options
	 *
	 * This function will add per_page option to screen options
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function add_screen_options() {

		// verify current screen
		$current_screen = get_current_screen();

		if ( ! $current_screen || $current_screen->id !== $this->screen )
			return;

		$args = array(
			'label'		=> __( 'Number of items per page:', 'hmembership' ),
			'default'	=> $this->default_per_page,
			'option'	=> $this->option_name,
		);

		add_screen_option( 'per_page', $args );

	}

	/**
	 * set_screen_options_filter
	 *
	 * This function will set per_page option value
	 *
	 * @since		1.0.0
	 * @param		$keep (bool)
	 * @param		$option (string)
	 * @param		$value (int)
	 * @return		(int)
	 */
	public function set_screen_options_filter( $keep, $option, $value ) {

		// return
		return $option === $this->option_name ? $value : $keep;

	}

}