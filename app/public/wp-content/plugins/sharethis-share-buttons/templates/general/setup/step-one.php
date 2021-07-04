<?php
/**
 * Step One Template
 *
 * The template wrapper for the step one set up page.
 *
 * @package ShareThisShareButtons
 */

?>
<div id="sharethis-step-one-wrap">
	<div class="sharethis-setup-steps">
		<?php
		foreach ( $setup_steps as $num => $step ) :
				$step_class = 1 === $num ? 'current-step' : '';
		?>
			<span class="step-num <?php echo esc_attr( $step_class ); ?>"><?php echo esc_html( $num ); ?></span>

			<div class="step-description"><?php echo esc_html( $step ); ?></div>

			<span class="step-spacer"></span>
		<?php endforeach; ?>
	</div>

	<h1><?php echo esc_html__( 'Let\'s get started!', 'sharethis-share-buttons' ); ?></h1>

	<h4>
		<?php echo esc_html__( 'Thanks for choosing ShareThis! To get started, select a type of share button. You can always add a second type later.', 'sharethis-share-buttons' ); ?>
	</h4>

	<div class="button-choices-wrap">
		<div class="sharethis-button-option">
			<img src="<?php echo esc_url( "{$this->plugin->dir_url}/assets/inline-setup-logo.png" ); ?>">

			<span>
				<?php echo esc_html__( 'Use inline to place buttons at specific locations, such as under headlines.', 'sharethis-share-buttons' ); ?>
			</span>

			<a href="?page=sharethis-general&s=2&b=i">
				<?php echo esc_html__( 'GET INLINE SHARE BUTTONS', 'sharethis-share-buttons' ); ?>
			</a>
		</div>
		<div class="sharethis-button-option">
			<img src="<?php echo esc_url( "{$this->plugin->dir_url}/assets/sticky-setup-logo.png" ); ?>">

			<span>
				<?php echo esc_html__( 'Sticks to the left or the right side of the screen on desktop and the bottom of mobile.', 'sharethis-share-buttons' ); ?>
			</span>

			<a href="?page=sharethis-general&s=2&b=s">
				<?php echo esc_html__( 'GET STICKY SHARE BUTTONS', 'sharethis-share-buttons' ); ?>
			</a>
		</div>
	</div>

	<div class="sharethis-login-message">
		<?php echo esc_html__( 'Already have a ShareThis account?', 'sharethis-share-buttons' ); ?>

		<a href="?page=sharethis-general&l=t">
			<?php echo esc_html__( 'Login and connect your property', 'sharethis-share-buttons' ); ?>
		</a>
	</div>
</div>
