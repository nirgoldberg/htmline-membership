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
	 * Shortcode defaults
	 *
	 * @var (array)
	 */
	private $defaults;

	/**
	 * Form status
	 *
	 * @var (bool)
	 */
	private $status;

	/**
	 * Admin Email
	 *
	 * @var (string)
	 */
	private $admin_email;

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

		// shortcode
		$this->shortcode	= 'hmembership-form';
		$this->defaults		= array();

		// status
		$status				= get_option( 'hmembership_user_registration_form_status', array( 'true' ) );
		$this->status		= is_array( $status ) && in_array( 'true', $status );

		// admin email
		$admin_email		= get_option( 'hmembership_admin_email' );
		$this->admin_email	= $admin_email ? $admin_email : get_option( 'admin_email ');

		// api
		hmembership_include( 'includes/api/api-form.php' );

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

		// add shortcode
		add_shortcode( $this->shortcode, array( $this, 'add_shortcode' ) );

		// action for 3rd party
		do_action( 'hmembership_form/init' );

	}

	/**
	 * add_shortcode
	 *
	 * This function will initiate form shortcode
	 *
	 * @since		1.0.0
	 * @param		$atts (array)
	 * @return		(string)
	 */
	public function add_shortcode( $atts ) {

		// exit if module is inactive
		if ( ! $this->status )
			return;

		// vars
		$fields = self::get_fields();
		$output = '';

		if ( ! $fields )
			return;

		if ( is_user_logged_in() )
			return $this->logged_in_user();

		// attributes
		$atts = shortcode_atts( $this->defaults, $atts );

		// user email field
		$label = get_option( 'hmembership_user_email_field_label', __( 'Email Address', 'hmembership' ) );
		$label = $label ? $label : __( 'Email Address', 'hmembership' );

		array_unshift( $fields, array(
			'id'		=> 'hmembership_user_email',
			'label'		=> $label,
			'type'		=> 'email',
			'options'	=> '',
			'default'	=> '',
			'required'	=> 'true',
		) );

		// form
		$output .=
			'<div class="hmembership-form">' .
			wp_nonce_field( 'hmembership_form_nonce', '_wpnonce', false, false ) .
			'<table><tbody>';

		foreach ( $fields as $field ) {
			$output .= $this->get_field_input( $field );
		}

		$output .=
			'</tbody></table>' .
			'<p class="submit">' .
				'<button class="button hmembership-form-button">' . __( 'Register', 'hmembership' ) . '</button>' .
				'<span class="ajax-loading dashicons dashicons-update"></span>' .
			'</p>' .
			'<div class="result"></div>' .
			'</div><!-- .hmembership-form -->';

		// return
		return apply_filters( 'hmembership_form/add_shortcode', $output, $atts );

	}

	/**
	 * logged_in_user
	 *
	 * This function will return user logged in info in case of user is logged in
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(string)
	 */
	private function logged_in_user() {

		// vars
		global $current_user;
		$output = '';

		$output .=	'<p>' .
						sprintf( __( 'Logged in as <b>%s</b>, ', 'hmembership' ), $current_user->user_login ) .
						'<a href="' . wp_logout_url( get_permalink() ) . '" title="' . __( 'Log out of this account', 'hmembership' ) . '">' . __( 'Log out &raquo;', 'hmembership' ) . '</a>' .
					'</p>';

		// return
		return $output;

	}

	/**
	 * get_field_input
	 *
	 * This function will return a form field input
	 *
	 * @since		1.0.0
	 * @param		$field (array)
	 * @return		(string)
	 */
	private function get_field_input( $field ) {

		// vars
		$field_input = '';

		switch ( $field[ 'type' ] ) {

			case 'text':
			case 'password':
			case 'number':
			case 'email':
				$field_input = $this->get_text_field_input( $field );
				break;

			case 'textarea':
				$field_input = $this->get_textarea_field_input( $field );
				break;

			case 'select':
			case 'multiselect':
				$field_input = $this->get_select_field_input( $field );
				break;

			case 'radio':
			case 'checkbox':
				$field_input = $this->get_radio_field_input( $field );
				break;

		}

		// return
		return $field_input;

	}

	/**
	 * get_text_field_input
	 *
	 * This function will return a text/password/number/email form field input
	 *
	 * @since		1.0.0
	 * @param		$field (array)
	 * @return		(string)
	 */
	private function get_text_field_input( $field ) {

		// vars
		$output = '';

		$output .=	sprintf( '<tr class="%3$s %4$s"><th scope="row"><label for="%1$s">%2$s</label>%5$s</th><td><input name="%1$s" id="%1$s" type="%3$s" value="" /></td></tr>',
						$field[ 'id' ],
						$field[ 'label' ],
						$field[ 'type' ],
						$field[ 'required' ] ? 'required' : '',
						$field[ 'required' ] ? '<span> *</span>' : ''
					);

		// return
		return apply_filters( 'hmembership_form/get_text_field_input', $output, $field );

	}

	/**
	 * get_textarea_field_input
	 *
	 * This function will return a textarea form field input
	 *
	 * @since		1.0.0
	 * @param		$field (array)
	 * @return		(string)
	 */
	private function get_textarea_field_input( $field ) {

		// vars
		$output = '';

		$output .=	sprintf( '<tr class="textarea %3$s"><th scope="row"><label for="%1$s">%2$s</label>%4$s</th><td><textarea name="%1$s" id="%1$s" rows="5" cols="50"></textarea></td></tr>',
						$field[ 'id' ],
						$field[ 'label' ],
						$field[ 'required' ] ? 'required' : '',
						$field[ 'required' ] ? '<span> *</span>' : ''
					);

		// return
		return apply_filters( 'hmembership_form/get_textarea_field_input', $output, $field );

	}

	/**
	 * get_select_field_input
	 *
	 * This function will return a select/multiselect form field input
	 *
	 * @since		1.0.0
	 * @param		$field (array)
	 * @return		(string)
	 */
	private function get_select_field_input( $field ) {

		// vars
		$output = '';

		if ( ! empty ( $field[ 'options' ] ) && is_array( $field[ 'options' ] ) ) {

			// vars
			$attributes				= '';
			$options_markup			= '';

			foreach ( $field[ 'options' ] as $key => $label ) {
				$options_markup .=	sprintf( '<option value="%s" %s>%s</option>',
										$key,
										selected( $field[ 'default' ], $key, false ),
										$label
									);
			}

			if ( 'multiselect' === $field[ 'type' ] ) {
				$attributes = ' multiple="multiple" ';
			}

			$output .=	sprintf( '<tr class="select %5$s"><th scope="row"><label for="%1$s">%2$s</label>%6$s</th><td><select name="%1$s[]" id="%1$s" %3$s>%4$s</select></td></tr>',
							$field[ 'id' ],
							$field[ 'label' ],
							$attributes,
							$options_markup,
							$field[ 'required' ] ? 'required' : '',
							$field[ 'required' ] ? '<span> *</span>' : ''
						);

		}

		// return
		return apply_filters( 'hmembership_form/get_select_field_input', $output, $field );

	}

	/**
	 * get_radio_field_input
	 *
	 * This function will return a radio/checkbox form field input
	 *
	 * @since		1.0.0
	 * @param		$field (array)
	 * @return		(string)
	 */
	private function get_radio_field_input( $field ) {

		// vars
		$output = '';

		if ( ! empty ( $field[ 'options' ] ) && is_array( $field[ 'options' ] ) ) {

			// vars
			$options_markup	= '';
			$iterator		= 0;

			foreach ( $field[ 'options' ] as $key => $label ) {

				$iterator++;
				$options_markup .=	sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /><span>%5$s</span></label><br/>',
										$field[ 'id' ],
										$field[ 'type' ],
										$key,
										checked( $field[ 'default' ], $key, false ),
										$label,
										$iterator
									);

			}

			$output .=	sprintf( '<tr class="fieldset %4$s"><th scope="row"><label for="%1$s">%2$s</label>%5$s</th><td><fieldset>%3$s</fieldset></td></tr>',
							$field[ 'id' ],
							$field[ 'label' ],
							$options_markup,
							$field[ 'required' ] ? 'required' : '',
							$field[ 'required' ] ? '<span> *</span>' : ''
						);

		}

		// return
		return apply_filters( 'hmembership_form/get_radio_field_input', $output, $field );

	}

	/**
	 * get_fields
	 *
	 * This function will initiate form shortcode
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		(array)
	 */
	public static function get_fields() {

		// vars
		$fields_count		= get_option( 'hmembership_section_user_custom_fields' );
		$field_labels		= get_option( 'hmembership_user_custom_field_label' );
		$field_types		= get_option( 'hmembership_user_custom_field_type' );
		$field_options		= get_option( 'hmembership_user_custom_field_options' );
		$field_defaults		= get_option( 'hmembership_user_custom_field_default' );
		$field_required		= get_option( 'hmembership_user_custom_field_required' );
		$field_column		= get_option( 'hmembership_user_custom_field_column' );
		$fields				= array();

		if ( ! $fields_count )
			return $fields;

		for( $i=0 ; $i<$fields_count ; $i++ ) {

			if ( ! isset( $field_labels[ $i ] ) || ! isset( $field_types[ $i ] ) || ! isset( $field_options[ $i ] ) || ! isset( $field_defaults[ $i ] ) || ! isset( $field_required[ $i ] ) || ! isset( $field_column[ $i ] ) )
				continue;

			// build options and default
			$options = '';
			$default = '';

			if ( $field_options[ $i ] ) {

				$options_arr	= explode( "\r\n", $field_options[ $i ] );
				$options		= array();

				foreach ( $options_arr as $key => $value ) {
					$options[ sanitize_title_with_dashes( $value ) ] = $value;
				}

				$default = sanitize_title_with_dashes( $field_defaults[ $i ] );

			}

			$fields[] = array(
				'id'		=> 'hmembership-' . $i . '-' . sanitize_title_with_dashes( $field_labels[ $i ] ),
				'label'		=> $field_labels[ $i ],
				'type'		=> $field_types[ $i ][0],
				'options'	=> $options,
				'default'	=> $default,
				'required'	=> is_array( $field_required[ $i ] ) && in_array( 'true', $field_required[ $i ] ),
				'column'	=> is_array( $field_column[ $i ] ) && in_array( 'true', $field_column[ $i ] ),
			);

		}

		// return
		return $fields;

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