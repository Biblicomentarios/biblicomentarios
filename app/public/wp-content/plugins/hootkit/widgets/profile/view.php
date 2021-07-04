<?php

// Get border classes
$top_class = hootkit_widget_borderclass( $border, 0, 'topborder-');
$bottom_class = hootkit_widget_borderclass( $border, 1, 'bottomborder-');

// Link Text
$button_text = ( !empty( $button_text ) ) ? $button_text : ( ( function_exists( 'hoot_get_mod' ) ) ? hoot_get_mod('read_more') : __( 'Know More', 'hootkit' ) );
$button_text = ( empty( $button_text ) ) ? sprintf( __( 'Read More %s', 'hootkit' ), '&rarr;' ) : $button_text;

// Set vars
$subtitle = ( !empty( $subtitle ) ) ? $subtitle : '';
?>

<div class="profile-widget-wrap <?php echo hoot_sanitize_html_classes( "{$top_class} {$bottom_class}" ); ?>">
	<div class="profile-widget">

		<?php
		/* Display Title */
		$titlemarkup = $titleclass = '';
		if ( !empty( $title ) ) {
			$titlemarkup .= $before_title . $title . $after_title;
			$titleclass .= ' hastitle';
		}
		$titlemarkup = ( !empty( $titlemarkup ) ) ? '<div class="widget-title-wrap' . $titleclass . '">' . $titlemarkup . '</div>' : '';
		$titlemarkup .= ( !empty( $subtitle ) ) ? '<div class="widget-subtitle hoot-subtitle">' . $subtitle . '</div>' : '';
		echo do_shortcode( wp_kses_post( apply_filters( 'hootkit_widget_title', $titlemarkup, 'profile', $title, $before_title, $after_title, $subtitle ) ) );

		/* Display Image */
		$image = intval( $image );
		if ( !empty( $image ) ) :
			$img_style = ( !empty( $img_style ) && in_array( $img_style, array( 'circle', 'square', 'full' ) ) ) ? $img_style : 'circle';
			if ( $img_style == 'circle' )
				$img_size = apply_filters( 'hootkit_profile_imgsize', 'hoot-preview-thumb', $img_style );
			elseif ( $img_style == 'square' )
				$img_size = apply_filters( 'hootkit_profile_imgsize', 'hoot-medium-thumb', $img_style );
			else
				$img_size = apply_filters( 'hootkit_profile_imgsize', 'full', $img_style );
			?>
			<div class="profile-image <?php echo 'profile-img-' . esc_attr( $img_style ); ?>">
				<?php
				if ( $img_style == 'full' ) {
					echo wp_get_attachment_image( $image, $img_size, '', array( 'class' => "profile-img attachment-{$img_size} size-{$img_size}", 'itemprop' => 'image' ) );
				} else {
					$img_src = wp_get_attachment_image_src( $image, $img_size );
					if ( !empty( $img_src[0] ) ) echo '<div class="profile-img-placeholder" style="' . "background-image:url(" . esc_url( $img_src[0] ) . ");" . '"></div>';
				} ?>
			</div>
			<?php
		endif;
		?>

		<?php if ( !empty( $content ) ) { ?>
			<div class="profile-content"><?php echo do_shortcode( wp_kses_post( wpautop( $content ) ) ); ?></div>
		<?php } ?>

		<?php if ( !empty( $url ) ) { ?>
			<?php if ( !empty( $link_type ) && $link_type == 'text' ) { ?>
				<div class="profile-textlink more-link">
					<a href="<?php echo esc_url( $url ); ?>" <?php hoot_attr( 'profile-link', ( ( !isset( $instance ) ) ? array() : $instance ) ); ?>><?php echo esc_html( $button_text ); ?></a>
				</div>
			<?php } else { ?>
				<div class="profile-buttonlink">
					<a href="<?php echo esc_url( $url ); ?>" <?php hoot_attr( 'profile-button', ( ( !isset( $instance ) ) ? array() : $instance ), 'button button-small border-box ' ); ?>><?php echo esc_html( $button_text ); ?></a>
				</div>
			<?php } ?>
		<?php } ?>

		<?php
		/* Display Social Links */
		$has_links = false;
		for ( $i=1; $i <= 5 ; $i++ ) { 
			$urlvar = "url{$i}";
			if ( !empty( $$urlvar ) ) {
				$has_links = true;
				break;
			}
		}
		if ( $has_links ) : ?>
			<div class="profile-links social-icons-widget social-icons-small">
				<?php
				for ( $i=1; $i <= 5 ; $i++ ) :
					$urlvar = "url{$i}";
					$iconvar = "icon{$i}";
					if ( !empty( $$urlvar ) && !empty( $$iconvar ) ) :
						echo '<div class="profile-link">';

							// @NU
							if ( $$iconvar == 'fa-skype' && function_exists( 'hootkit_get_skype_button' ) ) :
								echo '<div class="profile-link-inner profile-link-skype social-icons-icon fa-skype-block">'
									. '<i class="' . hoot_sanitize_fa( $$iconvar ) . '"></i>'
									. hootkit_get_skype_button ( $$urlvar )
									. '</div>';
							else:

								$icon_class = sanitize_html_class( $$iconvar ) . '-block social-icons-icon';
								$url = ( $$iconvar == 'fa-envelope' ) ? 'mailto:' . antispambot( sanitize_email( $$urlvar ) ) : esc_url( $$urlvar );
								?><a href="<?php echo $url ?>" <?php hoot_attr( 'profile-link-inner', $$iconvar, $icon_class ); ?>>
									<i class="<?php echo hoot_sanitize_fa( $$iconvar ); ?>"></i>
								</a><?php

							endif;

						echo '</div>';
					endif;
				endfor;
				?>
			</div>
			<?php
		endif;
		?>

	</div>
</div>