<?php
// Get total columns and set column counter
$columns = ( intval( $columns ) >= 1 && intval( $columns ) <= 3 ) ? intval( $columns ) : 1;

// Create a custom WP Query
$posts_per_page = 0;
for ( $ci = 1; $ci <= 3; $ci++ ) {
	${ 'count' . $ci } = ( empty( ${ 'count' . $ci } ) ) ? 0 : intval( ${ 'count' . $ci } );
	${ 'count' . $ci } = empty( ${ 'count' . $ci } ) ? 3 : ${ 'count' . $ci };
	if ( $ci <= $columns )
		$posts_per_page += ${ 'count' . $ci };
}
$query_args = array();
$query_args['post_type'] = 'product';
$query_args['posts_per_page'] = $posts_per_page;
$offset = ( empty( $offset ) ) ? 0 : intval( $offset );
if ( $offset )
	$query_args['offset'] = $offset;
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
$query_args = apply_filters( 'hootkit_products_list_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$products_list_query = new WP_Query( $query_args );

// Check if empty value (widgets added via Customizer)
if ( !isset( $firstpost ) ) $firstpost = array( 'size' => 'medium', 'show_price' => 1, 'show_addtocart' => 1 );

// Temporarily remove read more links from excerpts
if ( function_exists( 'hoot_remove_readmore_link' ) && apply_filters( 'hootkit_listwidget_remove_readmore', true, 'products-list' ) )
	hoot_remove_readmore_link();

// Style Manipulation
$userstyle = $style;
$style = ( $style == 'style0' ) ? 'style1' : $style;

// Set vars
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';

// Template modification Hook
do_action( 'hootkit_listwidget_wrap', 'products-list', ( ( !isset( $instance ) ) ? array() : $instance ), $products_list_query, $query_args );
?>

<div class="hk-list-widget products-list-widget hk-woo-products hk-list-<?php echo $style; ?>">

	<?php
	/* Display Title */
	$titlemarkup = $titleclass = '';
	if ( !empty( $title ) ) {
		$titlemarkup .= $before_title . $title . $after_title;
		$titleclass .= ' hastitle';
	}
	if ( $viewall == 'top' ) {
		$titlemarkup .= hootkit_get_viewall( false, 'product' );
		$titleclass .= ' hasviewall';
	}
	$titlemarkup = ( !empty( $titlemarkup ) ) ? '<div class="widget-title-wrap' . $titleclass . '">' . $titlemarkup . '</div>' : '';
	$titlemarkup .= ( !empty( $subtitle ) ) ? '<div class="widget-subtitle hoot-subtitle">' . $subtitle . '</div>' : '';
	echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'products-list', $title, $before_title, $after_title, $subtitle, $viewall ) ) );

	// Template modification Hook
	do_action( 'hootkit_listwidget_start', 'products-list', ( ( !isset( $instance ) ) ? array() : $instance ), $products_list_query, $query_args );

	// Variables
	global $post;
	$postcount = $colcount = 1;
	$count = $count1;
	$lastclass = ( $colcount == $columns ) ? 'hcol-last' : '';
	?>

	<div class="hk-list-columns">
		<div class="<?php echo "hcolumn-1-{$columns} hk-list-column-1 hcol-first {$lastclass}"; ?>">
			<?php
			if ( $products_list_query->have_posts() ) : while ( $products_list_query->have_posts() ) : $products_list_query->the_post();

				// Init
				global $product;
				setup_postdata( $post );
				if ( ! is_a( $product, 'WC_Product' ) ) break;
				$visual = ( $userstyle == 'style0' ) ? 0 : ( ( has_post_thumbnail() ) ? 1 : 0 );
				$metadisplay = array();

				if ( $postcount == 1 ) {
					$factor = ( $firstpost['size'] == 'thumb' ) ? 'small' : 'large';
					$showrating =    ( !empty ( $firstpost['show_rating']    ) ) ? $firstpost['show_rating'] : 0;
					$showprice =     ( !empty ( $firstpost['show_price']     ) ) ? $firstpost['show_price'] : 0;
					$showaddtocart = ( !empty ( $firstpost['show_addtocart'] ) ) ? $firstpost['show_addtocart'] : 0;
					$showcats =      ( !empty ( $firstpost['show_cats']      ) ) ? $firstpost['show_cats'] : 0;
					$showtags =      ( !empty ( $firstpost['show_tags']      ) ) ? $firstpost['show_tags'] : 0;
					$showcontent =   ( !empty ( $firstpost['show_content']   ) ) ? $firstpost['show_content'] : 'excerpt';
					$excerptlength = ( !empty ( $firstpost['excerpt_length'] ) ) ? intval( $firstpost['excerpt_length'] ) : '';
				} else {
					$factor = 'small';
					$showrating =    ( !empty ( $show_rating    ) ) ? $show_rating : 0;
					$showprice =     ( !empty ( $show_price     ) ) ? $show_price : 0;
					$showaddtocart = ( !empty ( $show_addtocart ) ) ? $show_addtocart : 0;
					$showcats =      ( !empty ( $show_cats      ) ) ? $show_cats : 0;
					$showtags =      ( !empty ( $show_tags      ) ) ? $show_tags : 0;
					$showcontent =   ( !empty ( $show_content   ) ) ? $show_content : 'none';
					$excerptlength = ( !empty ( $excerpt_length ) ) ? intval( $excerpt_length ) : '';
				}

				if ( $postcount == 1 ) {
					if ( $firstpost['size'] == 'thumb' ) $img_size = 'thumbnail';
					elseif( $firstpost['size'] == 'full' ) $img_size = 'full';
					else $img_size = 'hoot-large-thumb'; // hoot-preview-large -> blurry image when eg. 1035x425
				} else $img_size = 'thumbnail';
				$img_size = apply_filters( 'hootkit_listwidget_imgsize', $img_size, 'products-list', $postcount, $factor, $columns );
				$default_img_size = apply_filters( 'hoot_notheme_listwidget_imgsize', ( ( $factor == 'large' ) ? 'full' : 'thumbnail' ), 'products-list', $postcount, $factor, $columns );

				// Start Block Display
				$gridunit_attr = array();
				$gridunit_attr['class'] = "hk-listunit hk-listunit-{$factor}";
				$gridunit_attr['class'] .= ( $postcount == 1 ) ? ' hk-listunit-parent hk-imgsize-' . $firstpost['size'] : ' hk-listunit-child';
				$gridunit_attr['class'] .= ($visual) ? ' visual-img' : ' visual-none';
				$gridunit_attr['data-unitsize'] = $factor;
				$gridunit_attr['data-columns'] = $columns;
				?>

				<div <?php echo hoot_get_attr( 'hk-listunit', 'product', $gridunit_attr ) ?>>

					<?php
					if ( $visual ) :
						$gridimg_attr = array( 'class' => 'hk-listunit-image' );
						if ( $factor == 'large' && $firstpost['size'] == 'full' ) {
							$gridimg_attr['class'] .= ' hk-listunit-nobg';
						} else {
							$gridimg_attr['class'] .= ' hk-listunit-bg';
							$thumbnail_size = hootkit_thumbnail_size( $img_size, NULL, $default_img_size );
							$thumbnail_url = get_the_post_thumbnail_url( null, $thumbnail_size );
							$gridimg_attr['style'] = "background-image:url(" . esc_url($thumbnail_url) . ");";
						}
						?>
						<div <?php echo hoot_get_attr( 'hk-listunit-image', 'product', $gridimg_attr ) ?>>
							<?php hootkit_post_thumbnail( 'hk-listunit-img', $img_size, false, esc_url( get_permalink( $post->ID ) ), NULL, $default_img_size ); ?>
						</div>
					<?php endif; ?>

					<div class="hk-listunit-content">
						<h4 class="hk-listunit-title"><?php echo '<a href="' . esc_url( get_permalink() ) . '" ' . hoot_get_attr( 'product-list-link', ( ( !isset( $instance ) ) ? array() : $instance ) ) . '>';
							the_title();
							echo '</a>'; ?></h4>
						<?php
						if ( $showcats || $showtags ) :
							echo '<div class="hk-listunit-subtitle small"><div class="entry-byline">';
								if ( $showcats )
									echo wc_get_product_category_list( $product->get_id(), ', ',
										'<div class="entry-byline-block entry-byline-cats"><span class="entry-byline-label">' . __( 'En:', 'hootkit' ) . '</span>' . ' ',
										'</div>' );
								if ( $showtags )
									echo wc_get_product_tag_list( $product->get_id(), ', ',
										'<div class="entry-byline-block entry-byline-tags"><span class="entry-byline-label">' . __( 'Etiquetas:', 'hootkit' ) . '</span>' . ' ',
										'</div>' );
							echo '</div></div>';
						endif;
						if ( $showcontent === 'content' ) {
							echo '<div class="hk-listunit-text hk-listunit-fulltext">';
							the_content();
							echo '</div>';
						} elseif( $showcontent === 'desc' || $showcontent === 'excerpt' ) {
							$content = '';
							if ( $showcontent === 'desc' ) {
								$content = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
								if ( $content && apply_filters( 'hootkit_product_description_trim', false ) ) { // check if 'woocommerce_short_description' applies wpautop before setting to true
									$excerptlength = ( !empty( $excerptlength ) ) ? $excerptlength : (int) apply_filters( 'excerpt_length', 999 );
									$content = hoot_trim_content( $content, $excerptlength );
								}
							} else {
								$content = get_the_content();
								$content = apply_filters( 'the_content', $content );
								$content = str_replace( ']]>', ']]&gt;', $content );
								$excerptlength = ( !empty( $excerptlength ) ) ? $excerptlength : (int) apply_filters( 'excerpt_length', 55 );
								$content = ( $content ) ? hoot_trim_content( $content, $excerptlength ) : '';
							}
							if( $content ) {
								echo '<div class="hk-listunit-text hk-listunit-excerpt">';
								echo wpautop( $content );
								echo '</div>';
								// if ( function_exists( 'hoot_remove_readmore_link' ) && ( $style == 'style5' || $style == 'style6' ) )
								// 	echo $linktext;
							}
						}
						if ( !empty( $showrating ) ) {
							$rating_count = $product->get_rating_count();
							$average      = $product->get_average_rating();
							if ( $rating_count > 0 ) echo '<div class="listunit-product-rating"><a href="' . esc_url( get_permalink() ) . '" ' . hoot_get_attr( 'product-list-link', ( ( !isset( $instance ) ) ? array() : $instance ) ) . '>' . wc_get_rating_html( $average, $rating_count ) . '</a></div>';
						}
						if ( !empty( $showprice ) && $price_html = $product->get_price_html() )
							echo '<div class="listunit-product-price">' . $price_html . '</div>';
						if ( !empty( $showaddtocart ) ) {
							echo '<div class="listunit-addtocart">';
								woocommerce_template_loop_add_to_cart();
							echo '</div>';
						} ?>
					</div>

				</div><?php
				if ( $postcount == $count && $colcount < $columns ) {
					$colcount++;
					$count += ${ 'count' . $colcount };
					$lastclass = ( $colcount == $columns ) ? 'hcol-last' : '';
					echo "</div><div class='hcolumn-1-{$columns} hk-list-column-{$colcount} {$lastclass}'>";
				}
				$postcount++;

			endwhile; endif;

			wp_reset_postdata();
			?>
		</div>
		<div class="clearfix"></div>
	</div>

	<?php
	// View All link
	if ( !empty( $viewall ) && $viewall == 'bottom' ) hootkit_get_viewall( true, 'product' );

	// Template modification Hook
	do_action( 'hootkit_listwidget_end', 'products-list', ( ( !isset( $instance ) ) ? array() : $instance ), $products_list_query, $query_args );
	?>

</div>

<?php
// Reinstate read more links to excerpts
if ( function_exists( 'hoot_reinstate_readmore_link' ) && apply_filters( 'hootkit_listwidget_remove_readmore', true, 'products-list' ) )
	hoot_reinstate_readmore_link();