<?php
/**
 * Connection Template
 *
 * The template wrapper for the property id connection page.
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
<div class="wrap sharethis-connection-wrap">
	<div class="sharethis-setup-logo">
		<img src="<?php echo esc_url( "{$this->plugin->dir_url}/assets/sharethis-setup-logo.png" ); ?>">
	</div>

	<div id="sharethis-steps">
		<?php
		switch ( $page ) {
			case 'first':
				include( "{$this->plugin->dir_path}/templates/general/setup/step-one.php" );
				break;
			case 'second':
				include( "{$this->plugin->dir_path}/templates/general/setup/step-two.php" );
				break;
			case 'third':
				include( "{$this->plugin->dir_path}/templates/general/setup/step-three.php" );
				break;
			case 'login':
				include( "{$this->plugin->dir_path}/templates/general/setup/login.php" );
				break;
			case 'property':
				include( "{$this->plugin->dir_path}/templates/general/setup/property-select.php" );
				break;
		}
		?>
	</div>
	<div class="st-loading-gif">
		<img src="<?php echo esc_url( "{$this->plugin->dir_url}/assets/st-loading.gif" ); ?>">
	</div>
</div>
