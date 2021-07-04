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
$query_args['posts_per_page'] = $posts_per_page;
if ( isset( $category ) && is_string( $category ) ) $category = array( $category ); // Pre 1.0.10 compatibility with 'select' type
$exccategory = ( !empty( $exccategory ) && is_array( $exccategory ) ) ? array_map( 'hootkit_append_negative', $exccategory ) : array(); // undefined if none selected in multiselect
$category = ( !empty( $category ) && is_array( $category ) ) ? array_merge( $category, $exccategory) : $exccategory; // undefined if none selected in multiselect
if ( !empty( $category ) )
	$query_args['category'] = implode( ',', $category );
$offset = ( empty( $offset ) ) ? 0 : intval( $offset );
if ( $offset )
	$query_args['offset'] = $offset;
$query_args = apply_filters( 'hootkit_posts_list_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$posts_list_query = get_posts( $query_args );

// Check if empty value (widgets added via Customizer)
if ( !isset( $firstpost ) ) $firstpost = array( 'size' => 'medium', 'author' => 1, 'date' => 1 );

// Temporarily remove read more links from excerpts
if ( function_exists( 'hoot_remove_readmore_link' ) && apply_filters( 'hootkit_posts_list_remove_readmore', true ) )
	hoot_remove_readmore_link();

// Style Manipulation
$userstyle = $style;
$style = ( $style == 'style0' ) ? 'style1' : $style;

// Set vars
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';
$viewall = ( !empty( $viewall ) ) ? $viewall : '';

// Template modification Hook
do_action( 'hootkit_posts_list_wrap', 'posts-list', ( ( !isset( $instance ) ) ? array() : $instance ), $posts_list_query, $query_args );
?>

<div class="posts-list-widget posts-list-<?php echo $style; ?>">

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
	echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'posts-list', $title, $before_title, $after_title, $subtitle, $viewall ) ) );

	// Template modification Hook
	do_action( 'hootkit_posts_list_start', 'posts-list', ( ( !isset( $instance ) ) ? array() : $instance ), $posts_list_query, $query_args );

	// Variables
	global $post;
	$postcount = $colcount = 1;
	$count = $count1;
	$lastclass = ( $colcount == $columns ) ? 'hcol-last' : '';
	?>

	<div class="posts-list-columns">
		<div class="<?php echo "hcolumn-1-{$columns} posts-list-column-1 hcol-first {$lastclass}"; ?>">
			<?php
			foreach ( $posts_list_query as $post ) :

				// Init
				setup_postdata( $post );
				$visual = ( $userstyle == 'style0' ) ? 0 : ( ( has_post_thumbnail() ) ? 1 : 0 );
				$metadisplay = array();

				if ( $postcount == 1 ) {
					$factor = ( $firstpost['size'] == 'thumb' ) ? 'small' : 'large';
					if ( !empty( $firstpost['author'] ) ) $metadisplay[] = 'author';
					if ( !empty( $firstpost['date'] ) ) $metadisplay[] = 'date';
					if ( !empty( $firstpost['comments'] ) ) $metadisplay[] = 'comments';
					if ( !empty( $firstpost['cats'] ) ) $metadisplay[] = 'cats';
					if ( !empty( $firstpost['tags'] ) ) $metadisplay[] = 'tags';
					$showcontent = ( !empty ( $firstpost['show_content'] ) ) ? $firstpost['show_content'] : 'excerpt';
					$excerptlength = ( !empty( $firstpost['excerpt_length'] ) ) ? intval( $firstpost['excerpt_length'] ) : '';
				} else {
					$factor = 'small';
					if ( !empty( $show_author ) ) $metadisplay[] = 'author';
					if ( !empty( $show_date ) ) $metadisplay[] = 'date';
					if ( !empty( $show_comments ) ) $metadisplay[] = 'comments';
					if ( !empty( $show_cats ) ) $metadisplay[] = 'cats';
					if ( !empty( $show_tags ) ) $metadisplay[] = 'tags';
					$showcontent = ( !empty ( $show_content ) ) ? $show_content : 'none';
					$excerptlength = ( !empty( $excerpt_length ) ) ? intval( $excerpt_length ) : '';
				}

				if ( $postcount == 1 ) {
					if ( $firstpost['size'] == 'thumb' ) $img_size = 'thumbnail';
					elseif( $firstpost['size'] == 'full' ) $img_size = 'full';
					else $img_size = 'hoot-large-thumb'; // hoot-preview-large -> blurry image when eg. 1035x425
				} else $img_size = 'thumbnail';
				$img_size = apply_filters( 'hootkit_posts_list_imgsize', $img_size, $columns, $postcount, $factor );
				$default_img_size = apply_filters( 'hoot_notheme_posts_list_imgsize', ( ( $factor == 'large' ) ? 'full' : 'thumbnail' ), $columns, $postcount, $factor );

				// Start Block Display
				$gridunit_attr = array();
				$gridunit_attr['class'] = "posts-listunit posts-listunit-{$factor}";
				$gridunit_attr['class'] .= ( $postcount == 1 ) ? ' posts-listunit-parent posts-imgsize-' . $firstpost['size'] : ' posts-listunit-child';
				$gridunit_attr['class'] .= ($visual) ? ' visual-img' : ' visual-none';
				$gridunit_attr['data-unitsize'] = $factor;
				$gridunit_attr['data-columns'] = $columns;
				?>

				<div <?php echo hoot_get_attr( 'posts-listunit', '', $gridunit_attr ) ?>>

					<?php
					if ( $visual ) :
						$gridimg_attr = array( 'class' => 'posts-listunit-image' );
						if ( $factor == 'large' && $firstpost['size'] == 'full' ) {
							$gridimg_attr['class'] .= ' posts-listunit-nobg';
						} else {
							$gridimg_attr['class'] .= ' posts-listunit-bg';
							$thumbnail_size = hootkit_thumbnail_size( $img_size, NULL, $default_img_size );
							$thumbnail_url = get_the_post_thumbnail_url( null, $thumbnail_size );
							$gridimg_attr['style'] = "background-image:url(" . esc_url($thumbnail_url) . ");";
						}
						?>
						<div <?php echo hoot_get_attr( 'posts-listunit-image', '', $gridimg_attr ) ?>>
							<?php hootkit_post_thumbnail( 'posts-listunit-img', $img_size, false, esc_url( get_permalink( $post->ID ) ), NULL, $default_img_size ); ?>
						</div>
					<?php endif; ?>

					<div class="posts-listunit-content">
						<?php
						if ( in_array( 'cats', $metadisplay ) && apply_filters( 'hootkit_posts_list_display_catblock', false ) ) {
							hootkit_display_meta_info( array(
								'display' => array( 'cats' ),
								'context' => 'posts-listunit',
								'editlink' => false,
								'wrapper' => 'div',
								'wrapper_class' => 'posts-listunit-suptitle small',
								'empty' => '',
								) );
							$catkey = array_search ( 'cats', $metadisplay );
							unset( $metadisplay[ $catkey] );
						}
						?>
						<h4 class="posts-listunit-title"><?php echo '<a href="' . esc_url( get_permalink() ) . '" ' . hoot_get_attr( 'posts-listunit-link', 'permalink' ) . '>';
							the_title();
							echo '</a>'; ?></h4>
						<?php
						hootkit_display_meta_info( array(
							'display' => $metadisplay,
							'context' => 'posts-listunit',
							'editlink' => false,
							'wrapper' => 'div',
							'wrapper_class' => 'posts-listunit-subtitle small',
							'empty' => '',
						) );
						if ( $showcontent == 'excerpt' ) {
							echo '<div class="posts-listunit-text posts-listunit-excerpt">';
							echo hoot_get_excerpt( $excerptlength );
							echo '</div>';
						} elseif ( $showcontent == 'content' ) {
							echo '<div class="posts-listunit-text posts-listunit-fulltext">';
							the_content();
							echo '</div>';
						}
						?>
					</div>

				</div><?php
				if ( $postcount == $count && $colcount < $columns ) {
					$colcount++;
					$count += ${ 'count' . $colcount };
					$lastclass = ( $colcount == $columns ) ? 'hcol-last' : '';
					echo "</div><div class='hcolumn-1-{$columns} posts-list-column-{$colcount} {$lastclass}'>";
				}
				$postcount++;

			endforeach;

			wp_reset_postdata();
			?>
		</div>
		<div class="clearfix"></div>
	</div>

	<?php
	// View All link
	if ( !empty( $viewall ) && $viewall == 'bottom' ) hootkit_get_viewall( true );

	// Template modification Hook
	do_action( 'hootkit_posts_list_end', 'posts-list', ( ( !isset( $instance ) ) ? array() : $instance ), $posts_list_query, $query_args );
	?>

</div>

<?php
// Reinstate read more links to excerpts
if ( function_exists( 'hoot_reinstate_readmore_link' ) && apply_filters( 'hootkit_posts_list_remove_readmore', true ) )
	hoot_reinstate_readmore_link();