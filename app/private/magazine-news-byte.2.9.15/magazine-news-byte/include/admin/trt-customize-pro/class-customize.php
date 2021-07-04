<?php
/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @credit() Justin Tadlock https://github.com/justintadlock/trt-customizer-pro
 *
 * @since  1.0
 * @access public
 */
final class Hoot_Theme_Customize_Pro {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		require_once( hoot_data()->incdir . 'admin/trt-customize-pro/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'Hoot_Theme_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new Hoot_Theme_Customize_Section_Pro(
				$manager,
				'hoot_theme',
				apply_filters( 'hoot_theme_customize_section_pro', array(
					'title'    => esc_html__( 'Magazine NewsByte', 'magazine-news-byte' ),
					'priority' => 1,
					'pro_text' => esc_html__( 'Premium', 'magazine-news-byte' ),
					'pro_url'  => esc_url( admin_url('themes.php?page=magazine-news-byte-welcome') ),
				) )
			)
		);
	}

}

// Doing this customizer thang!
Hoot_Theme_Customize_Pro::get_instance();