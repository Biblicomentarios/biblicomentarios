<?php
/**
 * Defines customizer options
 *
 * This file is loaded at 'after_setup_theme' hook with 10 priority.
 *
 * @package    Magazine News Byte
 * @subpackage Theme
 */

/**
 * Theme default colors and fonts
 *
 * @since 1.0
 * @access public
 * @param string $key return a specific key value, else the entire defaults array
 * @return array|string
 */
if ( !function_exists( 'magnb_default_style' ) ) :
function magnb_default_style( $key = false ){

	// Do not use static cache variable as any reference to 'magnb_default_style()'
	// (example: get default value during declaring add_theme_support for WP custom background which
	// is also loaded at 'after_setup_theme' hook with 10 priority) will prevent further applying
	// of filter hook (by child-theme/plugin/premium). Ideally, this function should be called only
	// after 'after_setup_theme' hook with 11 priority
	$defaults = apply_filters( 'magnb_default_style', array(
		'accent_color'         => '#bd2e2e',
		'accent_font'          => '#ffffff',
		'module_bg_default'    => '#f5f5f5',
		'box_background'       => '#ffffff',
		'site_background'      => '#ffffff', // Used by WP custom-background
		'widgetmargin'         => 35,
		'logo_fontface'        => 'fontlo',
		'headings_fontface'    => 'fontro',
	) );

	if ( $key )
		return ( isset( $defaults[ $key ] ) ) ? $defaults[ $key ] : false;
	else
		return $defaults;
}
endif;

/**
 * Build the Customizer options (panels, sections, settings)
 *
 * Always remember to mention specific priority for non-static options like:
 *     - options being added based on a condition (eg: if woocommerce is active)
 *     - options which may get removed (eg: logo_size, headings_fontface)
 *     - options which may get rearranged (eg: logo_background_type)
 *     This will allow other options inserted with priority to be inserted at
 *     their intended place.
 *
 * @since 1.0
 * @access public
 * @return array
 */
if ( !function_exists( 'magnb_customizer_options' ) ) :
function magnb_customizer_options() {

	// Stores all the settings to be added
	$settings = array();

	// Stores all the sections to be added
	$sections = array();

	// Stores all the panels to be added
	$panels = array();

	// Theme default colors and fonts
	extract( magnb_default_style() );

	// Directory path for radioimage buttons
	$imagepath =  hoot_data()->incuri . 'admin/images/';

	// Logo Sizes (different range than standard typography range)
	$logosizes = array();
	$logosizerange = range( 14, 110 );
	foreach ( $logosizerange as $isr )
		$logosizes[ $isr . 'px' ] = $isr . 'px';
	$logosizes = apply_filters( 'magnb_options_logosizes', $logosizes);

	// Logo Font Options for Lite version
	$logofont = apply_filters( 'magnb_options_logofont', array(
					'heading'  => esc_html__( "Logo Font (set in 'Typography' section)", 'magazine-news-byte' ),
					'heading2' => esc_html__( "Heading Font (set in 'Typography' section)", 'magazine-news-byte' ),
					'standard' => esc_html__( "Standard Body Font", 'magazine-news-byte' ),
					) );

	/*** Add Options (Panels, Sections, Settings) ***/

	/** Section **/

	$section = 'links';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Demo Install / Support', 'magazine-news-byte' ),
		'priority'    => '2',
	);

	$lcontent = '';
	$lcontent .= '<a class="hoot-cust-link" href="' .
				 'https://demo.wphoot.com/magazine-news-byte/' .
				 '" target="_blank"><span class="hoot-cust-link-head">' .
				 '<i class="fas fa-eye"></i> ' .
				 esc_html__( "Demo", 'magazine-news-byte') . 
				 '</span><span class="hoot-cust-link-desc">' .
				 esc_html__( "Demo the theme features and options with sample content.", 'magazine-news-byte') .
				 '</span></a>';
	$ocdilink = ( function_exists( 'hoot_lib_premium_core' ) ) ? ( ( class_exists( 'OCDI_Plugin' ) ) ? admin_url( 'themes.php?page=pt-one-click-demo-import' ) : 'https://wphoot.com/support/magazine-news-byte/#docs-section-demo-content' ) : 'https://wphoot.com/support/magazine-news-byte/#docs-section-demo-content-free';
	$lcontent .= '<a class="hoot-cust-link" href="' .
				 esc_url( $ocdilink ) .
				 '" target="_blank"><span class="hoot-cust-link-head">' .
				 '<i class="fas fa-upload"></i> ' .
				 esc_html__( "1 Click Installation", 'magazine-news-byte') . 
				 '</span><span class="hoot-cust-link-desc">' .
				 esc_html__( "Install demo content to make your site look exactly like the Demo Site. Use it as a starting point instead of starting from scratch.", 'magazine-news-byte') .
				 '</span></a>';
	$lcontent .= '<a class="hoot-cust-link" href="' .
				 'https://wphoot.com/support/' .
				 '" target="_blank"><span class="hoot-cust-link-head">' .
				 '<i class="far fa-life-ring"></i> ' .
				 esc_html__( "Documentation / Support", 'magazine-news-byte') . 
				 '</span><span class="hoot-cust-link-desc">' .
				 esc_html__( "Get theme related support for both free and premium users.", 'magazine-news-byte') .
				 '</span></a>';
	$lcontent .= '<a class="hoot-cust-link" href="' .
				 'https://wordpress.org/support/theme/magazine-news-byte/reviews/#new-post' .
				 '" target="_blank"><span class="hoot-cust-link-head">' .
				 '<i class="fas fa-star"></i> ' .
				 esc_html__( "Rate Us", 'magazine-news-byte') . 
				 '</span><span class="hoot-cust-link-desc">' .
				 /* translators: five stars */
				 sprintf( esc_html__( 'If you are happy with the theme, please give us a %1$s rating on wordpress.org. Thanks in advance!', 'magazine-news-byte'), '<span style="color:#0073aa;">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ) .
				 '</span></a>';

	$settings['linksection'] = array(
		// 'label'       => esc_html__( 'Misc Links', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'content',
		'priority'    => '8', // Non static options must have a priority
		'content'     => $lcontent,
	);

	/** Section **/

	$section = 'title_tagline';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Setup &amp; Layout', 'magazine-news-byte' ),
	);

	$settings['site_layout'] = array(
		'label'       => esc_html__( 'Site Layout - Boxed vs Stretched', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'boxed'   => esc_html__( 'Boxed layout', 'magazine-news-byte' ),
			'stretch' => esc_html__( 'Stretched layout (full width)', 'magazine-news-byte' ),
		),
		'default'     => 'stretch',
		'transport' => 'postMessage',
	);

	$settings['site_width'] = array(
		'label'       => esc_html__( 'Max. Site Width (pixels)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'1440' => esc_html__( '1440px (wide)', 'magazine-news-byte' ),
			'1260' => esc_html__( '1260px (normal)', 'magazine-news-byte' ),
			'1080' => esc_html__( '1080px (compact)', 'magazine-news-byte' ),
		),
		'default'     => '1440',
		'transport' => 'postMessage',
	);

	$settings['load_minified'] = array(
		'label'       => esc_html__( 'Load Minified Styles and Scripts (when available)', 'magazine-news-byte' ),
		'sublabel'    => esc_html__( 'Checking this option reduces data size, hence increasing page load speed.', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		// 'default'     => 1,
	);

	$settings['sidebar'] = array(
		'label'       => esc_html__( 'Sidebar Layout (Site-wide)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radioimage',
		'choices'     => array(
			'wide-right'         => $imagepath . 'sidebar-wide-right.png',
			'narrow-right'       => $imagepath . 'sidebar-narrow-right.png',
			'wide-left'          => $imagepath . 'sidebar-wide-left.png',
			'narrow-left'        => $imagepath . 'sidebar-narrow-left.png',
			'narrow-left-right'  => $imagepath . 'sidebar-narrow-left-right.png',
			'narrow-left-left'   => $imagepath . 'sidebar-narrow-left-left.png',
			'narrow-right-right' => $imagepath . 'sidebar-narrow-right-right.png',
			'full-width'         => $imagepath . 'sidebar-full.png',
			'none'               => $imagepath . 'sidebar-none.png',
		),
		'default'     => 'wide-right',
		'description' => esc_html__( 'Set the default sidebar width and position for your site.', 'magazine-news-byte' ),
	);

	$settings['sidebar_fp'] = array(
		'label'       => esc_html__( 'Sidebar Layout (for Front Page)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radioimage',
		'choices'     => array(
			'wide-right'         => $imagepath . 'sidebar-wide-right.png',
			'narrow-right'       => $imagepath . 'sidebar-narrow-right.png',
			'wide-left'          => $imagepath . 'sidebar-wide-left.png',
			'narrow-left'        => $imagepath . 'sidebar-narrow-left.png',
			'narrow-left-right'  => $imagepath . 'sidebar-narrow-left-right.png',
			'narrow-left-left'   => $imagepath . 'sidebar-narrow-left-left.png',
			'narrow-right-right' => $imagepath . 'sidebar-narrow-right-right.png',
			'full-width'         => $imagepath . 'sidebar-full.png',
			'none'               => $imagepath . 'sidebar-none.png',
		),
		'default'     => ( ( 'page' == get_option('show_on_front' ) ) ? 'full-width' : 'wide-right' ),
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'description' => sprintf( esc_html__( 'This is sidebar for the Frontpage Content Module in %1$sFrontpage Modules Settings%2$s', 'magazine-news-byte' ), '<a href="' . esc_url( admin_url( 'customize.php?autofocus[control]=frontpage_content_desc' ) ) . '" rel="focuslink" data-focustype="control" data-href="frontpage_content_desc">', '</a>' ),
	);

	$settings['sidebar_archives'] = array(
		'label'       => esc_html__( 'Sidebar Layout (for Blog/Archives)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radioimage',
		'choices'     => array(
			'wide-right'         => $imagepath . 'sidebar-wide-right.png',
			'narrow-right'       => $imagepath . 'sidebar-narrow-right.png',
			'wide-left'          => $imagepath . 'sidebar-wide-left.png',
			'narrow-left'        => $imagepath . 'sidebar-narrow-left.png',
			'narrow-left-right'  => $imagepath . 'sidebar-narrow-left-right.png',
			'narrow-left-left'   => $imagepath . 'sidebar-narrow-left-left.png',
			'narrow-right-right' => $imagepath . 'sidebar-narrow-right-right.png',
			'full-width'         => $imagepath . 'sidebar-full.png',
			'none'               => $imagepath . 'sidebar-none.png',
		),
		'default'     => 'wide-right',
	);

	$settings['sidebar_pages'] = array(
		'label'       => esc_html__( 'Sidebar Layout (for Pages)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radioimage',
		'choices'     => array(
			'wide-right'         => $imagepath . 'sidebar-wide-right.png',
			'narrow-right'       => $imagepath . 'sidebar-narrow-right.png',
			'wide-left'          => $imagepath . 'sidebar-wide-left.png',
			'narrow-left'        => $imagepath . 'sidebar-narrow-left.png',
			'narrow-left-right'  => $imagepath . 'sidebar-narrow-left-right.png',
			'narrow-left-left'   => $imagepath . 'sidebar-narrow-left-left.png',
			'narrow-right-right' => $imagepath . 'sidebar-narrow-right-right.png',
			'full-width'         => $imagepath . 'sidebar-full.png',
			'none'               => $imagepath . 'sidebar-none.png',
		),
		'default'     => 'wide-right',
	);

	$settings['sidebar_posts'] = array(
		'label'       => esc_html__( 'Sidebar Layout (for single Posts)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radioimage',
		'choices'     => array(
			'wide-right'         => $imagepath . 'sidebar-wide-right.png',
			'narrow-right'       => $imagepath . 'sidebar-narrow-right.png',
			'wide-left'          => $imagepath . 'sidebar-wide-left.png',
			'narrow-left'        => $imagepath . 'sidebar-narrow-left.png',
			'narrow-left-right'  => $imagepath . 'sidebar-narrow-left-right.png',
			'narrow-left-left'   => $imagepath . 'sidebar-narrow-left-left.png',
			'narrow-right-right' => $imagepath . 'sidebar-narrow-right-right.png',
			'full-width'         => $imagepath . 'sidebar-full.png',
			'none'               => $imagepath . 'sidebar-none.png',
		),
		'default'     => 'wide-right',
	);

	if ( current_theme_supports( 'woocommerce' ) ) :

		$settings['sidebar_wooshop'] = array(
			'label'       => esc_html__( 'Sidebar Layout (Woocommerce Shop/Archives)', 'magazine-news-byte' ),
			'section'     => $section,
			'type'        => 'radioimage',
			'priority'    => '83', // Non static options must have a priority
			'choices'     => array(
				'wide-right'         => $imagepath . 'sidebar-wide-right.png',
				'narrow-right'       => $imagepath . 'sidebar-narrow-right.png',
				'wide-left'          => $imagepath . 'sidebar-wide-left.png',
				'narrow-left'        => $imagepath . 'sidebar-narrow-left.png',
				'narrow-left-right'  => $imagepath . 'sidebar-narrow-left-right.png',
				'narrow-left-left'   => $imagepath . 'sidebar-narrow-left-left.png',
				'narrow-right-right' => $imagepath . 'sidebar-narrow-right-right.png',
				'full-width'         => $imagepath . 'sidebar-full.png',
				'none'               => $imagepath . 'sidebar-none.png',
			),
			'default'     => 'wide-right',
			'description' => esc_html__( 'Set the default sidebar width and position for WooCommerce Shop and Archives pages like product categories etc.', 'magazine-news-byte' ),
		);

		$settings['sidebar_wooproduct'] = array(
			'label'       => esc_html__( 'Sidebar Layout (Woocommerce Single Product Page)', 'magazine-news-byte' ),
			'section'     => $section,
			'type'        => 'radioimage',
			'priority'    => '83', // Non static options must have a priority
			'choices'     => array(
				'wide-right'         => $imagepath . 'sidebar-wide-right.png',
				'narrow-right'       => $imagepath . 'sidebar-narrow-right.png',
				'wide-left'          => $imagepath . 'sidebar-wide-left.png',
				'narrow-left'        => $imagepath . 'sidebar-narrow-left.png',
				'narrow-left-right'  => $imagepath . 'sidebar-narrow-left-right.png',
				'narrow-left-left'   => $imagepath . 'sidebar-narrow-left-left.png',
				'narrow-right-right' => $imagepath . 'sidebar-narrow-right-right.png',
				'full-width'         => $imagepath . 'sidebar-full.png',
				'none'               => $imagepath . 'sidebar-none.png',
			),
			'default'     => 'wide-right',
			'description' => esc_html__( 'Set the default sidebar width and position for WooCommerce product page', 'magazine-news-byte' ),
		);

	endif;

	$settings['disable_sticky_sidebar'] = array(
		'label'       => esc_html__( 'Disable Sticky Sidebar', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		'description' => esc_html__( 'Check this if you do not want to display a fixed Sidebar the user scrolls down the page.', 'magazine-news-byte' ),
	);

	$settings['widgetmargin'] = array(
		'label'       => esc_html__( 'Widget Margin', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'text',
		'default'     => $widgetmargin,
		'description' => esc_html__( '(in pixels) Margin space above and below widgets. Leave empty if you dont want to change the default.', 'magazine-news-byte' ),
		'input_attrs' => array(
			'placeholder' => esc_html__( 'default: 35', 'magazine-news-byte' ),
		),
		'transport' => 'postMessage',
	);

	/** Section **/

	$section = 'header';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Header', 'magazine-news-byte' ),
	);

	$settings['menu_location'] = array(
		'label'       => esc_html__( 'Menu Location', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'top'        => esc_html__( 'Above Logo', 'magazine-news-byte' ),
			'side'       => esc_html__( 'Header Side (Right of Logo)', 'magazine-news-byte' ),
			'bottom'     => esc_html__( 'Below Logo', 'magazine-news-byte' ),
			'none'       => esc_html__( 'Do not display menu', 'magazine-news-byte' ),
		),
		'default'     => 'bottom',
	);

	$settings['logo_side'] = array(
		'label'       => esc_html__( 'Header Side (right of logo)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'search'      => esc_html__( 'Display Search', 'magazine-news-byte' ),
			'widget-area' => esc_html__( "'Header Side' widget area", 'magazine-news-byte' ),
			'none'        => esc_html__( 'None (Logo will get centre aligned)', 'magazine-news-byte' ),
		),
		'default'     => 'widget-area',
		'active_callback' => 'magnb_callback_logo_side', /*** Use JS API (in customize.js) for conditional controls using 'menu_location' setting in their active_callback - for quicker response ***/
		'selective_refresh' => array( 'logo_side_partial', array(
			'selector'            => '#header-aside',
			'settings'            => array( 'logo_side' ),
			'render_callback'     => 'magnb_header_aside',
			'container_inclusive' => true,
			) ),
	);

	$settings['fullwidth_menu_align'] = array(
		'label'       => esc_html__( 'Menu Area (alignment)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'left'      => esc_html__( 'Left', 'magazine-news-byte' ),
			'right'     => esc_html__( 'Right', 'magazine-news-byte' ),
			'center'    => esc_html__( 'Center', 'magazine-news-byte' ),
		),
		'default'     => 'left',
		'active_callback' => 'magnb_callback_logo_side', /*** Use JS API (in customize.js) for conditional controls using 'menu_location' setting in their active_callback - for quicker response ***/
		'transport' => 'postMessage',
	);

	$settings['disable_table_menu'] = array(
		'label'       => esc_html__( 'Disable Table Menu', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		// 'default'     => 1,
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'description' => sprintf( esc_html__( '%1$s%2$sDisable Table Menu if you have a lot of Top Level menu items, %3$sand dont have menu item descriptions.%4$s', 'magazine-news-byte' ), "<img src='{$imagepath}menu-table.png'>", '<br />', '<strong>', '</strong>' ),
		'transport' => 'postMessage',
	);

	$settings['mobile_menu'] = array(
		'label'       => esc_html__( 'Mobile Menu', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'inline' => esc_html__( 'Inline - Menu Slide Downs to open', 'magazine-news-byte' ),
			'fixed'  => esc_html__( 'Fixed - Menu opens on the left', 'magazine-news-byte' ),
		),
		'default'     => 'fixed',
		'transport' => 'postMessage',
	);

	$settings['mobile_submenu_click'] = array(
		'label'       => esc_html__( "[Mobile Menu] Submenu opens on 'Click'", 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		'default'     => 1,
		'description' => esc_html__( "Uncheck this option to make all Submenus appear in 'Open' state. By default, submenus open on clicking (i.e. single tap on mobile).", 'magazine-news-byte' ),
		'transport' => 'postMessage',
	);

	$settings['below_header_grid'] = array(
		'label'       => esc_html__( "'Below Header' widget area layout", 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radioimage',
		'choices'     => array(
			'boxed'   => $imagepath . 'fp-widgetarea-boxed.png',
			'stretch' => $imagepath . 'fp-widgetarea-stretch.png',
		),
		'default'     => 'boxed',
		'priority'    => '165',
		'transport' => 'postMessage',
	);

	/** Section **/

	$section = 'logo';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Logo', 'magazine-news-byte' ),
	);

	$settings['logo_background_type'] = array(
		'label'       => esc_html__( 'Logo Background', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'priority'    => '165', // Non static options must have a priority
		'choices'     => array(
			'transparent' => esc_html__( 'None', 'magazine-news-byte' ),
			'accent'      => esc_html__( 'Accent Color', 'magazine-news-byte' ),
		),
		'default'     => 'transparent',
		'transport' => 'postMessage',
	);

	$settings['logo_border'] = array(
		'label'       => esc_html__( 'Logo Border', 'magazine-news-byte' ),
		'sublabel'    => esc_html__( 'Display a border around logo.', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		'default'     => 0,
		'priority'    => '165',
		'transport' => 'postMessage',
	);

	$settings['logo'] = array(
		'label'       => esc_html__( 'Site Logo', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'text'        => esc_html__( 'Default Text (Site Title)', 'magazine-news-byte' ),
			'custom'      => esc_html__( 'Custom Text', 'magazine-news-byte' ),
			'image'       => esc_html__( 'Image Logo', 'magazine-news-byte' ),
			'mixed'       => esc_html__( 'Image &amp; Default Text (Site Title)', 'magazine-news-byte' ),
			'mixedcustom' => esc_html__( 'Image &amp; Custom Text', 'magazine-news-byte' ),
		),
		'default'     => 'text',
		/* Translators: 1 is the link start markup, 2 is link markup end */
		'description' => sprintf( esc_html__( 'Use %1$sSite Title%2$s as default text logo', 'magazine-news-byte' ), '<a href="' . esc_url( admin_url('options-general.php') ) . '" target="_blank">', '</a>' ),

		/*** Use JS API (in customize.js) for conditional controls using 'logo' setting in their active_callback ***/
		'selective_refresh' => array( 'logo_partial', array(
			'selector'            => '#branding',
			'settings'            => array( 'logo', 'show_tagline', 'logo_custom', 'custom_logo' ),	// Do not add 'logo_size' to 'settings' array
																									// since it is removed in premium, and hence this
																									// selective_refresh wont work
			'primary_setting'     => 'logo', // Redundant as 'logo' is first ID in settings array
			'render_callback'     => 'magnb_branding',
			'container_inclusive' => true,
			) ),

	);

	$settings['logo_size'] = array(
		'label'       => esc_html__( 'Logo Text Size', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'select',
		'priority'    => '175', // Non static options must have a priority
		'choices'     => array(
			'tiny'   => esc_html__( 'Tiny', 'magazine-news-byte'),
			'small'  => esc_html__( 'Small', 'magazine-news-byte'),
			'medium' => esc_html__( 'Medium', 'magazine-news-byte'),
			'large'  => esc_html__( 'Large', 'magazine-news-byte'),
			'huge'   => esc_html__( 'Huge', 'magazine-news-byte'),
		),
		'default'     => 'small',
		'active_callback' => 'magnb_callback_logo_size',
		'transport' => 'postMessage',
	);

	$settings['site_title_icon'] = array(
		'label'           => esc_html__( 'Site Title Icon (Optional)', 'magazine-news-byte' ),
		'section'         => $section,
		'type'            => 'icon',
		// 'default'         => 'fa-anchor fas',
		'description'     => esc_html__( 'Leave empty to hide icon.', 'magazine-news-byte' ),
		'active_callback' => 'magnb_callback_site_title_icon',
		'transport' => 'postMessage',
	);

	$settings['site_title_icon_size'] = array(
		'label'           => esc_html__( 'Site Title Icon Size', 'magazine-news-byte' ),
		'section'         => $section,
		'type'            => 'select',
		'choices'         => $logosizes,
		'default'         => '50px',
		'active_callback' => 'magnb_callback_site_title_icon',
		'transport' => 'postMessage',
	);

	$settings['logo_image_width'] = array(
		'label'           => esc_html__( 'Maximum Logo Width', 'magazine-news-byte' ),
		'section'         => $section,
		'type'            => 'text',
		'priority'        => '196', // Keep it with logo image ( 'custom_logo' )->priority logo
		'default'         => 200,
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'description'     => sprintf( esc_html__( '(in pixels)%1$sThe logo width may be automatically adjusted by the browser depending on title length and space available.', 'magazine-news-byte' ), '<hr>' ),
		'input_attrs'     => array(
			'placeholder' => esc_html__( '(in pixels)', 'magazine-news-byte' ),
		),
		'active_callback' => 'magnb_callback_logo_image_width',
		'transport' => 'postMessage',
	);

	$logo_custom_line_options = array(
		'text' => array(
			'label'       => esc_html__( 'Line Text', 'magazine-news-byte' ),
			'type'        => 'text',
		),
		'size' => array(
			'label'       => esc_html__( 'Line Size', 'magazine-news-byte' ),
			'type'        => 'select',
			'choices'     => $logosizes,
			'default'     => '24px',
		),
		'font' => array(
			'label'       => esc_html__( 'Line Font', 'magazine-news-byte' ),
			'type'        => 'select',
			'choices'     => $logofont,
			'default'     => 'heading',
		),
	);

	$settings['logo_custom'] = array(
		'label'           => esc_html__( 'Custom Logo Text', 'magazine-news-byte' ),
		'section'         => $section,
		'type'            => 'sortlist',
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'description'     => sprintf( esc_html__( 'Use &lt;b&gt; and &lt;em&gt; tags in "Line Text" fields below to emphasize different words. Example:%1$s%2$s&lt;b&gt;Magazine&lt;/b&gt; &lt;em&gt;NewsByte&lt;/em&gt;%3$s', 'magazine-news-byte' ), '<br />', '<code>', '</code>' ),
		'choices'         => array(
			'line1' => esc_html__( 'Line 1', 'magazine-news-byte' ),
			'line2' => esc_html__( 'Line 2', 'magazine-news-byte' ),
			'line3' => esc_html__( 'Line 3', 'magazine-news-byte' ),
			'line4' => esc_html__( 'Line 4', 'magazine-news-byte' ),
		),
		'default'     => array(
			'line3'  => array( 'sortitem_hide' => 1, ),
			'line4'  => array( 'sortitem_hide' => 1, ),
		),
		'options'         => array(
			'line1' => $logo_custom_line_options,
			'line2' => $logo_custom_line_options,
			'line3' => $logo_custom_line_options,
			'line4' => $logo_custom_line_options,
		),
		'attributes'      => array(
			'hideable'   => true,
			'sortable'   => false,
			// 'open-state' => 'first',
		),
		'active_callback' => 'magnb_callback_logo_custom',
		'transport' => 'postMessage', // to work with 'selective_refresh' added via 'logo'
	);

	$settings['show_tagline'] = array(
		'label'           => esc_html__( 'Show Tagline', 'magazine-news-byte' ),
		'sublabel'        => esc_html__( 'Display site description as tagline below logo.', 'magazine-news-byte' ),
		'section'         => $section,
		'type'            => 'checkbox',
		'default'         => 1,
		// 'active_callback' => 'magnb_callback_show_tagline',
		'transport' => 'postMessage', // to work with 'selective_refresh' added via 'logo'
	);

	/** Section **/

	$section = 'colors';

	// Redundant as 'colors' section is added by WP. But we still add it for brevity
	$sections[ $section ] = array(
		'title'       => esc_html__( 'Colors / Backgrounds', 'magazine-news-byte' ),
		'description' => __( 'The premium version comes with color and background options for different sections of your site like header, menu dropdown, content area, logo background, footer etc.', 'magazine-news-byte' ),
	);

	$settings['box_background_color'] = array(
		'label'       => esc_html__( 'Site Content Background', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'color',
		'priority'    => '215', // Non static options must have a priority
		'default'     => $box_background,
		'transport' => 'postMessage',
	);

	$settings['color_scheme'] = array(
		'label'       => esc_html__( 'Color Scheme', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'select',
		'priority'    => '218',
		'choices'     => array(
			'maroon' => esc_html__( 'Default (Deep Red)', 'magazine-news-byte'),
			'green'  => esc_html__( 'AquaGreen', 'magazine-news-byte'),
			'blue'   => esc_html__( 'Blue', 'magazine-news-byte'),
			'red'    => esc_html__( 'Bright Red', 'magazine-news-byte'),
			'orange' => esc_html__( 'Orange', 'magazine-news-byte'),
			'gold'   => esc_html__( 'Gold', 'magazine-news-byte'),
		),
		'default'     => 'maroon',
		'transport' => 'postMessage',
	);

	$settings['accent_color'] = array(
		'label'       => esc_html__( 'Accent Color', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'color',
		'default'     => $accent_color,
		'transport' => 'postMessage',
	);

	$settings['accent_font'] = array(
		'label'       => esc_html__( 'Font Color on Accent Color', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'color',
		'default'     => $accent_font,
		'transport' => 'postMessage',
	);

	/** Section **/

	$section = 'typography';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Typography', 'magazine-news-byte' ),
		'description' => esc_html__( 'The premium version offers complete typography control (color, style, size) for various headings, header, menu, footer, widgets, content sections etc (over 600 Google Fonts to chose from)', 'magazine-news-byte' ),
	);

	$settings['logo_fontface'] = array(
		'label'       => esc_html__( 'Logo Font (Free Version)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'select',
		'priority'    => 237, // Non static options must have a priority
		'choices'     => array(
			'fontro' => esc_html__( 'Standard Font (Roboto)', 'magazine-news-byte'),
			'fontcf' => esc_html__( 'Alternate Font (Comfortaa)', 'magazine-news-byte'),
			'fontow' => esc_html__( 'Display Font (Oswald)', 'magazine-news-byte'),
			'fontlo' => esc_html__( 'Heading Font 1 (Lora)', 'magazine-news-byte'),
			'fontsl' => esc_html__( 'Heading Font 2 (Slabo)', 'magazine-news-byte'),
		),
		'default'     => $logo_fontface,
	);

	$settings['logo_fontface_style'] = array(
		'label'       => esc_html__( 'Logo Font Style', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'select',
		'priority'    => 237, // Non static options must have a priority
		'choices'     => array(
			'standard'  => esc_html__( 'Standard', 'magazine-news-byte'),
			'uppercase' => esc_html__( 'Uppercase', 'magazine-news-byte'),
		),
		'default'     => 'uppercase',
		'transport' => 'postMessage',
	);

	$settings['headings_fontface'] = array(
		'label'       => esc_html__( 'Headings Font (Free Version)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'select',
		'priority'    => 237, // Non static options must have a priority
		'choices'     => array(
			'fontro' => esc_html__( 'Standard Font (Roboto)', 'magazine-news-byte'),
			'fontcf' => esc_html__( 'Alternate Font (Comfortaa)', 'magazine-news-byte'),
			'fontow' => esc_html__( 'Display Font (Oswald)', 'magazine-news-byte'),
			'fontlo' => esc_html__( 'Heading Font 1 (Lora)', 'magazine-news-byte'),
			'fontsl' => esc_html__( 'Heading Font 2 (Slabo)', 'magazine-news-byte'),
		),
		'default'     => $headings_fontface,
	);

	$settings['headings_fontface_style'] = array(
		'label'       => esc_html__( 'Heading Font Style', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'select',
		'priority'    => 237, // Non static options must have a priority
		'choices'     => array(
			'standard'  => esc_html__( 'Standard', 'magazine-news-byte'),
			'uppercase' => esc_html__( 'Uppercase', 'magazine-news-byte'),
		),
		'default'     => 'standard',
		'transport' => 'postMessage',
	);

	/** Section **/

	$section = 'frontpage';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Frontpage - Modules', 'magazine-news-byte' ),
	);

	$widget_area_options = array(
		'columns' => array(
			'label'   => esc_html__( 'Columns', 'magazine-news-byte' ),
			'type'    => 'select',
			'choices' => array(
				'100'         => esc_html__( 'One Column [100]', 'magazine-news-byte' ),
				'50-50'       => esc_html__( 'Two Columns [50 50]', 'magazine-news-byte' ),
				'33-66'       => esc_html__( 'Two Columns [33 66]', 'magazine-news-byte' ),
				'66-33'       => esc_html__( 'Two Columns [66 33]', 'magazine-news-byte' ),
				'25-75'       => esc_html__( 'Two Columns [25 75]', 'magazine-news-byte' ),
				'75-25'       => esc_html__( 'Two Columns [75 25]', 'magazine-news-byte' ),
				'33-33-33'    => esc_html__( 'Three Columns [33 33 33]', 'magazine-news-byte' ),
				'25-25-50'    => esc_html__( 'Three Columns [25 25 50]', 'magazine-news-byte' ),
				'25-50-25'    => esc_html__( 'Three Columns [25 50 25]', 'magazine-news-byte' ),
				'50-25-25'    => esc_html__( 'Three Columns [50 25 25]', 'magazine-news-byte' ),
				'25-25-25-25' => esc_html__( 'Four Columns [25 25 25 25]', 'magazine-news-byte' ),
			),
		),
		'grid' => array(
			'label'    => esc_html__( 'Layout', 'magazine-news-byte' ),
			'sublabel' => esc_html__( 'The fully stretched grid layout is especially useful for displaying full width slider widgets.', 'magazine-news-byte' ),
			'type'     => 'radioimage',
			'choices'     => array(
				'boxed'   => $imagepath . 'fp-widgetarea-boxed.png',
				'stretch' => $imagepath . 'fp-widgetarea-stretch.png',
			),
			'default'  => 'boxed',
		),
		'modulebg' => array(
			'label'       => '',
			'type'        => 'content',
			'content'     => '<div class="button">' . esc_html__( 'Module Background', 'magazine-news-byte' ) . '</div>',
		),
	);

	$settings['frontpage_sections'] = array(
		'label'       => esc_html__( 'Frontpage Widget Areas', 'magazine-news-byte' ),
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'sublabel'    => sprintf( esc_html__( '%1$s%3$sSort different sections of the Frontpage in the order you want them to appear.%4$s%3$sYou can add content to widget areas from the %5$sWidgets Management screen%6$s.%4$s%3$sYou can disable areas by clicking the "eye" icon. (This will hide them on the Widgets screen as well)%4$s%2$s', 'magazine-news-byte' ), '<ul>', '</ul>', '<li>', '</li>', '<a href="' . esc_url( admin_url('widgets.php') ) . '" target="_blank">', '</a>' ),
		'section'     => $section,
		'type'        => 'sortlist',
		'choices'     => array(
			'area_a'      => esc_html__( 'Widget Area A', 'magazine-news-byte' ),
			'area_b'      => esc_html__( 'Widget Area B', 'magazine-news-byte' ),
			'area_c'      => esc_html__( 'Widget Area C', 'magazine-news-byte' ),
			'area_d'      => esc_html__( 'Widget Area D', 'magazine-news-byte' ),
			'content'     => esc_html__( 'Frontpage Content', 'magazine-news-byte' ),
			'area_e'      => esc_html__( 'Widget Area E', 'magazine-news-byte' ),
			'area_f'      => esc_html__( 'Widget Area F', 'magazine-news-byte' ),
			'area_g'      => esc_html__( 'Widget Area G', 'magazine-news-byte' ),
			'area_h'      => esc_html__( 'Widget Area H', 'magazine-news-byte' ),
			'area_i'      => esc_html__( 'Widget Area I', 'magazine-news-byte' ),
			'area_j'      => esc_html__( 'Widget Area J', 'magazine-news-byte' ),
			'area_k'      => esc_html__( 'Widget Area K', 'magazine-news-byte' ),
			'area_l'      => esc_html__( 'Widget Area L', 'magazine-news-byte' ),
		),
		'default'     => array(
			// 'content' => array( 'sortitem_hide' => 1, ),
			'area_b'  => array( 'columns' => '50-50' ),
			'area_f'  => array( 'sortitem_hide' => 1, ),
			'area_g'  => array( 'sortitem_hide' => 1, ),
			'area_h'  => array( 'sortitem_hide' => 1, ),
			'area_i'  => array( 'sortitem_hide' => 1, ),
			'area_j'  => array( 'sortitem_hide' => 1, ),
			'area_k'  => array( 'sortitem_hide' => 1, ),
			'area_l'  => array( 'sortitem_hide' => 1, ),
		),
		'options'     => array(
			'area_a'      => $widget_area_options,
			'area_b'      => $widget_area_options,
			'area_c'      => $widget_area_options,
			'area_d'      => $widget_area_options,
			'area_e'      => $widget_area_options,
			'area_f'      => $widget_area_options,
			'area_g'      => $widget_area_options,
			'area_h'      => $widget_area_options,
			'area_i'      => $widget_area_options,
			'area_j'      => $widget_area_options,
			'area_k'      => $widget_area_options,
			'area_l'      => $widget_area_options,
			'content'     => array(
							'title' => array(
								'label'       => esc_html__( 'Title (optional)', 'magazine-news-byte' ),
								'type'        => 'text',
							),
							'modulebg' => array(
								'label'       => '',
								'type'        => 'content',
								'content'     => '<div class="button">' . esc_html__( 'Module Background', 'magazine-news-byte' ) . '</div>',
							), ),
		),
		'attributes'  => array(
			'hideable'      => true,
			'sortable'      => true,
			'open-state'    => 'area_a',
		),
		// /* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		// 'description' => sprintf( esc_html__( 'You must first save the changes you make here and refresh this screen for widget areas to appear in the Widgets panel (in customizer). Once you save the settings, you can add content to these widget areas using the %1$sWidgets Management screen%2$s.', 'magazine-news-byte' ), '<a href="' . esc_url( admin_url('widgets.php') ) . '" target="_blank">', '</a>' ),
	);

	$settings['frontpage_content_desc'] = array(
		'label'       => esc_html__( "Frontpage Content", 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'content',
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'content'     => sprintf( esc_html__( 'The "Frontpage Content" module in above list will show %1$s%3$sthe %5$s"Blog"%6$s if you have %5$sYour Latest Posts%6$s selectd in %7$sReading Settings%8$s %4$s%3$sthe %5$s"Page Content"%6$s of the page set as Front page if you have %5$sA static page%6$s selected in %7$sReading Settings%8$s %4$s%2$s',
				'magazine-news-byte' ), "<ul style='list-style:disc;margin:1em 0 0 2em;'>", '</ul>', '<li>', '</li>', '<strong>', '</strong>',
									 '<a href="' . esc_url( admin_url('options-reading.php') ) . '" target="_blank">', '</a>' ),
	);

	$frontpagemodule_bg = apply_filters( 'magnb_frontpage_widgetarea_sectionbg_index', array(
		'area_a'      => esc_html__( 'Widget Area A', 'magazine-news-byte' ),
		'area_b'      => esc_html__( 'Widget Area B', 'magazine-news-byte' ),
		'area_c'      => esc_html__( 'Widget Area C', 'magazine-news-byte' ),
		'area_d'      => esc_html__( 'Widget Area D', 'magazine-news-byte' ),
		'area_e'      => esc_html__( 'Widget Area E', 'magazine-news-byte' ),
		'area_f'      => esc_html__( 'Widget Area F', 'magazine-news-byte' ),
		'area_g'      => esc_html__( 'Widget Area G', 'magazine-news-byte' ),
		'area_h'      => esc_html__( 'Widget Area H', 'magazine-news-byte' ),
		'area_i'      => esc_html__( 'Widget Area I', 'magazine-news-byte' ),
		'area_j'      => esc_html__( 'Widget Area J', 'magazine-news-byte' ),
		'area_k'      => esc_html__( 'Widget Area K', 'magazine-news-byte' ),
		'area_l'      => esc_html__( 'Widget Area L', 'magazine-news-byte' ),
		'content'     => esc_html__( 'Frontpage Content', 'magazine-news-byte' ),
		) );

	foreach ( $frontpagemodule_bg as $fpgmodid => $fpgmodname ) {

		$settings["frontpage_sectionbg_{$fpgmodid}"] = array(
			'label'       => '',
			'section'     => $section,
			'type'        => 'group',
			'priority'    => 255,
			'startwrap'   => 'fp-section-bg-button',
			'button'      => esc_html__( 'Module Background', 'magazine-news-byte' ),
			'options'     => array(
				'description' => array(
					'label'       => '',
					'type'        => 'content',
					'content'     => '<span class="hoot-module-bg-title">' . $fpgmodname . '</span>',
				),
				'type' => array(
					'label'   => esc_html__( 'Background Type', 'magazine-news-byte' ),
					'type'    => 'radio',
					'choices' => array(
						'none'        => esc_html__( 'None', 'magazine-news-byte' ),
						// 'highlight'   => esc_html__( 'Highlight', 'magazine-news-byte' ),
						'color'       => esc_html__( 'Color', 'magazine-news-byte' ),
						'image'       => esc_html__( 'Image', 'magazine-news-byte' ),
					),
					'default' => 'none',
					// 'default' => ( ( $fpgmodid == 'area_b' ) ? 'image' :
					// 											( ( $fpgmodid == 'area_d' ) ? 'highlight' : 'none' )
					// 			 ),
					// 'default' => ( ( $fpgmodid == 'area_b' ) ? 'image' : 'none' ),
					'transport' => 'postMessage',
				),
				'color' => array(
					'label'       => esc_html__( "Background Color (Select 'Color' above)", 'magazine-news-byte' ),
					'type'        => 'color',
					'default'     => $module_bg_default,
					'transport' => 'postMessage',
				),
				'image' => array(
					'label'       => esc_html__( "Background Image (Select 'Image' above)", 'magazine-news-byte' ),
					'type'        => 'image',
					// 'default' => ( ( $fpgmodid == 'area_b' ) ? hoot_data()->template_uri . 'images/modulebg.jpg' : '' ),
					'transport' => 'postMessage',
				),
				'parallax' => array(
					'label'   => esc_html__( 'Apply Parallax Effect to Background Image', 'magazine-news-byte' ),
					'type'    => 'checkbox',
					// 'default' => 1,
					// 'default' => ( ( $fpgmodid == 'area_b' ) ? 1 : 0 ),
				),
				'font' => array(
					'label'   => esc_html__( 'Font Color', 'magazine-news-byte' ),
					'type'    => 'radio',
					'choices' => array(
						'theme'       => esc_html__( 'Default Theme Color', 'magazine-news-byte' ),
						'color'       => esc_html__( 'Custom Font Color', 'magazine-news-byte' ),
						'force'       => esc_html__( 'Force Custom Font Color', 'magazine-news-byte' ),
					),
					'default' => 'theme',
					'transport' => 'postMessage',
				),
				'fontcolor' => array(
					'label'       => esc_html__( "Custom Font Color (select 'Custom Font Color' above)", 'magazine-news-byte' ),
					'type'        => 'color',
					'default'     => '#aaaaaa',
					'transport' => 'postMessage',
				),
			),
		);

	} // end for

	/** Section **/

	$section = 'archives';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Archives (Blog, Cats, Tags)', 'magazine-news-byte' ),
	);

	$settings['archive_type'] = array(
		'label'       => esc_html__( 'Archive (Blog) Layout', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radioimage',
		'choices'     => array(
			'big'          => $imagepath . 'archive-big.png',
			'block2'       => $imagepath . 'archive-block2.png',
			'block3'       => $imagepath . 'archive-block3.png',
			'mixed-block2' => $imagepath . 'archive-mixed-block2.png',
			'mixed-block3' => $imagepath . 'archive-mixed-block3.png',
		),
		'default'     => 'mixed-block2',
	);

	$settings['archive_post_content'] = array(
		'label'       => esc_html__( 'Post Items Content', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'none' => esc_html__( 'None', 'magazine-news-byte' ),
			'excerpt' => esc_html__( 'Post Excerpt', 'magazine-news-byte' ),
			'full-content' => esc_html__( 'Full Post Content', 'magazine-news-byte' ),
		),
		'default'     => 'excerpt',
		'description' => esc_html__( 'Content to display for each post in the list', 'magazine-news-byte' ),
	);

	$settings['archive_post_meta'] = array(
		'label'       => esc_html__( 'Meta Information for Post List Items', 'magazine-news-byte' ),
		'sublabel'    => esc_html__( 'Check which meta information to display for each post item in the archive list.', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		'choices'     => array(
			'author'   => esc_html__( 'Author', 'magazine-news-byte' ),
			'date'     => esc_html__( 'Date', 'magazine-news-byte' ),
			'cats'     => esc_html__( 'Categories', 'magazine-news-byte' ),
			'tags'     => esc_html__( 'Tags', 'magazine-news-byte' ),
			'comments' => esc_html__( 'No. of comments', 'magazine-news-byte' ),
		),
		'default'     => 'author, date, cats, comments',
		'selective_refresh' => array( 'archive_post_meta_partial', array(
			'selector'            => '.blog .entry-byline, .home .entry-byline, .plural .entry-byline',
			'settings'            => array( 'archive_post_meta' ),
			'render_callback'     => 'magnb_callback_archive_post_meta',
			'container_inclusive' => true,
			'fallback_refresh'    => false, // prevents full refresh on non applicable views
			) ),
	);

	$settings['excerpt_length'] = array(
		'label'       => esc_html__( 'Excerpt Length', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'text',
		'description' => esc_html__( 'Number of words in excerpt. Default is 50. Leave empty if you dont want to change it.', 'magazine-news-byte' ),
		'input_attrs' => array(
			'placeholder' => esc_html__( 'default: 50', 'magazine-news-byte' ),
		),
	);

	$settings['read_more'] = array(
		'label'       => esc_html__( "'Continue Reading' Text", 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'text',
		'description' => esc_html__( "Replace the default 'Continue Reading' text. Leave empty if you dont want to change it.", 'magazine-news-byte' ),
		'input_attrs' => array(
			'placeholder' => esc_html__( 'default: Continue Reading', 'magazine-news-byte' ),
		),
		'default'     => esc_html__( 'Continue Reading', 'magazine-news-byte' ),
		// 'transport' => 'postMessage', // Interferes with defaults of hootkit widgets, custom user input readmore text in hootkit widgets
	);

	/** Section **/

	$section = 'singular';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Single (Posts, Pages)', 'magazine-news-byte' ),
	);

	$settings['page_header_full'] = array(
		'label'       => esc_html__( 'Stretch Page Title Area to Full Width', 'magazine-news-byte' ),
		'sublabel'    => '<img src="' . $imagepath . 'page-header.png">',
		'section'     => $section,
		'type'        => 'checkbox',
		'choices'     => array(
			'default'    => esc_html__( 'Default (Archives, Blog, Woocommerce etc.)', 'magazine-news-byte' ),
			'posts'      => esc_html__( 'For All Posts', 'magazine-news-byte' ),
			'pages'      => esc_html__( 'For All Pages', 'magazine-news-byte' ),
			'no-sidebar' => esc_html__( 'Always override for full width pages (any page which has no sidebar)', 'magazine-news-byte' ),
		),
		'default'     => 'default, pages, no-sidebar',
		'description' => esc_html__( 'This is the Page Header area containing Page/Post Title and Meta details like author, categories etc.', 'magazine-news-byte' ),
	);

	$settings['post_featured_image'] = array(
		'label'       => esc_html__( 'Display Featured Image (Post)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'select',
		'choices'     => array(
			'none'                => esc_html__( 'Do not display', 'magazine-news-byte'),
			'staticheader-nocrop' => esc_html__( 'Header Background (No Cropping)', 'magazine-news-byte'),
			'staticheader'        => esc_html__( 'Header Background (Cropped)', 'magazine-news-byte'),
			'header'              => esc_html__( 'Header Background (Parallax Effect)', 'magazine-news-byte'),
			'content'             => esc_html__( 'Beginning of content', 'magazine-news-byte'),
		),
		'default'     => 'content',
		'description' => esc_html__( 'Display featured image on a Post page.', 'magazine-news-byte' ),
	);

	$settings['post_featured_image_page'] = array(
		'label'       => esc_html__( 'Display Featured Image (Page)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'select',
		'choices'     => array(
			'none'                => esc_html__( 'Do not display', 'magazine-news-byte'),
			'staticheader-nocrop' => esc_html__( 'Header Background (No Cropping)', 'magazine-news-byte'),
			'staticheader'        => esc_html__( 'Header Background (Cropped)', 'magazine-news-byte'),
			'header'              => esc_html__( 'Header Background (Parallax Effect)', 'magazine-news-byte'),
			'content'             => esc_html__( 'Beginning of content', 'magazine-news-byte'),
		),
		'default'     => 'header',
		'description' => esc_html__( "Display featured image on a 'Page' page.", 'magazine-news-byte' ),
	);

	$settings['post_meta'] = array(
		'label'       => esc_html__( 'Meta Information on Posts', 'magazine-news-byte' ),
		'sublabel'    => esc_html__( "Check which meta information to display on an individual 'Post' page", 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		'choices'     => array(
			'author'   => esc_html__( 'Author', 'magazine-news-byte' ),
			'date'     => esc_html__( 'Date', 'magazine-news-byte' ),
			'cats'     => esc_html__( 'Categories', 'magazine-news-byte' ),
			'tags'     => esc_html__( 'Tags', 'magazine-news-byte' ),
			'comments' => esc_html__( 'No. of comments', 'magazine-news-byte' )
		),
		'default'     => 'author, date, cats, tags, comments',
		'selective_refresh' => array( 'post_meta_partial', array(
			'selector'            => '.singular-post .entry-byline',
			'settings'            => array( 'post_meta' ),
			'render_callback'     => 'magnb_callback_post_meta',
			'container_inclusive' => true,
			'fallback_refresh'    => false, // prevents full refresh on non applicable views
			) ),
	);

	$settings['page_meta'] = array(
		'label'       => esc_html__( 'Meta Information on Page', 'magazine-news-byte' ),
		'sublabel'    => esc_html__( "Check which meta information to display on an individual 'Page' page", 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		'choices'     => array(
			'author'   => esc_html__( 'Author', 'magazine-news-byte' ),
			'date'     => esc_html__( 'Date', 'magazine-news-byte' ),
			'comments' => esc_html__( 'No. of comments', 'magazine-news-byte' ),
		),
		'default'     => 'author, date, comments',
		'selective_refresh' => array( 'page_meta_partial', array(
			'selector'            => '.singular-page .entry-byline',
			'settings'            => array( 'page_meta' ),
			'render_callback'     => 'magnb_callback_page_meta',
			'container_inclusive' => true,
			'fallback_refresh'    => false, // prevents full refresh on non applicable views
			) ),
	);

	$settings['post_meta_location'] = array(
		'label'       => esc_html__( 'Meta Information location', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radio',
		'choices'     => array(
			'top'    => esc_html__( 'Top (below title)', 'magazine-news-byte' ),
			'bottom' => esc_html__( 'Bottom (after content)', 'magazine-news-byte' ),
		),
		'default'     => 'top',
	);

	$settings['post_prev_next_links'] = array(
		'label'       => esc_html__( 'Previous/Next Posts', 'magazine-news-byte' ),
		'sublabel'    => esc_html__( 'Display links to Prev/Next Post links at the end of post content.', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'checkbox',
		'default'     => 1,
		'selective_refresh' => array( 'post_prev_next_links_partial', array(
			'selector'            => '#loop-nav-wrap',
			'settings'            => array( 'post_prev_next_links' ),
			'render_callback'     => 'magnb_post_prev_next_links',
			'container_inclusive' => true,
			'fallback_refresh'    => false, // prevents full refresh on non applicable views
			) ),
	);

	$settings['contact-form'] = array(
		'label'       => esc_html__( 'Contact Form', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'content',
		'priority'    => '375', // Non static options must have a priority
		/* Translators: 1 is the link start markup, 2 is link markup end */
		'content'     => sprintf( esc_html__( 'This theme comes bundled with CSS required to style %1$sContact-Form-7%2$s forms. Simply install and activate the plugin to add Contact Forms to your pages.', 'magazine-news-byte' ), '<a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">', '</a>' ), // @todo update link to docs
	);

	/** Section **/

	$section = 'footer';

	$sections[ $section ] = array(
		'title'       => esc_html__( 'Footer', 'magazine-news-byte' ),
	);

	$settings['footer'] = array(
		'label'       => esc_html__( 'Footer Layout', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'radioimage',
		'choices'     => array(
			'1-1' => $imagepath . '1-1.png',
			'2-1' => $imagepath . '2-1.png',
			'2-2' => $imagepath . '2-2.png',
			'2-3' => $imagepath . '2-3.png',
			'3-1' => $imagepath . '3-1.png',
			'3-2' => $imagepath . '3-2.png',
			'3-3' => $imagepath . '3-3.png',
			'3-4' => $imagepath . '3-4.png',
			'4-1' => $imagepath . '4-1.png',
		),
		'default'     => '4-1',
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'description' => sprintf( esc_html__( 'You must first save the changes you make here and refresh this screen for footer columns to appear in the Widgets panel (in customizer).%3$s Once you save the settings here, you can add content to footer columns using the %1$sWidgets Management screen%2$s.', 'magazine-news-byte' ), '<a href="' . esc_url( admin_url('widgets.php') ) . '" target="_blank">', '</a>', '<hr>' ),
		'transport' => 'postMessage',
	);

	$settings['site_info'] = array(
		'label'       => esc_html__( 'Site Info Text (footer)', 'magazine-news-byte' ),
		'section'     => $section,
		'type'        => 'textarea',
		'default'     => esc_html__( '<!--default-->', 'magazine-news-byte'),
		/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
		'description' => sprintf( esc_html__( 'Text shown in footer. Useful for showing copyright info etc.%3$sUse the %4$s&lt;!--default--&gt;%5$s tag to show the default Info Text.%3$sUse the %4$s&lt;!--year--&gt;%5$s tag to insert the current year.%3$sAlways use %1$sHTML codes%2$s for symbols. For example, the HTML for &copy; is %4$s&amp;copy;%5$s', 'magazine-news-byte' ), '<a href="http://ascii.cl/htmlcodes.htm" target="_blank">', '</a>', '<hr>', '<code>', '</code>' ),
		'transport' => 'postMessage',
	);


	/*** Return Options Array ***/
	return apply_filters( 'magnb_customizer_options', array(
		'settings' => $settings,
		'sections' => $sections,
		'panels'   => $panels,
	) );

}
endif;

/**
 * Add Options (settings, sections and panels) to Hoot_Customize class options object
 *
 * @since 1.0
 * @access public
 * @return void
 */
if ( !function_exists( 'magnb_add_customizer_options' ) ) :
function magnb_add_customizer_options() {

	$hoot_customize = Hoot_Customize::get_instance();

	// Add Options
	$options = magnb_customizer_options();
	$hoot_customize->add_options( array(
		'settings' => $options['settings'],
		'sections' => $options['sections'],
		'panels' => $options['panels'],
		) );

}
endif;
add_action( 'init', 'magnb_add_customizer_options', 0 ); // cannot hook into 'after_setup_theme' as this hook is already being executed (this file is loaded at after_setup_theme @priority 10) (hooking into same hook from within while hook is being executed leads to undesirable effects as $GLOBALS[$wp_filter]['after_setup_theme'] has already been ksorted)
// Hence, we hook into 'init' @priority 0, so that settings array gets populated before 'widgets_init' action ( which itself is hooked to 'init' at priority 1 ) for creating widget areas ( settings array is needed for creating defaults when user value has not been stored )

/**
 * Enqueue custom scripts to customizer screen
 *
 * @since 1.0
 * @return void
 */
function magnb_customizer_enqueue_scripts() {
	// Enqueue Styles
	$style_uri = ( function_exists( 'hoot_locate_style' ) ) ? hoot_locate_style( hoot_data()->incuri . 'admin/css/customize' ) : hoot_data()->incuri . 'admin/css/customize.css';
	wp_enqueue_style( 'magnb-customize-styles', $style_uri, array(),  hoot_data()->hoot_version );
	// Enqueue Scripts
	$script_uri = ( function_exists( 'hoot_locate_script' ) ) ? hoot_locate_script( hoot_data()->incuri . 'admin/js/customize-controls' ) : hoot_data()->incuri . 'admin/js/customize-controls.js';
	wp_enqueue_script( 'magnb-customize-controls', $script_uri, array( 'jquery', 'wp-color-picker', 'customize-controls', 'hoot-customize' ), hoot_data()->hoot_version, true );
}
// Load scripts at priority 12 so that Hoot Customizer Interface (11) / Custom Controls (10) have loaded their scripts
add_action( 'customize_controls_enqueue_scripts', 'magnb_customizer_enqueue_scripts', 12 );

/**
 * Modify default WordPress Settings Sections and Panels
 *
 * @since 1.0
 * @param object $wp_customize
 * @return void
 */
function magnb_modify_default_customizer_options( $wp_customize ) {

	/**
	 * Defaults: [type] => cropped_image
	 *           [width] => 150
	 *           [height] => 150
	 *           [flex_width] => 1
	 *           [flex_height] => 1
	 *           [button_labels] => array(...)
	 *           [label] => Logo
	 */
	$wp_customize->get_control( 'custom_logo' )->section = 'logo';
	$wp_customize->get_control( 'custom_logo' )->priority = 175;
	$wp_customize->get_control( 'custom_logo' )->width = 300;
	$wp_customize->get_control( 'custom_logo' )->height = 180;
	// $wp_customize->get_control( 'custom_logo' )->type = 'image'; // Stored value becomes url instead of image ID (fns like the_custom_logo() dont work)
	$wp_customize->get_control( 'custom_logo' )->active_callback = 'magnb_callback_logo_image';

	if ( function_exists( 'get_site_icon_url' ) )
		$wp_customize->get_control( 'site_icon' )->priority = 10;

	$wp_customize->get_section( 'static_front_page' )->priority = 3;
	if ( current_theme_supports( 'custom-header' ) ) {
		$wp_customize->get_section( 'header_image' )->priority = 28;
		$wp_customize->get_section( 'header_image' )->title = esc_html__( 'Frontpage - Header Image', 'magazine-news-byte' );
	}

	// Backgrounds
	if ( current_theme_supports( 'custom-background' ) ) {
		$wp_customize->get_control( 'background_color' )->label =  esc_html__( 'Site Background Color', 'magazine-news-byte' );
		$wp_customize->get_section( 'background_image' )->priority = 23;
		$wp_customize->get_section( 'background_image' )->title = esc_html__( 'Site Background Image', 'magazine-news-byte' );
	}

	// $wp_customize->get_section( 'title_tagline' )->panel = 'general';
	// $wp_customize->get_section( 'title_tagline' )->priority = 1;
	// $wp_customize->get_section( 'colors' )->panel = 'styling';
	// 	$wp_customize->get_panel( 'nav_menus' )->priority = 999;
	// This will set the priority, however will give a 'Creating Default Object from Empty Value' error first.
	// $wp_customize->get_panel( 'widgets' )->priority = 999;

}
add_action( 'customize_register', 'magnb_modify_default_customizer_options', 100 );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since 1.0
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function magnb_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	$wp_customize->get_setting( 'custom_logo' )->transport = 'postMessage';
}
add_action( 'customize_register', 'magnb_customize_register' );

/**
 * Add style tag to support dynamic css via postMessage script in customizer preview
 *
 * @since 2.7
 * @access public
 */
function magnb_customize_dynamic_cssrules() {
	// Add in Customizer Only
	if ( is_customize_preview() ) {
		$handle = apply_filters( 'hoot_style_builder_inline_style_handle', 'hoot-style' );
		$settings = apply_filters( 'hoot_customize_dynamic_selectors', array(
			'site_width', 'widgetmargin', 'site_title_icon_size', 'logo_image_width', 'box_background_color', 'accent_color', 'accent_font', 'headings_fontface_style',
			) ); // Array of settings available for preview script to create dynamic css style tags
		$hootpload = ( function_exists( 'hoot_lib_premium_core' ) ) ? 1 : '';
		wp_localize_script( 'hoot-customize-preview', 'hootInlineStyles', array( $handle, $settings, $hootpload ) );
	}
}
add_action( 'wp_enqueue_scripts', 'magnb_customize_dynamic_cssrules', 999 );

/**
 * Callback Functions for customizer settings
 */

function magnb_callback_logo_side( $control ) {
	$selector = $control->manager->get_setting('menu_location')->value();
	return ( $selector == 'top' || $selector == 'bottom' || $selector == 'none' ) ? true : false;
}
function magnb_callback_logo_size( $control ) {
	$selector = $control->manager->get_setting('logo')->value();
	return ( $selector == 'text' || $selector == 'mixed' ) ? true : false;
}
function magnb_callback_site_title_icon( $control ) {
	$selector = $control->manager->get_setting('logo')->value();
	return ( $selector == 'text' || $selector == 'custom' ) ? true : false;
}
function magnb_callback_logo_image( $control ) {
	$selector = $control->manager->get_setting('logo')->value();
	return ( $selector == 'image' || $selector == 'mixed' || $selector == 'mixedcustom' ) ? true : false;
}
function magnb_callback_logo_image_width( $control ) {
	$selector = $control->manager->get_setting('logo')->value();
	return ( $selector == 'mixed' || $selector == 'mixedcustom' ) ? true : false;
}
function magnb_callback_logo_custom( $control ) {
	$selector = $control->manager->get_setting('logo')->value();
	return ( $selector == 'custom' || $selector == 'mixedcustom' ) ? true : false;
}
// function magnb_callback_show_tagline( $control ) {
// 	$selector = $control->manager->get_setting('logo')->value();
// 	return ( $selector == 'text' || $selector == 'custom' || $selector == 'mixed' || $selector == 'mixedcustom' ) ? true : false;
// }

/**
 * Callback Functions for selective refresh
 */

function magnb_callback_archive_post_meta(){
	$metarray = hoot_get_mod('archive_post_meta');
	hoot_display_meta_info( $metarray, 'customizer' ); // Bug: the_author_posts_link() does not work in selective refresh
}
function magnb_callback_post_meta(){
	$metarray = hoot_get_mod('post_meta');
	hoot_display_meta_info( $metarray, 'customizer' ); // Bug: the_author_posts_link() does not work in selective refresh
}
function magnb_callback_page_meta(){
	$metarray = hoot_get_mod('page_meta');
	hoot_display_meta_info( $metarray, 'customizer' ); // Bug: the_author_posts_link() does not work in selective refresh
}