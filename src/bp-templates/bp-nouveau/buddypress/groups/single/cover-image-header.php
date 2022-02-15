<?php
/**
 * BuddyBoss - Groups Cover Photo Header.
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/single/cover-image-header.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 */

$group_link               = bp_get_group_permalink();
$admin_link               = trailingslashit( $group_link . 'admin' );
$group_avatar             = trailingslashit( $admin_link . 'group-avatar' );
$group_cover_link         = trailingslashit( $admin_link . 'group-cover-image' );
$group_cover_width        = bb_get_group_cover_image_width();
$group_cover_height       = bb_get_group_cover_image_height();
$group_cover_image        = bp_attachments_get_attachment(
	'url',
	array(
		'object_dir' => 'groups',
		'item_id'    => bp_get_group_id(),
	)
);
$has_cover_image          = '';
$has_cover_image_position = '';
$has_default_cover        = bb_attachment_get_cover_image_class( bp_get_group_id(), 'group' );
?>

<div id="cover-image-container">

	<?php
	if ( ! empty( $group_cover_image ) ) {
		$group_cover_position = groups_get_groupmeta( bp_get_current_group_id(), 'bp_cover_position', true );
		$has_cover_image      = ' has-cover-image';
		if ( '' !== $group_cover_position ) {
			$has_cover_image_position = ' has-position';
		}
	}
	?>

	<div id="header-cover-image" class="<?php echo esc_attr( 'cover-' . $group_cover_height . ' width-' . $group_cover_width . $has_cover_image_position . $has_cover_image . $has_default_cover ); ?>">
		<?php
		if ( bp_group_use_cover_image_header() ) {

			if ( ! empty( $group_cover_image ) ) {
				echo '<img class="header-cover-img" src="' . esc_url( $group_cover_image ) . '"' . ( '' !== $group_cover_position ? ' data-top="' . esc_attr( $group_cover_position ) . '"' : '' ) . ( '' !== $group_cover_position ? ' style="top: ' . esc_attr( $group_cover_position ) . 'px"' : '' ) . ' alt="" />';
			}
			?>
			<?php if ( bp_is_item_admin() ) { ?>
				<a href="<?php echo esc_url( $group_cover_link ); ?>" class="link-change-cover-image bp-tooltip" data-bp-tooltip-pos="right" data-bp-tooltip="<?php esc_attr_e( 'Change Cover Photo', 'buddyboss' ); ?>">
				<i class="bb-icon-edit-thin"></i>
			</a>
			<?php } ?>

			<?php if ( ! empty( $group_cover_image ) && bp_is_item_admin() && bp_attachments_get_group_has_cover_image( bp_get_group_id() ) ) { ?>
				<a href="#" class="position-change-cover-image bp-tooltip" data-bp-tooltip-pos="right" data-bp-tooltip="<?php esc_attr_e( 'Reposition Cover Photo', 'buddyboss' ); ?>">
					<i class="bb-icon-move"></i>
				</a>
				<div class="header-cover-reposition-wrap">
					<a href="#" class="button small cover-image-cancel"><?php esc_html_e( 'Cancel', 'buddyboss' ); ?></a>
					<a href="#" class="button small cover-image-save"><?php esc_html_e( 'Save Changes', 'buddyboss' ); ?></a>
					<span class="drag-element-helper"><i class="bb-icon-menu"></i><?php esc_html_e( 'Drag to move cover photo', 'buddyboss' ); ?></span>
					<img src="<?php echo esc_url( $group_cover_image ); ?>" alt="<?php esc_html_e( 'Cover photo', 'buddyboss' ); ?>" />
				</div>
			<?php } ?>
		<?php } ?>
	</div>

	<?php $class = bp_disable_group_cover_image_uploads() ? 'bb-disable-cover-img' : 'bb-enable-cover-img'; ?>

	<div id="item-header-cover-image" class="item-header-wrap <?php echo esc_attr( $class ); ?>">
		<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
			<div id="item-header-avatar">
				<?php if ( bp_is_item_admin() ) { ?>
					<a href="<?php echo esc_url( $group_avatar ); ?>" class="link-change-profile-image bp-tooltip" data-bp-tooltip-pos="down" data-bp-tooltip="<?php esc_attr_e( 'Change Group Photo', 'buddyboss' ); ?>">
						<i class="bb-icon-edit-thin"></i>
					</a>
				<?php } ?>
				<?php bp_group_avatar(); ?>
			</div><!-- #item-header-avatar -->
		<?php endif; ?>

		<?php if ( ! bp_nouveau_groups_front_page_description() ) : ?>
			<div id="item-header-content">

				<?php if ( function_exists( 'bp_enable_group_hierarchies' ) && bp_enable_group_hierarchies() ) : ?>
					<?php
					$parent_id = bp_get_parent_group_id();
					if ( 0 !== $parent_id ) {
						?>
						<div class="bp-group-parent-wrap flex align-items-center">
							<?php bp_group_list_parents(); ?>
							<div class="bp-parent-group-title-wrap">
								<a class="bp-parent-group-title" href="<?php echo esc_url( bp_get_group_permalink( groups_get_group( $parent_id ) ) ); ?>"><?php echo wp_kses_post( bp_get_group_name( groups_get_group( $parent_id ) ) ); ?></a>
								<i class="bb-icon-chevron-right"></i>
								<span class="bp-current-group-title"><?php echo wp_kses_post( bp_get_group_name() ); ?></span>
							</div>
						</div>
					<?php } ?>
				<?php endif; ?>

				<div class="flex align-items-center bp-group-title-wrap">
					<h2 class="bb-bp-group-title"><?php echo wp_kses_post( bp_get_group_name() ); ?></h2>
					<p class="bp-group-meta bp-group-type"><?php echo wp_kses( bp_nouveau_group_meta()->status, array( 'span' => array( 'class' => array() ) ) ); ?></p>
				</div>

				<?php echo isset( bp_nouveau_group_meta()->group_type_list ) ? bp_nouveau_group_meta()->group_type_list : ''; ?>
				<?php bp_nouveau_group_hook( 'before', 'header_meta' ); ?>

				<?php if ( bp_nouveau_group_has_meta_extra() ) : ?>
					<div class="item-meta">
						<?php echo bp_nouveau_group_meta()->extra; ?>
					</div><!-- .item-meta -->
				<?php endif; ?>

				<p class="last-activity item-meta">
					<?php
					printf(
						/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
						esc_attr__( 'active %s', 'buddyboss' ),
						wp_kses_post( bp_get_group_last_active() )
					);
					?>
				</p>

				<?php if ( ! bp_nouveau_groups_front_page_description() && bp_nouveau_group_has_meta( 'description' ) ) : ?>
						<div class="group-description">
							<?php bp_group_description(); ?>
						</div><!-- //.group_description -->
				<?php endif; ?>

				<p class="bp-group-meta bp-group-type"><?php echo wp_kses( bp_nouveau_group_meta()->status, array( 'span' => array( 'class' => array() ) ) ); ?></p>


				<div class="group-actions-wrap" >
					<?php
					bp_get_template_part( 'groups/single/parts/header-item-actions' );
					?>
						<div class="group-actions-absolute">
					<?php
						if ( function_exists( 'bp_get_group_status_description' ) ) { ?>
							<p class="highlight bp-group-meta bp-group-status bp-tooltip" data-bp-tooltip-pos="up" data-bp-tooltip-length="large" data-bp-tooltip="<?php echo esc_attr( bp_get_group_status_description() ); ?>"><?php echo wp_kses( bp_nouveau_group_meta()->status, array( 'span' => array( 'class' => array() ) ) ); ?></p>
						<?php }
						bp_nouveau_group_header_buttons();
						bb_nouveau_group_header_bubble_buttons();
					?>
						</div>
				</div>

			</div><!-- #item-header-content -->
		<?php endif; ?>

	</div><!-- #item-header-cover-image -->

</div><!-- #cover-image-container -->

<!-- Group description popup -->
<div class="bb-action-popup" style="display: none">
	<transition name="modal">
		<div class="modal-mask bb-white bbm-model-wrap">
			<div class="modal-wrapper">
				<div class="modal-container">
					<header class="bb-model-header">
						<h4><span class="target_name"><?php echo esc_html__( 'Group Description', 'buddyboss' ); ?></span></h4>
						<a class="bb-close-action-popup bb-model-close-button" href="#">
							<span class="bb-icon bb-icon-close"></span>
						</a>
					</header>
					<div class="bb-action-popup-content">
						<p><?php bp_group_description(); ?></p>
					</div>
				</div>
			</div>
		</div>
	</transition>
</div> <!-- .bb-action-popup -->

