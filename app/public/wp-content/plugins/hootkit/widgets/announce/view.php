<?php
// Return if no message to show
if ( empty( $message ) && empty ( $icon ) )
	return;

// Backward compatibility for widgets which do not have this option
$iconsize = ( empty( $iconsize ) ) ? false : $iconsize;
$headline = ( empty( $headline ) ) ? false : $headline;
$headlinesize = ( empty( $headlinesize ) ) ? false : $headlinesize;

$inlinestyle = $styleclass = $iconstyle = $iconclass = $headlinestyle = $headlineclass = '';
if ( $background || $fontcolor ) {
	$styleclass .= ' announce-userstyle';
	$inlinestyle .= ' style="';
	$inlinestyle .= ( $background ) ? 'background:' . sanitize_hex_color( $background ) . ';' : '';
	$inlinestyle .= ( $fontcolor ) ? 'color:' . sanitize_hex_color( $fontcolor ) . ';' : '';
	$inlinestyle .= '"';
}
$styleclass .= ( $background ) ? ' announce-withbg' : '';
$styleclass .= ( !$headline && !$message ) ? ' announce-nomsg' : '';
$styleclass .= ( !$icon ) ? ' announce-noicon' : '';
if ( $iconcolor || $iconsize ) {
	$iconclass .= ' icon-userstyle';
	$iconstyle .= ' style="';
	$iconstyle .= ( $iconcolor ) ? 'color:' . sanitize_hex_color( $iconcolor ) . ';' : '';
	$iconstyle .= ( $iconsize ) ? 'font-size:' . intval( $iconsize ) . 'px;' : '';
	$iconstyle .= '"';
};
if ( $headlinesize ) {
	$headlineclass .= ' announce-headline-userstyle';
	$headlinestyle .= ' style="font-size:' . intval( $headlinesize ) . 'px;"';
}
?>

<div class="announce-widget <?php echo $styleclass; ?>" <?php echo $inlinestyle;?>>
	<?php if ( !empty( $url ) ) echo '<a href="' . esc_url( $url ) . '" ' . hoot_get_attr( 'announce-link', ( ( !isset( $instance ) ) ? array() : $instance ) ) . '><span>' . __( 'Click Here', 'hootkit' ) . '</span></a>'; ?>
	<div class="announce-box table">
		<?php if ( !empty( $icon ) ) : ?>
			<div class="announce-box-icon table-cell-mid"><i class="<?php echo hoot_sanitize_fa( $icon ) . $iconclass; ?>"<?php echo $iconstyle ?>></i></div>
		<?php endif; ?>
		<?php if ( !empty( $message ) || !empty( $headline ) ) : ?>
			<div class="announce-box-content table-cell-mid">
				<?php if ( !empty( $headline ) ) { ?>
					<h5 class="announce-headline<?php echo $headlineclass; ?>"<?php echo $headlinestyle ?>><?php echo do_shortcode( wp_kses_post( $headline ) ); ?></h5>
				<?php } ?>
				<?php if ( !empty( $message ) ) { ?>
					<div class="announce-message"><?php echo do_shortcode( wp_kses_post( $message ) ); ?></div>
				<?php } ?>
			</div>
		<?php endif; ?>
	</div>
</div>