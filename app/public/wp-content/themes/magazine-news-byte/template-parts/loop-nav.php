<?php
/**
 * If viewing a single post page.
 */
if ( is_singular( 'post' ) ) :

	magnb_post_prev_next_links();

/**
 * If viewing the blog, an archive, or search results.
 */
elseif ( is_home() || is_archive() || is_search() ) :

	?><div class="clearfix"></div><?php

	if ( function_exists('wp_pagenavi' ) ) {
		// Load WP-PageNavi plugin if installed and active
		wp_pagenavi();
	} else {
		$paginate_args = apply_filters( 'magnb_posts_pagination_args', array() );
		the_posts_pagination( $paginate_args );
	}

endif;