<?php
/**
 * Form functions
 *
 * @author		Nir Goldberg
 * @package		includes/api
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * hmembership_form_submission
 *
 * @since		1.0.0
 * @param		N/A
 * @return		N/A
 */
function hmembership_form_submission() {

	// verify nonce
	if ( ! wp_verify_nonce( $_REQUEST[ 'nonce' ], 'hmembership_form_nonce' ) ) {
		exit();
	}

	// vars
	$result = array(
		'errors'	=> array(),
		'data'		=> array(),
	);

	// register hmembership user
	hmembership_form_register_user( $result );

	// check if action was fired via Ajax call. If yes, JS code will be triggered, else the user will be redirected to the post page
	if ( ! empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && 'xmlhttprequest' == strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) {

		$result = json_encode( $result );
		echo $result;

	}
	else {

		header( "Location: " . $_SERVER[ "HTTP_REFERER" ] );

	}

	// die
	die();

}
add_action( 'wp_ajax_nopriv_hmembership_form_submission', 'hmembership_form_submission' );

/**
 * hmembership_form_register_user
 *
 * @since		1.0.0
 * @param		&$result (array)
 * @return		N/A
 */
function hmembership_form_register_user( &$result ) {

	// vars
	$fields = $_REQUEST[ 'fields' ];

	if ( ! $fields ) {

		// log - empty request
		hmembership_result_log( 'errors', array(
			'code'			=> '1',
			'description'	=> __( 'Empty request', 'hmembership' ),
		), $result );

		// return
		return;

	}

	// sanitize fields
	hmembership_form_sanitize_fields( $fields );

	// validate fields
	if ( ! hmembership_form_validate_fields( $fields, $result ) )
		return;

	// insert hmembership user to DB
	hmembership_form_insert_user( $fields, $result );

}

/**
 * hmembership_form_sanitize_fields
 *
 * @since		1.0.0
 * @param		&$fields (array)
 * @return		N/A
 */
function hmembership_form_sanitize_fields( &$fields ) {

	foreach ( $fields as $id => $field ) {

		// vars
		$type	= $field[ 'type' ];
		$value	= $field[ 'value' ];

		switch ( $type ) {

			case 'text':
				$fields[ $id ][ 'value' ] = sanitize_text_field( $value );
				break;

			case 'number':
				$fields[ $id ][ 'value' ] = is_numeric( $value ) ? $value : '';
				break;

			case 'email':
				$fields[ $id ][ 'value' ] = filter_var( $value, FILTER_SANITIZE_EMAIL );
				break;

			case 'textarea':
				$fields[ $id ][ 'value' ] = sanitize_textarea_field( $value );
				break;

			case 'select':
			case 'radio':
			case 'checkbox':
				if ( is_array( $value ) ) {
					foreach ( $value as $key => $val ) {
						$fields[ $id ][ 'value' ][ $key ] = filter_var( $val, FILTER_SANITIZE_STRING );
					}
				} else {
					$fields[ $id ][ 'value' ] = filter_var( $value, FILTER_SANITIZE_STRING );
				}

		}

	}

}

/**
 * hmembership_form_validate_fields
 *
 * @since		1.0.0
 * @param		$fields (array)
 * @param		&$result (array)
 * @return		(bool)
 */
function hmembership_form_validate_fields( $fields, &$result ) {

	// var
	$valid = true;

	foreach ( $fields as $id => $field ) {

		// vars
		$type		= $field[ 'type' ];
		$label		= $field[ 'label' ];
		$value		= $field[ 'value' ];
		$required	= $field[ 'required' ];

		if ( 'true' == $required && ! $value ) {

			// log - required field
			hmembership_result_log( 'errors', array(
				'code'			=> '2',
				'description'	=> sprintf( __( "<b><i>%s</i></b> can't be empty", 'hmembership' ), $label ),
			), $result );

			// not valid
			$valid = false;

		}

		if ( 'email' == $type && $value && ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {

			// log - email not valid
			hmembership_result_log( 'errors', array(
				'code'			=> '3',
				'description'	=> sprintf( __( '<b><i>%s</i></b> is not a valid Email address', 'hmembership' ), $value ),
			), $result );

			// not valid
			$valid = false;

		}

	}

	// return
	return $valid;

}

/**
 * hmembership_form_insert_user
 *
 * @since		1.0.0
 * @param		$fields (array)
 * @param		&$result (array)
 * @return		N/A
 */
function hmembership_form_insert_user( $fields, &$result ) {

	// vars
	global $wpdb;
	$users_table	= $wpdb->prefix . HTMLineMembership_USERS_TABLE;
	$user_email		= $fields[ 'hmembership_user_email' ][ 'value' ];

	if ( hmembership_users_get_user( $user_email ) ) {

		// log - user already exist
		hmembership_result_log( 'errors', array(
			'code'			=> '4',
			'description'	=> sprintf( __( 'User <b><i>%s</i></b> is already exists', 'hmembership' ), $user_email ),
		), $result );

		// return
		return;

	}

	unset( $fields[ 'hmembership_user_email' ] );

	$insert = 	$wpdb->insert( $users_table, array(
					'user_email'		=> $user_email,
					'user_registered'	=> current_time( 'mysql' ),
					'user_info'			=> serialize( $fields ),
				));

	if ( ! $insert ) {

		// log
		hmembership_result_log( 'errors', array(
			'code'			=> '5',
			'description'	=> sprintf( __( 'SQL Error: %s', 'hmembership' ), $wpdb->last_error ),
		), $result );

	} else {

		// log
		hmembership_result_log( 'data', $user_email, $result );

	}

}