<?php
/**
 * Login Template
 *
 * The template wrapper for the login set up page.
 *
 * @package ShareThisShareButtons
 */

?>
<a href="?page=sharethis-general" class="st-rc-back" type="button">BACK</a>
<div id="sharethis-login-wrap">
	<h4>
		<?php echo esc_html__( 'Login to your account.', 'sharethis-share-buttons' ); ?>
	</h4>

	<div class="sharethis-login-form">
		<div class="page-content" data-size="small" style="text-align: left;">
			<span>
				<div class="c-red text-center lh-18 h-18"></div>
			</span>

			<div class="input">
				<label name="email" class="">Email</label>

				<input type="text" id="st-login-email" name="email">
			</div>
			<div class="" style="height: 16px; width: 100%;"></div>
			<div class="input " style="margin-bottom: 10px;">
				<label name="password">Password</label>

				<input type="password" id="st-login-password" name="password">
			</div>

			<a class="login-account st-rc-link" href="#">
				<?php esc_html_e( 'LOGIN', 'sharethis-share-buttons' ); ?>
			</a>

			<p>
				Need an account? <a href="?page=sharethis-general">Get started!</a>
			</p>
		</div>
	</div>
</div>
<div id="sharethis-property-select-wrap">
	<h4>
		<?php echo esc_html__( 'Select your property to connect to WordPress, or create a new property.', 'sharethis-share-buttons' ); ?>
	</h4>

	<div class="sharethis-login-form property-connect">
		<div class="page-content" data-size="small">
			<select id="sharethis-properties">
				<option>No Properties Available</option>
			</select>

			<a id="connect-property" class="st-rc-link" href="#">
				<?php esc_html_e( 'CONNECT PROPERTY', 'sharethis-share-buttons' ); ?>
			</a>

			<a id="create-new-property" class="st-rc-link" href="#">
				<?php esc_html_e( 'CREATE NEW PROPERTY', 'sharethis-share-buttons' ); ?>
			</a>
			<input type="hidden" id="st-user-cred">
		</div>
	</div>
</div>
