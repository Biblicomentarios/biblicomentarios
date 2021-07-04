<?php

// Template modification Hook
do_action( 'magnb_before_menu', 'primary' );

if ( has_nav_menu( 'hoot-primary-menu' ) ) : // Check if there's a menu assigned to the 'primary' location.

	?>
	<div class="screen-reader-text"><?php esc_html_e( 'Primary Navigation Menu', 'magazine-news-byte' ); ?></div>
	<nav <?php hoot_attr( 'menu', 'primary' ); ?>>
		<a class="menu-toggle" href="#"><span class="menu-toggle-text"><?php esc_html_e( 'Menu', 'magazine-news-byte' ); ?></span><i class="fas fa-bars"></i></a>

		<?php
		/* Create Menu Args Array */
		$menu_args = array(
			'theme_location'  => 'hoot-primary-menu',
			'container'       => false,
			'menu_id'         => 'menu-primary-items',
			'menu_class'      => 'menu-items sf-menu menu',
			'fallback_cb'     => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			);

		/* Display Main Menu */
		wp_nav_menu( $menu_args ); ?>

	</nav><!-- #menu-primary -->
	<?php

endif; // End check for menu.

// Template modification Hook
do_action( 'magnb_after_menu', 'primary' );