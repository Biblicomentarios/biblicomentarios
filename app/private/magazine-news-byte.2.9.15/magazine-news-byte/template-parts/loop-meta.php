<?php
/**
 * Template modification Hooks
 */
$display_loop_meta = apply_filters( 'magnb_display_loop_meta', true );
do_action( 'magnb_loop_meta', 'start' );

if ( !$display_loop_meta )
	return;

/**
 * If viewing a multi post page 
 */
if ( !is_front_page() && !is_singular() ) :

	$display_title = apply_filters( 'magnb_loop_meta_display_title', true, 'plural' );
	if ( $display_title !== 'hide' ) :

		// Display Featured Image in header if present (static/parallax)
		$loopmetawrap = hoot_data( 'loop-meta-wrap' );
		$wrap_attr = ( !empty( $loopmetawrap[0] ) && is_array( $loopmetawrap[0] ) ) ? $loopmetawrap[0] : array();
		$image = ( !empty( $loopmetawrap[1] ) ) ? $loopmetawrap[1] : '';
		hoot_unset_data( 'loop-meta-wrap' );
		$wrap_attr['classes'] = empty( $wrap_attr['classes'] ) ? ' loop-meta-withtext' : $wrap_attr['classes'] . ' loop-meta-withtext';
		?>

		<div <?php hoot_attr( 'loop-meta-wrap', 'archive', $wrap_attr ); ?>>
			<?php if ( $image ) echo '<img ' . hoot_get_attr( 'entry-headerimg', '', array( 'src' => esc_url( $image ) ) ) . '>'; ?>
			<div class="hgrid">

				<div <?php hoot_attr( 'loop-meta', 'archive', 'hgrid-span-12' ); ?>>

					<?php if ( is_author() ) : ?>
						<div class="loop-meta-gravatar"><?php
							$author = get_user_by( 'slug', get_query_var( 'author_name' ) );
							$gwidth = apply_filters( 'magnb_loop_meta_gravatar', 0 );
							$gwidth = intval( $gwidth );
							$gwidth = ( !empty( $gwidth ) ) ? $gwidth : 150;
							add_filter( 'get_avatar', 'magnb_ns_filter_avatar', 10, 6 );
							echo get_avatar( $author->ID, $gwidth, '404' );
							remove_filter( 'get_avatar', 'magnb_ns_filter_avatar', 10, 6 );
							?></div>
					<?php endif; ?>

					<h1 <?php hoot_attr( 'loop-title', 'archive' ); ?>><?php echo wp_kses_post( get_the_archive_title() ); // Displays title for archive type (multi post) pages. ?></h1>

					<?php if ( $desc = get_the_archive_description() ) : ?>
						<div <?php hoot_attr( 'loop-description', 'archive' ); ?>>
							<?php echo wp_kses_post( $desc ); // Displays description for archive type (multi post) pages. ?>
						</div><!-- .loop-description -->
					<?php endif; // End paged check. ?>

				</div><!-- .loop-meta -->

			</div>
		</div>

	<?php
	hoot_set_data( 'loop_meta_displayed', true );
	endif;

/**
 * If viewing a single post/page
 */
elseif ( is_singular() ) :

	if ( have_posts() ) :

		// Begins the loop through found posts, and load the post data.
		while ( have_posts() ) : the_post();

			$display_title = apply_filters( 'magnb_loop_meta_display_title', '', 'singular' );
			if ( $display_title !== 'hide' ) :

				// Display Featured Image in header if present (static/parallax)
				$loopmetawrap = hoot_data( 'loop-meta-wrap' );
				$wrap_attr = ( !empty( $loopmetawrap[0] ) && is_array( $loopmetawrap[0] ) ) ? $loopmetawrap[0] : array();
				$image = ( !empty( $loopmetawrap[1] ) ) ? $loopmetawrap[1] : '';
				hoot_unset_data( 'loop-meta-wrap' );
				$wrap_attr['classes'] = empty( $wrap_attr['classes'] ) ? ' loop-meta-withtext' : $wrap_attr['classes'] . ' loop-meta-withtext';
				?>

				<div <?php hoot_attr( 'loop-meta-wrap', 'singular', $wrap_attr ); ?>>
					<?php if ( $image ) echo '<img ' . hoot_get_attr( 'entry-headerimg', '', array( 'src' => esc_url( $image ) ) ) . '>'; ?>
					<div class="hgrid">

						<div <?php hoot_attr( 'loop-meta', '', 'hgrid-span-12' ); ?>>
							<div class="entry-header">

								<?php
								global $post;
								$pretitle = ( !isset( $post->post_parent ) || empty( $post->post_parent ) ) ? '' : '<span class="loop-pretitle">' . get_the_title( $post->post_parent ) . ' &raquo; </span>';
								$pretitle = apply_filters( 'magnb_singular_loop_pretitle', $pretitle );
								?>
								<h1 <?php hoot_attr( 'loop-title' ); ?>><?php the_title( $pretitle ); ?></h1>

								<?php
								$hide_meta_info = apply_filters( 'magnb_hide_meta', false, 'top' );
								if ( !$hide_meta_info && function_exists( 'is_bbpress' ) && is_bbpress() ):
									if ( bbp_is_single_forum() ) {
										?><div <?php hoot_attr( 'loop-description' ); ?>><?php
											bbp_forum_content();
										?></div><!-- .loop-description --><?php
									};
								elseif ( !$hide_meta_info && 'top' == hoot_get_mod( 'post_meta_location' ) && !is_attachment() && !( function_exists( 'is_buddypress' ) && is_buddypress() ) ):
									$metarray = ( is_page() ) ? hoot_get_mod('page_meta') : hoot_get_mod('post_meta');
									if ( hoot_meta_info( $metarray, 'loop-meta', true ) ) :
										?><div <?php hoot_attr( 'loop-description' ); ?>><?php
											hoot_display_meta_info( $metarray, 'loop-meta' );
										?></div><!-- .loop-description --><?php
									endif;
								endif;
								?>

							</div><!-- .entry-header -->
						</div><!-- .loop-meta -->

					</div>
				</div>

			<?php
			hoot_set_data( 'loop_meta_displayed', true );
			endif;

		endwhile;
		rewind_posts();

	endif;

endif;

/**
 * Template modification Hooks
 */
do_action( 'magnb_loop_meta', 'end' );