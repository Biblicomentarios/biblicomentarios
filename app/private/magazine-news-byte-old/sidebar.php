<?php

// Dispay Sidebar if not a one-column layout
$sidebar_size = magnb_layout( 'sidebar' );
if ( !empty( $sidebar_size ) ) :
?>

	<aside <?php hoot_attr( 'sidebar', 'primary' ); ?>>
		<div <?php hoot_attr( 'sidebar-wrap', 'primary' ); ?>>

			<?php

			// Template modification Hook
			do_action( 'magnb_sidebar_start', 'primary' );

			if ( is_active_sidebar( 'hoot-primary-sidebar' ) ) : // If the sidebar has widgets.

				dynamic_sidebar( 'hoot-primary-sidebar' ); // Displays the primary sidebar.

			elseif ( current_user_can( 'edit_theme_options' ) && hoot_widget_exists( 'WP_Widget_Text' ) ): // plugins like AMP can replace Text widget in widget factory

				the_widget(
					'WP_Widget_Text',
					array(
						'title'  => __( 'Example Widget', 'magazine-news-byte' ),
						/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
						'text'   => sprintf( __( 'You can add widgets to this area from the %1$swidgets screen%2$s in wp-admin.<br /><br />(This example widget is only displayed to logged in admins when no widget has yet been added to this area.)<br /><strong>Your visitors will not see this example text.<strong>', 'magazine-news-byte' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">', '</a>' ),
						'filter' => true,
					),
					array(
						'before_widget' => '<section class="widget widget_text">',
						'after_widget'  => '</section>',
						'before_title'  => '<h3 class="widget-title"><span>',
						'after_title'   => '</span></h3>'
					)
				);

			endif; // End widgets check.

			// Template modification Hook
			do_action( 'magnb_sidebar_end', 'primary' );

			?>

		</div><!-- .sidebar-wrap -->
	</aside><!-- #sidebar-primary -->

	<?php
	// Display second sidebar if its a 2 column layout
	$currentlayout = hoot_data( 'currentlayout', 'layout' );
	if ( $currentlayout == 'narrow-left-left' || $currentlayout == 'narrow-left-right' || $currentlayout == 'narrow-right-right' ) :
	?>

		<aside <?php hoot_attr( 'sidebar', 'secondary' ); ?>>
			<div <?php hoot_attr( 'sidebar-wrap', 'secondary' ); ?>>

				<?php

				// Template modification Hook
				do_action( 'magnb_sidebar_start', 'secondary' );

				if ( is_active_sidebar( 'hoot-secondary-sidebar' ) ) : // If the sidebar has widgets.

					dynamic_sidebar( 'hoot-secondary-sidebar' ); // Displays the secondary sidebar.

				elseif ( current_user_can( 'edit_theme_options' ) && hoot_widget_exists( 'WP_Widget_Text' ) ): // plugins like AMP can replace Text widget in widget factory

					the_widget(
						'WP_Widget_Text',
						array(
							'title'  => __( 'Example Widget', 'magazine-news-byte' ),
							/* Translators: The %s are placeholders for HTML, so the order can't be changed. */
							'text'   => sprintf( __( 'You can add widgets to this area from the %1$swidgets screen%2$s in wp-admin.<br /><br />(This example widget is only displayed to logged in admins when no widget has yet been added to this area.)<br /><strong>Your visitors will not see this example text.<strong>', 'magazine-news-byte' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">', '</a>' ),
							'filter' => true,
						),
						array(
							'before_widget' => '<section class="widget widget_text">',
							'after_widget'  => '</section>',
							'before_title'  => '<h3 class="widget-title"><span>',
							'after_title'   => '</span></h3>'
						)
					);

				endif; // End widgets check.

				// Template modification Hook
				do_action( 'magnb_sidebar_end', 'secondary' );

				?>

			</div><!-- .sidebar-wrap -->
		</aside><!-- #sidebar-secondary -->

	<?php
	endif;
	?>

<?php
endif; // End layout check.