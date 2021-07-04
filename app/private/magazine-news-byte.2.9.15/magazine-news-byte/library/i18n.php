<?php
/**
 * Internationalization and translation functions. These functions handle properly loading translation
 * files for both the parent and child themes. It also contains some language functions for use by the theme.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

# Load translations for theme and child theme
add_action( 'after_setup_theme', 'hoot_load_textdomains', 5 );

# Filter the textdomain mofile to allow child themes to load the parent theme translation.
add_filter( 'load_textdomain_mofile', 'hoot_load_textdomain_mofile', 10, 2 );

/**
 * Loads the theme and child theme textdomains automatically. This also utilizes the 'Domain Path' header
 * from 'style.css'. It defaults to the 'languages' folder.
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_load_textdomains() {

	if ( is_child_theme() ) {

		// Load theme textdomain.
		if ( is_object( hoot_data( 'theme' )->parent() ) ) {
			$parent_domain = hoot_data( 'theme' )->parent()->get( 'TextDomain' );
			$parent_domainpath = hoot_data( 'theme' )->parent()->get( 'DomainPath' );
		}
		$textdomain = ( !empty( $parent_domain ) ) ? $parent_domain : get_template();
		$textdomain = sanitize_key( $textdomain );
		$path = ( !empty( $parent_domainpath ) ) ? trim( $parent_domainpath, '/' ) : 'languages';
		load_theme_textdomain( $textdomain, hoot_data()->template_dir . $path );

		// Load child theme textdomain.
		$child_domain = hoot_data( 'theme' )->get( 'TextDomain' );
		$child_domainpath = hoot_data( 'theme' )->get( 'DomainPath' );
		$textdomain = ( !empty( $child_domain ) ) ? $child_domain : get_template();
		$textdomain = sanitize_key( $textdomain );
		$path = ( !empty( $child_domainpath ) ) ? trim( $child_domainpath, '/' ) : 'languages';
		load_child_theme_textdomain( $textdomain, hoot_data()->child_dir . $path );

	} else {

		// Load theme textdomain.
		$theme_domain = hoot_data( 'theme' )->get( 'TextDomain' );
		$theme_domainpath = hoot_data( 'theme' )->get( 'DomainPath' );
		$textdomain = ( !empty( $theme_domain ) ) ? $theme_domain : get_template();
		$textdomain = sanitize_key( $textdomain );
		$path = ( !empty( $theme_domainpath ) ) ? trim( $theme_domainpath, '/' ) : 'languages';
		load_theme_textdomain( $textdomain, hoot_data()->template_dir . $path );

	}

}

/**
 * Filters the 'load_textdomain_mofile' filter hook so that we can change the directory and file name
 * of the mofile for translations. This allows child themes to have a folder called /languages with translations
 * of their parent theme so that the translations aren't lost on a parent theme upgrade.
 *
 * @since 3.0.0
 * @access public
 * @param string $mofile File name of the .mo file.
 * @param string $domain The textdomain currently being filtered.
 * @return string
 */
function hoot_load_textdomain_mofile( $mofile, $domain ) {

	$theme_domain = hoot_data( 'theme' )->get( 'TextDomain' );
	$themedomain = ( !empty( $theme_domain ) ) ? $theme_domain : get_template();
	$themedomain = sanitize_key( $themedomain );
	if ( is_child_theme() ) {
		if ( is_object( hoot_data( 'theme' )->parent() ) )
			$parent_domain = hoot_data( 'theme' )->parent()->get( 'TextDomain' );
		$templatedomain = ( !empty( $parent_domain ) ) ? $parent_domain : get_template();
		$templatedomain = sanitize_key( $templatedomain );
	} else {
		$templatedomain = $themedomain;
	}

	// If the $domain is for the parent or child theme, search for a $domain-$locale.mo file.
	if ( $domain == $themedomain || $domain == $templatedomain ) {

		// Get the locale.
		$locale = get_locale();

		// Get just the theme path and file name for the mofile.
		$mofile_short = str_replace( "{$locale}.mo", "{$domain}-{$locale}.mo", $mofile );
		$mofile_short = str_replace( array( hoot_data()->template_dir, hoot_data()->child_dir ), '', $mofile_short );

		// Attempt to find the correct mofile.
		$locate_mofile = locate_template( array( $mofile_short ) );

		// Return the mofile.
		return $locate_mofile ? $locate_mofile : $mofile;
	}

	return $mofile;
}

/**
 * Gets the language for the currently-viewed page. It strips the region from the locale if needed
 * and just returns the language code.
 *
 * @since 3.0.0
 * @access public
 * @param string  $locale
 * @return string
 */
function hoot_get_language( $locale = '' ) {

	if ( ! $locale )
		$locale = get_locale();

	return sanitize_key( preg_replace( '/(.*?)_.*?$/i', '$1', $locale ) );
}

/**
 * Gets the region for the currently viewed page.  It strips the language from the locale if needed.  Note that
 * not all locales will have a region, so this might actually return the same thing as 'hoot_get_language()'.
 *
 * @since 3.0.0
 * @access public
 * @param string  $locale
 * @return string
 */
function hoot_get_region( $locale = '' ) {

	if ( ! $locale )
		$locale = get_locale();

	return sanitize_key( preg_replace( '/.*?_(.*?)$/i', '$1', $locale ) );
}