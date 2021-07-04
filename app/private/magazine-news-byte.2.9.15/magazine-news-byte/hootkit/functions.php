<?php
/**
 * This file contains functions and hooks for styling Hootkit plugin
 *   Hootkit is a free plugin released under GPL license and hosted on wordpress.org.
 *   It is recommended to the user via wp-admin using TGMPA class
 *
 * This file is loaded at 'after_setup_theme' action @priority 10 ONLY IF hootkit plugin is active
 *
 * @package    Magazine News Byte
 * @subpackage HootKit
 */

// Register HootKit
add_filter( 'hootkit_register', 'magnb_register_hootkit', 5 );

// Set data for theme scripts localization. hootData is actually localized at priority 11, so populate data before that at priority 9
add_action( 'wp_enqueue_scripts', 'magnb_localize_hootkit', 9 );
// Add hootkit styles. Set priority to @11 (unlike other scripts/styles @10)
// However we set stylesheet dependency to main stylesheet so hootkit css is loaded afterwards.
// Hootkit plugin loads its styles at default @10 (we skip this using config 'theme_css')
// The theme's main style is loaded @12 (Hence dynamic styles are loaded after -> now hooked to hootkit)
// The theme's main script is loaded @11
add_action( 'wp_enqueue_scripts', 'magnb_enqueue_hootkit', 11 );
// Set dynamic css handle to hootkit
add_filter( 'hoot_style_builder_inline_style_handle', 'magnb_dynamic_css_hootkit_handle', 5 );

// Add dynamic CSS for hootkit
add_action( 'hoot_dynamic_cssrules', 'magnb_hootkit_dynamic_cssrules' );

/**
 * Register Hootkit
 *
 * @since 1.0
 * @param array $config
 * @return string
 */
if ( !function_exists( 'magnb_register_hootkit' ) ) :
function magnb_register_hootkit( $config ) {
	// Array of configuration settings.
	$config = array(
		'nohoot'    => false,
		'theme_css' => true,
		'modules'   => array( // @deprecated <= HootKit v1.2.0 @10.20 // @deprecated <= HootKit v2.0.3 @6.21
			'sliders'     => array( 'image', 'postimage' ),
			'widgets'     => array( 'announce', 'content-blocks', 'content-posts-blocks', 'cta', 'icon', 'post-grid', 'post-list', 'social-icons', 'ticker', 'content-grid', 'cover-image', 'profile', 'ticker-posts', ),
		),
		'settings'  => array( 'cta-styles' ), // @deprecated <= HootKit v1.0.5 @12.18
		'supports'  => array( 'cta-styles', 'content-blocks-style5', 'content-blocks-style6', 'slider-styles', 'post-grid-firstpost-slider', 'announce-headline', 'grid-widget', 'list-widget' ),
			// @deprecated <= HootKit v1.1.3 @9.20 'post-grid-firstpost-slider' and 'announce-headline'
			// @deprecated <= HootKit v1.1.3 @9.20 postgrid=>grid-widget postslist=>list-widget
		'premium'   => array( 'carousel', 'postcarousel', 'postlistcarousel', 'contact-info', 'number-blocks', 'vcards', 'buttons', 'icon-list', 'notice', 'toggle', 'tabs', ),
	);
	return $config;
}
endif;

/**
 * Enqueue Scripts and Styles
 *
 * @since 2.7
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_localize_hootkit' ) ) :
function magnb_localize_hootkit() {
	$scriptdata = hoot_data( 'scriptdata' );
	if ( empty( $scriptdata ) )
		$scriptdata = array();
	$scriptdata['contentblockhover'] = 'enable'; // This needs to be explicitly enabled by supporting themes
	$scriptdata['contentblockhovertext'] = 'disable'; // Disabling needed for proper positioning of animation in latest themes (jquery animation is now redundant) (may be deleted later once all hootkit themes ported)
	hoot_set_data( 'scriptdata', $scriptdata );
}
endif;

/**
 * Enqueue Scripts and Styles
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_enqueue_hootkit' ) ) :
function magnb_enqueue_hootkit() {

	/* Load Hootkit Style - Add dependency so that hotkit is loaded after */
	$style_uri = hoot_locate_style( 'hootkit/hootkit' );
	wp_enqueue_style( 'magnb-hootkit', $style_uri, array( 'hoot-style' ), hoot_data()->template_version );

}
endif;

/**
 * Set dynamic css handle to hootkit
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_dynamic_css_hootkit_handle' ) ) :
function magnb_dynamic_css_hootkit_handle( $handle ) {
	return 'magnb-hootkit';
}
endif;

/**
 * Custom CSS built from user theme options for hootkit features
 * For proper sanitization, always use functions from library/sanitization.php
 *
 * @since 1.0
 * @access public
 */
if ( !function_exists( 'magnb_hootkit_dynamic_cssrules' ) ) :
function magnb_hootkit_dynamic_cssrules() {

	// Get user based style values
	$styles = magnb_user_style(); // echo '<!-- '; print_r($styles); echo ' -->';
	extract( $styles );

	/*** Add Dynamic CSS ***/

	hoot_add_css_rule( array(
						'selector'  => '.flycart-toggle, .flycart-panel',
						'property'  => 'background',
						'value'     => $content_bg_color,
				) );

	/* Light Slider */

	hoot_add_css_rule( array(
						'selector'  => '.lSSlideOuter ul.lSPager.lSpg > li:hover a, .lSSlideOuter ul.lSPager.lSpg > li.active a',
						'property'  => 'background-color',
						'value'     => $accent_color,
						'idtag'     => 'accent_color',
					) );
	hoot_add_css_rule( array(
						'selector'  => '.lSSlideOuter ul.lSPager.lSpg > li a',
						'property'  => 'border-color',
						'value'     => $accent_color,
						'idtag'     => 'accent_color',
					) );

	// hoot_add_css_rule( array(
	// 					'selector'  => '.wrap-light-on-dark .hootkitslide-head, .wrap-dark-on-light .hootkitslide-head',
	// 					'property'  => array(
	// 						// property  => array( value, idtag, important, typography_reset ),
	// 						'background' => array( $accent_color, 'accent_color' ),
	// 						'color'      => array( $accent_font, 'accent_font' ),
	// 						),
	// 				) );

	hoot_add_css_rule( array(
						'selector'  => '.slider-style2 .lSAction > a',
						'property'  => array(
							// property  => array( value, idtag, important, typography_reset ),
							'border-color' => array( $accent_color, 'accent_color' ),
							'background'   => array( $accent_color, 'accent_color' ),
							'color'        => array( $accent_font, 'accent_font' ),
							),
						'media'     => 'only screen and (min-width: 970px)',
					) );
	hoot_add_css_rule( array(
						'selector'  => '.slider-style2 .lSAction > a:hover',
						'property'  => array(
							// property  => array( value, idtag, important, typography_reset ),
							'background' => array( $accent_font, 'accent_font' ),
							'color'      => array( $accent_color, 'accent_color' ),
							),
						'media'     => 'only screen and (min-width: 970px)',
					) );


	/* Sidebars and Widgets */

	hoot_add_css_rule( array(
						'selector'  => '.widget .viewall a',
						'property'  => 'background',
						'value'     => $content_bg_color,
					) );
	hoot_add_css_rule( array(
						'selector'  => '.widget .viewall a:hover',
						'property'  => array(
							// property  => array( value, idtag, important, typography_reset ),
							'background' => array( $accent_font, 'accent_font' ),
							'color'      => array( $accent_color, 'accent_color' ),
							),
					) );
	// @deprecated <= HootKit v1.1.0 @5.20 view-all
	hoot_add_css_rule( array(
						'selector'  => '.widget .view-all a:hover',
						'property'  => 'color',
						'value'     => $accent_color,
						'idtag'     => 'accent_color',
					) );
	// @deprecated <= HootKit v1.1.0 @5.20 view-all
	hoot_add_css_rule( array(
						'selector'  => '.sidebar .view-all-top.view-all-withtitle a, .sub-footer .view-all-top.view-all-withtitle a, .footer .view-all-top.view-all-withtitle a, .sidebar .view-all-top.view-all-withtitle a:hover, .sub-footer .view-all-top.view-all-withtitle a:hover, .footer .view-all-top.view-all-withtitle a:hover',
						'property'  => 'color',
						'value'     => $accent_font,
						'idtag'     => 'accent_font',
					) );

	if ( !empty( $widgetmargin ) ) :
		hoot_add_css_rule( array(
						'selector'  => '.bottomborder-line:after' . ',' . '.bottomborder-shadow:after',
						'property'  => 'margin-top',
						'value'     => $widgetmargin,
						'idtag'     => 'widgetmargin',
					) );
		hoot_add_css_rule( array(
						'selector'  => '.topborder-line:before' . ',' . '.topborder-shadow:before',
						'property'  => 'margin-bottom',
						'value'     => $widgetmargin,
						'idtag'     => 'widgetmargin',
					) );
	endif;

	hoot_add_css_rule( array(
						'selector'  => '.cta-subtitle',
						'property'  => 'color',
						'value'     => $accent_color,
						'idtag'     => 'accent_color',
					) );

	// hoot_add_css_rule( array(
	// 					'selector'  => '.social-icons-icon',
	// 					'property'  => array(
	// 						// property  => array( value, idtag, important, typography_reset ),
	// 						'background' => array( $accent_color, 'accent_color' ),
	// 						'color'      => array( $accent_font, 'accent_font' ),
	// 						),
	// 				) );

	hoot_add_css_rule( array(
						'selector' => '.content-block-icon i',
						'property' => 'color',
						'value'    => $accent_color,
						'idtag'    => 'accent_color',
					) );

	hoot_add_css_rule( array(
						'selector' => '.icon-style-circle' .',' . '.icon-style-square',
						'property' => 'border-color',
						'value'    => $accent_color,
						'idtag'    => 'accent_color',
					) );

	hoot_add_css_rule( array(
						'selector'  => '.content-block-style3 .content-block-icon',
						'property'  => 'background',
						'value'     => $content_bg_color,
					) );

}
endif;

/**
 * Modify Slider default style
 *
 * @since 2.7
 * @param array $settings
 * @return string
 */
// function magnb_slider_image_widget_settings( $settings ) {
// 	if ( isset( $settings['form_options']['style'] ) )
// 		$settings['form_options']['style']['std'] = 'style2';
// 	if ( isset( $settings['form_options']['slides']['fields']['caption_bg']['std'] ) )
// 		$settings['form_options']['slides']['fields']['caption_bg']['std'] = 'dark-on-light';
// 	return $settings;
// }
// add_filter( 'hootkit_slider_image_widget_settings', 'magnb_slider_image_widget_settings', 7 );
/**
 * Modify Slider default style
 *
 * @since 2.7
 * @param array $settings
 * @return string
 */
// function magnb_slider_postimage_widget_settings( $settings ) {
// 	if ( isset( $settings['form_options']['style'] ) )
// 		$settings['form_options']['style']['std'] = 'style2';
// 	if ( isset( $settings['form_options']['caption_bg']['std'] ) )
// 		$settings['form_options']['caption_bg']['std'] = 'dark-on-light';
// 	return $settings;
// }
// add_filter( 'hootkit_slider_postimage_widget_settings', 'magnb_slider_postimage_widget_settings', 7 );

/**
 * Set button styling (for user defined colors) in cover image widget
 *
 * @since 1.0
 * @param array $settings
 * @return string
 */
add_filter( 'hootkit_coverimage_inverthoverbuttons', '__return_true' );