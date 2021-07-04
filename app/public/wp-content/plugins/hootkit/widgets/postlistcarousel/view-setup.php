<?php
/**
 * General Variables available: $name, $params, $args, $content
 * $args has been 'extract'ed
 */

/* Do nothing if we dont have a template to show */
if ( !is_string( $slider_template ) || !file_exists( $slider_template ) )
	return;

/* Prevent errors : do not overwrite existing values */
$defaults = array( 'category' => '', 'count' => '', 'offset' => '' );
extract( $defaults, EXTR_SKIP );

/* Reset any previous slider */
global $hoot_data;
hoot_set_data( 'slider', array(), true );
hoot_set_data( 'slidersettings', array(), true );

/* Create slider settings object */
$slidersettings = array();
$slidersettings['type'] = 'postlistcarousel';
$slidersettings['source'] = 'slider-postlistcarousel.php';
// $slidersettings['widgetclass'] = ( !empty( $style ) ) ? ' slider-' . esc_attr( $style ) : ' slider-style1';
$slidersettings['class'] = 'hootkitslider-verticalcarousel hk-postlistcarousel';
$slidersettings['adaptiveheight'] = 'true'; // Default Setting else adaptiveheight = false and class .= fixedheight
// https://github.com/sachinchoolur/lightslider/issues/118
// https://github.com/sachinchoolur/lightslider/issues/119#issuecomment-93283923
$slidersettings['slidemove'] = '1';
$pause = empty( $pause ) ? 5 : absint( $pause );
$pause = ( $pause < 1 ) ? 1 : ( ( $pause > 15 ) ? 15 : $pause );
$slidersettings['pause'] = $pause * 1000;
$items = intval( $items );
$slidersettings['item'] = ( empty( $items ) ) ? '3' : $items;
// $slidersettings['widgetstyle'] = '';

/* Vertical Carousel */
$verticalunitdefaults = apply_filters( 'hootkit_listcarousel_unitdefaults', array(), 'post' );
$verticalunitdefaults = array_map( 'absint', $verticalunitdefaults );
$verticalunits['heightstyle1'] = ( !empty( $verticalunitdefaults['heightstyle1'] ) ) ? $verticalunitdefaults['heightstyle1'] : 80;
$verticalunits['heightstyle2'] = ( !empty( $verticalunitdefaults['heightstyle2'] ) ) ? $verticalunitdefaults['heightstyle2'] : 215;
$verticalunits['unitmargin'] = ( !empty( $verticalunitdefaults['unitmargin'] ) ) ? $verticalunitdefaults['unitmargin'] : 15;
if ( !empty( $unitheight ) ) $verticalunits['heightstyle1'] = $verticalunits['heightstyle2'] = $unitheight;

$slidersettings['verticalHeight'] = $slidersettings['item'] * ( $verticalunits['height' . $style] + $verticalunits['unitmargin'] );
$slidersettings['verticalHeight'] = absint( $slidersettings['verticalHeight'] );
if ( !empty( $slidersettings['verticalHeight'] ) )
	$slidersettings['vertical'] = 'true';
else
	unset( $slidersettings['verticalHeight'] );
$slidersettings['verticalunits'] = $verticalunits;

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
$query_args = apply_filters( 'hootkit_slider_postlistcarousel_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
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
	$slider[$key]['url']        = esc_url( get_permalink( $post->ID ) );
	$slider[$key]['title']      = '<a href="' . $slider[$key]['url'] . '">' . $slider[$key]['rawtitle'] . '</a>';
	$metadisplay = array();
	if ( !empty( $show_author ) ) $metadisplay[] = 'author';
	if ( !empty( $show_date ) ) $metadisplay[] = 'date';
	if ( !empty( $show_comments ) ) $metadisplay[] = 'comments';
	if ( !empty( $show_cats ) ) {
		if ( apply_filters( 'hootkit_slider_postlistcarousel_display_catblock', false ) ) {
			ob_start();
			hootkit_display_meta_info( array(
							'display' => array( 'cats' ),
							'context' => 'post-listcarouselunit',
							'editlink' => false,
							'wrapper' => 'div',
							'wrapper_class' => 'verticalcarousel-suptitle small',
							'empty' => '',
						) );
			$slider[$key]['meta_cats'] = ob_get_clean();
		} else $metadisplay[] = 'cats';
	}
	if ( !empty( $show_tags ) ) $metadisplay[] = 'tags';
	ob_start();
	hootkit_display_meta_info( array(
							'display' => $metadisplay,
							'context' => 'post-listcarouselunit',
							'editlink' => false,
							'wrapper' => 'div',
							'wrapper_class' => 'verticalcarousel-subtitle small',
							'empty' => '',
						) );
	$slider[$key]['meta'] = ob_get_clean();
endforeach;
wp_reset_postdata();

/* Set Slider */
hoot_set_data( 'slider', $slider, true );
hoot_set_data( 'slidersettings', $slidersettings, true );

/* Let developers alter slider */
do_action( 'hootkit_slider_loaded', 'postlistcarousel', ( ( !isset( $instance ) ) ? array() : $instance ) );

/* Finally get Slider Template HTML */
include ( $slider_template );