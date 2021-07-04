<?php
// Dispay Sidebar if sidebar has widgets
if ( is_active_sidebar( 'hoot-header' ) || current_user_can( 'edit_theme_options' ) ) :

	?>
	<div <?php hoot_attr( 'header-sidebar', '', 'inline-nav js-search hgrid-stretch' ); ?>>
		<?php

		// Template modification Hook
		do_action( 'magnb_sidebar_start', 'header-sidebar' );

		?>
		<aside <?php hoot_attr( 'sidebar', 'header-sidebar' ); ?>>
			<?php
				if ( is_active_sidebar( 'hoot-header' ) ):
					dynamic_sidebar( 'hoot-header' );
				elseif ( current_user_can( 'edit_theme_options' ) && hoot_widget_exists( 'WP_Widget_Text' ) ): // plugins like AMP can replace Text widget in widget factory
					the_widget(
						'WP_Widget_Text',
						array(
							'title'  => __( 'Example Widget', 'magazine-news-byte' ),
							/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
							'text'   => sprintf( __( 'You can add widgets here by adding them to<br />"Header Side" area in the %1$swidgets screen%2$s in wp-admin.<br />(This example widget is only displayed to logged in<br />admins when no widget has yet been added to this area.)<br /><strong>Your visitors will not see this example text.</strong>', 'magazine-news-byte' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">', '</a>' ),
							'filter' => true,
						),
						array(
							'before_widget' => '<section class="widget widget_text">',
							'after_widget'  => '</section>',
							'before_title'  => '<h3 class="widget-title"><span>',
							'after_title'   => '</span></h3>'
						)
					);
				endif;
				?>
		</aside>
		<?php

		// Template modification Hook
		do_action( 'magnb_sidebar_end', 'header-sidebar' );

		?>
	</div>
	<?php

endif;