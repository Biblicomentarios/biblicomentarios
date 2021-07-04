<?php
$searchlabel = __( 'Search Products', 'hootkit' );
$searchplaceholder = ( !empty( $placeholder ) ) ? $placeholder : '';
$searchsubmit = __( 'Search', 'hootkit' );
$searchquery = get_search_query();

$catlabel = apply_filters( 'hootkit_product_search_category_select', __( 'Select Category', 'hootkit' ) );
$searchcat = ( !empty( $_GET['product_cat'] ) ) ? $_GET['product_cat'] : '';
$searchclass = ( $show_cats ) ? 'search-cats' : 'search-allcats';

/* Display Title */
$titlemarkup = ( !empty( $title ) ) ? '<div class="widget-title-wrap">' . $before_title . $title . $after_title . '</div>' : '';
echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'products-search', $title, $before_title, $after_title ) ) );

echo '<div class="hk-searchbody hk-products-search ' . $searchclass . '">';

	echo '<form method="get" class="hk-searchform" action="' . esc_url( home_url( '/' ) ) . '" >';

		echo '<label class="screen-reader-text">' . esc_html( $searchlabel ) . '</label>';
		echo '<i class="fas fa-search"></i>';
		echo '<input type="text" class="hk-searchtext" name="s" placeholder="' . esc_attr( $searchplaceholder ) . '" value="' . esc_attr( $searchquery ) . '" />';
		if ( $show_cats ) {
			// $terms = (array)Hoot_List::get_terms( 0, 'product_cat' ); // use slug instead of id
			echo '<select name="product_cat" class="hk-searchselect">';
				echo '<option value="" ' . ( ( empty( $searchcat ) ) ? 'selected="selected"' : '' ) . '>' . esc_attr( $catlabel ) . '</option>';
				$terms = (array) get_terms( array( 'taxonomy' => 'product_cat', 'number' => 0 ) );
				foreach ( $terms as $term )
					echo '<option value="' . esc_attr( $term->slug ) . '" ' . ( ( $searchcat == $term->slug ) ? 'selected="selected"' : '' ) . '>' . esc_attr( $term->name ) . '</option>';
			echo '</select>';
		}
		echo '<input type="hidden" name="post_type" value="product">';
		echo '<input type="submit" class="hk-submit" name="submit" value="' . esc_attr( $searchsubmit ) . '" /><span class="js-search-placeholder"></span>';

	echo '</form>';

echo '</div><!-- /searchbody -->';