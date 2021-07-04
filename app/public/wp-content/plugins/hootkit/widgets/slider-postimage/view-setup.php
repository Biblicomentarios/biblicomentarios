<?php
/**
 * General Variables available: $name, $params, $args, $content
 * $args has been 'extract'ed
 */

/* Do nothing if we dont have a template to show */
if ( !is_string( $slider_template ) || !file_exists( $slider_template ) )
	return;

/* Prevent errors : do not overwrite existing values */
$defaults = array( 'category' => '', 'count' => '', 'offset' => '', 'caption_bg' => '', 'fullcontent' => '', 'excerptlength' => '', );
extract( $defaults, EXTR_SKIP );

/* Reset any previous slider */
global $hoot_data;
hoot_set_data( 'slider', array(), true );
hoot_set_data( 'slidersettings', array(), true );

/* Create slider settings object */
$slidersettings = array();
$slidersettings['type'] = 'postimage';
$slidersettings['source'] = 'slider-postimage.php';
$slidersettings['widgetclass'] = ( !empty( $style ) ) ? ' slider-' . esc_attr( $style ) : ' slider-style1';
$slidersettings['class'] = 'hootkitslider-postimage';
$slidersettings['adaptiveheight'] = 'true'; // Default Setting else adaptiveheight = false and class .= fixedheight
// https://github.com/sachinchoolur/lightslider/issues/118
// https://github.com/sachinchoolur/lightslider/issues/119#issuecomment-93283923
$slidersettings['slidemove'] = '1';
$pause = empty( $pause ) ? 5 : absint( $pause );
$pause = ( $pause < 1 ) ? 1 : ( ( $pause > 15 ) ? 15 : $pause );
$slidersettings['pause'] = $pause * 1000;
$width = ( !empty( $width ) ) ? intval( $width ) : 0;
if ( $width ) $slidersettings['widgetstyle'] = 'max-width:' . intval( $width ) . 'px;';

// Create a custom WP Query
$query_args = array();
$count = ( empty( $count ) ) ? 0 : intval( $count );
$query_args['posts_per_page'] = ( empty( $count ) || $count > 4 ) ? 4 : $count;
$offset = ( empty( $offset ) ) ? 0 : intval( $offset );
if ( $offset )
	$query_args['offset'] = $offset;
if ( isset( $category ) && is_string( $category ) ) $category = array( $category ); // Pre 1.0.10 compatibility with 'select' type
$exccategory = ( !empty( $exccategory ) && is_array( $exccategory ) ) ? array_map( 'hootkit_append_negative', $exccategory ) : array(); // undefined if none selected in multiselect
$category = ( !empty( $category ) && is_array( $category ) ) ? array_merge( $category, $exccategory) : $exccategory; // undefined if none selected in multiselect
if ( !empty( $category ) )
	$query_args['category'] = implode( ',', $category );
$query_args['meta_query'] = array(
	array(
		'key' => '_thumbnail_id',
		'compare' => 'EXISTS'
	),
);
$query_args = apply_filters( 'hootkit_slider_postimage_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$slider_posts_query = get_posts( $query_args );

/* Create Slides */
$slider = array();
$counter = 0;
global $post;
foreach ( $slider_posts_query as $post ) :
	setup_postdata( $post );
	$key = 'g' . $counter;
	$counter++;
	$slider[$key]['postid']     = $post->ID;
	$slider[$key]['image']      = ( has_post_thumbnail( $post->ID ) ) ? get_post_thumbnail_id( $post->ID ) : '';
	$slider[$key]['rawtitle']   = get_the_title( $post->ID );
	/*if ( $fullcontent === 'content' ) {
		$slider[$key]['caption'] = get_the_content();
	} else*/
	if( $fullcontent === 'excerpt' ) {
		$excerptlength = ( empty( $excerptlength ) ) ? '' : intval( $excerptlength );
		if ( function_exists( 'hoot_remove_readmore_link' ) ) hoot_remove_readmore_link();
		if( !empty( $excerptlength ) )
			$slider[$key]['caption'] = hoot_get_excerpt( $excerptlength );
		else
			$slider[$key]['caption'] = apply_filters( 'the_excerpt', get_the_excerpt() );
		if ( function_exists( 'hoot_reinstate_readmore_link' ) ) hoot_reinstate_readmore_link();
	}
	$slider[$key]['caption_bg'] = $caption_bg;
	// $slider[$key]['button']     = ( function_exists( 'hoot_get_mod' ) ) ? hoot_get_mod('read_more') : __( 'Know More', 'hootkit' );
	$slider[$key]['url']        = esc_url( get_permalink( $post->ID ) );
	$slider[$key]['title']      = '<a href="' . $slider[$key]['url'] . '">' . $slider[$key]['rawtitle'] . '</a>';
endforeach;
wp_reset_postdata();

/* Set Slider */
hoot_set_data( 'slider', $slider, true );
hoot_set_data( 'slidersettings', $slidersettings, true );

/* Let developers alter slider */
do_action( 'hootkit_slider_loaded', 'postimage', ( ( !isset( $instance ) ) ? array() : $instance ) );

/* Finally get Slider Template HTML */
include ( $slider_template );