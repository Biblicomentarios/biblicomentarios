<?php
/**
 * Customize for icon picker, extend the WP customizer
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Icon Picker Control Class extends the WP customizer
 *
 * @since 3.0.0
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
class Hoot_Customize_Icon_Control extends WP_Customize_Control {

	/**
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $type = 'icon';

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

			case 'icon' :

				if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif;

				if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif;

				if ( ! empty( $this->sublabel ) ) : ?>
					<span class="description customize-control-sublabel"><?php echo wp_kses_post( $this->sublabel ); ?></span>
				<?php endif;

				$iconvalue = hoot_sanitize_fa( $this->value() );
				?>
				<input class="hoot-customize-control-icon" value="<?php echo esc_attr( $iconvalue ) ?>" <?php $this->link(); ?> type="hidden"/>
				<div class="hoot-customize-control-icon-picked hoot-flypanel-button" data-flypaneltype="icon"><i class="<?php echo esc_attr( $iconvalue ) ?>"></i><span><?php esc_attr_e( 'Select Icon', 'magazine-news-byte' ) ?></span></div>
				<div class="hoot-customize-control-icon-remove"><i class="fas fa-ban"></i><span><?php esc_attr_e( 'Remove Icon', 'magazine-news-byte' ) ?></span></div>

				<?php
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
function hoot_customize_icon_control_interface ( $wp_customize, $id, $setting ) {
	if ( isset( $setting['type'] ) ) :
		if ( $setting['type'] == 'icon' || $setting['type'] == 'icons' ) {
			$setting['type'] = 'icon';
			$wp_customize->add_control(
				new Hoot_Customize_Icon_Control( $wp_customize, $id, $setting )
			);
		}
	endif;
}
add_action( 'hoot_customize_control_interface', 'hoot_customize_icon_control_interface', 10, 3 );
endif;

/**
 * Add Content to Customizer Panel Footer
 *
 * @since 3.0.0
 * @return void
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
function hoot_customize_footer_iconcontent() {

	?>
	<div id="hoot-flyicon" class="hoot-flypanel">
		<div class="hoot-flypanel-header hoot-flypanel-nav">
			<div class="primary-actions">
				<span class="hoot-flypanel-back" tabindex="-1"><span class="screen-reader-text"><?php esc_attr_e( 'Back', 'magazine-news-byte' ) ?></span></span>
			</div>
		</div>
		<div id="hoot-flyicon-content" class="hoot-flypanel-content">
		</div>
		<div class="hoot-flypanel-footer hoot-flypanel-nav">
			<div class="primary-actions">
				<span class="hoot-flypanel-back" tabindex="-1"><span class="screen-reader-text"><?php esc_attr_e( 'Back', 'magazine-news-byte' ) ?></span></span>
			</div>
		</div>
	</div><!-- .hoot-flypanel -->
	<?php

}
add_action( 'customize_controls_print_footer_scripts', 'hoot_customize_footer_iconcontent' );
endif;

/**
 * Add Content to JS object passed to hoot-customize-script
 *
 * @since 3.0.0
 * @return void
 */
// Only load in customizer (not in frontend)
if ( class_exists( 'WP_Customize_Control' ) ) :
function hoot_customize_controls_icon_control_js_object( $data ) {

	$iconslist = '';
	$section_icons = hoot_enum_icons('icons');

	$iconslist .= '<div class="hoot-icon-list-wrap">';

	foreach ( hoot_enum_icons('sections') as $s_key => $s_title ) {
		$iconslist .= "<h4>$s_title</h4>";
		$iconslist .= '<div class="hoot-icon-list">';
		foreach ( $section_icons[$s_key] as $i_key => $i_class ) {
			$iconslist .= "<i class='$i_class' data-value='$i_class' data-category='$s_key'></i>";
		}
		$iconslist .= '</div>';
	}

	$iconslist .= '</div>';

	$data['iconslist'] = $iconslist;
	return $data;

}
add_filter( 'hoot_customize_control_footer_js_data_object', 'hoot_customize_controls_icon_control_js_object' );
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
function hoot_customize_sanitize_icon_callback( $callback, $type, $setting, $name ) {
	if ( $type == 'icon' )
		$callback = 'hoot_sanitize_icon';
	return $callback;
}
add_filter( 'hoot_customize_sanitize_callback', 'hoot_customize_sanitize_icon_callback', 5, 4 );