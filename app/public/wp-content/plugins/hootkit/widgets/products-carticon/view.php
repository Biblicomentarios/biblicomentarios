<?php
$carticon = ( !empty( $carticon ) ) ? $carticon : 'fa-shopping-cart fas';
$cartempty = WC()->cart->is_empty();
$cartitems = ( !$cartempty ) ? WC()->cart->get_cart_contents_count() : apply_filters( 'hk_carticon_itemnumber_when_noitem', '' ); // count( WC()->cart->get_cart_contents() ) // count( WC()->cart->get_cart() )
$cartvalue = ( !$cartempty ) ? WC()->cart->get_cart_subtotal() : apply_filters( 'hk_carticon_value_when_noitem', '' ); // WC()->cart->cart_contents_total : no currency sign

$inlinestyle = $invertstyle = $styleclass = '';
if ( $background || $fontcolor ) {
	$styleclass .= ' announce-userstyle';
	$inlinestyle .= ' style="';
	$inlinestyle .= ( $background ) ? 'background:' . sanitize_hex_color( $background ) . ';' : '';
	$inlinestyle .= ( $fontcolor ) ? 'color:' . sanitize_hex_color( $fontcolor ) . ';' : '';
	$inlinestyle .= '"';
	$invertstyle .= ' style="';
	$invertstyle .= ( $background ) ? 'color:' . sanitize_hex_color( $background ) . ';' : '';
	$invertstyle .= ( $fontcolor ) ? 'background:' . sanitize_hex_color( $fontcolor ) . ';' : '';
	$invertstyle .= '"';
}
$styleclass .= ( $background ) ? ' announce-withbg' : '';
$styleclass .= ( empty( $cartvalue ) ) ? ' announce-nomsg' : '';

$nonce = wp_create_nonce( 'hootkit-carticon-widget' );
?>

<div class="carticon-widget announce-widget <?php echo $styleclass; ?>" <?php echo $inlinestyle;?> data-nonce="<?php echo $nonce; ?>">
	<div class="carticon-refresh"></div>
	<?php
	if ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'cart' ) > 0 )
		echo '<a href="' . esc_url( get_permalink( wc_get_page_id( 'cart' ) ) ) . '" ' . hoot_get_attr( 'announce-link', ( ( !isset( $instance ) ) ? array() : $instance ), 'carticon-link' ) . '><span>' . __( 'Click Here', 'hootkit' ) . '</span></a>'; ?>
	<div class="announce-box table">
		<div class="carticon-icon announce-box-icon table-cell-mid">
			<i class="<?php echo hoot_sanitize_fa( $carticon ); ?>"></i>
			<?php
			if ( !empty( $cartitems ) )
				echo '<div class="carticon-cartitems" ' . $invertstyle . '>' . $cartitems . '</div>';
			else
				echo '<div class="carticon-cartitems no-cartitems" ' . $invertstyle . '>' . esc_html( apply_filters( 'carticon_empty_hovertag', '0' ) ) . '</div>';
			?>
		</div>
		<?php if ( !empty( $cartvalue ) ): ?>
			<div class="carticon-cartvalue announce-box-content table-cell-mid"><?php echo $cartvalue; ?></div>
		<?php else: ?>
			<div class="carticon-cartvalue announce-box-content table-cell-mid no-cartvalue"><?php echo esc_html( apply_filters( 'carticon_empty_value', '0.00' ) ); ?></div>
		<?php endif; ?>
	</div>
</div>