<?php
/**
 * Admin users HTML content
 *
 * @author		Nir Goldberg
 * @package		includes/admin/views
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// extract args
extract( $args );

// modify $page_title if within an action
if ( isset( $_REQUEST[ 'action' ] ) && '-1' != $_REQUEST[ 'action' ] ) {
	$page_title = hmembership_action()->get_page_title( $_REQUEST[ 'action' ] );
} elseif ( isset( $_REQUEST[ 'action2' ] ) && '-1' != $_REQUEST[ 'action2' ] ) {
	$page_title = hmembership_action()->get_page_title( $_REQUEST[ 'action2' ] );
}

?>

<div class="wrap hmembership-wrap" id="<?php echo $menu_slug; ?>">

	<h1><?php echo $page_title; ?></h1>

	<div class="hmembership-users">

		<?php

			$users_list_table = hmembership_users_get_list_table();

			if ( $users_list_table ) {

				// query, filter, and sort the data
				$users_list_table->prepare_items();

				if ( ! ( isset( $_REQUEST[ 'action' ] ) && '-1' != $_REQUEST[ 'action' ] || isset( $_REQUEST[ 'action2' ] ) && '-1' != $_REQUEST[ 'action2' ] ) ) { ?>

					<form id="users-list-form" method="get">

						<input type="hidden" name="page" value="<?php echo $menu_slug; ?>">

						<?php
							// display search box
							$users_list_table->search_box( __( 'Search Users', 'hmembership' ), 'search' );

							// display filter views
							$users_list_table->views();

							// display data
							$users_list_table->display();
						?>

					</form>

				<?php }

			}

		?>

	</div><!-- .hmembership-users -->

</div><!-- #<?php echo $menu_slug; ?> -->