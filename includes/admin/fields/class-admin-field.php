<?php
/**
 * Admin settings field
 *
 * @author		Nir Goldberg
 * @package		includes/admin/fields
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'HTMLineMembership_Admin_Field' ) ) :

class HTMLineMembership_Admin_Field {

	/**
	 * Instances array
	 *
	 * @var (array)
	 */
	protected static $_instances = array();

	/**
	 * Field
	 *
	 * @var (array)
	 */
	protected $field;

	/**
	 * __construct
	 *
	 * This function will initialize the admin settings field
	 *
	 * @since		1.0.0
	 * @param		$field (array)
	 * @return		N/A
	 */
	public function __construct( $field ) {

		/**
		 * field structure:
		 *
		 * array(
		 *		'target'		=> [main/local],
		 *		'uid'			=> [field slug],
		 *		'label'			=> [field label],
		 *		'label_for'		=> [field label_for],
		 *		'tab'			=> [tab slug],
		 *		'section'		=> [section slug],
		 *		'type'			=> [field type: text/password/number/email/textarea/select/multiselect/radio/checkbox],
		 *		'placeholder'	=> [field placeholder],
		 *		'options'		=> [array of field options: slugs and labels],
		 *		'default'		=> [array of field option slug],
		 *		'supplimental'	=> [field description text],
		 *		'helper'		=> [field helper text],
		 * )
		 */
		$this->field = $field;

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
	 * display_field
	 *
	 * This function will display the field
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	public function display_field() {}

	/**
	 * display_field_meta
	 *
	 * This function will display the field supplimental and helper tags
	 *
	 * @since		1.0.0
	 * @param		N/A
	 * @return		N/A
	 */
	protected function display_field_meta() {

		// supplimental text
		if ( isset( $this->field[ 'supplimental' ] ) ) {
			printf( '<p class="description">%s</p>', $this->field[ 'supplimental' ] );
		}

		// helper text
		if ( isset( $this->field[ 'helper' ] ) ) {
			printf( '<span class="helper"> %s</span>', $this->field[ 'helper' ] );
		}

	}

	/**
	 * sanitize
	 *
	 * This function will sanitize the field value before saving to DB
	 *
	 * @since		1.0.0
	 * @param		$value (mixed)
	 * @return		(array)
	 */
	public function sanitize( $value ) {

		// return
		return apply_filters( $this->field[ 'type' ] . '/sanitize', $value, $value );

	}

	/**
	 * get_field
	 *
	 * This function will return field args
	 *
	 * @since		1.0.0
	 * @param		$field_uid (string)
	 * @return		(mixed)
	 */
	public static function get_field( $field_uid ) {

		$fields = self::get_instances();

		if ( ! $fields )
			return false;

		foreach ( $fields as $field ) {
			if ( $field_uid == $field->field[ 'uid' ] ) {

				// return
				return $field->field;

			}
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