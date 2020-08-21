<?php
/**
 * Helper functions
 *
 * @author		Nir Goldberg
 * @package		includes/api
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * hmembership_define
 *
 * Alias of hmembership()->define()
 *
 * @since		1.0.0
 * @param		$name (string)
 * @param		$value (mixed)
 * @return		N/A
 */
function hmembership_define( $name, $value ) {

	hmembership()->define( $name, $value );

}

/**
 * hmembership_has_setting
 *
 * Alias of hmembership()->has_setting()
 *
 * @since		1.0.0
 * @param		$name (string)
 * @return		(boolean)
 */
function hmembership_has_setting( $name = '' ) {

	// return
	return hmembership()->has_setting( $name );

}

/**
 * hmembership_get_setting
 *
 * This function will return a value from the settings array found in the hmembership object
 *
 * @since		1.0.0
 * @param		$name (string)
 * @param		$default (mixed)
 * @return		(mixed)
 */
function hmembership_get_setting( $name, $default = null ) {

	// vars
	$settings = hmembership()->settings;

	// find setting
	$setting = hmembership_maybe_get( $settings, $name, $default );

	// filter for 3rd party
	$setting = apply_filters( "hmembership/settings/{$name}", $setting );

	// return
	return $setting;

}

/**
 * hmembership_update_setting
 *
 * Alias of hmembership()->update_setting()
 *
 * @since		1.0.0
 * @param		$name (string)
 * @param		$value (mixed)
 * @return		N/A
 */
function hmembership_update_setting( $name, $value ) {

	// return
	return hmembership()->update_setting( $name, $value );

}

/**
 * hmembership_get_path
 *
 * This function will return the path to a file within the plugin folder
 *
 * @since		1.0.0
 * @param		$path (string) The relative path from the root of the plugin folder
 * @return		(string)
 */
function hmembership_get_path( $path = '' ) {

	// return
	return HTMLineMembership_PATH . $path;

}

/**
 * hmembership_get_url
 *
 * This function will return the url to a file within the plugin folder
 *
 * @since		1.0.0
 * @param		$path (string) The relative path from the root of the plugin folder
 * @return		(string)
 */
function hmembership_get_url( $path = '' ) {

	// define HTMLineMembership_URL to optimize performance
	hmembership_define( 'HTMLineMembership_URL', hmembership_get_setting( 'url' ) );

	// return
	return HTMLineMembership_URL . $path;

}

/**
 * hmembership_include
 *
 * This function will include a file
 *
 * @since		1.0.0
 * @param		$file (string) The file name to be included
 * @return		N/A
 */
function hmembership_include( $file ) {

	$path = hmembership_get_path( $file );

	if ( file_exists( $path ) ) {
		include_once( $path );
	}

}

/**
 * hmembership_get_view
 *
 * This function will load in a file from the 'includes/admin/views' folder and allow variables to be passed through
 *
 * @since		1.0.0
 * @param		$view_name (string)
 * @param		$args (array)
 * @return		N/A
 */
function hmembership_get_view( $view_name = '', $args = array() ) {

	// vars
	$path = hmembership_get_path( "includes/admin/views/{$view_name}.php" );

	if( file_exists( $path ) ) {
		include( $path );
	}

}

/**
 * hmembership_maybe_get
 *
 * This function will return a variable if it exists in an array
 *
 * @since		1.0.0
 * @param		$array (array) The array to look within
 * @param		$key (key) The array key to look for
 * @param		$default (mixed) The value returned if not found
 * @return		(mixed)
 */
function hmembership_maybe_get( $array = array(), $key = 0, $default = null ) {

	// return
	return isset( $array[ $key ] ) ? $array[ $key ] : $default;

}

/**
 * hmembership_get_locale
 *
 * This function is a wrapper for the get_locale() function
 *
 * @since		1.0.0
 * @param		N/A
 * @return		(string)
 */
function hmembership_get_locale() {

	// return
	return is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();

}

/**
 * hmembership_result_log
 *
 * This function will log API activity
 *
 * @since		1.0.0
 * @param		$type (string) Log type
 * @param		$data (array)
 * @param		&$result (array)
 * @return		N/A
 */
function hmembership_result_log( $type, $data, &$result ) {

	if ( ! $result[ $type ] ) {
		$result[ $type ] = array();
	}

	$result[ $type ][] = $data;

}