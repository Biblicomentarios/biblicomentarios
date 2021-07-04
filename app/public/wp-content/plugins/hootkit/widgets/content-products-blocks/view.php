<?php
// Get border classes
$top_class = hootkit_widget_borderclass( $border, 0, 'topborder-');
$bottom_class = hootkit_widget_borderclass( $border, 1, 'bottomborder-');

// Get total columns and set column counter
$columns = ( intval( $columns ) >= 1 && intval( $columns ) <= 5 ) ? intval( $columns ) : 3;
$column = $counter = 1;

// Set clearfix to avoid error if there are no boxes
$clearfix = 1;

// Set user defined style for content boxes
$userstyle = $style;

// Create a custom WP Query
$count = ( empty( $count ) ) ? 0 : intval( $count );
$query_args = array();
$query_args['post_type'] = 'product';
$query_args['posts_per_page'] = ( empty( $count ) ) ? 4 : $count;
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
$query_args = apply_filters( 'hootkit_content_products_blocks_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$content_blocks_query = new WP_Query( $query_args );

// Temporarily remove read more links from excerpts
if ( function_exists( 'hoot_remove_readmore_link' ) )
	hoot_remove_readmore_link();

$excerptlength = ( empty( $excerptlength ) ) ? '' : intval( $excerptlength );

// Set vars
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';

// Template modification Hook
do_action( 'hootkit_content_blocks_wrap', 'products', ( ( !isset( $instance ) ) ? array() : $instance ), $content_blocks_query, $query_args );
?>

<div class="content-blocks-widget-wrap content-blocks-products hk-woo-products <?php echo hoot_sanitize_html_classes( "{$top_class} {$bottom_class}" ); ?>">
	<div class="content-blocks-widget">

		<?php
		/* Display Title */
		$titlemarkup = $titleclass = '';
		if ( !empty( $title ) ) {
			$titlemarkup .= $before_title . $title . $after_title;
			$titleclass .= ' hastitle';
		}
		if ( $viewall == 'top' ) {
			$titlemarkup .= hootkit_get_viewall( false, 'product');
			$titleclass .= ' hasviewall';
		}
		$titlemarkup = ( !empty( $titlemarkup ) ) ? '<div class="widget-title-wrap' . $titleclass . '">' . $titlemarkup . '</div>' : '';
		$titlemarkup .= ( !empty( $subtitle ) ) ? '<div class="widget-subtitle hoot-subtitle">' . $subtitle . '</div>' : '';
		echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'content-products-blocks', $title, $before_title, $after_title, $subtitle, $viewall ) ) );

		// Template modification Hook
		do_action( 'hootkit_content_blocks_start', 'products', ( ( !isset( $instance ) ) ? array() : $instance ), $content_blocks_query, $query_args );
		?>

		<div class="flush-columns">
			<?php
					global $post;
					if ( $content_blocks_query->have_posts() ) : while ( $content_blocks_query->have_posts() ) : $content_blocks_query->the_post();

							// Init
							global $product;
							setup_postdata( $post );
							if ( ! is_a( $product, 'WC_Product' ) ) break;
							$visual = $visualtype = '';

							// Refresh user style (to add future op of diff styles for each block)
							$style = $userstyle;
							// Style 5,6 exceptions: doesnt work great with non images (no visual). So revert to Style 1 for this scenario
							$style = ( ( $style == 'style5' || $style == 'style6' ) && !has_post_thumbnail() ) ? 'style1' : $style;

							$style = apply_filters( 'hootkit_content_products_block_style', $style, $userstyle, $product, ( ( !isset( $instance ) ) ? array() : $instance ) );

							// Set image or icon
							if ( has_post_thumbnail() ) {
								$visualtype = 'image';
								if ( $style == 'style4' ) {
									switch ( $columns ) {
										case 1: $img_size = 2; break;
										case 2: $img_size = 4; break;
										default: $img_size = 5;
									}
								} else {
									$img_size = $columns;
								}
								$default_img_size = apply_filters( 'hootkit_nohoot_content_products_block_imgsize', ( ( $style != 'style4' ) ? 'full' : 'thumbnail' ), $columns, $style );
								$img_size = hootkit_thumbnail_size( 'column-1-' . $img_size, NULL, $default_img_size );
								$img_size = apply_filters( 'hootkit_content_products_block_imgsize', $img_size, $columns, $style );
								$visual = 1;
							}

							// Set Block Class (if no visual for style 2/3, then dont highlight)
							$column_class = ( !empty( $visualtype ) ) ? "hasvisual visual-{$visualtype}" : 'visual-none';

							// Set URL
							$linktag = '<a href="' . esc_url( get_permalink() ) . '" ' . hoot_get_attr( 'content-products-blocks-link', ( ( !isset( $instance ) ) ? array() : $instance ) ) . '>';
							$linktagend = '</a>';
							// $linktext = ( function_exists( 'hoot_get_mod' ) ) ? hoot_get_mod('read_more') : __( 'Know More', 'hootkit' );
							// $linktext = ( empty( $linktext ) ) ? sprintf( __( 'Read More %s', 'hootkit' ), '&rarr;' ) : $linktext;
							// $linktext = '<p class="more-link">' . $linktag . esc_html( $linktext ) . $linktagend . '</p>';

							// Start Block Display
							if ( $column == 1 ) echo '<div class="content-block-row">';
							?>

							<div class="content-block-column <?php echo hoot_sanitize_html_classes( "hcolumn-1-{$columns} content-block-{$counter} content-block-{$style} {$column_class}" ); ?>">
								<div <?php hoot_attr( 'content-block',
													  array( 'visual' => $visual, 'visualtype' => $visualtype, 'style' => $style ),
													  'no-highlight'
													); ?>>

									<?php if ( $visualtype == 'image' ) : ?>
										<div class="content-block-visual content-block-image">
											<?php echo $linktag;
											$jplazyclass = ( $style == 'style5' ) ? 'skip-lazy' : '';
											hootkit_post_thumbnail( "content-block-img {$jplazyclass}", $img_size, false, '', NULL, $default_img_size );
											echo $linktagend; ?>
										</div>
									<?php endif; ?>

									<div class="content-block-content<?php
										if ( $visualtype == 'image' ) echo ' content-block-content-hasimage';
										else echo ' no-visual';
										?>">
										<h4 class="content-block-title"><?php echo $linktag;
											the_title();
											echo $linktagend; ?></h4>
										<?php
										if ( $show_cats || $show_tags ) :
											echo '<div class="content-block-subtitle small hoot-subtitle"><div class="entry-byline">';
												if ( $show_cats )
													echo wc_get_product_category_list( $product->get_id(), ', ',
														'<div class="entry-byline-block entry-byline-cats"><span class="entry-byline-label">' . __( 'En:', 'hootkit' ) . '</span>' . ' ',
														'</div>' );
												if ( $show_tags )
													echo wc_get_product_tag_list( $product->get_id(), ', ',
														'<div class="entry-byline-block entry-byline-tags"><span class="entry-byline-label">' . __( 'Etiquetas:', 'hootkit' ) . '</span>' . ' ',
														'</div>' );
											echo '</div></div>';
										endif;
										if ( $fullcontent === 'content' ) {
											echo '<div class="content-block-text">';
											the_content();
											echo '</div>';
										} elseif( $fullcontent === 'desc' || $fullcontent === 'excerpt' ) {
											$content = '';
											if ( $fullcontent === 'desc' ) {
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
												echo '<div class="content-block-text">';
												echo wpautop( $content );
												echo '</div>';
												// if ( function_exists( 'hoot_remove_readmore_link' ) && ( $style == 'style5' || $style == 'style6' ) )
												// 	echo $linktext;
											}
										}
										if ( !empty( $show_rating ) ) {
											$rating_count = $product->get_rating_count();
											$average      = $product->get_average_rating();
											if ( $rating_count > 0 ) echo '<div class="content-block-product-rating">' . $linktag . wc_get_rating_html( $average, $rating_count ) . $linktagend . '</div>';
										}
										if ( !empty( $show_price ) && $price_html = $product->get_price_html() )
											echo '<div class="content-block-product-price">' . $price_html . '</div>';
										if ( !empty( $show_addtocart ) ) {
											echo '<div class="content-block-addtocart">';
												woocommerce_template_loop_add_to_cart();
											echo '</div>';
										} ?>
									</div>

								</div>
								<?php
								// if ( $fullcontent === 'excerpt' && function_exists( 'hoot_remove_readmore_link' ) && $style != 'style5' && $style != 'style6' )
								// 	echo $linktext;
								?>
							</div><?php

							$counter++;
							if ( $column == $columns ) {
								echo '</div>';
								$column = $clearfix = 1;
							} else {
								$clearfix = false;
								$column++;
							}

					endwhile; endif;

					wp_reset_postdata();

			if ( !$clearfix ) echo '</div>';
			?>
		</div>

		<?php
		// View All link
		if ( !empty( $viewall ) && $viewall == 'bottom' ) hootkit_get_viewall( true, 'product' );

		// Template modification Hook
		do_action( 'hootkit_content_blocks_end', 'posts', ( ( !isset( $instance ) ) ? array() : $instance ), $content_blocks_query, $query_args );
		?>

	</div>
</div>

<?php
// Reinstate read more links to excerpts
if ( function_exists( 'hoot_reinstate_readmore_link' ) )
	hoot_reinstate_readmore_link();