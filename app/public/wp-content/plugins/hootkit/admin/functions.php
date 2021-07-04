<?php
/**
 * Functions for themes.php
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Reorder parent theme to after active child theme in themes.php
 * Located in fn wp_prepare_themes_for_js() in wp-admin/includes/theme.php
 *
 * @since 1.0.11
 * @param array $prepared_themes Array of themes.
 * @return array
 */
if ( !function_exists( 'hootkit_prepare_themes_for_display' ) ):
function hootkit_prepare_themes_for_display( $prepared_themes ) {
	if ( is_child_theme() ) {
		$template_name = get_template();
		$child_name = get_stylesheet();
		if ( isset( $prepared_themes[ $template_name ] ) && isset( $prepared_themes[ $child_name ] ) ) {
			$cachechild = array( $child_name => $prepared_themes[ $child_name ] );
			$cachetemplate = array( $template_name => $prepared_themes[ $template_name ] );
			unset( $prepared_themes[ $child_name ] );
			unset( $prepared_themes[ $template_name ] );
			return $cachechild + $cachetemplate + $prepared_themes;
		}
	}
	return $prepared_themes;
};
endif;
add_filter( 'wp_prepare_themes_for_js', 'hootkit_prepare_themes_for_display' );

/**
 * Change background color and font weight for Parent Theme Title
 *
 * @since 1.0.11
 * @param string $hook
 * @return void
 */
if ( !function_exists( 'hootkit_parent_theme_title_style' ) ):
function hootkit_parent_theme_title_style( $hook ) {
	if ( 'themes.php' == $hook && is_child_theme() ) {
		echo '<style>.theme.active + .theme .theme-name { background: #515d69; color: #fff; font-weight: 300; } .theme.active + .theme .theme-name:before { content: "Parent: "; font-weight: bold; }</style>';
	}
}
endif;
add_action( 'admin_enqueue_scripts', 'hootkit_parent_theme_title_style' );

/**
 * Reorder menu items
 *
 * @since 1.0.3
 * @access public
 * @param array $attr
 * @param string $context
 * @return array
 */
if ( !function_exists( 'hootkit_reorder_custom_options_page' ) ):
function hootkit_reorder_custom_options_page() {
	global $submenu;
	$themelist = hootkit()->get_config( 'themelist' );
	foreach ( $themelist as &$tlval )
		$tlval = $tlval . '-welcome';
	$indexes = array();
	if ( !isset( $submenu['themes.php'] ) )
		return;
	foreach ( $submenu['themes.php'] as $key => $sm ) {
		if ( $sm[2] == 'tgmpa-install-plugins' ) {
			$indexes[] = $key; break;
	} }
	foreach ( $submenu['themes.php'] as $key => $sm ) {
		if ( in_array( $sm[2], $themelist ) ) {
			$indexes[] = $key; break;
	} }

	foreach ( $indexes as $index ) { if ( ! empty( $index ) ) {
		//$item = $submenu['themes.php'][ $index ];
		//unset( $submenu['themes.php'][ $index ] );
		//array_splice( $submenu['themes.php'], 1, 0, array($item) );
		/* array_splice does not preserve numeric keys, so instead we do our own rearranging. */
		$smthemes = array();
		foreach ( $submenu['themes.php'] as $key => $sm ) {
			if ( $key != $index ) {
				$setkey = $key;
				// Find next available position if current one is taken
				for ( $i = $key; $i < 1000; $i++ ) {
					if( !isset( $smthemes[$i] ) ) {
						$setkey = $i;
						break;
					}
				}
				$smthemes[ $setkey ] = $sm;
				if ( $sm[1] == 'customize' ) { // if ( $sm[2] == 'themes.php' ) {
					$smthemes[ $setkey + 1 ] = $submenu['themes.php'][ $index ];
				}
			}
		}
		$submenu['themes.php'] = $smthemes;
	} }

}
endif;
add_action( 'admin_menu', 'hootkit_reorder_custom_options_page', 9990 );