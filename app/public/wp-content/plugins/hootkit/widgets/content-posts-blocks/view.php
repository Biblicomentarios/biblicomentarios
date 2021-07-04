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
$query_args = array();
$count = ( empty( $count ) ) ? 0 : intval( $count );
$query_args['posts_per_page'] = ( empty( $count ) ) ? 4 : $count;
$offset = ( empty( $offset ) ) ? 0 : intval( $offset );
if ( $offset )
	$query_args['offset'] = $offset;
if ( isset( $category ) && is_string( $category ) ) $category = array( $category ); // Pre 1.0.10 compatibility with 'select' type
$exccategory = ( !empty( $exccategory ) && is_array( $exccategory ) ) ? array_map( 'hootkit_append_negative', $exccategory ) : array(); // undefined if none selected in multiselect
$category = ( !empty( $category ) && is_array( $category ) ) ? array_merge( $category, $exccategory) : $exccategory; // undefined if none selected in multiselect
if ( !empty( $category ) )
	$query_args['category'] = implode( ',', $category );
$query_args = apply_filters( 'hootkit_content_posts_blocks_query', $query_args, ( ( !isset( $instance ) ) ? array() : $instance ) );
$content_blocks_query = get_posts( $query_args );

// Temporarily remove read more links from excerpts
if ( function_exists( 'hoot_remove_readmore_link' ) )
	hoot_remove_readmore_link();

$excerptlength = ( empty( $excerptlength ) ) ? '' : intval( $excerptlength );

// Set vars
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';
$viewall = ( !empty( $viewall ) ) ? $viewall : '';

// Template modification Hook
do_action( 'hootkit_content_blocks_wrap', 'posts', ( ( !isset( $instance ) ) ? array() : $instance ), $content_blocks_query, $query_args );
?>

<div class="content-blocks-widget-wrap content-blocks-posts <?php echo hoot_sanitize_html_classes( "{$top_class} {$bottom_class}" ); ?>">
	<div class="content-blocks-widget">

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
		echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'content-posts-blocks', $title, $before_title, $after_title, $subtitle, $viewall ) ) );

		// Template modification Hook
		do_action( 'hootkit_content_blocks_start', 'posts', ( ( !isset( $instance ) ) ? array() : $instance ), $content_blocks_query, $query_args );
		?>

		<div class="flush-columns">
			<?php
					global $post;
					// $fullcontent = ( empty( $fullcontent ) ) ? 'excerpt' :
					// 				( ( $fullcontent === 1 ) ? 'content' : $fullcontent ); // Backward Compatible

					foreach ( $content_blocks_query as $post ) :

							// Init
							setup_postdata( $post );
							$visual = $visualtype = '';

							// Refresh user style (to add future op of diff styles for each block)
							$style = $userstyle;
							// Style 5,6 exceptions: doesnt work great with non images (no visual). So revert to Style 1 for this scenario
							$style = ( ( $style == 'style5' || $style == 'style6' ) && !has_post_thumbnail() ) ? 'style1' : $style;

							$style = apply_filters( 'hootkit_content_posts_block_style', $style, $userstyle, $post, ( ( !isset( $instance ) ) ? array() : $instance ) );

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
								$default_img_size = apply_filters( 'hootkit_nohoot_content_posts_block_imgsize', ( ( $style != 'style4' ) ? 'full' : 'thumbnail' ), $columns, $style );
								$img_size = hootkit_thumbnail_size( 'column-1-' . $img_size, NULL, $default_img_size );
								$img_size = apply_filters( 'hootkit_content_posts_block_imgsize', $img_size, $columns, $style );
								$visual = 1;
							}

							// Set Block Class (if no visual for style 2/3, then dont highlight)
							$column_class = ( !empty( $visualtype ) ) ? "hasvisual visual-{$visualtype}" : 'visual-none';

							// Set URL
							$linktag = '<a href="' . esc_url( get_permalink() ) . '" ' . hoot_get_attr( 'content-posts-blocks-link', ( ( !isset( $instance ) ) ? array() : $instance ) ) . '>';
							$linktagend = '</a>';
							$linktext = ( function_exists( 'hoot_get_mod' ) ) ? hoot_get_mod('read_more') : __( 'Know More', 'hootkit' );
							$linktext = ( empty( $linktext ) ) ? sprintf( __( 'Read More %s', 'hootkit' ), '&rarr;' ) : $linktext;
							$linktext = '<p class="more-link">' . $linktag . esc_html( $linktext ) . $linktagend . '</p>';

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
										<?php
										$metadisplay = array();;
										if ( !empty( $show_author ) ) $metadisplay[] = 'author';
										if ( !empty( $show_date ) ) $metadisplay[] = 'date';
										if ( !empty( $show_comments ) ) $metadisplay[] = 'comments';
										if ( !empty( $show_cats ) ) $metadisplay[] = 'cats';
										if ( !empty( $show_tags ) ) $metadisplay[] = 'tags';
										if ( in_array( 'cats', $metadisplay ) && apply_filters( 'hootkit_content_posts_block_display_catblock', false ) ) {
											hootkit_display_meta_info( array(
												'display' => array( 'cats' ),
												'context' => 'content-post-block',
												'editlink' => false,
												'wrapper' => 'div',
												'wrapper_class' => 'content-block-suptitle small',
												'empty' => '',
											) );
											$catkey = array_search ( 'cats', $metadisplay );
											unset( $metadisplay[ $catkey] );
										}
										?>
										<h4 class="content-block-title"><?php echo $linktag;
											the_title();
											echo $linktagend; ?></h4>
										<?php
										hootkit_display_meta_info( array(
											'display' => $metadisplay,
											'context' => 'content-post-block',
											'editlink' => false,
											'wrapper' => 'div',
											'wrapper_class' => 'content-block-subtitle small hoot-subtitle',
											'empty' => '',
										) ); ?>
										<?php
										if ( $fullcontent === 'content' ) {
											echo '<div class="content-block-text">';
											the_content();
											echo '</div>';
										} elseif( $fullcontent === 'excerpt' ) {
											echo '<div class="content-block-text">';
											if( !empty( $excerptlength ) )
												echo hoot_get_excerpt( $excerptlength );
											else
												the_excerpt();
											echo '</div>';
											if ( function_exists( 'hoot_remove_readmore_link' ) && ( $style == 'style5' || $style == 'style6' ) )
												echo $linktext;
										}
										?>
									</div>

								</div>
								<?php
								if ( $fullcontent === 'excerpt' && function_exists( 'hoot_remove_readmore_link' ) && $style != 'style5' && $style != 'style6' )
									echo $linktext;
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

					endforeach;

					wp_reset_postdata();

			if ( !$clearfix ) echo '</div>';
			?>
		</div>

		<?php
		// View All link
		if ( !empty( $viewall ) && $viewall == 'bottom' ) hootkit_get_viewall( true );

		// Template modification Hook
		do_action( 'hootkit_content_blocks_end', 'posts', ( ( !isset( $instance ) ) ? array() : $instance ), $content_blocks_query, $query_args );
		?>

	</div>
</div>

<?php
// Reinstate read more links to excerpts
if ( function_exists( 'hoot_reinstate_readmore_link' ) )
	hoot_reinstate_readmore_link();