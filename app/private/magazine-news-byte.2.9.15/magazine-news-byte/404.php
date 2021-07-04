<?php 
// Loads the header.php template.
get_header();
?>

<?php
// Template modification Hook
do_action( 'magnb_before_content_grid', '404.php' );
?>

<div class="hgrid main-content-grid">

	<main <?php hoot_attr( 'content' ); ?>>
		<div <?php hoot_attr( 'content-wrap', '404' ); ?>>

			<?php
			// Template modification Hook
			do_action( 'magnb_main_start', '404.php' );

			// Loads the template-parts/error.php template.
			get_template_part( 'template-parts/error' );

			// Template modification Hook
			do_action( 'magnb_main_end', '404.php' );
			?>

		</div><!-- #content-wrap -->
	</main><!-- #content -->

	<?php hoot_get_sidebar(); // Loads the sidebar.php template. ?>

</div><!-- .main-content-grid -->

<?php get_footer(); // Loads the footer.php template. ?>