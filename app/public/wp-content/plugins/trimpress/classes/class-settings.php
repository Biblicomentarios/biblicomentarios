<?php
/**
 * The file that defines the settings class.
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
 * The settings class.
 *
 * @package    TrimPress
 * @subpackage TrimPress/classes
 */
class Settings {

	/**
	 * The database options.
	 */
	public $options;
	
	/**
	 * The class constructor.
	 */
	public function __construct() {
		$this->options = get_option( 'trimpress_settings' );
		add_action( 'admin_init', array( $this, 'create_settings' ) );	
	}
    
	/**
	 * Initialize the settings sections and fields.
	 */
	public function create_settings() {
		register_setting( 'trimpress', 'trimpress_settings' );

		add_settings_section( 'section_trim', '', '', 'trimpress' );

		add_settings_field(
			'adj_posts',
			__( 'Adjacent Post Links', 'trimpress' ),
			array( $this, 'adj_posts_cb' ),
			'trimpress',
			'section_trim'
		);

		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		    add_settings_field(
				'wc_cart_fragments',
				__( 'Cart Fragments', 'trimpress' ),
				array( $this, 'wc_cart_fragments_cb' ),
				'trimpress',
				'section_trim'
			);
		}

		add_settings_field(
			'editors',
			__( 'Code Editors', 'trimpress' ),
			array( $this, 'editors_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'comment_links',
			__( 'Comment Autolinks', 'trimpress' ),
			array( $this, 'comment_links_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'emojis',
			__( 'Emojis', 'trimpress' ),
			array( $this, 'emojis_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'heartbeat',
			__( 'Heartbeat', 'trimpress' ),
			array( $this, 'heartbeat_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'oembed',
			'oEmbed',
			array( $this, 'oembed_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'revisions',
			__( 'Post Revisions', 'trimpress' ),
			array( $this, 'revisions_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'shortlink',
			__( 'Post Shortlinks', 'trimpress' ),
			array( $this, 'shortlink_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'rsd',
			__( 'RSD Link', 'trimpress' ),
			array( $this, 'rsd_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'auto_rss',
			__( 'RSS Links', 'trimpress' ),
			array( $this, 'auto_rss_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'version',
			__( 'Version Info', 'trimpress' ),
			array( $this, 'version_cb' ),
			'trimpress',
			'section_trim'
		);
	
		add_settings_field(
			'wlwmanifest',
			__( 'WLW Manifest Link', 'trimpress' ),
			array( $this, 'wlwmanifest_cb' ),
			'trimpress',
			'section_trim'
		);

		add_settings_field(
			'xmlrpc',
			'XML-RPC',
			array( $this, 'xmlrpc_cb' ),
			'trimpress',
			'section_trim'
		);
	}

	/**
	 * The adj_posts field callback.
	 */
	public function adj_posts_cb() {
		$this->indicate_safety( 1 );
		?>

		<input type="checkbox" name="trimpress_settings[adj_posts]" value="1" <?php checked( isset( $this->options['adj_posts'] ) ); ?>>
  		
		<label for="trimpress_settings[adj_posts]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'This will remove links to the next and previous posts in the header, if present.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The wc_cart_fragments field callback.
	 */
	public function wc_cart_fragments_cb() {
		$this->indicate_safety( 2 );
		$cart_settings_url = admin_url() . 'admin.php?page=wc-settings&tab=products';
		?>

		<input type="checkbox" name="trimpress_settings[wc_cart_fragments]" value="1" <?php checked( isset( $this->options['wc_cart_fragments'] ) ); ?>>
  		
		<label for="trimpress_settings[wc_cart_fragments]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php echo sprintf( __( 'The <strong>WooCommerce</strong> cart fragments script can be very resource-intensive. If you remove this, don\'t forget to <a href="%s" target="_blank">adjust the cart behaviour</a> to redirect to the cart page after adding a product.', 'trimpress' ), esc_url( $cart_settings_url ) ); ?></p>

		<?php
	}

	/**
	 * The code editor field callback.
	 */
	public function editors_cb() {
		$this->indicate_safety( 1 );
		$file_edit_url = 'https://wordpress.org/support/article/editing-wp-config-php/#disable-the-plugin-and-theme-editor';
		?>

		<input type="checkbox" name="trimpress_settings[editors]" value="1" <?php checked( isset( $this->options['editors'] ) ); ?>>
  		
		<label for="trimpress_settings[emojis]"><?php _e( 'Disable', 'trimpress' ); ?></label>

		<p class="description"><?php echo sprintf( __( 'Disable the built-in WordPress code editors that allow users to modify plugin and theme code. If this setting has <strong>no effect</strong>, it means the <a href="%s" target="_blank">file edit constant</a> has already been set elsewhere (e.g. in your <strong>wp-config</strong>).', 'trimpress' ), esc_url( $file_edit_url ) ); ?></p>

		<?php
	}

	/**
	 * The comment autolinks field callback.
	 */
	public function comment_links_cb() {
		$this->indicate_safety( 1 );
		?>

		<input type="checkbox" name="trimpress_settings[comment_links]" value="1" <?php checked( isset( $this->options['comment_links'] ) ); ?>>
  		
		<label for="trimpress_settings[comment_links]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'Stop WordPress from automatically converting URLs left in comments to clickable hyperlinks. This feature can often be exploited by spammers.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The emojis field callback.
	 */
	public function emojis_cb() {
		$this->indicate_safety( 1 );
		?>

		<input type="checkbox" name="trimpress_settings[emojis]" value="1" <?php checked( isset( $this->options['emojis'] ) ); ?>>
  		
		<label for="trimpress_settings[emojis]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'Remove several inline styles and scripts used for the automatic detection and rendering of emojis.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The heartbeat field callback.
	 */
	public function heartbeat_cb() {
		$this->indicate_safety( 1 );
		$heartbeat_url = 'https://developer.wordpress.org/plugins/javascript/heartbeat-api/';
		?>

		<input type="checkbox" name="trimpress_settings[heartbeat]" value="1" <?php checked( isset( $this->options['heartbeat'] ) ); ?>>
  		
		<label for="trimpress_settings[heartbeat]"><?php _e( 'Reduce', 'trimpress' ); ?></label>

		<p class="description"><?php echo sprintf( __( 'Reduce the frequency of the <a href="%s">Heartbeat API</a> to pulse <strong>once every 60 seconds</strong> (the default interval is 15 seconds). This can significantly reduce admin-ajax usage.', 'trimpress' ), esc_url( $heartbeat_url ) ); ?></p>

		<?php
	}

	/**
	 * The oembed field callback.
	 */
	public function oembed_cb() {
		$this->indicate_safety( 2 );
		?>

		<input type="checkbox" name="trimpress_settings[oembed]" value="1" <?php checked( isset( $this->options['oembed'] ) ); ?>>
  		
		<label for="trimpress_settings[oembed]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'Removes the <code>oEmbed</code> script, which transforms <strong>YouTube</strong>, <strong>Twitter</strong> and other links into embedded media by fetching data from these sites. Remove it if you don\'t want this default behaviour.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The revisions field callback.
	 */
	public function revisions_cb() {
		$this->indicate_safety( 1 );
		$revisions_url = 'https://wordpress.org/support/article/editing-wp-config-php/#disable-post-revisions';
		?>

		<input type="checkbox" name="trimpress_settings[revisions]" value="1" <?php checked( isset( $this->options['revisions'] ) ); ?>>
  		
		<label for="trimpress_settings[revisions]"><?php _e( 'Limit', 'trimpress' ); ?></label>

		<p class="description"><?php echo sprintf( __( 'Unlimited post revisions (the default) can cause database bloat. This limits post revisions to a <strong>5</strong>. If this setting has <strong>no effect</strong>, it either means the <a href="%s" target="_blank">post revisions constant</a> has already been set (e.g. in your <strong>wp-config</strong>), or you need to create a new revision.', 'trimpress' ), esc_url( $revisions_url ) ); ?></p>

		<?php
	}

	/**
	 * The shortlink field callback.
	 */
	public function shortlink_cb() {
		$this->indicate_safety( 1 );
		?>

		<input type="checkbox" name="trimpress_settings[shortlink]" value="1" <?php checked( isset( $this->options['shortlink'] ) ); ?>>
  		
		<label for="trimpress_settings[shortlink]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'This will remove the post <code>shortlink</code> url, if present.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The auto_rss field callback.
	 */
	public function auto_rss_cb() {
		$this->indicate_safety( 1 );
		?>

		<input type="checkbox" name="trimpress_settings[auto_rss]" value="1" <?php checked( isset( $this->options['auto_rss'] ) ); ?>>
  		
		<label for="trimpress_settings[auto_rss]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'This will remove <strong>Really Simple Syndication</strong> (RSS) links from the header. The RSS links will still exist; they just won\'t be automatically loaded.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The rsd field callback.
	 */
	public function rsd_cb() {
		$this->indicate_safety( 1 );
		?>

		<input type="checkbox" name="trimpress_settings[rsd]" value="1" <?php checked( isset( $this->options['rsd'] ) ); ?>>
  		
		<label for="trimpress_settings[rsd]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'This will remove the <strong>Really Simple Discovery</strong> (RSD) service endpoint link used for automatic pingbacks.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The version field callback.
	 */
	public function version_cb() {
		$this->indicate_safety( 1 );
		?>

		<input type="checkbox" name="trimpress_settings[version]" value="1" <?php checked( isset( $this->options['version'] ) ); ?>>
  		
		<label for="trimpress_settings[version]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'This will remove the <code>meta</code> generator tag and <code>ver</code> url parameters that let potential attackers know what WordPress version you\'re using.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The wlwmanifest field callback.
	 */
	public function wlwmanifest_cb() {
		$this->indicate_safety( 1 );
		?>

		<input type="checkbox" name="trimpress_settings[wlwmanifest]" value="1" <?php checked( isset( $this->options['wlwmanifest'] ) ); ?>>
  		
		<label for="trimpress_settings[wlwmanifest]"><?php _e( 'Remove', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'This will remove the link to <code>wlwmanifest.xml</code>, used for <strong>Windows Live Writer</strong> support (a discontinued desktop application).', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * The xmlrpc field callback.
	 */
	public function xmlrpc_cb() {
		$this->indicate_safety( 2 );
		?>

		<input type="checkbox" name="trimpress_settings[xmlrpc]" value="1" <?php checked( isset( $this->options['xmlrpc'] ) ); ?>>
  		
		<label for="trimpress_settings[xmlrpc]"><?php _e( 'Disable', 'trimpress' ); ?></label>

		<p class="description"><?php _e( 'Disables the <code>XML-RPC</code> interface, an older system for remote WordPress access that can be exploited by hackers. If you don\'t use <strong>Jetpack</strong> or the <strong>WordPress App</strong> it\'s generally safe to disable.', 'trimpress' ); ?></p>

		<?php
	}

	/**
	 * Indicate the relative safety of the option.
	 * 
	 * @param int $n The relative safety of the option.
	 */
	public function indicate_safety( $n ) {
		$color;
		$label;
		switch( $n ) {
			case 1:
				$color = '#46b450';
				$label = 'safe';
			break;
			case 2:
				$color = '#00A0D2';
				$label = 'caution';
			break;
			default: $color = '#469246';
		}
		echo '<span aria-label="' . $label . '" style="display: inline-block; width: 3px; height: 14px; margin-bottom: -3px; border-radius: 1px; background-color: ' . $color . ';"></span>';
	}
}
