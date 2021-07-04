<?php
// Return if no boxes to show
if ( empty( $boxes ) || !is_array( $boxes ) )
	return;

// Get total columns and set column counter
$columns = ( intval( $columns ) >= 1 && intval( $columns ) <= 5 ) ? intval( $columns ) : 4;
$firstgridcount = ( !empty( $firstgrid['count'] ) ) ? intval( $firstgrid['count'] ) : 1;

// Edge case
if ( $columns == 1 )
	$firstgrid['standard'] = 1;

// Create array for first grid unit
$firstgrid_boxes = array();
for ( $index = 0; $index < $firstgridcount;  $index++ ) { 
	if ( !empty( $boxes ) ) $firstgrid_boxes[] = array_shift( $boxes );
}

// Set vars
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';

// Display Grid Function
if ( !function_exists( 'hootkit_content_grid_displayunit' ) ):
function hootkit_content_grid_displayunit( $box, $gridcount, $factor, $columns, $gridunit_height = 200, $firstgrid = array() ){

	/* Vars */
	extract( $box, EXTR_OVERWRITE );
	$content_title = $title;
	$content_subtitle = $subtitle;
	$height = true;
	$coverimg_attr = array( 'style' => '' );
	// $caption_align_dist = ( $factor == 2 ) ? $firstgrid['caption_align_dist'] : apply_filters( 'hootkit_gridwidget_caption_align_dist', '' ); // set to 0 for edges
	// $caption_align =  ( $factor == 2 ) ? $firstgrid['caption_align'] : apply_filters( 'hootkit_gridwidget_caption_align', 'bottom-center' );
	$caption_align_dist = ( !empty( $caption_align_dist ) ) ? $caption_align_dist : apply_filters( 'hootkit_gridwidget_caption_align_dist', '' ); // set to 0 for edges
	$caption_align =  ( !empty( $caption_align ) ) ? $caption_align : apply_filters( 'hootkit_gridwidget_caption_align', 'bottom-center' );

	/* Image */
	$image = intval( $image );
	// $img_size = hootkit_thumbnail_size( "column-{$factor}-{$columns}" );
	$img_size = 'hoot-large-thumb'; // hoot-preview-large -> blurry image when eg. 1035x425
	$img_size = apply_filters( 'hootkit_gridwidget_imgsize', $img_size, 'content-grid', $gridcount, $factor, $columns );
	$default_img_size = apply_filters( 'hoot_notheme_gridwidget_imgsize', ( ( $factor == 2 ) ? 'full' : 'thumbnail' ), 'content-grid', $gridcount, $factor, $columns );
	$thumbnail_size = hootkit_thumbnail_size( $img_size, NULL, $default_img_size );
	$img_src = ( $image ) ? wp_get_attachment_image_src( $image, $thumbnail_size ) : array();
	$thumbnail_url = ( !empty( $img_src[0] ) ) ? $img_src[0] : '';

		if ( $thumbnail_url ) $coverimg_attr['style'] .= "background-image:url(" . esc_url($thumbnail_url) . ");";
		if ( $gridunit_height ) $coverimg_attr['style'] .= 'height:' . esc_attr( $gridunit_height * $factor ) . 'px;';
		$coverimg_attr['class'] = 'coverimage-wrap hk-gridunit-image';

		?><div <?php echo hoot_get_attr( 'coverimage-wrap', 'content-grid', $coverimg_attr ) ?>><?php

			if ( !empty( $url ) ) echo '<a href="' . esc_url( $url ) . '" ' . hoot_get_attr( 'content-grid-link', ( ( !isset( $instance ) ) ? array() : $instance ), 'hk-gridunit-imglink' ) . '></a>';
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
								echo '<a href="' . esc_url( ${"buttonurl{$b}"} ) .'" ' . hoot_get_attr( 'content-grid-button', $box, $buttonattr ) . '>';
									echo esc_html( ${"button{$b}"} );
								echo '</a>';
							} }
						echo '</div>';
					}
				echo '</div></div>';
			}

		?></div><?php

}
endif;



/*** START TEMPLATE ***/

// Template modification Hook
do_action( 'hootkit_gridwidget_wrap', 'content-grid', ( ( !isset( $instance ) ) ? array() : $instance ) );
?>

<div class="hk-grid-widget content-grid-widget">

	<?php
	/* Display Title */
	$titlemarkup = $titleclass = '';
	if ( !empty( $title ) ) {
		$titlemarkup .= $before_title . $title . $after_title;
		$titleclass .= ' hastitle';
	}
	$titlemarkup = ( !empty( $titlemarkup ) ) ? '<div class="widget-title-wrap' . $titleclass . '">' . $titlemarkup . '</div>' : '';
	$titlemarkup .= ( !empty( $subtitle ) ) ? '<div class="widget-subtitle hoot-subtitle">' . $subtitle . '</div>' : '';
	echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'content-grid', $title, $before_title, $after_title, $subtitle ) ) );

	// Template modification Hook
	do_action( 'hootkit_gridwidget_start', 'content-grid', ( ( !isset( $instance ) ) ? array() : $instance ) );
	?>

	<div class="hk-grid-columns">
		<?php
		$gridcount = 1;

		/* First Grid Unit */
		$factor = ( $columns == 1 || !empty( $firstgrid['standard'] ) ) ? '1' : '2';
		$gridunit_attr = array();
		$gridunit_attr['class'] = "hk-gridunit hcolumn-{$factor}-{$columns} hk-gridunit-size{$factor}";
		$gridunit_attr['data-unitsize'] = $factor;
		$gridunit_attr['data-columns'] = $columns;
		$gridunit_height = ( empty( $unitheight ) ) ? 0 : ( intval( $unitheight ) );
		$gridunit_style = ( $gridunit_height && $factor == 2 ) ? 'style="height:' . esc_attr( $gridunit_height ) . 'px;"' : '';
		$gridslider = ( $firstgridcount > 1 );
		?>

		<div <?php echo hoot_get_attr( 'hk-gridunit', 'content-grid', $gridunit_attr ) ?> <?php echo $gridunit_style; ?>>
			<?php
			if ( $gridslider ) echo '<div ' . hoot_get_attr( 'hk-gridslider', 'content-grid', 'lightSlider' ) . '>';
			foreach ( $firstgrid_boxes as $box ) :
				if ( $gridslider ) echo '<div class="hk-grid-slide">';;
				hootkit_content_grid_displayunit( $box, $gridcount, $factor, $columns, $gridunit_height, $firstgrid );
				if ( $gridslider ) echo '</div>';
			endforeach;
			if ( $gridslider ) echo '</div>';
			?>
		</div>

		<?php
		$gridcount++;

		/* Remaining Grid Units */
		if ( !empty( $boxes ) ): // Custom query was still created if posts_per_page = 0
		$factor = '1';
		$gridunit_attr['class'] = "hk-gridunit hcolumn-{$factor}-{$columns} hk-gridunit-size{$factor}";
		$gridunit_attr['data-unitsize'] = $factor;
		$gridunit_style = '';
		foreach ( $boxes as $box ) : ?>

		<div <?php echo hoot_get_attr( 'hk-gridunit', 'content-grid', $gridunit_attr ) ?> <?php echo $gridunit_style; ?>>
			<?php
				hootkit_content_grid_displayunit( $box, $gridcount, $factor, $columns, $gridunit_height, array() );
			?>
		</div>

		<?php
		$gridcount++;
		endforeach;
		endif;

		echo '<div class="clearfix"></div>';
		?>
	</div>

	<?php
	// Template modification Hook
	do_action( 'hootkit_gridwidget_end', 'content-grid', ( ( !isset( $instance ) ) ? array() : $instance ) );
	?>

</div>