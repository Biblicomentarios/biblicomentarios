<?php
/**
 * Template to display single static page content
 */

/**
 * If viewing a single page (pages can occur in archive lists as well. Example: search results)
 */
if ( is_page() ) :
?>

	<article <?php hoot_attr( 'page' ); ?>>

		<div <?php hoot_attr( 'entry-content' ); ?>>

			<div class="entry-the-content">
				<?php the_content(); ?>
			</div>
			<?php wp_link_pages(); ?>

		</div><!-- .entry-content -->

		<div class="screen-reader-text" itemprop="datePublished" itemtype="https://schema.org/Date"><?php echo get_the_date('Y-m-d'); ?></div>

		<?php
		$hide_meta_info = apply_filters( 'magnb_hide_meta', false, 'bottom' );
		if ( !$hide_meta_info && 'bottom' == hoot_get_mod( 'post_meta_location' ) ):
			$metarray = hoot_get_mod('page_meta');
			if ( hoot_meta_info( $metarray, 'page', true ) ) :
			?>
			<footer class="entry-footer">
				<?php hoot_display_meta_info( $metarray, 'page' ); ?>
			</footer><!-- .entry-footer -->
			<?php
			endif;
		endif;
		?>

	</article><!-- .entry -->

<?php
/**
 * If not viewing a single page i.e. viewing the page in a list index (Example: search results)
 */
else :

	if ( ! apply_filters( 'magnb_searchresults_hide_pages', false ) ) {
		// Loads the template-parts/archive-{$post_type}-{$archive_type}.php template.
		hoot_get_archive_content();
	}

endif;
?>