<?php
/**
 * Share Button Settings Template
 *
 * The template wrapper for the share buttons settings page.
 *
 * @package ShareThisShareButtons
 */

?>
<div id="detectadblock">
	<div class="adBanner">
	</div>
</div>
<div id="adblocker-notice" class="notice notice-error is-dismissible">
	<p>
		<?php echo esc_html__( 'It appears you have an ad blocker enabled. To avoid affecting this plugin\'s functionality, please disable while using its admin configurations and registrations. Thank you.', 'sharethis-share-buttons' ); ?>
	</p>
</div>
<hr class="wp-header-end" style="display:none;">
<div class="wrap sharethis-wrap">
	<?php echo wp_kses_post( $description ); ?>

	<form action="options.php" method="post">
		<?php
		settings_fields( $this->menu_slug . '-share-buttons' );
		do_settings_sections( $this->menu_slug . '-share-buttons' );
		submit_button( esc_html__( 'Update', 'sharethis-share-buttons' ) );
		?>
	</form>
</div>
