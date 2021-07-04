<?php
/**
 * Template to display single post content on archive pages
 * Archive Post Style: Block3
 */
?>

<article <?php hoot_attr( 'post', '', 'archive-block archive-block3 hcolumn-1-3' ); ?>>

	<div class="entry-grid hgrid">

		<?php if ( is_sticky() ) : ?>
			<div class="entry-sticky-tag invert-typo"><?php esc_html_e( 'Sticky', 'magazine-news-byte' ) ?></div>
		<?php endif; ?>

		<?php $img_size = apply_filters( 'magnb_archive_imgsize', 'hoot-medium-thumb', 'block3' );
		hoot_post_thumbnail( 'entry-content-featured-img entry-grid-featured-img', $img_size, true, esc_url( get_permalink() ) ); ?>

		<div class="entry-grid-content">

			<header class="entry-header">
				<?php the_title( '<h2 ' . hoot_get_attr( 'entry-title' ) . '><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" itemprop="url">', '</a></h2>' ); ?>
			</header><!-- .entry-header -->

			<div class="screen-reader-text" itemprop="datePublished" itemtype="https://schema.org/Date"><?php echo get_the_date('Y-m-d'); ?></div>
			<?php hoot_display_meta_info( hoot_get_mod('archive_post_meta'), 'archive-block3' ); ?>

			<?php
			$archive_post_content = hoot_get_mod('archive_post_content');
			if ( 'full-content' == $archive_post_content ) {
				?><div <?php hoot_attr( 'entry-summary', 'content' ); ?>><?php
					the_content();
				?></div><?php
				wp_link_pages();
			} elseif ( 'excerpt' == $archive_post_content ) {
				?><div <?php hoot_attr( 'entry-summary', 'excerpt' ); ?>><?php
					the_excerpt();
				?></div><?php
			}
			?>

		</div><!-- .entry-grid-content -->

	</div><!-- .entry-grid -->

</article><!-- .entry -->