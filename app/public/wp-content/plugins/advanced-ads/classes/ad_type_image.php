<?php
/**
 * Advanced Ads Image Ad Type
 *
 * @package   Advanced_Ads
 * @author    Thomas Maier <support@wpadvancedads.com>
 * @license   GPL-2.0+
 * @link      https://wpadvancedads.com
 * @copyright 2015 Thomas Maier, Advanced Ads GmbH
 *
 * Class containing information about the content ad type
 * this should also work as an example for other ad types
 */
class Advanced_Ads_Ad_Type_Image extends Advanced_Ads_Ad_Type_Abstract {

	/**
	 * ID - internal type of the ad type
	 * must be static so set your own ad type ID here
	 * use slug like format, only lower case, underscores and hyphens
	 *
	 * @var string $ID ad type ID.
	 */
	public $ID = 'image';

	/**
	 * Set basic attributes
	 */
	public function __construct() {
		$this->title       = __( 'Image Ad', 'advanced-ads' );
		$this->description = __( 'Ads in various image formats.', 'advanced-ads' );
		$this->parameters  = array(
			'image_url'   => '',
			'image_title' => '',
			'image_alt'   => '',
		);
	}

	/**
	 * Output for the ad parameters metabox
	 *
	 * @param Advanced_Ads_Ad $ad ad object.
	 */
	public function render_parameters( $ad ) {
		// load tinymc content exitor
		$id  = ( isset( $ad->output['image_id'] ) ) ? $ad->output['image_id'] : '';
		$url = ( isset( $ad->url ) ) ? esc_attr( $ad->url ) : '';

		?><p><button href="#" class="advads_image_upload button button-secondary" type="button" data-uploader-title="
		<?php
			esc_html_e( 'Insert File', 'advanced-ads' );
		?>
			" data-uploader-button-text="<?php esc_html_e( 'Insert', 'advanced-ads' ); ?>" onclick="return false;"><?php esc_html_e( 'select image', 'advanced-ads' ); ?></button>
			<a id="advads-image-edit-link" href="
			<?php
			if ( $id ) {
				echo esc_url( get_edit_post_link( $id ) );
			}
			?>
			"><?php esc_html_e( 'edit', 'advanced-ads' ); ?></a>
		</p>
		<input type="hidden" name="advanced_ad[output][image_id]" value="<?php echo absint( $id ); ?>" id="advads-image-id"/>
		<div id="advads-image-preview">
			<?php $this->create_image_tag( $id, $ad ); ?>
		</div>
		<?php
		// don’t show if tracking plugin enabled
		if ( ! defined( 'AAT_VERSION' ) ) :
			?>
			<label for="advads-url" class="label"><?php esc_html_e( 'URL', 'advanced-ads' ); ?></label>
			<div>
				<input type="url" name="advanced_ad[url]" id="advads-url" class="advads-ad-url" value="<?php echo esc_url( $url ); ?>" placeholder="https://www.example.com/"/>
				<p class="description">
					<?php esc_html_e( 'Link to target site including http(s)', 'advanced-ads' ); ?>
				</p>
			</div>
			<hr/>
			<?php
		endif;
	}

	/**
	 * Render image tag
	 *
	 * @param int             $attachment_id post id of the image.
	 * @param Advanced_Ads_Ad $ad ad object.
	 */
	public function create_image_tag( $attachment_id, $ad ) {

		$image = wp_get_attachment_image_src( $attachment_id, 'full' );
		$style = '';

		// if we don't have an image, bail early.
		if ( ! $image ) {
			return;
		}

		list( $src, $width, $height ) = $image;
		// override image sizes with the sizes given in ad options, but in frontend only
		if (
			! is_admin()
			|| wp_doing_ajax()
		) {
			$width  = isset( $ad->width ) ? absint( $ad->width ) : $width;
			$height = isset( $ad->height ) ? absint( $ad->height ) : $height;
		}
		$hwstring = image_hwstring( $width, $height );
		$alt      = trim( esc_textarea( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

		global $wp_current_filter;

		// TODO: use an array for attributes so they are simpler to extend
		$sizes           = '';
		$srcset          = '';
		$more_attributes = $srcset;
		// create srcset and sizes attributes if we are in the the_content filter and in WordPress 4.4
		if (
			isset( $wp_current_filter )
			&& in_array( 'the_content', $wp_current_filter, true )
			&& ! defined( 'ADVADS_DISABLE_RESPONSIVE_IMAGES' )
		) {
			if ( function_exists( 'wp_get_attachment_image_srcset' ) ) {
				$srcset = wp_get_attachment_image_srcset( $attachment_id, 'full' );
			}
			if ( function_exists( 'wp_get_attachment_image_sizes' ) ) {
				$sizes = wp_get_attachment_image_sizes( $attachment_id, 'full' );
			}
			if ( $srcset && $sizes ) {
				$more_attributes .= ' srcset="' . $srcset . '" sizes="' . $sizes . '"';
			}
		}

		// TODO: move to classes/compabtility.php when we have a simpler filter for additional attributes
		// compabitility with WP Smush. Disables their lazy load for image ads because it caused them to not show up in certain positions at all.
		$wp_smush_settings = get_option( 'wp-smush-settings' );
		if ( isset( $wp_smush_settings['lazy_load'] ) && $wp_smush_settings['lazy_load'] ) {
			// Lazy load is enabled.
			$more_attributes .= ' class="no-lazyload"';
		}

		// add css rule to be able to center the ad.
		if ( isset( $ad->output['position'] ) && 'center' === $ad->output['position'] ) {
			$style .= 'display: inline-block;';
		}

		$style = apply_filters( 'advanced-ads-ad-image-tag-style', $style );
		$style = '' !== $style ? 'style="' . $style . '"' : '';

		$more_attributes  = apply_filters( 'advanced-ads-ad-image-tag-attributes', $more_attributes );
		$more_attributes .= ' ' . $hwstring . ' ' . $style;
		$img              = sprintf( '<img src="%s" alt="%s" %s />', esc_url( $src ), esc_attr( $alt ), $more_attributes );

		// Add 'loading' attribute if applicable, available from WP 5.5.
		if (
			$wp_current_filter
			&& function_exists( 'wp_lazy_loading_enabled' )
			&& wp_lazy_loading_enabled( 'img', $wp_current_filter )
			&& ! strpos( $more_attributes, 'loading=' )
		) {
			$img = wp_img_tag_add_loading_attr( $img, $wp_current_filter );
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- use unescaped image tag here
		echo $img;
	}

	/**
	 * Render image icon for overview pages
	 *
	 * @param int $attachment_id post id of the image.
	 */
	public function create_image_icon( $attachment_id ) {

		$image = wp_get_attachment_image_src( $attachment_id, 'medium', true );
		if ( $image ) {
			list( $src, $width, $height ) = $image;

			// scale down width or height to max 100px
			if ( $width > $height ) {
				$height = absint( $height / ( $width / 100 ) );
				$width  = 100;
			} else {
				$width  = absint( $width / ( $height / 100 ) );
				$height = 100;
			}

			$hwstring = trim( image_hwstring( $width, $height ) );
			$alt      = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $hwstring is not something we can escape.
			printf( '<img src="%s" alt="%s" %s/>', esc_url( $src ), esc_attr( $alt ), $hwstring );
		}
	}

	/**
	 * Prepare the ads frontend output by adding <object> tags
	 *
	 * @param Advanced_Ads_Ad $ad ad object.
	 * @return string $content ad content prepared for frontend output
	 */
	public function prepare_output( $ad ) {

		$id  = ( isset( $ad->output['image_id'] ) ) ? absint( $ad->output['image_id'] ) : '';
		$url = ( isset( $ad->url ) ) ? esc_url( $ad->url ) : '';

		ob_start();
		$this->create_image_tag( $id, $ad );
		$img = ob_get_clean();
		if ( ! defined( 'AAT_VERSION' ) && $url ) {
			// get general target setting
			$options      = Advanced_Ads::get_instance()->options();
			$target_blank = ! empty( $options['target-blank'] ) ? ' target="_blank"' : '';
			$img          = sprintf( '<a href="%s"%s>%s</a>', esc_url( $url ), $target_blank, $img );
		}

		return $img;
	}

	/**
	 * Generate a string with the original image size for output in the backend
	 * Only show, if different from entered image sizes
	 *
	 * @param   Advanced_Ads_Ad $ad ad object.
	 * @return  string empty, if the entered size is the same as the original size
	 */
	public static function show_original_image_size( $ad ) {

		$attachment_id = ( isset( $ad->output['image_id'] ) ) ? absint( $ad->output['image_id'] ) : '';

		$image = wp_get_attachment_image_src( $attachment_id, 'full' );

		if ( $image ) {
			list( $src, $width, $height ) = $image;
			?>
			<p class="description">
			<?php
			if ( ( isset( $ad->width ) && $ad->width !== $width )
				|| ( isset( $ad->height ) && $ad->height !== $height ) ) {
				printf(
				/**
				 * This string shows up on the ad edit page of image ads if the size entered for the ad is different from the size of the uploaded image.
				 */
				 // translators: $s is a size string like "728 x 90".
					esc_attr__( 'Original size: %s', 'advanced-ads' ),
					esc_html( $width ) . '&nbsp;x&nbsp;' . esc_html( $height )
				);
				?>
				</p>
							<?php
			}
		}

		return '';

	}

}
