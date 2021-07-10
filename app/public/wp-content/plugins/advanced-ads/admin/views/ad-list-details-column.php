<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
		<?php if ( ! empty( $type ) ) :
			// display image, if this is the image type.
			if ( 'image' === $ad->type ) {
				$image_id = ( isset( $ad->output['image_id'] ) ) ? $ad->output['image_id'] : '';
				$types[ $ad->type ]->create_image_icon( $image_id );
			}
			?><p><strong class="advads-ad-type"><?php echo esc_attr( $type ); ?></strong></p>
			<?php
		endif;
		if ( ! empty( $size ) ) :
			?>
			<p class="advads-ad-size"><?php echo esc_attr( $size ); ?></p>
												 <?php
		endif;
		?>
		<?php if ( $privacy_overriden ) : ?>
			<p><?php esc_html_e( 'Consent disabled', 'advanced-ads' ); ?></p>
		<?php endif; ?>
		<?php
		do_action( 'advanced-ads-ad-list-details-column-after', $ad );
		?>
	</div>
</fieldset>
