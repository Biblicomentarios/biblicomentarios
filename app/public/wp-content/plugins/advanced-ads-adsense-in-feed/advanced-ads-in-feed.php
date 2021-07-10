<?php
/**
 * Plugin Name:       AdSense In-feed Placement for WordPress
 * Plugin URI:        https://wpadvancedads.com
 * Description:       Display AdSense In-feed ads between posts
 * Version:           1.1.1
 * Author:            Thomas Maier
 * Author URI:        https://webgilde.com
 * Text Domain:       advanced-ads-adsense-in-feed
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Advanced_Ads_In_Feed
 */
class Advanced_Ads_In_Feed {
	/**
	 * Advanced_Ads_In_Feed constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'init', array( $this, 'check_dependencies' ) );
	}

	/**
	 * Load when plugin are loaded
	 */
	public function plugins_loaded() {
		if ( ! defined( 'ADVADS_VERSION' ) ) {
			return;
		}
		// load translations.
		add_filter( 'advanced-ads-placement-types', array( $this, 'add_placement_types' ) );
		// inject ads into archive pages.
		add_action( 'the_post', array( $this, 'inject_in_feed' ), 20, 2 );
		// options for placement.
		add_action( 'advanced-ads-placement-options-before', array( $this, 'placement_options_check' ), 10, 2 );
		add_action( 'advanced-ads-placement-options-after', array( $this, 'placement_options' ), 11, 2 );
	}


	/**
	 * Check if Advanced Ads basic plugin is activated
	 */
	public function check_dependencies() {
		// Check if Advanced Ads is installed.
		if ( ! defined( 'ADVADS_VERSION' ) ) {
			// Display notice that Advanced Ads is required.
			add_action( 'admin_notices', array( $this, 'show_advads_version_notice' ) );
		}
	}

	/**
	 * Add placement types
	 *
	 * @param array $types array of ad types.
	 *
	 * @return array $types
	 * @since   1.0.0
	 */
	public function add_placement_types( $types ) {
		// ad injection at a hand selected element in the frontend.
		$types['adsense_in_feed'] = array(
			'title'       => __( 'AdSense In-feed', 'advanced-ads-adsense-in-feed' ),
			'description' => __( 'Display an AdSense In-feed ad between posts', 'advanced-ads-adsense-in-feed' ),
			'image'       => plugin_dir_url( __FILE__ ) . 'adsense-in-feed.png',
			'options'     => array(
				'show_position'  => true,
				'show_lazy_load' => true,
			),
		);

		return $types;
	}

	/**
	 * Echo ad before/after posts in loops on archive pages
	 *
	 * @param array    $post post object.
	 * @param WP_Query $wp_query query object.
	 *
	 * @since 1.0
	 *
	 */
	public function inject_in_feed( $post, $wp_query = null ) {
		if ( ! $wp_query instanceof WP_Query
		     || is_feed()
		     || is_admin()
		     || $wp_query->is_singular()
		     || ! $wp_query->in_the_loop
		     || ! isset( $wp_query->current_post )
		     || ! $wp_query->is_main_query() ) {
			return;
		}

		$curr_index = $wp_query->current_post + 1; // normalize index.

		// handle the situation when wp_reset_postdata() is used after secondary query inside main query.
		static $handled_indexes = array();
		if ( isset( $handled_indexes[ $curr_index ] ) ) {
			return;
		}
		$handled_indexes[] = $curr_index;

		$placements = get_option( 'advads-ads-placements', array() );
		if ( is_array( $placements ) ) {
			foreach ( $placements as $_placement_id => $_placement ) {
				if ( empty( $_placement['item'] ) ) {
					continue;
				}

				if ( isset( $_placement['type'] ) && 'adsense_in_feed' === $_placement['type'] ) {
					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();
					$ad_index = isset( $_options['adsense_in_feed_pages_index'] ) ? absint( ( $_options['adsense_in_feed_pages_index'] ) ) : 1;
					if ( $ad_index === $curr_index ) {
						$_options['placement']['type'] = $_placement['type'];
						if ( isset( $_placement['item'] ) && $this->is_infeed_ad_item( $_placement['item'] ) ) {
							echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, Advanced_Ads_Select::PLACEMENT, $_options );
						}
					}
				}
			}
		}
	}

	/**
	 * Check if the current ad is indeed an infeed item
	 *
	 * @param string $item_id item ID from a placement.
	 *
	 * @return  bool    true if it is an infeed ad
	 */
	private function is_infeed_ad_item( $item_id ) {
		$_item = explode( '_', $item_id );

		if ( 'ad' !== $_item[0] || ! isset( $_item[1] ) || empty( $_item[1] ) ) {
			return false;
		}

		// load the ad.
		$ad = new Advanced_Ads_Ad( $_item[1] );
		if ( isset( $ad->type )
		     && 'adsense' === $ad->type
		     && isset( $ad->content )
		     && strpos( $ad->content, 'in-feed' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Show warning about missing Advanced Ads basic plugin
	 */
	public function show_advads_version_notice() {
		$plugin_data = get_plugin_data( __FILE__ );
		$plugins     = get_plugins();
		if ( isset( $plugins['advanced-ads/advanced-ads.php'] ) ) { // is installed, but not active.
			$link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">' . __( 'Activate Now', 'advanced-ads-adsense-in-feed' ) . '</a>';
		} else {
			$link = '<a class="button button-primary" href="' . wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . 'advanced-ads' ), 'install-plugin_' . 'advanced-ads' ) . '">' . __( 'Install Now', 'advanced-ads-adsense-in-feed' ) . '</a>';
		}
		echo '
		<div class="error">
		  <p>' . sprintf( __( '<strong>%s</strong> requires the <strong><a href="https://wpadvancedads.com/#utm_source=advanced-ads&utm_medium=link&utm_campaign=activate-in-feed" target="_blank">Advanced Ads</a></strong> plugin to be installed and activated on your site.', 'advanced-ads-adsense-in-feed' ), $plugin_data['Name'] ) .
		     '&nbsp;' . $link . '</p></div>';
	}

	function load_plugin_textdomain() {
		load_plugin_textdomain( 'advanced-ads-adsense-in-feed', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Check if this is indeed an In-feed ad
	 *
	 * @param string $placement_slug id of the placement.
	 * @param array  $placement array with placement options.
	 *
	 * @since 1.0
	 */
	public function placement_options_check( $placement_slug, $placement ) {
		if ( isset( $placement['type'] ) && 'adsense_in_feed' === $placement['type'] ) {
			// check if this is the correct item type (AdSense In-feed).
			if ( isset( $placement['item'] ) && ! $this->is_infeed_ad_item( $placement['item'] ) ) {
				echo '<p class="advads-error-message">' . sprintf(
					// translators: %s is a link.
						__( 'This placement can only deliver a single In-feed ad. Use the <a href="%s" target="_blank">Post Lists placement</a> for other ad types.', 'advanced-ads-adsense-in-feed' ),
						esc_url( ADVADS_URL ) . 'manual/placement-post-lists/'
					) . '</p>';
			}
		}
	}

	/**
	 * Render placement options
	 *
	 * @param string $placement_slug id of the placement.
	 * @param array  $placement array with placement options.
	 *
	 * @since 1.0
	 */
	public function placement_options( $placement_slug, $placement ) {
		if ( isset( $placement['type'] ) && 'adsense_in_feed' === $placement['type'] ) {
			$index          = ( isset( $placement['options']['adsense_in_feed_pages_index'] ) ) ? $placement['options']['adsense_in_feed_pages_index'] : 1;
			$index_option   = '<input type="number" name="advads[placements][' . $placement_slug . '][options][adsense_in_feed_pages_index]" value="'
			                  . $index . '" name="advads-placements-adsense-in-feed-index' . $placement_slug . '"/>';
			$option_content = sprintf(
			// translators: $s is an index, e.g., "5".
				__( 'Inject before %s. post', 'advanced-ads-adsense-in-feed' ),
				$index_option
			);

			$description = __( 'Before which post to inject the ad on post lists.', 'advanced-ads-adsense-in-feed' );
			if ( class_exists( 'Advanced_Ads_Admin_Options' ) ) {
				Advanced_Ads_Admin_Options::render_option(
					'placement-in-feed-position',
					__( 'position', 'advanced-ads-adsense-in-feed' ),
					$option_content,
					$description
				);
			}
		}
	}

}

new Advanced_Ads_In_Feed();
