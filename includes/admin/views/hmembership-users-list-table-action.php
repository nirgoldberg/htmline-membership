<?php
/**
 * Admin users / list table action HTML content
 *
 * @author		Nir Goldberg
 * @package		includes/admin/views
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// extract args
extract( $args );

$action_config = $action[ key( $action ) ];
$action = key( $action );

// check user permissions
$permission	= hmembership_action()->action_allowed( $action_config );

if ( ! $permission ) {

	// not allowed
	echo '<p>' . __( 'You are not allowed to make this request.', 'hmembership' ) . '</p>';
	return;

}

if ( ! $user_ids ) {

	// no specified users
	echo '<p>' . sprintf( __( 'No users have been specified for %s.', 'hmembership' ), $action_config[ 'strings' ][ 'str2' ] ) . '</p>';
	return;

}

// refer to user_ids as array
$user_ids = (array) $user_ids;

// get users
$users = hmembership_users_get_users_by_id( $user_ids );

if ( ! $users ) {

	// error
	echo '<p>' . _n( 'Error getting user information.', 'Error getting users information.', count( $user_ids ), 'hmembership' ) . '</p>';
	return;

}

// store the user IDs which are might be processed according to their status
$user_ids_to_process = array();

foreach ( $users as $user ) {

	$user_status	= hmembership_status()->get_status_by_code( $user[ 'user_status' ] );
	$can_action		= hmembership_status()->can( $action );

	if ( $user_status && in_array( $user_status, $can_action ) ) {
		$user_ids_to_process[] = $user[ 'ID' ];
	}

}

?>

<form id="<?php echo $action_config[ 'table-action' ]; ?>" method="post">

	<?php wp_nonce_field( 'hmembership_update_users_' . $action_config[ 'do_action' ] . '_nonce', '_wpnonce', false, true ); ?>

	<p><?php echo sprintf( _n( 'You have specified this user for %1$s:', 'You have specified these users for %1$s:', count( $users ), 'hmembership' ), $action_config[ 'strings' ][ 'str2' ] ); ?></p>

	<ul>
		<?php foreach ( $users as $user ) {

			$allowed			= in_array( $user[ 'ID' ], $user_ids_to_process );
			$not_allowed_msg	= '';

			if ( $allowed ) {
				echo '<input type="hidden" name="hmembership_user_ids[]" value="' . $user[ 'ID' ] . '">';
			} else {

				$user_status_label = hmembership_status()->get_label_by_code( $user[ 'user_status' ] );
				$not_allowed_msg = sprintf( __( '%s user cannot be %s', 'hmembership' ), $user_status_label, $action_config[ 'strings' ][ 'str3' ] );

			}

			echo	'<li>' .
						__( 'ID', 'hmembership' ) . ' #' . $user[ 'ID' ] . ': <b>' . $user[ 'user_email' ] . '</b>' .
						( ! $allowed ? ' (' . __( 'Will not be processed', 'hmembership' ) . ': ' . $not_allowed_msg . ')' : '' ) .
					'</li>';

		} ?>
	</ul>

	<input type="hidden" name="action" value="<?php echo $action_config[ 'do_action' ]; ?>">

	<?php if ( count( $user_ids_to_process ) ) {

		submit_button( sprintf( __( 'Confirm %s', 'hmembership' ), $action_config[ 'strings'][ 'str1' ] ) );

	} ?>

</form>