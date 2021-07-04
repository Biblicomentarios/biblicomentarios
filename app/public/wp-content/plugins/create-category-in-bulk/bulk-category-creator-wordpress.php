<?php
/**
 * Plugin Name: Bulk Post Category Creator
 * Plugin URI: https://kartechify.com/product/create-category-in-bulk/
 * Description: This plugin allows you to create multiple post categories in one go.
 * Version: 1.4
 * Author: Kartik Parmar
 * Author URI: https://twitter.com/kartikparmar19
 * Requires PHP: 5.6
 * License: GPL2
 *
 * @package  BWCC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BWCC_Bulk_WordPress_Category_Creator class
 */
if ( ! class_exists( 'BWCC_Bulk_WordPress_Category_Creator' ) ) {

	/**
	 * BWCC_Bulk_WordPress_Category_Creator class
	 */
	class BWCC_Bulk_WordPress_Category_Creator {

		/**
		 * BWCC_Bulk_WordPress_Category_Creator Constructor
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'bwcc_category_creator_menu' ) );
			// Language Translation.
			add_action( 'init', array( &$this, 'bwcc_update_po_file' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'bwcc_enqueue_scripts' ) );
		}

		/**
		 * Including JS and CSS files
		 *
		 * @since 1.2
		 */
		public function bwcc_enqueue_scripts() {

			if ( isset( $_GET['page'] ) && 'bulk_wordpress_category_creator' === $_GET['page'] ) {
				wp_enqueue_style(
					'bwcc-woocommerce_admin_styles',
					plugins_url() . '/woocommerce/assets/css/admin.css',
					'',
					'1.4',
					false
				);

				wp_register_script(
					'select2',
					plugins_url() . '/woocommerce/assets/js/select2/select2.min.js',
					array( 'jquery', 'jquery-ui-widget', 'jquery-ui-core' ),
					'1.4',
					false
				);

				wp_enqueue_script( 'select2' );

				wp_enqueue_script(
					'bulk-wordpress-category-creator',
					plugins_url() . '/create-category-in-bulk/js/bulk-wordpress-category-creator.js',
					array( 'jquery', 'select2' ),
					'1.5',
					false
				);
			}
		}

		/**
		 * Adds Bulk WordPress Category Creator menu under Product Menu
		 *
		 * @since 1.0
		 */
		public static function bwcc_category_creator_menu() {

			add_submenu_page(
				'edit.php',
				__( 'Bulk WordPress Category Creator Page', 'bwcc-bulk-wordpress-category-creator' ),
				__( 'Create Bulk Categories', 'bwcc-bulk-wordpress-category-creator' ),
				'manage_options',
				'bulk_wordpress_category_creator',
				array( 'BWCC_Bulk_WordPress_Category_Creator', 'bwcc_category_settings_page' )
			);

			add_action( 'admin_init', array( 'BWCC_Bulk_WordPress_Category_Creator', 'bwcc_register_plugin_settings' ) );
		}

		/**
		 * Language Translation
		 *
		 * @since 1.0
		 */
		public static function bwcc_update_po_file() {

			$domain = 'bwcc-bulk-wordpress-category-creator';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '-' . $locale . '.mo' ) ) {
				return $loaded;
			} else {
				load_plugin_textdomain( $domain, '', basename( dirname( __FILE__ ) ) . '/languages/' );
			}
		}

		/**
		 * Registering the settings
		 *
		 * @since 1.0
		 */
		public static function bwcc_register_plugin_settings() {

			// register our settings.
			register_setting( 'bwcc-bulk-category-creator-group', 'options_textarea' );

			self::bwcc_create_categories();
		}

		/**
		 * Check for the added categoies and based on that create categories
		 *
		 * @since 1.0
		 */
		public static function bwcc_create_categories() {

			if ( isset( $_POST['bwcc_options_textarea'] ) ) {

				if ( isset( $_POST['bwcc_nonce_field'] ) && wp_verify_nonce( $_POST['bwcc_nonce_field'], 'bwcc_action' ) ) {

					$returnedStr    = sanitize_textarea_field( $_POST['bwcc_options_textarea'] );
					$parent_id      = ( isset( $_POST['bwcc_parent'] ) && $_POST['bwcc_parent'] != '' ) ? sanitize_title( ( $_POST['bwcc_parent'] ) ) : 0;
					$description    = isset( $_POST['bpcc_description_textarea'] ) ? sanitize_textarea_field( $_POST['bpcc_description_textarea'] ) : '';
					$selected_posts = isset( $_POST['bpcc_posts'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['bpcc_posts'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification
					$all_posts      = isset( $_POST['custId'] ) ? sanitize_text_field( wp_unslash( $_POST['custId'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

					if ( ! empty( $selected_posts ) && in_array( 'all_posts', $selected_posts, true ) ) { // If all product is selected then get all product ids.
						$all_post_ids   = $all_posts;
						$selected_posts = explode( ',', $all_post_ids );
					}

					if ( $returnedStr != '' ) {

						$trimmed          = trim( $returnedStr );
						$categories_array = explode( ',', $trimmed );
						$term_ids         = array();

						foreach ( $categories_array as $key => $value ) {

							$term = term_exists( $value, 'category' );

							if ( $term == 0 || is_null( $term ) ) {
								$term_id = BWCC_Bulk_WordPress_Category_Creator::bwcc_create_category( $value, $parent_id, $description );
								$term_ids[] = $term_id;
							}
						}

						/**
						 * Setting category to product
						 */
						if ( count( $selected_posts ) > 0 ) {
							foreach ( $selected_posts as $key => $value ) {
								$post_term_ids = array();
								$terms         = wp_get_object_terms( $value, 'category' );

								if ( count( $terms ) > 0 ) {
									foreach ( $terms as $item ) {
										$post_term_ids[] = $item->term_id;
									}
									$final_term_ids = array_merge( $post_term_ids, $term_ids );
								} else {
									$final_term_ids = $term_ids;
								}
								wp_set_object_terms( $value, $final_term_ids, 'category' );
							}
						}

						add_action( 'admin_notices', array( 'BWCC_Bulk_WordPress_Category_Creator', 'bwcc_admin_notice_success' ) );
					} else {
						add_action( 'admin_notices', array( 'BWCC_Bulk_WordPress_Category_Creator', 'bwcc_admin_notice_error' ) );
					}
				}
			}
		}

		/**
		 * Success notice
		 *
		 * @since 1.0
		 */
		public static function bwcc_admin_notice_success() {
			?>
			<div class="notice notice-success">
				<p><?php esc_html_e( 'Categories are created!', 'bwcc-bulk-wordpress-category-creator' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Error notice
		 *
		 * @since 1.0
		 */
		public static function bwcc_admin_notice_error() {
			?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'You have not entered anything into the category textbox.', 'bwcc-bulk-wordpress-category-creator' ); ?></p>
			</div>
			<?php
		}

		/**
		 * Create WordPress category
		 *
		 * @param string $value string of the categories.
		 * @param int    $parent_id Parent ID of the Category.
		 * @param string $description Description.
		 *
		 * @since 1.0
		 */
		public static function bwcc_create_category( $value, $parent_id, $description ) {

			$trimmed_value    = trim( $value );
			$hyphenated_value = str_replace( ' ', '-', $trimmed_value );

			$insert = wp_insert_term(
				$trimmed_value,
				'category',
				array(
					'description' => $description,
					'slug'        => $hyphenated_value,
					'parent'      => $parent_id,
				)
			);

			$id = $insert['term_id'];

			return $id;
		}

		/**
		 * Bulk Category Creator Page
		 *
		 * @since 1.0
		 */
		public static function bwcc_category_settings_page() {

			$dropdown_args = array(
				'hide_empty'       => 0,
				'hide_if_empty'    => false,
				'taxonomy'         => 'category',
				'name'             => 'bwcc_parent',
				'orderby'          => 'name',
				'hierarchical'     => true,
				'show_option_none' => __( 'None' ),
			);

			$dropdown_args = apply_filters( 'taxonomy_parent_dropdown_args', $dropdown_args, 'category', 'new' );

			$args = array(
				'post_type'      => array( 'post' ),
				'posts_per_page' => -1,
				'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private', 'inherit' ),
			);

			$posts = get_posts( $args );

			?>
			<div class="wrap">

			<h1><?php esc_html_e( 'Bulk Category Creator', 'bwcc-bulk-wordpress-category-creator' ); ?> </h1>

			<form method='post'><input type='hidden' name='form-name' value='form 1' />

				<?php settings_fields( 'bwcc-bulk-category-creator-group' ); ?>

				<?php do_settings_sections( 'bwcc-bulk-category-creator-group' ); ?>

				<?php wp_nonce_field( 'bwcc_action', 'bwcc_nonce_field' ); ?>

				<input type="hidden" id="_wpnonce" name="_wpnonce" value="807d8877c2" />

				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Enter categories separated by commas', 'bwcc-bulk-wordpress-category-creator' ); ?>  </th>
						<td>
							<textarea cols="50" rows="8" name="bwcc_options_textarea"></textarea>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Parent Category', 'bwcc-bulk-wordpress-category-creator' ); ?>  </th>
						<td>
							<?php wp_dropdown_categories( $dropdown_args ); ?>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Description', 'bwcc-bulk-wordpress-category-creator' ); ?>  </th>
						<td>
							<textarea name="bpcc_description_textarea" id="bpcc_description_textarea" rows="5" cols="40" spellcheck="false"></textarea>
							<p><i><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.', 'bwcc-bulk-wordpress-category-creator' ); ?></i></p>
						</td>
					</tr>

					<tr>
						<th scope="row" valign="top"><label><?php esc_html_e( 'Select Posts', 'woocommerce' ); ?></label></th>
						<td>
							<select id="bpcc_posts"
									name="bpcc_posts[]"
									placehoder="Select Posts"
									class="bpcc_posts"
									style="width: 300px"
									multiple="multiple">
								<option value="all_posts"><?php esc_html_e( 'All Posts', 'woocommerce-booking' ); ?></option>
								<?php
								$postss = '';
								foreach ( $posts as $bkey => $bval ) {

									$postss .= $bval->ID . ',';
									?>
									<option value="<?php echo esc_attr( $bval->ID ); ?>"><?php echo esc_html( $bval->post_title ); ?></option>
									<?php

								}
								if ( '' !== $postss ) {
									$postss = substr( $postss, 0, -1 );
								}
								?>
							</select>
							<p><i><?php esc_html_e( 'Automatically assign the created categories to the selected posts.', 'bwcc-bulk-woocommerce-categor-creator' ); ?></i></p>
							<input type="hidden" id="bpcc_all_posts" name="custId" value="<?php echo esc_attr( $postss ); ?>">
						</td>	
					</tr>
				</table>

				<?php submit_button( __( 'Submit Categories', 'bwcc-bulk-wordpress-category-creator' ) ); ?>

			</form>
			<?php
		}
	}
	$bwcc = new BWCC_Bulk_WordPress_Category_Creator();
}
