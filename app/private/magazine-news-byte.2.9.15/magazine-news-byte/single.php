<?php 
// Loads the header.php template.
get_header();
?>

<?php
// Dispay Loop Meta at top
magnb_add_custom_title_content( 'pre', 'single.php' );
if ( magnb_titlearea_top() ) {
	magnb_loopmeta_header_img( 'post', false );
	get_template_part( 'template-parts/loop-meta' ); // Loads the template-parts/loop-meta.php template to display Title Area with Meta Info (of the loop)
	magnb_add_custom_title_content( 'post', 'single.php' );
} else {
	magnb_loopmeta_header_img( 'post', true );
}

// Template modification Hook
do_action( 'magnb_before_content_grid', 'single.php' );
?>

<div class="hgrid main-content-grid">

	<main <?php hoot_attr( 'content' ); ?>>
		<div <?php hoot_attr( 'content-wrap', 'single' ); ?>>

			<?php
			// Template modification Hook
			do_action( 'magnb_main_start', 'single.php' );

			// Checks if any posts were found.
			if ( have_posts() ) :

				// Display Featured Image if present
				if ( hoot_get_mod( 'post_featured_image' ) == 'content' ) {
					$img_size = apply_filters( 'magnb_post_imgsize', '', 'content' );
					hoot_post_thumbnail( 'entry-content-featured-img', $img_size, true );
				}

				// Dispay Loop Meta in content wrap
				if ( ! magnb_titlearea_top() ) {
					magnb_add_custom_title_content( 'post', 'single.php' );
					get_template_part( 'template-parts/loop-meta' ); // Loads the template-parts/loop-meta.php template to display Title Area with Meta Info (of the loop)
				}

				// Template modification Hook
				do_action( 'magnb_loop_start', 'single.php' );

				// Begins the loop through found posts, and load the post data.
				while ( have_posts() ) : the_post();

					// Loads the template-parts/content-{$post_type}.php template.
					hoot_get_content_template();

				// End found posts loop.
				endwhile;

				// Template modification Hook
				do_action( 'magnb_loop_end', 'single.php' );

				// Loads the template-parts/loop-nav.php template.
				get_template_part( 'template-parts/loop-nav' );

				// Template modification Hook
				do_action( 'magnb_after_content_wrap', 'single.php' );

				// Loads the comments.php template
				if ( !is_attachment() ) {
					comments_template( '', true );
				};

			// If no posts were found.
			else :

				// Loads the template-parts/error.php template.
				get_template_part( 'template-parts/error' );

			// End check for posts.
			endif;

			// Template modification Hook
			do_action( 'magnb_main_end', 'single.php' );
			?>

		</div><!-- #content-wrap -->
	</main><!-- #content -->

	<?php hoot_get_sidebar(); // Loads the sidebar.php template. ?>

</div><!-- .main-content-grid -->

<?php get_footer(); // Loads the footer.php template. ?>