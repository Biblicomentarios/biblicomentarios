<!DOCTYPE html>
<html <?php language_attributes( 'html' ); ?>>

<head>
<?php
// Fire the wp_head action required for hooking in scripts, styles, and other <head> tags.
wp_head();
?>
</head>

<body <?php hoot_attr( 'body' ); ?>>

	<?php wp_body_open(); ?>

	<a href="#main" class="screen-reader-text"><?php esc_html_e( 'Skip to content', 'magazine-news-byte' ); ?></a>

	<?php
	// Display Topbar
	get_template_part( 'template-parts/topbar' );
	?>

	<div <?php hoot_attr( 'page-wrapper' ); ?>>

		<?php
		// Template modification Hook
		do_action( 'magnb_site_start' );
		?>

		<header <?php hoot_attr( 'header' ); ?>>

			<?php
			// Display Secondary Menu
			magnb_secondary_menu( 'top' );
			?>

			<div <?php hoot_attr( 'header-part', 'primary' ); ?>>
				<div class="hgrid">
					<div class="table hgrid-span-12">
						<?php
						// Display Branding
						magnb_branding();

						// Display Menu
						magnb_header_aside();
						?>
					</div>
				</div>
			</div>

			<?php
			// Display Secondary Menu
			magnb_secondary_menu( 'bottom' );
			?>

		</header><!-- #header -->

		<?php hoot_get_sidebar( 'below-header' ); // Loads the template-parts/sidebar-below-header.php template. ?>

		<div <?php hoot_attr( 'main' ); ?>>
			<?php
			// Template modification Hook
			do_action( 'magnb_main_wrapper_start' );