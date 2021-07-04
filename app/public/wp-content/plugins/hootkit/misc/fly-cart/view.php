<?php

if ( is_cart() || is_checkout() )
	return;

// Get option values
foreach ( array(
		'icon',
		'location',
		'showonadd',
	) as $id ) {
	$$id = hoot_get_mod( 'hkfc_' . $id );
}

?>

<div id="fly-cart" class="fly-cart flycart-<?php echo sanitize_html_class( $location ); ?> <?php if ( $showonadd ) echo 'flycart-showonadd'; if ( is_customize_preview() ) echo ' force-custview'; ?> woocommerce widget_shopping_cart">
	<a class="flycart-toggle" href="#"><i class="<?php echo hoot_sanitize_fa( $icon ); ?>"></i></a>
	<div class="flycart-panel">
		<div class="flycart-content">
			<div class="flycart-topicon"><i class="<?php echo hoot_sanitize_fa( $icon ); ?>"></i></div>
			<div class="widget_shopping_cart_content"><?php if ( function_exists( 'woocommerce_mini_cart' ) ) woocommerce_mini_cart(); ?></div>
		</div>
	</div>
</div>