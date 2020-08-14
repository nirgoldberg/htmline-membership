<?php
/**
 * Admin tools HTML content
 *
 * @author		Nir Goldberg
 * @package		includes/admin/views
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// extract args
extract( $args );

?>

<div class="hmembership-admin-box">

	<div class="content">

		<?php

			// load tool view
			hmembership_get_view( 'hmembership-tools-' . $active_tab );

		?>

	</div>

</div><!-- .hmembership-admin-box -->