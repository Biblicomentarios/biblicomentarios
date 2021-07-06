<?php
/**
 * The file that defines the trim class.
 *
 * @package    TrimPress
 * @subpackage TrimPress/classes
 */

// Define the plugin namespace.
Namespace TrimPress;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The trim class, which looks after cleanup.
 *
 * @package    TrimPress
 * @subpackage TrimPress/classes
 */
class Trim {

	/**
	 * The class constructor.
	 */
	public function __construct() {
		$this->file_edit_is_defined = defined( 'DISALLOW_FILE_EDIT' );
        $this->clean_up();
	}
	
	/**
	 * The main clean up method.
	 */
	public function clean_up() {
		$options = get_option( 'trimpress_settings' );

		if ( ! is_admin() && isset( $options['adj_posts'] ) && $options['adj_posts'] === '1' ) {
			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		}

		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			if ( ! is_admin() && isset( $options['wc_cart_fragments'] ) && $options['wc_cart_fragments'] === '1' ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'disable_cart_fragments' ), 11 );
			}
		}

		if ( ! defined( 'DISALLOW_FILE_EDIT' ) && isset( $options['editors'] ) && $options['editors'] === '1' ) {
			define( 'DISALLOW_FILE_EDIT', true );
		}
 
		if ( ! is_admin() && isset( $options['comment_links'] ) && $options['comment_links'] === '1' ) {
			remove_filter( 'comment_text', 'make_clickable', 9 );
		}

		if ( isset( $options['emojis'] ) && $options['emojis'] === '1' ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		}

		if ( isset( $options['emojis'] ) && $options['emojis'] === '1' ) {
			add_filter( 'heartbeat_settings', array( $this, 'slow_heartbeat' ) );
		}

		if ( ! is_admin() && isset( $options['oembed'] ) && $options['oembed'] === '1' ) {
			add_action( 'wp_footer', array( $this, 'disable_oembed' ), 11 );
		}

		if ( ! defined( 'WP_POST_REVISIONS' ) && isset( $options['revisions'] ) && $options['revisions'] === '1' ) {
			define( 'WP_POST_REVISIONS', 5 );
		}
		

		if ( ! is_admin() && isset( $options['shortlink'] ) && $options['shortlink'] === '1' ) {
			remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		}

		if ( ! is_admin() && isset( $options['rsd'] ) && $options['rsd'] === '1' ) {
			remove_action( 'wp_head', 'rsd_link' );
		}

		if ( isset( $options['auto_rss'] ) && $options['auto_rss'] === '1' ) {
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}

		if ( ! is_admin() && isset( $options['version'] ) && $options['version'] === '1' ) {
			remove_action( 'wp_head', 'wp_generator' );
			add_filter( 'script_loader_src', array( $this, 'remove_ver_param' ) );
			add_filter( 'style_loader_src', array( $this, 'remove_ver_param' ) );
		}

		if ( ! is_admin() && isset( $options['wlwmanifest'] ) && $options['wlwmanifest'] === '1' ) {
			remove_action( 'wp_head', 'wlwmanifest_link' );
		}

		if ( isset( $options['xmlrpc'] ) && $options['xmlrpc'] === '1' ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
		}
		
	}

	/**
	 * Remove the WordPress version info url parameter.
	 */
	public function remove_ver_param( $url ) {
		return remove_query_arg( 'ver', $url );
	}

	/**
	 * Dequeue the WooCommerce cart fragments script.
	 */
	public function disable_cart_fragments() { 
		wp_dequeue_script( 'wc-cart-fragments' ); 
	}

	/**
	 * Slow the Heartbeat API to 60 seconds.
	 * 
	 * @param array $settngs WordPress settings array.
	 */
	public function slow_heartbeat( $settings ) {
		$settings['interval'] = 60;
		return $settings;
	}

	/**
	 * Dequeue the oEmbed script.
	 */
	public function disable_oembed() { 
		wp_dequeue_script( 'wp-embed' ); 
	}
}
