<?php
/**
 * Multipage Admin Premium.
 *
 * @package Multipage
 * @since 1.5
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main settings section description for the settings page.
 *
 * @since 1.5
 */
function mpp_admin_premium_callback_main_section() { }

/** Premium Page *********************************************************************/

/**
 * The premium page
 *
 * @since 1.5
 *
 */
function mpp_admin_premium() {
	// We're saving our own options, until the WP Settings API is updated to work with Multisite.
	$form_action = add_query_arg( 'page', 'mpp-premium', mpp_get_admin_url( 'options-general.php' ) );

	?>

	<div class="wrap">

		<h1><?php _e( 'Multipage Settings', 'sgr-nextpage-titles' ); ?></h1>
		
		<h2 class="nav-tab-wrapper"><?php mpp_admin_tabs( __( 'Premium', 'sgr-nextpage-titles' ) ); ?></h2>
		
		<h2><?php _e( 'Multipage Premium', 'sgr-nextpage-titles' ); ?></h2>
		
		<p><?php _e( 'Please leave this settings to their default values, change only if you really know what to do.', 'sgr-nextpage-titles' ); ?></p>
		
	</div><!-- .wrap -->
	
<?php
}
