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

?>

<div class="wrap hmembership-wrap" id="<?php echo $menu_slug; ?>">

	<h1><?php echo $page_title; ?></h1>

	<div class="hmembership-users">

		<?php

			$users_list_table = hmembership_get_users_list_table();

			if ( $users_list_table ) {

				// query, filter, and sort the data
				$users_list_table->prepare_items(); ?>

				<form id="users-list-form" method="get">

					<input type="hidden" name="page" value="<?php echo $menu_slug; ?>">

					<?php
						// display search box
						$users_list_table->search_box( __( 'Search Users', 'hmembership' ), 'search' );

						// display data
						$users_list_table->display();
					?>

				</form>

			<?php }

		?>

	</div><!-- .hmembership-users -->

</div><!-- #<?php echo $menu_slug; ?> -->