<?php
// Get total columns and set column counter
$columns = ( intval( $columns ) >= 1 && intval( $columns ) <= 5 ) ? intval( $columns ) : 4;

// Get total rows and set row counter
$rows = ( empty( $rows ) ) ? 0 : intval( $rows );
$rows = ( empty( $rows ) ) ? 2 : $rows;

// Edge case
if ( $rows == 1 || $columns == 1 )
	$firstpost['standard'] = 1;

// Create category array from main options 
if ( isset( $category ) && is_string( $category ) ) $category = array( $category ); // Pre 1.0.10 compatibility with 'select' type
$exccategory = ( !empty( $exccategory ) && is_array( $exccategory ) ) ? array_map( 'hootkit_append_negative', $exccategory ) : array(); // undefined if none selected in multiselect
$category = ( !empty( $category ) && is_array( $category ) ) ? array_merge( $category, $exccategory) : $exccategory; // undefined if none selected in multiselect


/*** Create a custom WP Query for first post grid ***/

$fpquery_args = array();

// Count
$fpquery_args['posts_per_page'] = ( !empty( $firstpost['count'] ) ) ? intval( $firstpost['count'] ) : 1;

// Categories : Follow widget cat option if firstpost categories is empty
if ( !empty( $firstpost['category'] ) ) // undefined if none selected in multiselect
	$fpquery_args['category'] = implode( ',', $firstpost['category'] );
elseif ( !empty( $category ) )
	$fpquery_args['category'] = implode( ',', $category );

// Skip posts without image
$fpquery_args['meta_query'] = array(
	array(
		'key' => '_thumbnail_id',
		'compare' => 'EXISTS'
	),
);

// Create Query
$fpquery_args = apply_filters( 'hootkit_post_grid_firstquery', $fpquery_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$post_firstgrid_query = get_posts( $fpquery_args );


/*** Create a custom WP Query for remaining post grids ***/

$query_args = array();

// Count
$count = $rows * $columns;
$count--; // Remove count for first post
if ( empty( $firstpost['standard'] ) ) {
	$count = $count - 3;
	if ( $count < 0 ) $count = 0; // redundant after introduction of edge case logic above
}
$query_args['posts_per_page'] = $count;

// Categories : Exclude firstpost categories if not empty ; else skip number of posts from first post grid
if ( !empty( $firstpost['category'] ) ) // undefined if none selected in multiselect
	$category = array_merge( $category, array_map( 'hootkit_append_negative', $firstpost['category'] ) );
else
	$query_args['offset'] = $fpquery_args['posts_per_page'];
if ( !empty( $category ) )
	$query_args['category'] = implode( ',', $category );

// Skip posts without image
$query_args['meta_query'] = array(
	array(
		'key' => '_thumbnail_id',
		'compare' => 'EXISTS'
	),
);

// Create Query
$query_args = apply_filters( 'hootkit_post_grid_stdquery', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$post_grid_query = get_posts( $query_args );

// Set vars
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';
$viewall = ( !empty( $viewall ) ) ? $viewall : '';


/*** Template Functions ***/
// @todo : Improve template file with proper location for template functions within plugin in respect to theme template management

// Display Grid Function
if ( !function_exists( 'hootkit_post_grid_displayunit' ) ):
function hootkit_post_grid_displayunit( $columns, $postcount, $show_title = true, $gridunit_height = 0, $metadisplay = array(), $factor = 1 ){
				// $img_size = hootkit_thumbnail_size( "column-{$factor}-{$columns}" );
				$img_size = 'hoot-large-thumb'; // hoot-preview-large -> blurry image when eg. 1035x425
				$img_size = apply_filters( 'hootkit_post_grid_imgsize', $img_size, $columns, $postcount, $factor );
				$default_img_size = apply_filters( 'hoot_notheme_post_grid_imgsize', ( ( $factor == 2 ) ? 'full' : 'thumbnail' ), $columns, $postcount, $factor );
				$gridimg_attr = array( 'style' => '' );
				$thumbnail_size = hootkit_thumbnail_size( $img_size, NULL, $default_img_size );
				$thumbnail_url = get_the_post_thumbnail_url( null, $thumbnail_size );
				if ( $thumbnail_url ) $gridimg_attr['style'] .= "background-image:url(" . esc_url($thumbnail_url) . ");";
				if ( $gridunit_height ) $gridimg_attr['style'] .= 'height:' . esc_attr( $gridunit_height * $factor ) . 'px;';
				?>

				<div <?php echo hoot_get_attr( 'post-gridunit-image', '', $gridimg_attr ) ?>>
					<?php hootkit_post_thumbnail( 'post-gridunit-img', $img_size, false, '', NULL, $default_img_size ); // Redundant, but we use it for SEO (related images) ?>
				</div>

				<div class="post-gridunit-bg"><?php echo '<a href="' . esc_url( get_permalink() ) . '" ' . hoot_get_attr( 'post-gridunit-imglink', 'permalink' ) . '></a>'; ?></div>

				<div class="post-gridunit-content">
					<?php
					if ( in_array( 'cats', $metadisplay ) && apply_filters( 'hootkit_post_grid_display_catblock', false ) ) {
						hootkit_display_meta_info( array(
							'display' => array( 'cats' ),
							'context' => 'post-gridunit',
							'editlink' => false,
							'wrapper' => 'div',
							'wrapper_class' => 'post-gridunit-suptitle small',
							'empty' => '',
						) );
						$catkey = array_search ( 'cats', $metadisplay );
						unset( $metadisplay[ $catkey] );
					}
					?>
					<?php if ( !empty( $show_title ) ) : ?>
						<h4 class="post-gridunit-title"><?php echo '<a href="' . esc_url( get_permalink() ) . '" ' . hoot_get_attr( 'post-gridunit-link', 'permalink' ) . '>';
							the_title();
							echo '</a>'; ?></h4>
					<?php endif; ?>
					<?php
					hootkit_display_meta_info( array(
						'display' => $metadisplay,
						'context' => 'post-gridunit',
						'editlink' => false,
						'wrapper' => 'div',
						'wrapper_class' => 'post-gridunit-subtitle small',
						'empty' => '',
					) );
					?>
				</div>
				<?php
}
endif;



/*** START TEMPLATE ***/

// Template modification Hook
do_action( 'hootkit_post_grid_wrap', 'post-grid', ( ( !isset( $instance ) ) ? array() : $instance ), $post_grid_query, $query_args );
?>

<div class="post-grid-widget">

	<?php
	/* Display Title */
	$titlemarkup = $titleclass = '';
	if ( !empty( $title ) ) {
		$titlemarkup .= $before_title . $title . $after_title;
		$titleclass .= ' hastitle';
	}
	if ( $viewall == 'top' ) {
		$titlemarkup .= hootkit_get_viewall();
		$titleclass .= ' hasviewall';
	}
	$titlemarkup = ( !empty( $titlemarkup ) ) ? '<div class="widget-title-wrap' . $titleclass . '">' . $titlemarkup . '</div>' : '';
	$titlemarkup .= ( !empty( $subtitle ) ) ? '<div class="widget-subtitle hoot-subtitle">' . $subtitle . '</div>' : '';
	echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'post-grid', $title, $before_title, $after_title, $subtitle, $viewall ) ) );

	// Template modification Hook
	do_action( 'hootkit_post_grid_start', 'post-grid', ( ( !isset( $instance ) ) ? array() : $instance ), $post_grid_query, $query_args );
	?>

	<div class="post-grid-columns">
		<?php
		global $post;
		$postcount = 1;

		/* First Post Grid */

		$factor = ( $columns == 1 || !empty( $firstpost['standard'] ) ) ? '1' : '2';
		$gridunit_attr = array();
		$gridunit_attr['class'] = "post-gridunit hcolumn-{$factor}-{$columns} post-gridunit-size{$factor}";
		$gridunit_attr['data-unitsize'] = $factor;
		$gridunit_attr['data-columns'] = $columns;
		$gridunit_height = ( empty( $unitheight ) ) ? 0 : ( intval( $unitheight ) );
		$gridunit_style = ( $gridunit_height && $factor == 2 ) ? 'style="height:' . esc_attr( $gridunit_height ) . 'px;"' : '';
		$gridslider = ( !empty( $fpquery_args['posts_per_page'] ) && intval( $fpquery_args['posts_per_page'] ) > 1 );
		?>

		<div <?php echo hoot_get_attr( 'post-gridunit', '', $gridunit_attr ) ?> <?php echo $gridunit_style; ?>>

			<?php
			if ( $gridslider ) echo '<div ' . hoot_get_attr( 'post-gridslider', '', 'lightSlider' ) . '>';
			foreach ( $post_firstgrid_query as $post ) :

				setup_postdata( $post );

				$metadisplay = array();
				if ( !empty( $firstpost['author'] ) ) $metadisplay[] = 'author';
				if ( !empty( $firstpost['date'] ) ) $metadisplay[] = 'date';
				if ( !empty( $firstpost['comments'] ) ) $metadisplay[] = 'comments';
				if ( !empty( $firstpost['cats'] ) ) $metadisplay[] = 'cats';
				if ( !empty( $firstpost['tags'] ) ) $metadisplay[] = 'tags';

				if ( $gridslider ) echo '<div class="post-grid-slide">';;
				hootkit_post_grid_displayunit( $columns, $postcount, $show_title, $gridunit_height, $metadisplay, $factor );
				if ( $gridslider ) echo '</div>';

			endforeach;
			if ( $gridslider ) echo '</div>';
			?>

		</div>

		<?php
		$postcount++;
		wp_reset_postdata();

		/* Remaining Post Grids */

		if ( !empty( $query_args['posts_per_page'] ) ): // Custom query was still created if posts_per_page = 0
		foreach ( $post_grid_query as $post ) :

		$factor = '1';
		$gridunit_attr = array();
		$gridunit_attr['class'] = "post-gridunit hcolumn-{$factor}-{$columns} post-gridunit-size{$factor}";
		$gridunit_attr['data-unitsize'] = $factor;
		$gridunit_attr['data-columns'] = $columns;
		$gridunit_height = ( empty( $unitheight ) ) ? 0 : ( intval( $unitheight ) );
		$gridunit_style = ( $gridunit_height && $factor == 2 ) ? 'style="height:' . esc_attr( $gridunit_height ) . 'px;"' : '';
		?>

		<div <?php echo hoot_get_attr( 'post-gridunit', '', $gridunit_attr ) ?> <?php echo $gridunit_style; ?>>

				<?php
				setup_postdata( $post );

				$metadisplay = array();
				if ( !empty( $show_author ) ) $metadisplay[] = 'author';
				if ( !empty( $show_date ) ) $metadisplay[] = 'date';
				if ( !empty( $show_comments ) ) $metadisplay[] = 'comments';
				if ( !empty( $show_cats ) ) $metadisplay[] = 'cats';
				if ( !empty( $show_tags ) ) $metadisplay[] = 'tags';

				hootkit_post_grid_displayunit( $columns, $postcount, $show_title, $gridunit_height, $metadisplay, $factor );

		?>
		</div>

		<?php
		$postcount++;
		endforeach;
		wp_reset_postdata();
		endif;

		echo '<div class="clearfix"></div>';
		?>
	</div>

	<?php
	// View All link
	if ( !empty( $viewall ) && $viewall == 'bottom' ) hootkit_get_viewall( true );

	// Template modification Hook
	do_action( 'hootkit_post_grid_end', 'post-grid', ( ( !isset( $instance ) ) ? array() : $instance ), $post_grid_query, $query_args );
	?>

</div>