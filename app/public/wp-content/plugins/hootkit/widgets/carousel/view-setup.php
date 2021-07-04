<?php
/**
 * General Variables available: $name, $params, $args, $content
 * $args has been 'extract'ed
 */

/* Do nothing if we dont have a template to show */
if ( !is_string( $slider_template ) || !file_exists( $slider_template ) )
	return;

/* Prevent errors : do not overwrite existing values */
$defaults = array( 'slides' => array(), );
extract( $defaults, EXTR_SKIP );

/* Reset any previous slider */
global $hoot_data;
hoot_set_data( 'slider', array(), true );
hoot_set_data( 'slidersettings', array(), true );

/* Create slider settings object */
$slidersettings = array();
$slidersettings['type'] = 'carousel';
$slidersettings['source'] = 'slider-carousel.php';
// $slidersettings['widgetclass'] = ( !empty( $style ) ) ? ' slider-' . esc_attr( $style ) : ' slider-style1';
$slidersettings['class'] = 'hootkitslider-carousel';
$slidersettings['adaptiveheight'] = 'true'; // Default Setting else adaptiveheight = false and class .= fixedheight
// https://github.com/sachinchoolur/lightslider/issues/118
// https://github.com/sachinchoolur/lightslider/issues/119#issuecomment-93283923
$slidersettings['slidemove'] = '1';
$pause = empty( $pause ) ? 5 : absint( $pause );
$pause = ( $pause < 1 ) ? 1 : ( ( $pause > 15 ) ? 15 : $pause );
$slidersettings['pause'] = $pause * 1000;
$items = intval( $items );
$slidersettings['item'] = ( empty( $items ) ) ? '3' : $items;
$width = ( !empty( $width ) ) ? intval( $width ) : 0;
if ( $width ) $slidersettings['widgetstyle'] = 'max-width:' . intval( $width ) . 'px;';

/* Set Slider */
$slider = array();
$counter = 0;
foreach ( $slides as $slide ) {
	$key = 'g' . $counter;
	$counter++;
	$slider[$key] = $slide;
}
hoot_set_data( 'slider', $slider, true );
hoot_set_data( 'slidersettings', $slidersettings, true );

/* Let developers alter slider */
do_action( 'hootkit_slider_loaded', 'carousel', ( ( !isset( $instance ) ) ? array() : $instance ) );

/* Finally get Slider Template HTML */
include ( $slider_template );