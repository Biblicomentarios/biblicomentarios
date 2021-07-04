<?php
/**
 * General Variables available: $name, $params, $args, $content
 * $args has been 'extract'ed
 */

/* Do nothing if we dont have a template to show */
if ( !is_string( $slider_template ) || !file_exists( $slider_template ) )
	return;

/* Prevent errors : do not overwrite existing values */
$defaults = array( 'category' => '', 'count' => '', 'offset' => '' );
extract( $defaults, EXTR_SKIP );

/* Reset any previous slider */
global $hoot_data;
hoot_set_data( 'slider', array(), true );
hoot_set_data( 'slidersettings', array(), true );

/* Create slider settings object */
$slidersettings = array();
$slidersettings['type'] = 'productlistcarousel';
$slidersettings['source'] = 'slider-productlistcarousel.php';
// $slidersettings['widgetclass'] = ( !empty( $style ) ) ? ' slider-' . esc_attr( $style ) : ' slider-style1';
$slidersettings['widgetclass'] = ' hk-woo-products ';
$slidersettings['class'] = 'hootkitslider-verticalcarousel hk-productlistcarousel';
$slidersettings['adaptiveheight'] = 'true'; // Default Setting else adaptiveheight = false and class .= fixedheight
// https://github.com/sachinchoolur/lightslider/issues/118
// https://github.com/sachinchoolur/lightslider/issues/119#issuecomment-93283923
$slidersettings['slidemove'] = '1';
$pause = empty( $pause ) ? 5 : absint( $pause );
$pause = ( $pause < 1 ) ? 1 : ( ( $pause > 15 ) ? 15 : $pause );
$slidersettings['pause'] = $pause * 1000;
$items = intval( $items );
$slidersettings['item'] = ( empty( $items ) ) ? '3' : $items;
// $slidersettings['widgetstyle'] = '';

/* Vertical Carousel */
$verticalunitdefaults = apply_filters( 'hootkit_listcarousel_unitdefaults', array(), 'product' );
$verticalunitdefaults = array_map( 'absint', $verticalunitdefaults );
$verticalunits['heightstyle1'] = ( !empty( $verticalunitdefaults['heightstyle1'] ) ) ? $verticalunitdefaults['heightstyle1'] : 80;
$verticalunits['heightstyle2'] = ( !empty( $verticalunitdefaults['heightstyle2'] ) ) ? $verticalunitdefaults['heightstyle2'] : 215;
$verticalunits['unitmargin'] = ( !empty( $verticalunitdefaults['unitmargin'] ) ) ? $verticalunitdefaults['unitmargin'] : 15;
if ( !empty( $unitheight ) ) $verticalunits['heightstyle1'] = $verticalunits['heightstyle2'] = $unitheight;

$slidersettings['verticalHeight'] = $slidersettings['item'] * ( $verticalunits['height' . $style] + $verticalunits['unitmargin'] );
$slidersettings['verticalHeight'] = absint( $slidersettings['verticalHeight'] );
if ( !empty( $slidersettings['verticalHeight'] ) )
	$slidersettings['vertical'] = 'true';
else
	unset( $slidersettings['verticalHeight'] );
$slidersettings['verticalunits'] = $verticalunits;

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
$query_args = apply_filters( 'hootkit_slider_productlistcarousel_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
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
	$slider[$key]['url']        = esc_url( get_permalink( $post->ID ) );
	$slider[$key]['title']      = '<a href="' . $slider[$key]['url'] . '">' . $slider[$key]['rawtitle'] . '</a>';
	if ( $show_cats || $show_tags ) {
		$slider[$key]['meta'] = '<div class="verticalcarousel-subtitle small"><div class="entry-byline">';
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
	if ( $show_rating ) {
		$rating_count = $product->get_rating_count();
		$average      = $product->get_average_rating();
		if ( $rating_count > 0 )
			$slider[$key]['caption'] .= '<div class="verticalcarousel-product-rating invert-accent-typo">' . wc_get_rating_html( $average, $rating_count ) . '</div>';
	}
	if ( $show_price && $price_html = $product->get_price_html() ) {
		$slider[$key]['caption'] .= '<div class="verticalcarousel-product-price">' . $price_html . '</div>';
	}
	if ( $show_addtocart ) {
		$slider[$key]['caption'] .= '<div class="verticalcarousel-addtocart">';
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
do_action( 'hootkit_slider_loaded', 'productlistcarousel', ( ( !isset( $instance ) ) ? array() : $instance ) );

/* Finally get Slider Template HTML */
include ( $slider_template );