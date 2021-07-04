<?php
/**
 * Property Select Template
 *
 * The template wrapper for the property selector set up page.
 *
 * @package ShareThisShareButtons
 */

?>
<div id="sharethis-property-select-wrap">
	<h4>
		<?php echo esc_html__( 'Select your property to connect to WordPress, or create a new property.', 'sharethis-share-buttons' ); ?>
	</h4>

	<div class="sharethis-login-form">
		<div class="page-content" data-size="small" style="text-align: left;">
			<select id="sharethis-properties">
				<option>No Properties Available</option>
			</select>

			<a id="connect-property" class="login-account st-rc-link" href="#">
				<?php esc_html_e( 'CONNECT PROPERTY', 'sharethis-share-buttons' ); ?>
			</a>

			<a class="login-account st-rc-link" href="#">
				<?php esc_html_e( 'CREATE NEW PROPERTY', 'sharethis-share-buttons' ); ?>
			</a>
		</div>
	</div>
</div>
