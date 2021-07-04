<?php
// Set vars
$height = intval( $height );
$boxes = ( isset( $boxes ) ) ? $boxes : array();
$is_slider = ( !empty( $boxes ) && is_array( $boxes ) );
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';

// Set cover images
$firstimage = array(
	'image' => $image,
	'content_title' => $content_title,
	'content_subtitle' => $content_subtitle,
	'content' => $content,
	'url' => $url,
	'caption_bg' => $caption_bg,
	'caption_align' => $caption_align,
	'caption_align_dist' => $caption_align_dist,
	'button1' => $button1,
	'buttonurl1' => $buttonurl1,
	'buttoncolor1' => $buttoncolor1,
	'buttonfont1' => $buttonfont1,
	'button2' => $button2,
	'buttonurl2' => $buttonurl2,
	'buttoncolor2' => $buttoncolor2,
	'buttonfont2' => $buttonfont2,
);
array_unshift( $boxes, $firstimage );

// Display Cover Image Function
if ( !function_exists( 'hootkit_coverimage_displayunit' ) ):
function hootkit_coverimage_displayunit( $box, $height ){

	/* Vars */
	extract( $box, EXTR_OVERWRITE );
	$coverimg_attr = array( 'style' => '' );
	$caption_align_dist = ( !empty( $caption_align_dist ) || $caption_align_dist === 0 ) ? intval( $caption_align_dist ) : '';

	/* Image */
	$image = intval( $image );
	$img_src = array();
	if ( !empty( $image ) ) {
		$img_size = ( !empty( $height ) ) ? apply_filters( 'hootkit_coverimage_imgsize', 'full', $image ) : apply_filters( 'hootkit_coverimage_fullimgsize', 'full', $image, $height );
		$img_src = wp_get_attachment_image_src( $image, $img_size );
	}

	if ( !empty( $img_src[0] ) ) :

		if ( !empty( $height ) ) $coverimg_attr['style'] .= "background-image:url(" . esc_url( $img_src[0] ) . ");";
		if ( !empty( $height ) ) $coverimg_attr['style'] .= "height:{$height}px;";

		?><div <?php echo hoot_get_attr( 'coverimage-wrap', 'coverimage', $coverimg_attr ) ?>><?php

			if ( !empty( $url ) ) echo '<a href="' . esc_url( $url ) . '" ' . hoot_get_attr( 'coverimage-link', ( ( !isset( $instance ) ) ? array() : $instance ) ) . '></a>';
			if ( empty( $height ) ) echo '<div class="coverimage-fullimg"><img src=" ' . esc_url( $img_src[0] ) . '"></div>';

			/* Display Content */
			if ( !empty( $content_title ) || !empty( $content_subtitle ) || !empty( $content ) ) {
				$contentstyle = '';
				if ( ( !empty( $caption_align_dist ) || $caption_align_dist === 0 ) && $caption_align_dist <= 100 ) {
					$contentstyle .= 'style="';
					if ( strpos( $caption_align, 'top' ) !== false ) $contentstyle .= "top:{$caption_align_dist}%;";
					if ( strpos( $caption_align, 'bottom' ) !== false ) $contentstyle .= "bottom:{$caption_align_dist}%;";
					if ( strpos( $caption_align, 'left' ) !== false ) $contentstyle .= "left:{$caption_align_dist}%;";
					if ( strpos( $caption_align, 'right' ) !== false ) $contentstyle .= "right:{$caption_align_dist}%;";
					if ( strpos( $caption_align, 'center' ) !== false ) $contentstyle .= "left:{$caption_align_dist}%;right:{$caption_align_dist}%;";
					if ( strpos( $caption_align, 'middle' ) !== false ) $contentstyle .= "top:{$caption_align_dist}%;bottom:{$caption_align_dist}%;";
					$contentstyle .= '"';
				}
				echo "<div class='coverimage-content align-{$caption_align}' {$contentstyle}><div class='coverimage-content-block style-{$caption_bg}'>";
					if ( !empty( $content_title ) )
						echo '<h4 class="coverimage-title">' . esc_html( $content_title ) . '</h4>';
					if ( !empty( $content_subtitle ) )
						echo '<div class="coverimage-subtitle hoot-subtitle">' . do_shortcode( wp_kses_post( $content_subtitle ) ) . '</div>';
					if ( !empty( $content ) )
						echo '<div class="coverimage-text">' . do_shortcode( wp_kses_post( wpautop( $content ) ) ) . '</div>';

					if ( !empty( $buttonurl1 ) || !empty( $buttonurl2 ) ) {
						echo '<div class="coverimage-buttons">';
							$invertbutton = apply_filters( 'hootkit_coverimage_inverthoverbuttons', false );
							for ( $b=1; $b <=2 ; $b++ ) { if ( !empty( ${"buttonurl{$b}"} ) ) {
								$buttonattr = array();
								if ( !empty( ${"buttoncolor{$b}"} ) || !empty( ${"buttonfont{$b}"} ) ) {
									$buttonattr['style'] = '';
									if ( $invertbutton ) $buttonattr['onMouseOver'] = $buttonattr['onMouseOut'] = '';
									if ( !empty( ${"buttoncolor{$b}"} ) ) {
										$buttonattr['style'] .= 'background:' . sanitize_hex_color( ${"buttoncolor{$b}"} ) . ';';
										$buttonattr['style'] .= 'border-color:' . sanitize_hex_color( ${"buttoncolor{$b}"} ) . ';';
										if ( $invertbutton ) $buttonattr['onMouseOver'] .= "this.style.color='" . sanitize_hex_color( ${"buttoncolor{$b}"} ) . "';";
										if ( $invertbutton ) $buttonattr['onMouseOut'] .= "this.style.background='" . sanitize_hex_color( ${"buttoncolor{$b}"} ) . "';";
									}
									if ( !empty( ${"buttonfont{$b}"} ) ) {
										$buttonattr['style'] .= 'color:' . sanitize_hex_color( ${"buttonfont{$b}"} ) . ';';
										if ( $invertbutton ) $buttonattr['onMouseOver'] .= "this.style.background='" . sanitize_hex_color( ${"buttonfont{$b}"} ) . "';";
										if ( $invertbutton ) $buttonattr['onMouseOut'] .= "this.style.color='" . sanitize_hex_color( ${"buttonfont{$b}"} ) . "';";
									}
								}
								$buttonattr['class'] = 'coverimage-button button button-small';
								$buttonattr['data-button'] = $b;
								echo '<a href="' . esc_url( ${"buttonurl{$b}"} ) .'" ' . hoot_get_attr( 'coverimage-button', $box, $buttonattr ) . '>';
									echo esc_html( ${"button{$b}"} );
								echo '</a>';
							} }
						echo '</div>';
					}
				echo '</div></div>';
			}

		?></div><?php

	endif;

}
endif;
?>

<div class="coverimage-widget">

	<?php
	/* Display Title */
	$titlemarkup = $titleclass = '';
	if ( !empty( $title ) ) {
		$titlemarkup .= $before_title . $title . $after_title;
		$titleclass .= ' hastitle';
	}
	$titlemarkup = ( !empty( $titlemarkup ) ) ? '<div class="widget-title-wrap' . $titleclass . '">' . $titlemarkup . '</div>' : '';
	$titlemarkup .= ( !empty( $subtitle ) ) ? '<div class="widget-subtitle hoot-subtitle">' . $subtitle . '</div>' : '';
	echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'social-icons', $title, $before_title, $after_title, $subtitle ) ) );

	/* Display Image(s) */
	if ( $is_slider ) echo '<div ' . hoot_get_attr( 'coverimage-slider', 'coverimage', 'lightSlider' ) . '>';
	foreach ( $boxes as $box ) :
		hootkit_coverimage_displayunit( $box, $height );
	endforeach;
	if ( $is_slider ) echo '</div>';

	?>

</div>