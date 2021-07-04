<?php
/**
 * Helper Media functions.
 *
 * @package    Magazine News Byte
 * @subpackage Library
 */

/**
 * Registers custom image sizes for the theme. 
 *
 * @since 3.0.0
 * @access public
 * @return void
 */
function hoot_register_image_sizes() {
	$sizes = array();
	$sizes = apply_filters( 'hoot_custom_image_sizes', $sizes );

	foreach ( $sizes as $name => $size ) :

		$default_size = array(
			'label'          => '',
			'width'          => 0,
			'height'         => 0,
			'crop'           => false,
			'show_in_editor' => false,
		);
		$size = wp_parse_args( $size, $default_size );

		/* Add image size if its not a Reserved Name */
		if ( $name != 'thumb' && $name != 'thumbnail' && $name != 'medium' && $name != 'large' && $name != 'post-thumbnail' ) {

			if ( intval( $size['width'] ) != 0 || intval( $size['height'] ) != 0 )
				add_image_size( $name, intval( $size['width'] ), intval( $size['height'] ), $size['crop'] );

		} elseif ( $name == 'post-thumbnail' ){

			/* Sets the 'post-thumbnail' size. */
			set_post_thumbnail_size( $size['width'], $size['height'], $size['crop'] );

		}

	endforeach;

}
add_action( 'init', 'hoot_register_image_sizes', 0 );

/**
 * Get the image size to use in a span/column of the CSS grid
 * *  Case 1: $grid can be a container span, for when a spanN is in a grid which itself is a spanN/Column
 * *  Case 2: Account for responsive spans i.e. set a minimum span size for smaller spans so that mobile viewports
 * *          will show bigger width images for available screen space. Example: span1,2,3 will have image sizes
 * *          corresponding to span4, so that in mobile view where all spans have 100% width, images are displayed
 * *          more nicely!
 * *  Case 3: Maybe find a robust (not hard coded) way to account for span padding as well (curently $swidth
 * *          does this by using $gridadjust)
 *
 * @since 3.0.0
 * @access public
 * @param string $span span size or column size
 * @param NULL|bool $crop get only cropped if true, only noncropped if false, either for anything else.
 * @param int $gridadjust Grid's Width Adjustment for various paddings (possible value 80)
 * @return string
 */
function hoot_get_image_size_name( $span, $crop=NULL, $gridadjust=0 ) {

	/* Get the Grid (int)Width from Options else Default */
	$grid = 1380; // 1440
	// if ( function_exists( 'hoot_customize_get_choices' ) ) {
	// 	$grid_choices = hoot_customize_get_choices('site_width');
	// 	if ( $grid_choices )
	// 		$grid = max( array_map( 'absint', array_keys( $grid_choices ) ) );
	// }
	$grid -= $gridadjust;

	/* Get the Span/Column factor */
	if ( strpos( $span, 'span-' ) !== false ) {
		$pieces = explode( "span-", $span );
		$factor = $pieces[1];
	} elseif ( strpos( $span, 'column-' ) !== false ) {
		$pieces = explode( "column-", $span );
		$factors = explode( "-", $pieces[1] );
		$factor = ( $factors[0] * 12 ) / $factors[1];
	} else {
		return false;
	}

	/* Responsive Grid: Any span below 3 gets an image size fit for atleast span3 to display nicely on smaller screens */
	$factorint = intval( $factor );
	$factor = ( empty( $factorint ) || round( $factor ) < 3 ) ? 3 : round( $factor );

	/* Get width array arranged in ascending order */
	if ( $crop === true )
		$iwidths = hoot_get_image_sizes( 'sort_by_width_crop' );
	elseif ( $crop === false )
		$iwidths = hoot_get_image_sizes( 'sort_by_width_nocrop' );
	else
		$iwidths = hoot_get_image_sizes( 'sort_by_width' );

	/* Get Image size corresponding to span width */
	$swidth = ( $factor / 12 ) * $grid;
	foreach ( $iwidths as $name => $iwidth ) {
		if ( (int)$swidth <= (int)$iwidth )
			return $name;
	}

	/* If this was a crop/no-crop request and we didn't find any image size, then search all available sizes. */
	if ( $crop === true || $crop === false ){
		$iwidths = hoot_get_image_sizes( 'sort_by_width' );
		foreach ( $iwidths as $name => $iwidth ) {
			if ( (int)$swidth <= (int)$iwidth )
				return $name;
		}
	}

	/* Full size image (largest width) */
	return 'full';

}

/**
 * Get all (or one) registered image sizes with width and height
 *
 * @since 3.0.0
 * @access public
 * @param string $return specific image size to return, or 'sort_by_width' to return array sorted by inc. widths,
 *                       or 'sort_by_width_crop' for sorted (by width) only cropped sizes, or 'sort_by_width_nocrop'
 *                       for sorted (by width) only noncropped sizes
 * @return array
 */
function hoot_get_image_sizes( $return = '' ) {
	static $sizes = array(); // cache
	static $sort_by_width = array();
	static $sort_by_width_crop = array();
	static $sort_by_width_nocrop = array();

	if ( empty( $sizes ) ) {
		global $_wp_additional_image_sizes;
		$get_intermediate_image_sizes = get_intermediate_image_sizes();

		// Create the full array with sizes and crop info
		foreach( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ]['width'] = $_wp_additional_image_sizes[ $_size ]['width'];
				$sizes[ $_size ]['height'] = $_wp_additional_image_sizes[ $_size ]['height'];
				$sizes[ $_size ]['crop'] = $_wp_additional_image_sizes[ $_size ]['crop'];
			}
			// Create additional arrays
			if ( isset( $sizes[ $_size ]['width'] ) ){
				$sort_by_width[ $_size ] = $sizes[ $_size ]['width'];
				if ( $sizes[ $_size ]['crop'] )
					$sort_by_width_crop[ $_size ] = $sizes[ $_size ]['width'];
				else
					$sort_by_width_nocrop[ $_size ] = $sizes[ $_size ]['width'];
			}
		}

		// Note: With asort, if 2 values are equal, their order in resulting array is undefined. Instead we can use:
		// uksort($sort_by_width, function($x, $y) use ($sort_by_width) {
		// 	if($sort_by_width[$x]==$sort_by_width[$y])
		// 		return $x<$y?-1:$x!=$y;
		// 	return $sort_by_width[$x]-$sort_by_width[$y];
		// });
		asort( $sort_by_width, SORT_NUMERIC );
		asort( $sort_by_width_crop, SORT_NUMERIC );
		asort( $sort_by_width_nocrop, SORT_NUMERIC );
	}

	if ( $return ) {
		if ( 'sort_by_width' == $return ){
			return $sort_by_width;
		} elseif ( 'sort_by_width_crop' == $return ){
			return $sort_by_width_crop;
		} elseif ( 'sort_by_width_nocrop' == $return ){
			return $sort_by_width_nocrop;
		} elseif ( isset( $sizes[ $return ] ) ) {
			return $sizes[ $return ];
		} else {
			return false;
		}
	}
	return $sizes;
}

/**
 * Calculate size and display the post thumbnail image
 * (along with proper image microdata)
 *
 * @since 3.0.1
 * @access public
 * @param string $classes    additional classes
 * @param string $size       span or column size or actual image size name. Default is content width span.
 * @param bool   $miscrodata true|false Add microdata or not
 * @param string $link       image link url
 * @param bool   $crop       true|false|null Using null will return closest matched image irrespective of its crop setting
 * @return void
 */
if ( !function_exists( 'hoot_post_thumbnail' ) ):
function hoot_post_thumbnail( $classes = '', $size = '', $microdata = false, $link = '', $crop = NULL ) {

	if ( has_post_thumbnail() ) :

		/* Add custom Classes if any */
		$custom_class = ( !empty( $classes ) ) ? hoot_sanitize_html_classes( $classes ) : '';

		/* Calculate the size to display */
		$thumbnail_size = hoot_thumbnail_size( $size, $crop );

		/* Finally display the image */
		if ( $microdata ) {
			$iid = get_post_thumbnail_id();
			if ( !empty( $iid ) ) {
				$isrc = wp_get_attachment_image_src( $iid, $thumbnail_size );
				if( empty( $isrc ) ) $isrc = wp_get_attachment_image_src( $iid, $thumbnail_size, true );
				if ( !empty( $isrc[0] ) ) {
					$iwidth = ( empty( $isrc[1] ) ) ? '' : $isrc[1];
					$iheight = ( empty( $isrc[2] ) ) ? '' : $isrc[2];
					echo wp_kses( apply_filters( 'hoot_post_thumbnail_microdata',
						'<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject" class="entry-featured-img-wrap">' .
						'<meta itemprop="url" content="' . $isrc[0] . '">' .
						'<meta itemprop="width" content="' . $iwidth . '">' .
						'<meta itemprop="height" content="' . $iheight . '">'
						, $isrc[0], $iwidth, $iheight ), hoot_data( 'hootallowedtags' ) );
					$microdatadisplay = true;
				}
			}
		}
		if ( empty( $microdatadisplay ) )
			echo '<div class="entry-featured-img-wrap">';
		if ( !empty( $link ) ) echo '<a href="' . esc_url( $link ) . '" ' . hoot_get_attr( 'entry-featured-img-link' ) . '>';
		the_post_thumbnail( $thumbnail_size, array( 'class' => "attachment-$thumbnail_size $custom_class", 'itemscope' => '' ) );
		if ( !empty( $link ) ) echo '</a>';
		echo '</div>';

	endif;

}
endif;

/**
 * Get the thumbnail size based on column span
 * This function is sort of a wrapper for 'hoot_get_image_size_name'
 *
 * @since 3.0.1
 * @access public
 * @param string $size span or column size or actual image size name. Default is content width span.
 * @param bool   $crop true|false|null Using null will return closest matched image irrespective of its crop setting
 * @return string
 */
if ( !function_exists( 'hoot_thumbnail_size' ) ):
function hoot_thumbnail_size( $size = '', $crop = NULL ) {

	/* Calculate the size to display */
	if ( !empty( $size ) ) {
		if ( 0 === strpos( $size, 'span-' ) || 0 === strpos( $size, 'column-' ) )
			$thumbnail_size = hoot_get_image_size_name( $size, $crop );
		else
			$thumbnail_size = $size;
	} else {
		$size = apply_filters( 'hoot_thumbnail_size_contentwidth', 'span-12' );
		$thumbnail_size = hoot_get_image_size_name( $size, $crop );
	}

	/* Let child themes filter the size name */
	$thumbnail_size = apply_filters( 'hoot_post_thumbnail_size' , $thumbnail_size, $size, $crop );

	return $thumbnail_size;
}
endif;

/**
 * Get an attachment ID given a URL.
 * @credit https://wpscholar.com/blog/get-attachment-id-from-wp-image-url/
 * @NU
 *
 * @since 3.0.0
 * @access public
 * @param string $url
 * @return int Attachment ID on success, 0 on failure
 */
function hoot_get_attachid_url( $url ) {
	$url = esc_url( $url );
	if ( empty( $url ) )
		return 0;

	$attachment_id = 0;
	$dir = wp_upload_dir();
	if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
		$file = basename( $url );
		$query_args = array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'fields'      => 'ids',
			'meta_query'  => array(
				array(
					'value'   => $file,
					'compare' => 'LIKE',
					'key'     => '_wp_attachment_metadata',
				),
			)
		);
		$query = new WP_Query( $query_args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post_id ) {
				$meta = wp_get_attachment_metadata( $post_id );
				$original_file = basename( $meta['file'] );
				// $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
				// if ( $original_file === $file || in_array( $file, $cropped_image_files ) )
				$uploads = wp_upload_dir();
				$relurl = str_replace( trailingslashit($uploads['baseurl']), '', $url ); // 2015/01/slide-01-150x150.jpg
				if ( $meta['file'] == $relurl ) :
					$attachment_id = $post_id;
					break;
				elseif( !empty( $meta['sizes'] ) ) :
					foreach ( $meta['sizes'] as $metasize ) :
						$relurl2 = str_replace( $metasize['file'], $original_file, $relurl );
						if ( $meta['file'] == $relurl2 ) {
							$attachment_id = $post_id;
							break;
						}
					endforeach;
				endif;
			}
		}
	}
	// Debug: echo "=== {$url} \n {$relurl} \n {$relurl2} \n ".$meta['file']." \n {$attachment_id} ===";
	return $attachment_id;
}