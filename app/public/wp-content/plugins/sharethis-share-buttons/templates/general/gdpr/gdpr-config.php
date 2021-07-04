<?php
/**
 * Configure tool template for gdpr onboarding
 */
?>
<div class="gdpr-platform platform-config-wrapper">
	<hr>

	<h4 style="text-align: left; font-size: 15px;"><?php echo esc_html__( 'Configure', 'sharethis-share-buttons' ); ?></h4>
	<div class="st-design-message"><?php echo esc_html__( 'Use the settings below to configure your GDPR compliance tool popup.', 'sharethis-share-buttons' ); ?></div>

	<div id="starter-questions">
		<label>
			<?php echo esc_html__('PUBLISHER NAME * (this will be displayed in the consent tool)',
				'sharethis-share-buttons'); ?>
		</label>

		<input type="text" id="sharethis-publisher-name" placeholder="Enter your company name">

		<label>
			<?php echo esc_html__('WHICH USERS SHOULD BE ASKED FOR CONSENT?',
				'sharethis-share-buttons'); ?>
		</label>

		<select id="sharethis-user-type">
			<?php foreach ($user_types as $user_value => $name) : ?>
				<option value="<?php echo esc_attr($user_value); ?>">
					<?php echo esc_html($name); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<label>
			<?php echo esc_html__('CONSENT SCOPE', 'sharethis-share-buttons'); ?>
		</label>

		<select id="sharethis-consent-type">
			<?php foreach ($consent_types as $consent_value => $c_name) : ?>
				<option
					value="<?php echo esc_attr($consent_value); ?>">
					<?php echo esc_html($c_name); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<label>
			<?php echo esc_html__('SELECT LANGUAGE', 'sharethis-share-buttons'); ?>
		</label>

		<select id="st-language">
			<?php foreach ($languages as $language => $code) : ?>
				<option value="<?php echo esc_attr($code); ?>">
					<?php echo esc_html($language); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<p class="form-color">
			<label>
				<?php echo esc_html__(
					'CHOOSE FORM COLOR',
					'gdpr-complianc-tool'
				); ?>
			</label>
		<div id="sharethis-form-color">
			<?php foreach ($colors as $color) : ?>
				<div class="color"
				     data-value="<?php echo esc_attr($color); ?>"
				     style="max-width: 30px; max-height: 30px; overflow: hidden;">
					<span style="content: ' '; background-color:<?php echo esc_html($color); ?>; padding: 40px;"></span>
				</div>
			<?php endforeach; ?>
		</div>
		</p>
	</div>
	<div class="accor-wrap switch" id="purposes">
		<div class="accor-tab">
			<span class="accor-arrow">&#9658;</span>
			<?php echo esc_html__('WHY ARE YOU COLLECTING CUSTOMER DATA?', 'sharethis-share-buttons'); ?>
		</div>

		<div class="accor-content" id="publisher-purpose" class="switch">
			<?php include $this->plugin->dir_path . '/templates/general/gdpr/purposes.php'; ?>
		</div>
	</div>
	<?php if(isset($vendor_data)) : ?>
	<div class="accor-wrap restrict-vendors">
		<div class="accor-tab">
			<span class="accor-arrow">&#9658;</span>
			<?php echo esc_html__( 'VENDOR EXCLUSIONS', 'sharethis-share-buttons' ); ?>
		</div>
		<div class="accor-content">
			<div class="well">
				<?php include $this->plugin->dir_path . '/templates/general/gdpr/exclusions.php'; ?>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>

