<?php
/**
 * Admin users / delete user HTML content
 *
 * @author		Nir Goldberg
 * @package		includes/admin/views
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// extract args
extract( $args );

// check user permissions
$delete_permission	= get_option( 'hmembership_delete_users', array( 'true' ) );
$delete_permission	= $delete_permission && in_array( 'true', $delete_permission );

if ( ! $delete_permission ) {

	// not allowed
	echo '<p>' . __( 'You are not allowed to make this request.', 'hmembership' ) . '</p>';
	return;

}

if ( ! $user_ids ) {

	// no specified users
	echo '<p>' . __( 'No users have been specified for removal.', 'hmembership' ) . '</p>';
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
$users_ids_to_process = array();

foreach ( $users as $user ) {
	if ( in_array( $user[ 'user_status' ], array( '0', '2', '3' ) ) ) {
		$users_ids_to_process[] = $user[ 'ID' ];
	}
}

?>

<form id="delete-users" method="post">

	<?php wp_nonce_field( 'hmembership_update_users_do_delete_nonce', '_wpnonce', false, true ); ?>

	<p><?php echo _n( 'You have specified this user for removal:', 'You have specified these users for removal:', count( $users ), 'hmembership' ); ?></p>

	<ul>
		<?php foreach ( $users as $user ) {

			$allowed = in_array( $user[ 'ID' ], $users_ids_to_process );

			if ( $allowed ) {
				echo '<input type="hidden" name="hmembership_user_ids[]" value="' . $user[ 'ID' ] . '">';
			}

			echo	'<li>' .
						__( 'ID', 'hmembership' ) . ' #' . $user[ 'ID' ] . ': <b>' . $user[ 'user_email' ] . '</b>' .
						( ! $allowed ? ' (' . __( 'Will not be processed', 'hmembership' ) . ': ' . __( 'approved user cannot be deleted', 'hmembership' ) . ')' : '' ) .
					'</li>';

		} ?>
	</ul>

	<input type="hidden" name="action" value="do_delete">

	<?php if ( count( $users_ids_to_process ) ) {

		submit_button( __( 'Confirm Removal', 'hmembership' ) );

	} ?>

</form>