<?php
/**
 * Add featured image support for taxonomies
 * 
 * This file is loaded at 'after_setup_theme' hook with 4 priority.
 * @credit https://wordpress.org/plugins/simple-featured-image/
 * @credit https://wordpress.org/plugins/featured-image-admin-thumb-fiat/
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Hoot Taxonomy Field class.
 *
 * @since 3.0.0
 */
class Hoot_Taxonomy_Field {

	/**
	 * Holds the instance of this class.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var object
	 */
	private static $instance;

	/**
	 * Holds the taxonomies.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var object
	 */
	public $taxonomies = array();

	/**
	 * Initialize everything
	 * 
	 * @since 3.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		if ( is_admin() ) :
			$this->taxonomies = apply_filters( 'hoot_taxonomy_field_taxonomies', array('category','post_tag') );

			if ( !empty( $this->taxonomies ) ) {
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_wp_styles_scripts' ) );
				foreach ( $this->taxonomies as $taxonomy ) {
					add_filter( "manage_edit-{$taxonomy}_columns", array( $this, 'taxonomy_columns_header' ) );
					add_filter( "manage_{$taxonomy}_custom_column", array( $this, 'taxonomy_column' ), 10, 3 );
					add_action( "{$taxonomy}_add_form_fields", array( $this, 'add_taxonomy_field' ) );
					add_filter( "{$taxonomy}_edit_form_fields", array( $this, 'edit_taxonomy_field' ) );
				}
				// add_action( 'create_term', array( $this, 'term_save') );
				add_action( 'created_term', array( $this, 'term_update' ), 10, 3 );
				add_action( 'edit_term', array( $this, 'term_update' ), 10, 3 );
			}

		endif;
	}

	public function enqueue_wp_styles_scripts( $hook ) {
		$screen = get_current_screen();
		$currenttax = str_replace( 'edit-', '', $screen->id );
		if ( in_array( $currenttax, $this->taxonomies ) ) {
			add_thickbox();
			wp_enqueue_media();
			$script_uri = hoot_locate_script( hoot_data()->liburi . 'js/taxonomy-image' );
			wp_enqueue_script( 'hoot-taxonomyimg', $script_uri, array(), hoot_data()->hoot_version, true );
			// $style_uri = hoot_locate_style( hoot_data()->liburi . 'css/taxonomy-image' );
			// wp_enqueue_style( 'hoot-taxonomyimg', $style_uri, array(),  hoot_data()->hoot_version );
		}
	}

	public function taxonomy_columns_header( $defaults ){
		$defaults['hoot_term_img']  = __( 'Featured Image', 'magazine-news-byte' );
		return $defaults;
	}

	public function taxonomy_column( $columns, $column, $id ){
		if ( 'hoot_term_img' === $column ) {
			$thumbnail_id = get_term_meta( $id, 'hoot_term_img', true );
			$image = ( $thumbnail_id ) ? wp_get_attachment_thumb_url( $thumbnail_id ) : hoot_data()->liburi . 'images/placeholder-image.png';
			$columns .= '<img data-id="'.$thumbnail_id.'" src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'magazine-news-byte' ) . '" class="hoot_term_img" width="35px" />';
		}
		return $columns;
	}

	public function add_taxonomy_field( $term ) {
		$image = hoot_data()->liburi . 'images/placeholder-image.png';
		?><div class="form-field">
			<label for="hoot_term_img"><?php esc_html_e( 'Featured Image', 'magazine-news-byte' ); ?></label>
			<input type="hidden" id="hoot_term_img" name="hoot_term_img" value="" />
			<img src="<?php echo esc_url( $image ); ?>" width="60px" style="margin-right:10px" />
			<button type="button" class="hoot_term_img_add button"><?php esc_html_e( 'Upload/Add image', 'magazine-news-byte' ); ?></button>
			<button type="button" class="hoot_term_img_remove button"><?php esc_html_e( 'Remove image', 'magazine-news-byte' ); ?></button>
		</div><?php
	}

	public function edit_taxonomy_field( $term ) {
		$thumbnail_id = get_term_meta( $term->term_id, 'hoot_term_img', true );
		$image = ( $thumbnail_id ) ? wp_get_attachment_thumb_url( $thumbnail_id ) : hoot_data()->liburi . 'images/placeholder-image.png';
		?><tr class="form-field">
			<th scope="row" valign="top">
				<label for="hoot_term_img"><?php esc_html_e( 'Featured Image', 'magazine-news-byte' ); ?></label>
			</th>
			<td>
				<input type="hidden" id="hoot_term_img" name="hoot_term_img" value="<?php echo intval( $thumbnail_id ); ?>" />
				<img src="<?php echo esc_url( $image ); ?>" width="60px" style="margin-right:10px" data-placeholder="<?php echo hoot_data()->liburi . 'images/placeholder-image.png'; ?>" />
				<button type="button" class="hoot_term_img_add button"><?php esc_html_e( 'Upload/Add image', 'magazine-news-byte' ); ?></button>
				<button type="button" class="hoot_term_img_remove button"><?php esc_html_e( 'Remove image', 'magazine-news-byte' ); ?></button>
			</td>
		</tr><?php
	}

	// @ref. https://developer.wordpress.org/reference/hooks/edit_term/
	public function term_update( $term_id, $tt_id = '', $taxonomy = '' ){
		if ( isset( $_POST['hoot_term_img'] ) && in_array( $taxonomy, $this->taxonomies ) ) {
			update_term_meta( $term_id, 'hoot_term_img', intval( $_POST['hoot_term_img'] ) );
		}
	}

	/**
	 * Returns the instance.
	 *
	 * @since 3.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

}

/* Initialize class */
global $hoot_taxonomy_field;
$hoot_taxonomy_field = Hoot_Taxonomy_Field::get_instance();

function hoot_term_image( $term_id ) {
	$term_id = intval( $term_id );
	if ( !empty( $term_id ) ) {
		$thumbnail_id = get_term_meta( $term_id, 'hoot_term_img', true );
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
			return '<img src="' . esc_url( $image ) . '" />';
		}
	}
	return '';
}

function hoot_term_image_url( $term_id ) {
	$term_id = intval( $term_id );
	if ( !empty( $term_id ) ) {
		$thumbnail_id = get_term_meta( $term_id, 'hoot_term_img', true );
		return wp_get_attachment_thumb_url( $thumbnail_id );
	}
	return '';
}

function hoot_term_image_id( $term_id ) {
	$term_id = intval( $term_id );
	if ( !empty( $term_id ) ) {
		return get_term_meta( $term_id, 'hoot_term_img', true );
	}
	return '';
}