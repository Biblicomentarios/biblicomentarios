<?php
$speed = intval( $speed );
$speed = ( empty( $speed ) ) ? 5*0.01 : $speed*0.01;
$thumbheight = ( empty( $thumbheight ) ) ? 0 : intval( $thumbheight );
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
$style = ( empty( $style ) ) ? 'style2' : $style;
$styleclass .= ' ticker-' . $style;

// Create a custom WP Query
$count = ( empty( $count ) ) ? 0 : intval( $count );
$query_args = array();
$query_args['post_type'] = 'product';
$query_args['posts_per_page'] = ( empty( $count ) ) ? 10 : $count;
$query_args['orderby'] = array(
					'menu_order' => 'ASC',
					'ID'         => 'DESC',
				);
if ( !empty( $category ) && is_array( $category ) )
	$catarray = array(
		'taxonomy' => 'product_cat',
		'field'    => 'term_id',
		'terms'    => $category,
	);
if ( !empty( $exccategory ) && is_array( $exccategory ) )
	$exccatarray = array(
		'taxonomy' => 'product_cat',
		'field'    => 'term_id',
		'terms'    => $exccategory,
		'operator' => 'NOT IN',
	);
if ( !empty( $catarray ) || !empty( $exccatarray ) ) {
	$query_args['tax_query'] = array();
	if ( !empty( $catarray ) && !empty( $exccatarray ) )
		$query_args['tax_query']['relation'] = 'AND'; // Add this only if there is more than 1 inner taxonomy array
	if ( !empty( $catarray ) )
		$query_args['tax_query'][] = $catarray;
	if ( !empty( $exccatarray ) )
		$query_args['tax_query'][] = $exccatarray;
}
$query_args = apply_filters( 'hootkit_products_ticker_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$prods_ticker_query = new WP_Query( $query_args );

// Template modification Hook
do_action( 'hootkit_ticker_wrap', 'products', ( ( !isset( $instance ) ) ? array() : $instance ), $prods_ticker_query, $query_args );
?>

<div class="ticker-widget ticker-products ticker-combined <?php echo $styleclass; ?>" <?php echo $widgetstyle;?>><?php

	/* Display Title */
	if ( !empty( $title ) )
		echo wp_kses_post( apply_filters( 'hootkit_widget_ticker_title', '<div class="ticker-title">' . $title . '</div>', $title, 'products-ticker' ) );

	/* Start Ticker Message Box */
	?>
	<div class="ticker-msg-box" <?php echo $inlinestyle;?> data-speed='<?php echo $speed; ?>'>
		<div class="ticker-msgs">
			<?php
			global $post;
			if ( $prods_ticker_query->have_posts() ) : while ( $prods_ticker_query->have_posts() ) : $prods_ticker_query->the_post();

				// Init
				global $product; // setup_postdata( $post );
				if ( ! is_a( $product, 'WC_Product' ) ) break;
				$visual = ( has_post_thumbnail() ) ? 1 : 0;
				$imgclass = ( $visual ) ? 'visual-img' : 'visual-none';
				$img_size = apply_filters( 'hootkit_products_ticker_imgsize', 'thumbnail' );
				?>

				<div class="ticker-msg <?php echo $imgclass; ?>">
					<?php
					if ( $visual ) :
						$thumbnail_url = get_the_post_thumbnail_url( null, $img_size );
						$imgstyle = "background-image:url(" . esc_url($thumbnail_url) . ");";
						$imgstyle .= ( !empty( $thumbheight ) ) ? 'height:' . intval( $thumbheight ) . 'px;width:' . intval( $thumbheight ) * 1.5 . 'px;' : '';
						?>
						<div class="ticker-img" style="<?php echo esc_attr( $imgstyle ); ?>">
							<?php hootkit_post_thumbnail( 'product-ticker-img', $img_size, false, esc_url( get_permalink( $post->ID ) ), NULL, 'thumbnail' ); ?>
						</div>
						<?php
					else: // Since we are not using a flexbox on ticker-msgs anymore, add an empty image box of same height to maintain middle alignment of ticker-msg without $visual
						$imgstyle = 'width:0;';
						$imgstyle .= ( !empty( $thumbheight ) ) ? 'height:' . intval( $thumbheight ) . 'px;' : '';
						?>
						<div class="ticker-img noimge" style="<?php echo esc_attr( $imgstyle ); ?>"></div>
						<?php
					endif;
					?>

					<div class="ticker-content">
						<div class="ticker-msgtitle"><a href="<?php echo esc_url( get_permalink() ); ?>" <?php echo hoot_get_attr( 'product-ticker-link', ( ( !isset( $instance ) ) ? array() : $instance ) ); ?>><?php the_title(); ?></a></div>
						<?php
						if ( !empty( $show_price ) && $price_html = $product->get_price_html() )
							echo '<div class="ticker-product-price">' . $price_html . '</div>';
						if ( !empty( $show_addtocart ) ) {
							echo '<div class="ticker-addtocart">';
								woocommerce_template_loop_add_to_cart();
							echo '</div>';
						} ?>
					</div>
				</div>
				<?php

			endwhile; endif;
			wp_reset_postdata();
			?>
		</div>
	</div>

</div>