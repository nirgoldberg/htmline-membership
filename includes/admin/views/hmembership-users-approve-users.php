<?php
/**
 * Admin users / approve user HTML content
 *
 * @author		Nir Goldberg
 * @package		includes/admin/views
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// extract args
extract( $args );

if ( ! $user_ids ) {

	// no specified users
	echo '<p>' . __( 'No users have been specified for approval.', 'hmembership' ) . '</p>';
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
	if ( in_array( $user[ 'user_status' ], array( '0', '2' ) ) ) {
		$users_ids_to_process[] = $user[ 'ID' ];
	}
}

?>

<form id="approve-users" method="post">

	<?php wp_nonce_field( 'hmembership_update_users_do_approve_nonce', '_wpnonce', false, true ); ?>

	<p><?php echo _n( 'You have specified this user for approval:', 'You have specified these users for approval:', count( $users ), 'hmembership' ); ?></p>

	<ul>
		<?php foreach ( $users as $user ) {

			$allowed			= in_array( $user[ 'ID' ], $users_ids_to_process );
			$not_allowed_msg	= '';

			if ( $allowed ) {
				echo '<input type="hidden" name="hmembership_user_ids[]" value="' . $user[ 'ID' ] . '">';
			} else {

				$user_status = intval( $user[ 'user_status' ] );

				if ( 1 == $user_status ) {
					$not_allowed_msg = __( 'user is already approved', 'hmembership' );
				} elseif ( 3 == $user_status ) {
					$not_allowed_msg = __( 'deleted user cannot be reapproved', 'hmembership' );
				}

			}

			echo	'<li>' .
						__( 'ID', 'hmembership' ) . ' #' . $user[ 'ID' ] . ': <b>' . $user[ 'user_email' ] . '</b>' .
						( ! $allowed ? ' (' . __( 'Will not be processed', 'hmembership' ) . ': ' . $not_allowed_msg . ')' : '' ) .
					'</li>';

		} ?>
	</ul>

	<input type="hidden" name="action" value="do_approve">

	<?php if ( count( $users_ids_to_process ) ) {

		submit_button( __( 'Confirm Approval', 'hmembership' ) );

	} ?>

</form>