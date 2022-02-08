<?php
/**
 * BuddyBoss - Groups Loop
 *
 * This template can be overridden by copying it to yourtheme/buddypress/groups/groups-loop.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 */

bp_nouveau_before_loop(); ?>

<?php if ( bp_get_current_group_directory_type() ) : ?>
	<div class="bp-feedback info">
	<span class="bp-icon" aria-hidden="true"></span>
	<p class="current-group-type"><?php bp_current_group_directory_type_message(); ?></p>
	</div>
<?php endif; ?>

<?php

	$cover_class		= ( bp_disable_group_cover_image_uploads() || ( function_exists('bb_platform_group_element_enable') && !bb_platform_group_element_enable('cover-images') ) ) ? 'bb-cover-disabled' : 'bb-cover-enabled';
	$meta_privacy		= function_exists('bb_platform_group_element_enable') && !bb_platform_group_element_enable('group-privacy') ? 'meta-privacy-hidden' : '';
	$meta_group_type	= function_exists('bb_platform_group_element_enable') && !bb_platform_group_element_enable('group-type') ? 'meta-group-type-hidden' : '';
	$group_members		= function_exists('bb_platform_group_element_enable') && !bb_platform_group_element_enable('members') ? 'group-members-hidden' : '';
	$group_alignment	= function_exists('bb_platform_group_grid_style') ? bb_platform_group_grid_style( 'left' )  : 'left';

?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<ul id="groups-list" class="<?php bp_nouveau_loop_classes(); ?> <?php echo $cover_class . ' ' . $group_alignment ?> groups-dir-list">

	<?php
	while ( bp_groups() ) :
		bp_the_group();
		?>

		<li <?php bp_group_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups">
			<div class="list-wrap">

				<?php if ( ! bp_disable_group_cover_image_uploads() ) { ?>
					<?php if ( ! function_exists('bb_platform_group_element_enable') || ( function_exists('bb_platform_group_element_enable') && bb_platform_group_element_enable('cover-images') ) ) { ?>
					<?php
					$group_cover_image_url = bp_attachments_get_attachment(
						'url',
						array(
							'object_dir' => 'groups',
							'item_id'    => bp_get_group_id(),
						)
					);
					$has_default_cover     = function_exists( 'bb_attachment_get_cover_image_class' ) ? bb_attachment_get_cover_image_class( bp_get_group_id(), 'group' ) : '';
					?>
						<div class="bs-group-cover only-grid-view <?php echo esc_attr( $has_default_cover ); ?>"><a href="<?php bp_group_permalink(); ?>"><img src="<?php echo esc_url( $group_cover_image_url ); ?>"></a></div>

					<?php } ?>
				<?php } ?>

				<?php if ( ! bp_disable_group_avatar_uploads() ) : ?>
					<?php if ( ! function_exists('bb_platform_group_element_enable') || ( function_exists('bb_platform_group_element_enable') && bb_platform_group_element_enable('avatars') ) ) { ?>
					<div class="item-avatar">
						<a href="<?php bp_group_permalink(); ?>" class="group-avatar-wrap"><?php bp_group_avatar( bp_nouveau_avatar_args() ); ?></a>
					</div>
					<?php } ?>
				<?php endif; ?>

				<div class="item">

					<div class="group-item-wrap">

						<div class="item-block">

							<h2 class="list-title groups-title"><?php bp_group_link(); ?></h2>

								<div class="item-meta-wrap">

									<?php if ( bp_nouveau_group_has_meta() ) : ?>

										<p class="item-meta group-details <?php echo $meta_privacy . ' ' . $meta_group_type; ?>">
										<?php
											$meta = bp_nouveau_get_group_meta();
											echo $meta['status'];
										?>
										</p>
									<?php endif; ?>

									<?php if ( ! function_exists('bb_platform_group_element_enable') || ( function_exists('bb_platform_group_element_enable') && bb_platform_group_element_enable('last-activity') ) ) { ?>
										<p class="last-activity item-meta"><?php
											printf(
												/* translators: %s = last activity timestamp (e.g. "active 1 hour ago") */
												__( 'active %s', 'buddyboss' ),
												bp_get_group_last_active()
											);
										?></p>
									<?php } ?>

								</div>

						</div>

						<?php if ( ! function_exists('bb_platform_group_element_enable') || ( function_exists('bb_platform_group_element_enable') && bb_platform_group_element_enable('group-descriptions') ) ) { ?>
							<div class="item-desc group-item-desc only-list-view"><?php bp_group_description_excerpt( false, 150 ); ?></div>
						<?php } ?>

					</div>

					<?php bp_nouveau_groups_loop_item(); ?>

					<div class="group-footer-wrap <?php echo $group_members; ?>">
						<div class="group-members-wrap">
							<?php bb_groups_loop_members(); ?>
						</div>
						<?php if ( ! function_exists('bb_platform_group_element_enable') || ( function_exists('bb_platform_group_element_enable') && bb_platform_group_element_enable('join-buttons') ) ) { ?>
							<div class="groups-loop-buttons footer-button-wrap"><?php bp_nouveau_groups_loop_buttons(); ?></div>
						<?php } ?>
					</div>

				</div>


			</div>
		</li>

	<?php endwhile; ?>

	</ul>

	<!-- Leave Group confirmation popup -->
	<div class="bb-leave-group-popup" style="display: none">
		<transition name="modal">
			<div class="modal-mask bb-white bbm-model-wrap">
				<div class="modal-wrapper">
					<div class="modal-container">
						<header class="bb-model-header">
							<h4><span class="target_name"><?php _e( 'Leave Group', 'buddyboss' ); ?></span></h4>
							<a class="bb-close-leave-group bb-model-close-button" href="#">
								<span class="bb-icon bb-icon-close"></span>
							</a>
						</header>
						<div class="bb-leave-group-content">
							<p><?php _e( 'Are you sure you want to leave ', 'buddyboss' ); ?><span class="bb-group-name"></span></p>
						</div>
						<footer class="bb-model-footer flex align-items-center">
							<a class="bb-close-leave-group" href="#"><?php _e( 'Cancel', 'buddyboss' ); ?></a>
							<a class="button push-right bb-confirm-leave-group" href="#"><?php _e( 'Confirm', 'buddyboss' ); ?></a>
						</footer>

					</div>
				</div>
			</div>
		</transition>
	</div> <!-- .bb-leave-group-popup -->

	<?php bp_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php bp_nouveau_user_feedback( 'groups-loop-none' ); ?>

<?php endif; ?>

<?php
bp_nouveau_after_loop();
