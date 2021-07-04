<?php
/**
 * Handle media (i.e. images, attachments) for the theme.
 * This file is loaded via the 'after_setup_theme' hook at priority '10'
 *
 * @package    Magazine News Byte
 * @subpackage Theme
 */

/* Add sizes to get registered using 'add_image_size()' */
add_filter( 'hoot_custom_image_sizes', 'magnb_custom_image_sizes', 5 );

/**
 * Add custom image sizes to be used throughout the theme.
 * Also define whether to show the custom image size in the Image Editor in the Post Editor.
 *
 * Note, When using hoot_get_image_size_name(), any span below 3 gets upgraded to span3, thereby
 * getting bigger images which display much better on smaller screens (where all spans become 100%)
 * Effectively, this means on a grid of 1260, custom images sizes should have atleast 315px width as
 * images below this width would not be used by this function.
 *
 * Note, order of sizes in this array matters. hoot_get_image_size_name() automatically returns the
 * first image size it finds matching the width needed (and matching crop criteria).
 *
 * @since 1.0
 * @access public
 * @param array $sizes Default custom image sizes.
 * @return array
 */
function magnb_custom_image_sizes( $sizes ) {
	$sizes = array(

		// Let span3 use 'hoot-medium-thumb' as we need width to be 420 for mobile
		// // 240 x 180 (suitable for span3, calculated using logic of hoot_get_image_size_name fn)
		// 'hoot-small-thumb' => 315 x 215

		// (calculated using logic of hoot_get_image_size_name fn)
		// @span4 @hcolumn-1-3 @hcolumn-1-4 @hcolumn-1-5 @post-list(big) @archive-small @archive-block3
		'hoot-medium-thumb' => array(
			'label'          => __( 'Medium Thumbnail', 'magazine-news-byte' ),
			'width'          => 460,
			'height'         => 300,
			'crop'           => true,
			'show_in_editor' => false,
		),

		// (width is 465 instead of 460, so that 'hoot-medium-thumb' stays as default for span4)
		// @archive-medium @archive-mosaic3 @archive-mosaic4
		'hoot-preview' => array(
			'label'          => __( 'Preview', 'magazine-news-byte' ),
			'width'          => 465,
			'height'         => 550,
			'crop'           => false,
			'show_in_editor' => false,
		),
		// @carousel
		'hoot-preview-thumb' => array(
			'label'          => __( 'Preview Thumbnail', 'magazine-news-byte' ),
			'width'          => 465,
			'height'         => 550,
			'crop'           => true,
			'show_in_editor' => false,
		),

		// (calculated using logic of hoot_get_image_size_name fn)
		// @hcolumn-1-2 @span6 @archive-block2
		'hoot-large-thumb' => array(
			'label'          => __( 'Large Thumbnail', 'magazine-news-byte' ),
			'width'          => 690,
			'height'         => 550,
			'crop'           => true,
			'show_in_editor' => false,
		),

		// (width is 695 instead of 690, so that 'hoot-large-thumb' stays as default for span6)
		// @post-grid(big) @archive-mosaic2
		'hoot-preview-large' => array(
			'label'          => __( 'Large Preview', 'magazine-news-byte' ),
			'width'          => 695,
			'height'         => 500,
			'crop'           => false,
			'show_in_editor' => false,
		),

		// (calculated using logic of hoot_get_image_size_name fn)
		// @span8 @archive-big
		'hoot-wide-thumb' => array(
			'label'          => __( 'Wide', 'magazine-news-byte' ),
			'width'          => 920,
			'height'         => 425,
			'crop'           => true,
			'show_in_editor' => false,
		),

		// (calculated using logic of hoot_get_image_size_name fn)
		// @span9 @archive-big
		'hoot-extra-wide-thumb' => array(
			'label'          => __( 'Extra Wide', 'magazine-news-byte' ),
			'width'          => 1035,
			'height'         => 425,
			'crop'           => true,
			'show_in_editor' => false,
		),

	);
	return $sizes;
}
