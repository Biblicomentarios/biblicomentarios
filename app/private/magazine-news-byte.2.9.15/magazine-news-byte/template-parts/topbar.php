<?php
// Get Content
global $hoot_data;
hoot_set_data( 'topbar_left', is_active_sidebar( 'hoot-topbar-left' ) );
hoot_set_data( 'topbar_right', is_active_sidebar( 'hoot-topbar-right' ) );

// Template modification Hook
do_action( 'magnb_before_topbar' );

// Display Topbar
$hoot_topbar_left = hoot_data()->topbar_left;
$hoot_topbar_right = hoot_data()->topbar_right;
if ( !empty( $hoot_topbar_left ) || !empty( $hoot_topbar_right ) ) :

	?>
	<div <?php hoot_attr( 'topbar', '', 'inline-nav js-search social-icons-invert hgrid-stretch' ); ?>>
		<div class="hgrid">
			<div class="hgrid-span-12">

				<div class="topbar-inner table<?php if ( !empty( $hoot_topbar_left ) && !empty( $hoot_topbar_right ) ) echo ' topbar-parts'; ?>">
					<?php if ( $hoot_topbar_left ): ?>
						<?php $topbarid = ( $hoot_topbar_right ) ? 'left' : 'center'; ?>
						<div id="topbar-<?php echo $topbarid; ?>" class="table-cell-mid topbar-part">
							<?php dynamic_sidebar( 'hoot-topbar-left' ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $hoot_topbar_right ): ?>
						<?php $topbarid = ( $hoot_topbar_left ) ? 'right' : 'center'; ?>
						<div id="topbar-<?php echo $topbarid; ?>" class="table-cell-mid topbar-part">
							<?php dynamic_sidebar( 'hoot-topbar-right' ); ?>
						</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>
	<?php

endif;

// Template modification Hook
do_action( 'magnb_after_topbar' );