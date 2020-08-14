<?php
/**
 * Admin settings form HTML content
 *
 * @author		Nir Goldberg
 * @package		includes/admin/views
 * @version		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// extract args
extract( $args );

// get dynamic section count
$count = get_option( 'hmembership_section_' . $section_slug );

?>

<div class="hmembership-admin-box <?php echo ( 'dynamic' == $section[ 'type' ] && ! $count ) ? 'no-sections' : ''; ?>">

	<?php if ( $section[ 'title' ] || $section[ 'description' ] ) : ?>

		<div class="title">

			<?php
				echo $section[ 'title' ]		? '<h3>' . $section[ 'title' ] . '</h3>'					: '';
				echo $section[ 'description' ]	? '<p class="desc">' . $section[ 'description' ] . '</p>'	: '';
			?>

		</div>

	<?php endif; ?>

	<?php if ( 'dynamic' == $section[ 'type' ] ) { ?>

		<div class="hmembership-admin-box-content-sortable" data-section="<?php echo 'hmembership_section_' . $section_slug; ?>">

		<?php for ( $i=1 ; $i<=$count ; $i++ ) { ?>

			<div class="content <?php echo $section[ 'type' ]; ?>">

				<span class="dashicons dashicons-no-alt remove-section" title="<?php _e( 'Remove Field', 'hmembership' ); ?>"></span>

				<table class="form-table">

					<?php do_settings_fields( $options_group_id, $section_id . '_' . $i ); ?>

				</table>

			</div>

		<?php }

	} else { ?>

		<div class="content <?php echo $section[ 'type' ]; ?>">

			<table class="form-table">

				<?php do_settings_fields( $options_group_id, $section_id ); ?>

			</table>

		</div>

	<?php }

	if ( 'dynamic' == $section[ 'type' ] ) { ?>

		</div><a class="button button-primary add-section"><?php _e( 'Add Field', 'hmembership' ); ?></a>

	<?php } ?>

</div><!-- .hmembership-admin-box -->