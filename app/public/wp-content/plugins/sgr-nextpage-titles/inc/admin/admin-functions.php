<?php

/**
 * This tells WP to highlight the Settings > Multipage menu item,
 * regardless of which actual Multipage admin screen we are on.
 *
 * @global string $plugin_page
 * @global array $submenu
 *
 * @since 1.4
 */
function mpp_modify_admin_menu_highlight() {
	global $plugin_page, $submenu_file;

	// This tweaks the Settings subnav menu to show only one Multipage menu item.
	if ( in_array( $plugin_page, array( 'mpp-advanced-settings', 'mpp-premium' ) ) ) {
		$submenu_file = 'mpp-settings';
	}
	
	//// Network Admin > Tools.
	//if ( in_array( $plugin_page, array( 'mpp-tools' ) ) ) {
	//	$submenu_file = $plugin_page;
	//}
}

/**
 * Output the correct admin URL based on WordPress configuration.
 *
 * @since 1.4
 *
 *
 * @param string $path   See {@link mpp_get_admin_url()}.
 * @param string $scheme See {@link mpp_get_admin_url()}.
 */
function mpp_admin_url( $path = '', $scheme = 'admin' ) {
	echo esc_url( mpp_get_admin_url( $path, $scheme ) );
}
	/**
	 * Return the correct admin URL based on WordPress configuration.
	 *
	 * @since 1.4
	 *
	 *
	 * @param string $path   Optional. The sub-path under /wp-admin to be
	 *                       appended to the admin URL.
	 * @param string $scheme The scheme to use. Default is 'admin', which
	 *                       obeys {@link force_ssl_admin()} and {@link is_ssl()}. 'http'
	 *                       or 'https' can be passed to force those schemes.
	 * @return string Admin url link with optional path appended.
	 */
	function mpp_get_admin_url( $path = '', $scheme = 'admin' ) {
		$url = admin_url( $path, $scheme );

		return $url;
	}

/**
 * Output the tabs in the admin area.
 *
 * @since 1.4
 *
 * @param string $active_tab Name of the tab that is active. Optional.
 */
function mpp_admin_tabs( $active_tab = '' ) {
	$tabs_html    = '';
	$idle_class   = 'nav-tab';
	$active_class = 'nav-tab nav-tab-active';
	
	/**
	 * Filters the admin tabs to be displayed.
	 *
	 * @since 1.4
	 *
	 * @param array $value Array of tabs to output to the admin area.
	 */
	$tabs         = apply_filters( 'mpp_admin_tabs', mpp_get_admin_tabs( $active_tab ) );

	// Loop through tabs and build navigation.
	foreach ( array_values( $tabs ) as $tab_data ) {
		$is_current = (bool) ( $tab_data['name'] == $active_tab );
		$tab_class  = $is_current ? $active_class : $idle_class;
		$tabs_html .= '<a href="' . esc_url( $tab_data['href'] ) . '" class="' . esc_attr( $tab_class ) . '">' . esc_html( $tab_data['name'] ) . '</a>';
	}

	echo $tabs_html;

	/**
	 * Fires after the output of tabs for the admin area.
	 *
	 * @since 1.4
	 */
	do_action( 'mpp_admin_tabs' );
}

/**
 * Get the data for the tabs in the admin area.
 *
 * @since 1.4
 *
 * @param string $active_tab Name of the tab that is active. Optional.
 * @return string
 */
function mpp_get_admin_tabs( $active_tab = '' ) {
	$tabs = array(
		'0' => array(
			'href' => mpp_get_admin_url( add_query_arg( array( 'page' => 'mpp-settings' ), 'options-general.php' ) ),
			'name' => __( 'Options', 'sgr-nextpage-titles' )
		),
		'1' => array(
			'href' => mpp_get_admin_url( add_query_arg( array( 'page' => 'mpp-advanced-settings' ), 'options-general.php' ) ),
			'name' => __( 'Advanced', 'sgr-nextpage-titles' )
		),
		//'2' => array(
		//	'href' => mpp_get_admin_url( add_query_arg( array( 'page' => 'mpp-premium' ), 'options-general.php' ) ),
		//	'name' => __( 'Premium', 'sgr-nextpage-titles' )
		//),
	);

	/**
	 * Filters the tab data used in our wp-admin screens.
	 *
	 * @since 1.4
	 *
	 * @param array $tabs Tab data.
	 */
	return apply_filters( 'mpp_get_admin_tabs', $tabs );
}

/**
 * Check if Block Editor is active.
 * Must only be used after plugins_loaded action is fired.
 *
 * @link https://wordpress.stackexchange.com/questions/320653/how-to-detect-the-usage-of-gutenberg
 * @return bool
 */
function mpp_is_block_editor_active() {
    // Gutenberg plugin is installed and activated.
    $gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );

    // Block editor since 5.0.
    $block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

    if ( ! $gutenberg && ! $block_editor ) {
        return false;
    }

    if ( mpp_is_classic_editor_plugin_active() ) {
        $editor_option       = get_option( 'classic-editor-replace' );
        $block_editor_active = array( 'no-replace', 'block' );

        return in_array( $editor_option, $block_editor_active, true );
    }

    return true;
}

/**
 * Check if Classic Editor plugin is active.
 *
 * @link https://wordpress.stackexchange.com/questions/320653/how-to-detect-the-usage-of-gutenberg
 * @return bool
 */
function mpp_is_classic_editor_plugin_active() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
        return true;
    }

    return false;
}

?>