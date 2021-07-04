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
$style = ( empty( $style ) ) ? 'style1' : $style;
$styleclass .= ' ticker-' . $style;

// Create a custom WP Query
$count = ( empty( $count ) ) ? 0 : intval( $count );
$query_args = array();
$query_args['posts_per_page'] = ( empty( $count ) ) ? 10 : $count;
if ( isset( $category ) && is_string( $category ) ) $category = array( $category ); // Pre 1.0.10 compatibility with 'select' type
$exccategory = ( !empty( $exccategory ) && is_array( $exccategory ) ) ? array_map( 'hootkit_append_negative', $exccategory ) : array(); // undefined if none selected in multiselect
$category = ( !empty( $category ) && is_array( $category ) ) ? array_merge( $category, $exccategory) : $exccategory; // undefined if none selected in multiselect
if ( !empty( $category ) )
	$query_args['category'] = implode( ',', $category );
$query_args = apply_filters( 'hootkit_ticker_posts_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$ticker_posts_query = get_posts( $query_args );

// Template modification Hook
do_action( 'hootkit_ticker_wrap', 'posts', ( ( !isset( $instance ) ) ? array() : $instance ), $ticker_posts_query, $query_args );
?>

<div class="ticker-widget ticker-posts ticker-combined <?php echo $styleclass; ?>" <?php echo $widgetstyle;?>><?php

	/* Display Title */
	if ( !empty( $title ) )
		echo wp_kses_post( apply_filters( 'hootkit_widget_ticker_title', '<div class="ticker-title">' . $title . '</div>', $title, 'ticker-posts' ) );

	/* Start Ticker Message Box */
	?>
	<div class="ticker-msg-box" <?php echo $inlinestyle;?> data-speed='<?php echo $speed; ?>'>
		<div class="ticker-msgs">
			<?php
			global $post;
			foreach ( $ticker_posts_query as $post ) :

				// Init
				setup_postdata( $post );
				$visual = ( has_post_thumbnail() ) ? 1 : 0;
				$imgclass = ( $visual ) ? 'visual-img' : 'visual-none';
				$img_size = apply_filters( 'hootkit_ticker_posts_imgsize', 'thumbnail' );
				?>

				<div class="ticker-msg <?php echo $imgclass; ?>">
					<?php
					if ( $visual ) :
						$thumbnail_url = get_the_post_thumbnail_url( null, $img_size );
						$imgstyle = "background-image:url(" . esc_url($thumbnail_url) . ");";
						$imgstyle .= ( !empty( $thumbheight ) ) ? 'height:' . intval( $thumbheight ) . 'px;width:' . intval( $thumbheight ) * 1.5 . 'px;' : '';
						?>
						<div class="ticker-img" style="<?php echo esc_attr( $imgstyle ); ?>">
							<?php hootkit_post_thumbnail( 'ticker-post-img', $img_size, false, esc_url( get_permalink( $post->ID ) ), NULL, 'thumbnail' ); ?>
						</div>
						<?php
					else: // Since we are not using a flexbox on ticker-msgs anymore, add an empty image box of same height to maintain middle alignment of ticker-msg without $visual
						$imgstyle = 'width:0;';
						$imgstyle .= ( !empty( $thumbheight ) ) ? 'height:' . intval( $thumbheight ) . 'px;' : '';
						?>
						<div class="ticker-img noimge" style="<?php echo esc_attr( $imgstyle ); ?>"></div>
						<?php
					endif;

					if ( $style == 'style2' ) $contentstyle = 'style="max-width:210px;white-space:normal;"'; else $contentstyle = 'style="max-width:none;white-space:nowrap;"'; // JNES@deprecated <= Unos v2.9.1 @6.20 (only added for transition of theme css to v.2.9.1) ?>
					<div class="ticker-content" <?php echo $contentstyle ?>>
						<div class="ticker-msgtitle"><a href="<?php echo esc_url( get_permalink() ); ?>" <?php echo hoot_get_attr( 'post-ticker-link', ( ( !isset( $instance ) ) ? array() : $instance ) ); ?>><?php the_title(); ?></a></div>
					</div>
				</div>

				<?php
			endforeach;
			wp_reset_postdata();
			?>
		</div>
	</div>

</div>