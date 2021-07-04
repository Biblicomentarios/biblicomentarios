<?php
// Return if no icon to show
if ( empty( $icon ) )
	return;

if ( $background || $color || $size ) {
	$styleclass = 'iconwidget-userstyle';
	$inlinestyle = ' style="';
	$inlinestyle .= ( $background ) ? 'background:' . sanitize_hex_color( $background ) . ';' : '';
	$inlinestyle .= ( $color ) ? 'color:' . sanitize_hex_color( $color ) . ';' : '';
	$inlinestyle .= ( $size ) ? 'font-size:' . intval( $size ) . 'px;' : '';
	$inlinestyle .= '" ';
} else $inlinestyle = $styleclass = '';
$styleclass .= ( $background ) ? ' iconwidget-withbg' : '';
?>

<div class="icon-widget <?php echo $styleclass; ?>" <?php echo $inlinestyle;?>>
	<?php if ( !empty( $url ) ) echo '<a href="' . esc_url( $url ) . '" ' . hoot_get_attr( 'iconwidget-link', ( ( !isset( $instance ) ) ? array() : $instance ) ) . '>';?>
		<i class="<?php echo hoot_sanitize_fa( $icon ) ?>"></i>
	<?php if ( !empty( $url ) ) echo '</a>'; ?>
</div>