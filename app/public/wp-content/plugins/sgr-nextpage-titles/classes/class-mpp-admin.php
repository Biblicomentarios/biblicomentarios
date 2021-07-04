<?php

class MPP_Admin {

	/** Directory *************************************************************/

	/**
	 * Path to the Multipage admin directory.
	 *
	 * @since 1.5
	 * @var string $admin_dir
	 */
	public $admin_dir = '';

	/** URLs ******************************************************************/

	/**
	 * URL to the Multipage admin directory.
	 *
	 * @since 1.5
	 * @var string $admin_url
	 */
	public $admin_url = '';

	/** Other *****************************************************************/

	/**
	 * Notices used for user feedback, like saving settings.
	 *
	 * @since 1.9.0
	 * @var array()
	 */
	public $notices = array();
	
	/** Methods ***************************************************************/

	/**
	 * The main Multipage admin loader.
	 *
	 * @since 1.4
	 *
	 */	
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Set admin-related globals.
	 *
	 * @since 1.4
	 */
	private function setup_globals() {
		$mpp = multipage();

		// Paths and URLs
		$this->admin_dir  = trailingslashit( $mpp->plugin_dir  . 'inc/admin' ); // Admin path.
		$this->admin_url  = trailingslashit( $mpp->plugin_url  . 'inc/admin' ); // Admin url.

		// Main settings page.
		$this->settings_page = 'options-general.php';
	}
	
	/**
	 * Include required files.
	 *
	 * @since 1.4
	 */
	private function includes() { // Valutare uno spostamento delle directory
		require( $this->admin_dir . 'admin-actions.php'				 );
		require( $this->admin_dir . 'admin-functions.php'			 );
		require( $this->admin_dir . 'admin-settings.php'			 );
		require( $this->admin_dir . 'admin-advanced-settings.php'	 );
		//require( $this->admin_dir . 'admin-premium.php'	 );
	}

	/**
	 * Set up the admin hooks, actions, and filters.
	 *
	 * @since 1.4
	 *
	 */
	private function setup_actions() { 
		// Add some page specific output to the <head>.
		add_action( 'admin_head',            			 array( $this, 'admin_head'  ), 999 );

		// Add menu item to settings menu.
		add_action( 'admin_menu',						 array( $this, 'admin_menus' ), 5 ); # Priority 5.
		
		// Add settings.
		add_action( 'mpp_register_admin_settings',		 array( $this, 'register_admin_settings' ) );
		
		// Add styles to the admin.
		add_action( 'admin_enqueue_scripts',			 array( $this, 'enqueue_scripts' ) );
		
		// Add link to settings page.
		add_filter( 'plugin_action_links',               array( $this, 'modify_plugin_action_links' ), 10, 2 );
		add_filter( 'network_admin_plugin_action_links', array( $this, 'modify_plugin_action_links' ), 10, 2 );

		// Check if TinyMCE is enabled (disabled if Gutenberg is running)
		if ( get_user_option( 'rich_editing' ) == 'true' && mpp_disable_tinymce_buttons() != true ) {

			// Add TinyMCE Plugin
			add_filter( 'mce_css', array( &$this, 'mpp_mce_css' ) );
			add_filter( 'mce_buttons', array( &$this, 'mpp_mce_button' ) );
			add_filter( 'mce_external_plugins', array( &$this, 'mpp_mce_external_plugin' ) );
			add_filter( 'wp_mce_translation', array( &$this, 'mpp_wp_mce_translation' ), 10, 2 ); // Used on the Gutenberg Classic Editor
		}

		// If Gutenberg is running add the Classic Editor Button
		if ( mpp_is_block_editor_active() ) {
			
			// Enqueue block editor assets
			add_action( 'enqueue_block_editor_assets', array( $this, 'mpp_enqueue_block_editor_assets' ), 9 );
		}
		
		// Add HTML Editor button
		add_action( 'admin_print_footer_scripts',		 array( &$this, 'editor_add_quicktags' ) );
	}

	/**
	 * Register site-admin nav menu elements.
	 *
	 * @since 1.4
	 */
	public function admin_menus() {
		$hooks = array();
	
		// Add the option pages.
		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'Multipage Options', 'sgr-nextpage-titles' ),
			__( 'Multipage', 'sgr-nextpage-titles' ),
			'manage_options',
			'mpp-settings',
			'mpp_admin_settings'
		);

		$hooks[] = add_submenu_page(
			$this->settings_page,
			__( 'Multipage Advaced Settings', 'sgr-nextpage-titles' ),
			__( 'Multipage Advaced Settings', 'sgr-nextpage-titles' ),
			'manage_options',
			'mpp-advanced-settings',
			'mpp_admin_advanced'
		);
		
		//$hooks[] = add_submenu_page(
		//	$this->settings_page,
		//	__( 'Premium', 'sgr-nextpage-titles' ),
		//	__( 'Premium', 'sgr-nextpage-titles' ),
		//	'manage_options',
		//	'mpp-premium',
		//	'mpp_admin_premium'
		//);

		foreach( $hooks as $hook ) {
			add_action( "admin_head-$hook", 'mpp_modify_admin_menu_highlight' );
		}
	}

	/**
	 * Register the settings.
	 *
	 * @since 1.6.0
	 *
	 */
	public function register_admin_settings() {
		
		/* Main Settings ******************************************************/

		// Add the main section.
		add_settings_section( 'mpp_main', __( 'Main Settings', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_main_section', 'multipage' );

		// Hide default intro title.
		add_settings_field( 'mpp-hide-intro-title', __( 'Intro', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_hide_intro_title', 'multipage', 'mpp_main', array( 'label_for' => 'hide-intro-title' ) );
		register_setting( 'mpp-settings', 'mpp-hide-intro-title', 'intval' );
		
		// Display comments on pages.
		add_settings_field( 'mpp-comments-on-page', __( 'Display comments on', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_comments_on_page', 'multipage', 'mpp_main', array( 'label_for' => 'comments-on-page' ) );
		register_setting( 'mpp-settings', 'mpp-comments-on-page', '' );
		
		// Hide default intro title.
		//add_settings_field( 'mpp-prettylinks', __( 'Pretty Links', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_prettylinks', 'multipage', 'mpp_main', array( 'label_for' => 'prettylinks' ) );
		//register_setting( 'mpp-settings', 'mpp-prettylinks', 'intval' );

		// Add the pagination section.
		add_settings_section( 'mpp_pagination', __( 'Pagination', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_pagination_section', 'multipage' );

		// Display the previous page link.
		add_settings_field( 'mpp-continue-or-prev-next', __( 'Navigation type', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_continue_or_prev_next', 'multipage', 'mpp_pagination', array( 'label_for' => 'continue-or-prev-next' ) );
		register_setting( 'mpp-advanced-settings', 'mpp-continue-or-prev-next', '' );

		// Disable the standard WordPress pagination.
		add_settings_field( 'mpp-disable-standard-pagination', __( 'Disable the standard pagination', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_disable_standard_pagination', 'multipage', 'mpp_pagination', array( 'label_for' => 'mpp-disable-standard-pagination' ) );
		register_setting( 'mpp-advanced-settings', 'mpp-disable-standard-pagination', 'intval' );

		// Add the table of contents section.
		add_settings_section( 'mpp_toc', __( 'Table of contents', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_toc_section', 'multipage' );

		// Disable the standard WordPress pagination.
		add_settings_field( 'mpp-toc-only-on-the-first-page', __( 'Only on the first page', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_toc_only_on_the_first_page', 'multipage', 'mpp_toc', array( 'label_for' => 'toc-only-on-the-first-page' ) );
		register_setting( 'mpp_toc', 'mpp-toc-only-on-the-first-page', 'intval' );

		// Set the table of contents position.
		add_settings_field( 'mpp-toc-position', __( 'Position', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_toc_position', 'multipage', 'mpp_toc', array( 'label_for' => 'toc-position' ) );
		register_setting( 'mpp_toc', 'mpp-toc-position', '' );

		// Define row labels.
		add_settings_field( 'mpp-toc-row-labels', __( 'Row labels', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_toc_row_labels', 'multipage', 'mpp_toc', array( 'label_for' => 'toc-row-labels' ) );
		register_setting( 'mpp_toc', 'mpp-toc-row-labels', '' );

		// Hide the table of contents header.
		add_settings_field( 'mpp-hide-toc-header', __( 'Hide header', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_hide_toc_header', 'multipage', 'mpp_toc', array( 'label_for' => 'hide-toc-header' ) );
		register_setting( 'mpp_toc', 'mpp-hide-toc-header', 'intval' );

		// Add a link to comments inside the table of contents.
		add_settings_field( 'mpp-comments-toc-link', __( 'Comments link', 'sgr-nextpage-titles' ), 'mpp_admin_settings_callback_comments_toc_link', 'multipage', 'mpp_toc', array( 'label_for' => 'comments-toc-link' ) );
		register_setting( 'mpp_toc', 'mpp-comments-toc-link', 'intval' );
		
		/* Advanced Settings ******************************************************/

		// Add the main section.
		add_settings_section( 'mpp_advanced', '', 'mpp_admin_advanced_settings_callback_main_section', 'mpp-advanced-settings' );
	
		// Set the title rewrite rule.
		add_settings_field( '_mpp-rewrite-title-priority', __( 'Rewrite Title Priority', 'sgr-nextpage-titles' ), 'mpp_admin_advanced_callback_rewrite_title_priority', 'mpp-advanced-settings', 'mpp_advanced', array( 'label_for' => 'rewrite-title-priority' ) );
		register_setting( 'mpp-advanced-settings', '_mpp-rewrite-title-priority', 'intval' );
		
		// Set the content rewrite rule.
		add_settings_field( '_mpp-rewrite-content-priority', __( 'Rewrite Content Priority', 'sgr-nextpage-titles' ), 'mpp_admin_advanced_callback_rewrite_content_priority', 'mpp-advanced-settings', 'mpp_advanced', array( 'label_for' => 'rewrite-content-priority' ) );
		register_setting( 'mpp-advanced-settings', '_mpp-rewrite-content-priority', 'intval' );

		// Disable TinyMCE Buttons inside the editor to preserve older WordPress versions to work.
		add_settings_field( 'mpp-disable-tinymce-buttons', __( 'Disable TinyMCE Buttons', 'sgr-nextpage-titles' ), 'mpp_admin_advanced_callback_disable_tinymce_buttons', 'mpp-advanced-settings', 'mpp_advanced' );
		register_setting( 'mpp-advanced-settings', 'mpp-disable-tinymce-buttons', 'intval' );

		// Build Multipage postmetas. We only showing this if the update process didn't run the postmetas building.
		if ( ! get_option( '_mpp-postmeta-built' ) ) {
			add_settings_field( '_mpp-postmeta-built', __( 'Build Multipage postmetas', 'sgr-nextpage-titles' ), 'mpp_admin_advanced_callback_build_mpp_postmeta_data', 'mpp-advanced-settings', 'mpp_advanced' );
			register_setting( 'mpp-advanced-settings', '_mpp-postmeta-built', 'intval' );
		}
		
		/* Premium ****************************************************************/
		
		// Add the main section.
		//add_settings_section( 'mpp_premium', '', 'mpp_admin_premium_callback_main_section', 'mpp-premium' );
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 0.93
	 *
	 * @uses wp_enqueue_style()
	 * @return void
	 */
	public static function enqueue_scripts() {
		$handle = 'multipage-admin';

		// LTR or RTL
		$file = is_rtl() ? 'admin/css/'. $handle . '-rtl' : 'admin/css/' . $handle;

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		
		$handle = 'multipage-admin';
		$file = $file . $suffix . '.css';

		// Enqueue the Multipage Plugin styling
		wp_enqueue_style( $handle, trailingslashit( MPP_PLUGIN_URL ) . $file, array(), '', 'screen' );
	}

	/**
	 * Add Settings link to plugins area.
	 *
	 * @since 1.4
	 *
	 * @param array  $links Links array in which we would prepend our link.
	 * @param string $file  Current plugin basename.
	 * @return array Processed links.
	 */
	public function modify_plugin_action_links( $links, $file ) {
		$temp_dir = str_replace( '/trunk', '', MPP__PLUGIN_DIR ); // Check this and the following...
		
		// Return normal links if not Multipage.
		if ( basename( $temp_dir ) . '/sgr-nextpage-titles.php' != $file ) { // ...this one
			return $links;
		}

		// Add a few links to the existing links array.
		return array_merge( array(
		//	'premium' => '<a style="font-weight: bold;" href="https://www.envire.it/wordpress/plugins/multipage-premium/" target="_blank">Premium Support</a>',
			'settings' => '<a href="' . esc_url( add_query_arg( array( 'page' => 'mpp-settings' ), mpp_get_admin_url( 'options-general.php' ) ) ) . '">' . esc_html__( 'Settings', 'sgr-nextpage-titles' ) . '</a>',
		), $links );
	}

	/**
	 * Add some general styling to the admin area.
	 *
	 * @since 1.4
	 */
	public function admin_head() {
		// Settings pages.
		remove_submenu_page( $this->settings_page, 'mpp-advanced-settings' );
		//remove_submenu_page( $this->settings_page, 'mpp-premium' );
	}

	/**
	 * Add HTML Text Editor Subpage button
	 *
	 * @since 1.3
	 */
	public static function editor_add_quicktags() {
		if ( wp_script_is( 'quicktags' ) ) {
	?>
	<script type="text/javascript">
		QTags.addButton( 'eg_subpage', '<?php _e( 'subpage', 'sgr-nextpage-titles' ); ?>', prompt_subtitle, '', '', '<?php _e( 'Start a new Subpage', 'sgr-nextpage-titles' ); ?>', 121 );
		
		function prompt_subtitle(e, c, ed) {
			var subtitle = prompt( '<?php _e( 'Enter the subpage title', 'sgr-nextpage-titles' ); ?>' ),
				shortcode, t = this;

			if (typeof subtitle != 'undefined' && subtitle.length < 2) return;

			t.tagStart = '[nextpage title="' + subtitle + '"]\n\n';
			t.tagEnd = false;
			
			// now we've defined all the tagStart, tagEnd and openTags we process it all to the active window
			QTags.TagButton.prototype.callback.call(t, e, c, ed);
		};
	</script>
	<?php
		}
	}
	
	/**
	 * Add a new TinyMCE css.
	 *
	 * @since 1.3
	 *
	 * @return string
	 */
	public static function mpp_mce_css( $mce_css ) {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		if ( ! empty( $mce_css ) )
			$mce_css .= ',';

		$mce_css .= MPP_PLUGIN_URL . 'inc/admin/tinymce/css/multipage' . $suffix . '.css';
		return $mce_css;
	}

	/**
	 * Add the new subpage TinyMCE button.
	 *
	 * @since 1.3
	 *
	 * @return array $buttons
	 */
	public static function mpp_mce_button( $buttons ) {
		// Insert 'Subpage' button after the 'WP More' button
		$wp_more_key = array_search( 'wp_more', $buttons ) +1;
		$buttons_after = array_splice( $buttons, $wp_more_key);
		
		array_unshift( $buttons_after, 'subpage' );
		
		$buttons = array_merge( $buttons, $buttons_after );
		
		return $buttons;
	}

	/**
	 * Add the new TinyMCE plugin.
	 *
	 * @since 1.3
	 *
	 * @return array $plugin_array
	 */
	public static function mpp_mce_external_plugin( $plugin_array ) {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$plugin_array['multipage'] = MPP_PLUGIN_URL . 'inc/admin/tinymce/js/plugin' . $suffix . '.js';
		return $plugin_array;
	}
	
	/**
	 * Add the new TinyMCE plugin locale.
	 *
	 * @since 1.5
	 *
	 * @return array $mce_translation
	 */
	public static function mpp_wp_mce_translation( $mce_translation, $mce_locale ) {
		$mpp_wp_mce_strings = array(
			'Start a new Subpage'		=> __( 'Start a new Subpage', 'sgr-nextpage-titles' ),
			'Enter the subpage title'	=> __( 'Enter the subpage title', 'sgr-nextpage-titles' ),
		);
		
		$mce_translation = array_merge( $mce_translation, $mpp_wp_mce_strings );

		return $mce_translation;
	}

	/**
	 * Enqueue filters for extending core blocks attributes.
	 * Has to be loaded before registering the blocks in registerCoreBlocks.
	 */
	public static function mpp_enqueue_block_editor_assets() {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// Register block editor script for backend.
		wp_register_script(
			'mpp-editor-blocks', // Handle.
			MPP_PLUGIN_URL . 'admin/js/mpp-editor-blocks.js', //MPP_PLUGIN_URL . 'admin/js/mpp-editor-blocks' . $suffix . '.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
			MPP_VERSION, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
			true // Enqueue the script in the footer.
		);

		// Register block editor styles for backend.
		wp_register_style(
			'mpp-editor-blocks-style', // Handle.
			MPP_PLUGIN_URL . 'admin/css/mpp-editor-blocks' . $suffix . '.css', // Block style CSS.
			array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
			MPP_VERSION // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
		);

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'mpp-editor-blocks', 'sgr-nextpage-titles' );
		}

		/**
		 * Register Gutenberg block on server-side.
		 *
		 * Register the block on server-side to ensure that the block
		 * scripts and styles for both frontend and backend are
		 * enqueued when the editor loads.
		 *
		 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
		 * @since 1.3.9
		 */
		register_block_type(
			'multipage/subpage', array(
				// Enqueue blocks.style.build.css on both frontend & backend.
				//'style'         => 'mpp-style-css',
				// Enqueue blocks.build.js in the editor only.
				'editor_script' => 'mpp-editor-blocks',
				// Enqueue blocks.editor.build.css in the editor only.
				'editor_style'  => 'mpp-editor-blocks-style',
			)
		);
	}
}