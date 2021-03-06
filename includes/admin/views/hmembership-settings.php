<?php
/**
 * Admin settings HTML content
 *
 * @author		Nir Goldberg
 * @package		includes/admin/views
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// extract args
extract( $args );

// vars
$options_group_id = ( ! empty( $tabs ) ) ? $menu_slug . '-' . $active_tab : $menu_slug;

// check if the user have submitted the settings
if ( isset( $_GET[ 'settings-updated' ] ) ) {

	// add settings saved message with the class of "updated"
	add_settings_error( $options_group_id . '_messages', $options_group_id . '_message', __( 'Settings saved.', 'hmembership' ), 'updated' );

}

// display notifications only if outside wordpress settings page
if ( 'options-general.php' != $parent_slug ) {
	settings_errors( $options_group_id . '_messages' );
}

?>

<div class="wrap hmembership-wrap" id="<?php echo $menu_slug; ?>">

	<h1><?php echo $page_title; ?></h1>

	<?php
		// display tabs
		if ( ! empty( $tabs ) ) : ?>

			<h2 class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab_slug => $tab ) :

					// verify section target
					if ( isset( $tab[ 'target' ] ) && ( ( $is_main_site && 'local' == $tab[ 'target' ] ) || ( ! $is_main_site && 'main' == $tab[ 'target' ] ) ) )
						continue;

					?>

					<a class="nav-tab<?php echo ( $active_tab == $tab_slug ) ? ' nav-tab-active' : ''; ?>" href="<?php echo admin_url( "admin.php?page={$menu_slug}&tab={$tab_slug}" ); ?>"><?php echo $tab[ 'title' ]; ?></a>

				<?php endforeach; ?>
			</h2>

		<?php endif;
	?>

	<form method="post" action="options.php">
		<?php

			settings_fields( $options_group_id );

			if ( ! empty( $tabs ) ) {
				// tabs
				foreach ( $tabs[ $active_tab ][ 'sections' ] as $section_slug => $section ) {

					// verify section target
					if ( isset( $section[ 'target' ] ) && ( ( $is_main_site && 'local' == $section[ 'target' ] ) || ( ! $is_main_site && 'main' == $section[ 'target' ] ) ) )
						continue;

					// vars
					$section_id = $active_tab . '-' . $section_slug;

					// load view
					hmembership_get_view( 'hmembership-settings-form', array(
						'options_group_id'	=> $options_group_id,
						'section_id'		=> $section_id,
						'section_slug'		=> $section_slug,
						'section'			=> $section,
					) );

				}
			} elseif ( ! empty( $sections ) ) {
				// no tabs, only sections
				foreach ( $sections as $section_slug => $section ) {

					// verify section target
					if ( isset( $section[ 'target' ] ) && ( ( $is_main_site && 'local' == $section[ 'target' ] ) || ( ! $is_main_site && 'main' == $section[ 'target' ] ) ) )
						continue;

					// vars
					$section_id = $section_slug;

					// load view
					hmembership_get_view( 'hmembership-settings-form', array(
						'options_group_id'	=> $options_group_id,
						'section_id'		=> $section_id,
						'section_slug'		=> $section_slug,
						'section'			=> $section,
					) );

				}
			}

			submit_button( __( 'Save Settings', 'hmembership' ) );

		?>
	</form>

	<?php
		/**
		 * dynamic section template
		 */
	?>
	<div class="hmembership-dynamic-section-template hidden">
		<div class="content <?php echo $section[ 'type' ]; ?>">
			<span class="dashicons dashicons-no-alt remove-section" title="<?php _e( 'Remove Field', 'hmembership' ); ?>"></span>
			<table class="form-table">

				<?php do_settings_fields( $options_group_id, $section_id . '_1' ); ?>

			</table>
		</div>
	</div>

</div><!-- #<?php echo $menu_slug; ?> -->