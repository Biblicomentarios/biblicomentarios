<?php
/**
 * Shortcodes (limited)
 * This file is loaded in HootKit->loadplugin() via 'after_setup_theme' action @priority 95
 *
 * @package Hootkit
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/* Add Shortcodes */
add_shortcode( 'HKtimer', 'hootkit_shortcode_timer' );

/**
 * Display timer
 *
 * @since 1.1.1
 * @access public
 * @param array $atts
 * @param string $content
 * @return string
 */
function hootkit_shortcode_timer( $atts, $content = null ) {
	if ( is_customize_preview() )
		return '<span class="hootkit-timer" style="opacity:0.6;font-weight:normal"><em>' . esc_html__( '&laquo; Timer preview is not available in customize view &raquo;', 'hootkit' ) . '</em></span>';

	$display = '';
	extract( shortcode_atts( array(
		'year'   => '',
		'month'  => '',
		'day'    => '',
		'hour'   => '',
		'minute' => '',
		// 'yearlabel'    => esc_html__( 'year', 'hootkit' ),
		// 'yearslabel'   => esc_html__( 'years', 'hootkit' ),
		// 'monthlabel'   => esc_html__( 'month', 'hootkit' ),
		// 'monthslabel'  => esc_html__( 'months', 'hootkit' ),
		'daylabel'     => esc_html__( 'day', 'hootkit' ),
		'dayslabel'    => esc_html__( 'days', 'hootkit' ),
		'expiredlabel' => esc_html__( 'Expired', 'hootkit' ),
	), $atts ) );

	// Sanitize values
	$timezone = wp_timezone(); // https://developer.wordpress.org/reference/functions/current_time/
	$datetime = new DateTime( 'now', $timezone );
	foreach ( array( 'year', 'month', 'day', 'hour', 'minute' ) as $v )
		$$v = absint( $$v );
	if ( $hour == 24 ) $hour = 0;
	if ( $minute == 60 ) $minute = 0;
	$year   = (   !empty( $year )                     && $year >= 1000 && $year <= 9999 ) ? sprintf( '%04d', $year )   : $datetime->format('Y');
	$month  = (   !empty( $month )                    && $month >= 1   && $month <= 12 )  ? sprintf( '%02d', $month )  : $datetime->format('m');
	$day    = (   !empty( $day )                      && $day >= 1     && $day <= 31 )    ? sprintf( '%02d', $day )    : $datetime->format('d');
	$hour   = ( ( !empty( $hour ) || $hour == 0 )     && $hour >= 0    && $hour <= 23 )   ? sprintf( '%02d', $hour )   : '00';
	$minute = ( ( !empty( $minute ) || $minute == 0 ) && $minute >= 0  && $minute <= 59 ) ? sprintf( '%02d', $minute ) : '00';
	$second = '00';
	// display .= $year . ' ' . $month . ' ' . $day . ' ' . $hour . ' ' . $minute;

	// Set times
	$currenttime = current_time('timestamp');
	$deadline = strtotime( "{$day}-{$month}-{$year} {$hour}:{$minute}:{$second}" );
	$timedifference = $deadline - $currenttime;
	// GMT         : $display .= '<br />' . ( time() - strtotime('2020-06-29 4:45:00') );
	// Server Time : $display .= '<br />' . ( current_time('timestamp') - strtotime('2020-06-29 10:15:00') );

	$display .= '<span class="hootkit-timer" ' .
						'data-diff="' . esc_attr( $timedifference ) . '" ' .
						// 'data-yearlabel = "' . esc_attr( $yearlabel ) . '" ' .
						// 'data-yearslabel = "' . esc_attr( $yearslabel ) . '" ' .
						// 'data-monthlabel = "' . esc_attr( $monthlabel ) . '" ' .
						// 'data-monthslabel = "' . esc_attr( $monthslabel ) . '" ' .
						'data-daylabel = "' . esc_attr( $daylabel ) . '" ' .
						'data-dayslabel = "' . esc_attr( $dayslabel ) . '" ' .
						'data-expiredlabel = "' . esc_attr( $expiredlabel ) . '" ' .
					'></span>';

	return $display;
}