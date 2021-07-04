<?php

namespace CNMD\TMT;

/**
 * Class TermManagementTools
 *
 * @package CNMD\TMT
 */
class TermManagementTools extends Base {

	/**
	 * The WPML class reference.
	 *
	 * @var WPML|null
	 */
	protected $wpml = null;

	/**
	 * The Handlres class reference.
	 *
	 * @var Handlers|null
	 */
	protected $handler = null;

	/**
	 * The HTML class reference.
	 *
	 * @var Handlers|null
	 */
	protected $html = null;


	/**
	 * Term_Management_Tools constructor.
	 * @codeCoverageIgnore
	 */
	public function __construct() {
		if ( function_exists( 'icl_object_id' ) ) {
			$this->wpml           = new WPML();
			$this->wpml_is_active = true;
		}
		$this->handler = new Handlers();
		$this->html    = new HTML();
	}


	/**
	 * Fire it up.
	 * @codeCoverageIgnore
	 */
	public function init() : void {
		if ( $this->wpml_is_active ) {
			$this->wpml->init();
		}
		$this->set_hooks();
		load_plugin_textdomain( 'term-management-tools', false, CNMD_TMT_PLUGIN_URL_RELATIVE . '/lang' );
	}


	/**
	 * Set up global hooks (actions and filters)
	 * @codeCoverageIgnore
	 */
	private function set_hooks() : void {
		add_action( 'load-edit-tags.php', array( $this, 'dispatcher' ) );
		add_action( 'admin_notices', array( $this, 'notice' ) );
	}


	/**
	 * Set up required vars and calls the action delegation
	 * @codeCoverageIgnore
	 */
	public function dispatcher() : void {
		$defaults = array(
			'taxonomy'    => 'post_tag',
			'delete_tags' => false,
			'action'      => false,
			'action2'     => false,
		);

		// phpcs:ignore
		$data = shortcode_atts( $defaults, $_REQUEST );

		$tax = get_taxonomy( $data['taxonomy'] );
		if ( ! $tax ) {
			return;
		}
		if ( ! current_user_can( $tax->cap->manage_terms ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_footer', array( $this->html, 'insert' ) );

		// Up to now, everything done is required by default. From now on, only do stuff if there is a
		// request to do it.
		if ( empty( $data['delete_tags'] ) ) {
			return;
		}
		$action = false;
		foreach ( array( 'action', 'action2' ) as $key ) {
			if ( $data[ $key ] && '-1' !== $data[ $key ] ) {
				$action = $data[ $key ];
			}
		}

		if ( ! $action ) {
			return;
		}

		$success = null;
		foreach ( array_keys( $this->get_actions( $data['taxonomy'] ) ) as $key ) {
			if ( 'bulk_' . $key === $action ) {
				// Bail if nonce is invalid.
				check_admin_referer( 'bulk-tags' );

				$success = $this->handler->do( $key, $data['taxonomy'], $data['delete_tags'] );
				break;
			}
		}

		// If we didn't do anything, this will still be null.
		if ( null === $success ) {
			return;
		}

		$referer = wp_get_referer();

		// Note: explicit check for false because strpos() is 0-indexed.
		if ( $referer && false !== strpos( $referer, 'edit-tags.php' ) ) {
			$location = $referer;
		} else {
			$location = add_query_arg( 'taxonomy', $data['taxonomy'], 'edit-tags.php' );
		}

		if ( isset( $_REQUEST['post_type'] ) && 'post' !== $_REQUEST['post_type'] ) {
			$location = add_query_arg( 'post_type', wp_unslash( sanitize_text_field( $_REQUEST['post_type'] ) ), $location );
		}
		nocache_headers();
		wp_safe_redirect( add_query_arg( 'message', $success ? 'tmt-updated' : 'tmt-error', $location ) );
		die;
	}




	/**
	 * Shows a message to the user when the attempt to modify the terms is complete.
	 */
	public function notice() {
		// phpcs:ignore
		if ( ! isset( $_GET['message'] ) ) {  //
			return;
		}

		// phpcs:ignore
		switch ( $_GET['message'] ) {
			case 'tmt-updated':
				echo '<div id="message" class="updated"><p>' . esc_html__( 'Terms updated.', 'term-management-tools' ) . '</p></div>';
				break;
			case 'tmt-error':
				echo '<div id="message" class="error"><p>' . esc_html__( 'Terms not updated.', 'term-management-tools' ) . '</p></div>';
				break;
		}
	}



	/**
	 * Enqueue and localize the support JS
	 *
	 * @codeCoverageIgnore
	 */
	public function enqueue_assets() {
		global $taxonomy;
		$js_dev = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'src/' : '';
		wp_enqueue_script( 'term-management-tools', CNMD_TMT_URL . 'assets/' . $js_dev . 'script.js', array( 'jquery' ), '2.0.0', true );
		wp_localize_script( 'term-management-tools', 'tmtL10n', $this->get_actions( $taxonomy ) );
	}

}

