<?php
/**
 * The file that defines the core plugin class.
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
 * The core plugin class.
 *
 * @package    TrimPress
 * @subpackage TrimPress/classes
 */
class TrimPress {

	/**
	 * The class constructor.
	 */
	public function __construct() {
		$this->init_settings();
		$this->init_admin();
		$this->init_actions();
		$this->init_settings_link();
	}

	/**
	 * Initialize the settings section.
	 */
	public function init_settings() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-settings.php';
		new Settings();
	}
	
	/**
	 * Initialize the admin area.
	 */
	public function init_admin() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-admin.php';
		new Admin();
	}	

	/**
	 * Trigger the clean up actions.
	 */
	public function init_actions() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'classes/class-trim.php';
		new Trim();
	}

	/**
	 * Create the settings link from the plugin admin area.
	 */
	public function settings_link( $plugin_actions, $plugin_file ) {
		$tr_actions = array();
		if ( basename( dirname ( dirname( __FILE__ ) ) ) . '/trimpress.php' === $plugin_file ) {
			$tr_settings_url = esc_url( add_query_arg( array( 'page' => 'trimpress' ), admin_url( 'admin.php' ) ) );
			$tr_actions['tr_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'trimpress' ), $tr_settings_url );
		}
		return array_merge( $tr_actions, $plugin_actions );
	}

	/**
	 * Add the settings link from the plugin admin area.
	 */
	public function init_settings_link() {
		add_filter(
			'plugin_action_links', 
			array( $this, 'settings_link' ), 
			10,
			2
		);
	}
}
