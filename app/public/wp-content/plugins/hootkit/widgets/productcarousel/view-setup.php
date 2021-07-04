<?php
/**
 * General Variables available: $name, $params, $args, $content
 * $args has been 'extract'ed
 */

/* Do nothing if we dont have a template to show */
if ( !is_string( $slider_template ) || !file_exists( $slider_template ) )
	return;

/* Prevent errors : do not overwrite existing values */
$defaults = array( 'category' => '', 'count' => '', 'offset' => '', 'fullcontent' => '', 'excerptlength' => '', );
extract( $defaults, EXTR_SKIP );

/* Reset any previous slider */
global $hoot_data;
hoot_set_data( 'slider', array(), true );
hoot_set_data( 'slidersettings', array(), true );

/* Create slider settings object */
$slidersettings = array();
$slidersettings['type'] = 'productcarousel';
$slidersettings['source'] = 'slider-productcarousel.php';
// $slidersettings['widgetclass'] = ( !empty( $style ) ) ? ' slider-' . esc_attr( $style ) : ' slider-style1';
$slidersettings['widgetclass'] = ' hk-woo-products ';
$slidersettings['class'] = 'hootkitslider-productcarousel';
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

// Create a custom WP Query
$query_args = array();
$query_args['post_type'] = 'product';
$count = ( empty( $count ) ) ? 0 : intval( $count );
$query_args['posts_per_page'] = ( empty( $count ) || $count > 4 ) ? 4 : $count;
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
$query_args = apply_filters( 'hootkit_slider_productcarousel_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$slider_products_query = new WP_Query( $query_args );

/* Create Slides */
$slider = array();
$counter = 0;
global $post;
if ( $slider_products_query->have_posts() ) : while ( $slider_products_query->have_posts() ) : $slider_products_query->the_post();
	global $product;
	setup_postdata( $post );
	$key = 'g' . $counter;
	$counter++;
	$slider[$key]['postid']     = $post->ID;
	$slider[$key]['image']      = ( has_post_thumbnail( $post->ID ) ) ? get_post_thumbnail_id( $post->ID ) : '';
	$slider[$key]['rawtitle']   = get_the_title( $post->ID );
	// $slider[$key]['button']     = ( function_exists( 'hoot_get_mod' ) ) ? hoot_get_mod('read_more') : __( 'Know More', 'hootkit' );
	// $slider[$key]['url']        = esc_url( get_permalink( $post->ID ) );
	$slider[$key]['title']      = '<a href="' . esc_url( get_permalink( $post->ID ) ) . '">' . $slider[$key]['rawtitle'] . '</a>';
	if ( $show_cats || $show_tags ) {
		$slider[$key]['meta'] = '<div class="hootkitcarousel-subtitle small"><div class="entry-byline">';
		if ( $show_cats )
			$slider[$key]['meta'] .= wc_get_product_category_list( $product->get_id(), ', ',
				'<div class="entry-byline-block entry-byline-cats"><span class="entry-byline-label">' . __( 'En:', 'hootkit' ) . '</span>' . ' ',
				'</div>' );
		if ( $show_tags )
			$slider[$key]['meta'] .= wc_get_product_tag_list( $product->get_id(), ', ',
				'<div class="entry-byline-block entry-byline-tags"><span class="entry-byline-label">' . __( 'Etiquetas:', 'hootkit' ) . '</span>' . ' ',
				'</div>' );
		$slider[$key]['meta'] .= '</div></div>';
	}

	$slider[$key]['caption'] = '';
	if( $fullcontent === 'desc' || $fullcontent === 'excerpt' ) {
		if ( function_exists( 'hoot_remove_readmore_link' ) ) hoot_remove_readmore_link();
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
			$slider[$key]['caption'] = '<div class="content-block-text">' . wpautop( $content ) . '</div>';
		}
		if ( function_exists( 'hoot_reinstate_readmore_link' ) ) hoot_reinstate_readmore_link();
	}
	if ( $show_rating ) {
		$rating_count = $product->get_rating_count();
		$average      = $product->get_average_rating();
		if ( $rating_count > 0 )
			$slider[$key]['caption'] .= '<div class="productcarousel-product-rating invert-accent-typo">' . wc_get_rating_html( $average, $rating_count ) . '</div>';
	}
	if ( $show_price && $price_html = $product->get_price_html() ) {
		$slider[$key]['caption'] .= '<div class="productcarousel-product-price">' . $price_html . '</div>';
	}
	if ( $show_addtocart ) {
		$slider[$key]['caption'] .= '<div class="productcarousel-addtocart">';
		ob_start();
		woocommerce_template_loop_add_to_cart();
		$slider[$key]['caption'] .= ob_get_clean();
		$slider[$key]['caption'] .= '</div>';
	}
endwhile; endif;
wp_reset_postdata();

/* Set Slider */
hoot_set_data( 'slider', $slider, true );
hoot_set_data( 'slidersettings', $slidersettings, true );

/* Let developers alter slider */
do_action( 'hootkit_slider_loaded', 'productcarousel', ( ( !isset( $instance ) ) ? array() : $instance ) );

/* Finally get Slider Template HTML */
include ( $slider_template );