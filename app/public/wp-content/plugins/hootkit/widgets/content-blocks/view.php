<?php
// Return if no boxes to show
if ( empty( $boxes ) || !is_array( $boxes ) )
	return;

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

// Set vars
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';

// Template modification Hook
do_action( 'hootkit_content_blocks_wrap', 'custom', ( ( !isset( $instance ) ) ? array() : $instance ) );
?>

<div class="content-blocks-widget-wrap content-blocks-custom <?php echo hoot_sanitize_html_classes( "{$top_class} {$bottom_class}" ); ?>">
	<div class="content-blocks-widget">

		<?php
		/* Display Title */
		$titlemarkup = $titleclass = '';
		if ( !empty( $title ) ) {
			$titlemarkup .= $before_title . $title . $after_title;
			$titleclass .= ' hastitle';
		}
		$titlemarkup = ( !empty( $titlemarkup ) ) ? '<div class="widget-title-wrap' . $titleclass . '">' . $titlemarkup . '</div>' : '';
		$titlemarkup .= ( !empty( $subtitle ) ) ? '<div class="widget-subtitle hoot-subtitle">' . $subtitle . '</div>' : '';
		echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'content-blocks', $title, $before_title, $after_title, $subtitle ) ) );

		// Template modification Hook
		do_action( 'hootkit_content_blocks_start', 'custom', ( ( !isset( $instance ) ) ? array() : $instance ) );
		?>

		<div class="flush-columns">
			<?php
			foreach ( $boxes as $key => $box ) :

				// Init
				$visual = $visualtype = '';
				$box['icon_style'] = ( isset( $box['icon_style'] ) ) ? $box['icon_style'] : 'none';

				// Refresh user style (to add future op of diff styles for each block)
				$style = $userstyle;
				// Style-3 exceptions: doesnt work great with non icons (images or no visual). So revert to Style-1 for this scenario.
				$style = ( $style == 'style3' && empty( $box['icon'] ) ) ? 'style1' : $style;
				// Style 5,6 exceptions: doesnt work great with non images (icons or no visual). So revert to Style 1 for this scenario
				$style = ( ( $style == 'style5' || $style == 'style6' ) && empty( $box['image'] ) ) ? 'style1' : $style;

				$style = apply_filters( 'hootkit_content_block_style', $style, $userstyle, $box, ( ( !isset( $instance ) ) ? array() : $instance ) );

				// Set image or icon
				if ( !empty( $box['icon'] ) ) {
					$visualtype = 'icon';
					$visual = '<i class="' . hoot_sanitize_fa( $box['icon'] ) . '"></i>';
				} elseif ( !empty( $box['image'] ) ) {
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
					$default_img_size = apply_filters( 'hootkit_nohoot_content_block_imgsize', ( ( $style != 'style4' ) ? 'full' : 'thumbnail' ), $columns, $style );
					$img_size = hootkit_thumbnail_size( 'column-1-' . $img_size, NULL, $default_img_size );
					$img_size = apply_filters( 'hootkit_content_block_imgsize', $img_size, $columns, $style );
					$visual = 1;
				}

				// Set Block Class (if no visual for style 2/3, then dont highlight)
				$column_class = ( !empty( $visualtype ) ) ? "hasvisual visual-{$visualtype}" : 'visual-none';

				// Set URL
				if ( !empty( $box['url'] ) ) {
					$linktag = '<a href="' . esc_url( $box['url'] ) . '" ' . hoot_get_attr( 'content-block-link', ( ( !isset( $instance ) ) ? array() : $instance ) ) . '>';
					$linktagend = '</a>';
					$linktext = ( !empty( $box['link'] ) ) ? $box['link'] : ( ( function_exists( 'hoot_get_mod' ) ) ? hoot_get_mod('read_more') : __( 'Know More', 'hootkit' ) );
					$linktext = ( empty( $linktext ) ) ? sprintf( __( 'Read More %s', 'hootkit' ), '&rarr;' ) : $linktext;
					$linktext = '<p class="more-link">' . $linktag . esc_html( $linktext ) . $linktagend . '</p>';
				} else {
					$linktag = $linktagend = $linktext = '';
				}

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
								<?php
								$jplazyclass = ( $style == 'style5' ) ? 'skip-lazy' : '';
								$imageid = intval( $box['image'] );
								if ( !empty( $imageid ) )
									echo $linktag . '<div class="entry-featured-img-wrap">' . wp_get_attachment_image( $imageid, $img_size, '', array( 'class' => "content-block-img attachment-{$img_size} size-{$img_size} {$jplazyclass}", 'itemprop' => 'image' ) ) . '</div>' . $linktagend;
								?>
							</div>
						<?php elseif ( $visualtype == 'icon' ) : ?>
							<?php
							$contrast_class = ( 'none' == $box['icon_style'] || 'style4' == $style ) ? '' : ' accent-typo';
							$contrast_class = ( 'none' == $box['icon_style'] ) ? '' :
											  ( ( 'style4' == $style ) ? ' accent-typo ' : ' invert-typo ' );
							$contrast_class = ( 'style3' == $style ) ? ' enforce-typo ' : $contrast_class;
							$contrast_class = apply_filters( 'hootkit_content_blocks_icon_style', '', $box, $style );
							?>
							<div class="content-block-visual content-block-icon <?php echo 'icon-style-' . esc_attr( $box['icon_style'] ); echo ' ' . esc_attr( $contrast_class ); ?>">
								<?php echo $linktag . $visual . $linktagend; ?>
							</div>
						<?php endif; ?>

						<?php if ( !empty( $box['title'] ) || !empty( $box['subtitle'] ) || !empty( $box['content'] ) || $linktext ) : ?>
						<div class="content-block-content<?php
							if ( $visualtype == 'image' ) echo ' content-block-content-hasimage';
							elseif ( $visualtype == 'icon' ) echo ' content-block-content-hasicon';
							else echo ' no-visual';
							?>">
							<?php
							if ( !empty( $box['title'] ) )
								echo '<h4 class="content-block-title">' . $linktag . esc_html( $box['title'] ) . $linktagend . '</h4>';
							if ( !empty( $box['subtitle'] ) )
								echo '<div class="content-block-subtitle small hoot-subtitle">' . do_shortcode( wp_kses_post( $box['subtitle'] ) ) . '</div>';
							if ( !empty( $box['content'] ) )
								echo '<div class="content-block-text">' . do_shortcode( wp_kses_post( wpautop( $box['content'] ) ) ) . '</div>';
							if ( $style == 'style5' || $style == 'style6' )
								echo $linktext;
							?>
						</div>
						<?php endif; ?>

					</div>
					<?php
					if ( $style != 'style5' && $style != 'style6' )
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

			if ( !$clearfix ) echo '</div>';
			?>
		</div>

		<?php
		// Template modification Hook
		do_action( 'hootkit_content_blocks_end', 'custom', ( ( !isset( $instance ) ) ? array() : $instance ) );
		?>

	</div>
</div>