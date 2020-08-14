<?php
/**
 * Admin tools / export users HTML content
 *
 * @author		Nir Goldberg
 * @package		includes/admin/views
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// vars
$nonce = wp_create_nonce( 'export_users' );

?>

<p class="description"><?php _e( 'Export users', 'hmembership' ); ?></p>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary hmembership-export-users" value="<?php _e( 'Export Users', 'hmembership' ); ?>" data-nonce="<?php echo $nonce; ?>">
	<span class="ajax-loading dashicons dashicons-update"></span>
</p>

<div class="export-users-summary"></div>

<div class="export-users-result"></div>