<?php
/**
 * Customize for radioimage, extend the WP customizer
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Radioimage Control Class extends the WP customizer
 *
 * @since 3.0.0
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
class Hoot_Customize_Radioimage_Control extends WP_Customize_Control {

	/**
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $type = 'radioimage';

	/**
	 * Define variable to whitelist sublabel parameter
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $sublabel = '';

	/**
	 * Render the control's content.
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_content() {

		switch ( $this->type ) {

			case 'radioimage' :
				if ( empty( $this->choices ) )
					return;

				$name = '_customize-radio-' . $this->id;

				if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;

				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif;

				if ( ! empty( $this->sublabel ) ) : ?>
					<span class="description customize-control-sublabel"><?php echo wp_kses_post( $this->sublabel ); ?></span>
				<?php endif;

				foreach ( $this->choices as $value => $image ) :
					$checked = checked( $this->value(), $value, false );
					?>
					<label class="hoot-customize-radioimage<?php if ($checked) echo ' radiocheck' ?>">
						<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); echo $checked; ?> />
						<img src="<?php echo esc_url( $image ); ?>" />
					</label>
					<?php
				endforeach;

				echo '<div class="clear"></div>';

				break;

		}

	}

}
endif;

/**
 * Hook into control display interface
 *
 * @since 3.0.0
 * @param object $wp_customize
 * @param string $id
 * @param array $setting
 * @return void
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
function hoot_customize_radioimage_control_interface ( $wp_customize, $id, $setting ) {
	if ( isset( $setting['type'] ) ) :
		if ( $setting['type'] == 'radioimage' ) {
			$wp_customize->add_control(
				new Hoot_Customize_Radioimage_Control( $wp_customize, $id, $setting )
			);
		}
	endif;
}
add_action( 'hoot_customize_control_interface', 'hoot_customize_radioimage_control_interface', 10, 3 );
endif;

/**
 * Add sanitization function
 *
 * @since 3.0.0
 * @param string $callback
 * @param string $type
 * @param array $setting
 * @param string $name name (id) of the setting
 * @return string
 */
function hoot_customize_sanitize_radioimage_callback( $callback, $type, $setting, $name ) {
	if ( $type == 'radioimage' )
		$callback = 'hoot_sanitize_customize_enum';
	return $callback;
}
add_filter( 'hoot_customize_sanitize_callback', 'hoot_customize_sanitize_radioimage_callback', 5, 4 );