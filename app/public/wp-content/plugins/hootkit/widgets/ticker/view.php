<?php
// Return if no message to show
if ( empty( $message ) )
	return;

$speed = intval( $speed );
$speed = ( empty( $speed ) ) ? 5*0.01 : $speed*0.01;
$width = intval( $width );
$inlinestyle = $widgetstyle = $styleclass = '';
if ( $background || $fontcolor ) {
	$styleclass = 'ticker-userstyle';
	$widgetstyle = ' style="';
	$widgetstyle .= ( $background ) ? 'background:' . sanitize_hex_color( $background ) . ';' : '';
	$widgetstyle .= ( $fontcolor ) ? 'color:' . sanitize_hex_color( $fontcolor ) . ';' : '';
	$widgetstyle .= '" ';
}
if ( $width ) {
	$styleclass = 'ticker-userstyle';
	$inlinestyle = ' style="width:' . $width . 'px;"';
}
$styleclass .= ( $background ) ? ' ticker-withbg' : '';
$style = ( empty( $style ) ) ? 'style1' : $style;
$styleclass .= ' ticker-' . $style;

if ( !empty( $iconcolor ) ) {
	$iconclass = ' icon-userstyle ticker-icon';
	$iconstyle = ' style="color:' . sanitize_hex_color( $iconcolor ) . ';"';
} else {
	$iconclass = ' ticker-icon';
	$iconstyle = '';
}
$icon = ( !empty( $icon ) ) ? '<i class="' . hoot_sanitize_fa( $icon ) . $iconclass . '"' . $iconstyle . '></i>' : '';

// Template modification Hook
do_action( 'hootkit_ticker_wrap', 'ticker', ( ( !isset( $instance ) ) ? array() : $instance ) );
?>

<div class="ticker-widget ticker-usercontent ticker-simple <?php echo $styleclass; ?>" <?php echo $widgetstyle;?>><?php

	/* Display Title */
	if ( !empty( $title ) )
		echo wp_kses_post( apply_filters( 'hootkit_widget_ticker_title', '<div class="ticker-title">' . $icon . $title . '</div>', $title, 'ticker', $icon ) );
	elseif ( !empty( $icon ) )
		echo wp_kses_post( $icon );

	/* Start Ticker Message Box */
	?>
	<div class="ticker-msg-box" <?php echo $inlinestyle;?> data-speed='<?php echo $speed; ?>'>
		<div class="ticker-msgs">
			<?php
			$msgs = str_replace( array( "\n", "\t" ), '</div></div><div class="ticker-msg"><div class="ticker-msg-inner">', $message ); // We do not include "\r"
			echo '<div class="ticker-msg"><div class="ticker-msg-inner">' . do_shortcode( wp_kses_post( $msgs ) ) . '</div></div>';
			?>
		</div>
	</div>

</div>