<?php
// Template modification Hook
do_action( 'magnb_before_footer' );

// Get Footer Columns
$columns = magnb_get_footer_columns();
$alphas = range('a', 'e');
$structure = magnb_footer_structure();
$footercols = array();
$footerdisplay = false;
for ( $i=0; $i < $columns; $i++ ) {
	$footercols[ 'hoot-footer-' . $alphas[ $i ] ] = $structure[ $i ];
	if ( is_active_sidebar( 'hoot-footer-' . $alphas[ $i ] ) )
		$footerdisplay = true;
}
$inline_nav = ( $columns == 1 ) ? 'inline-nav' : 'inline-nav';

// Display footer columns for transport=>postMessage
if ( is_customize_preview() ) :
?>

<footer <?php hoot_attr( 'footer', '', "footer hgrid-stretch {$inline_nav}" ); ?>>
	<div class="hgrid">
		<?php foreach ( array( 'hoot-footer-a', 'hoot-footer-b', 'hoot-footer-c', 'hoot-footer-d' ) as $check ) { ?>
			<?php $span = ( isset( $footercols[$check] ) ) ? $footercols[$check] : '12';
				  $nowidget = ( isset( $footercols[$check] ) ) ? '' : ' nowidget'; ?>
			<div class="<?php echo sanitize_html_class( 'hgrid-span-' . $span ) . $nowidget; ?> footer-column">
				<?php dynamic_sidebar( $check ); ?>
			</div>
		<?php } ?>
	</div>
</footer><!-- #footer -->

<?php
// Dont display if nothing to show
elseif ( $footerdisplay ) :
?>

<footer <?php hoot_attr( 'footer', '', "footer hgrid-stretch {$inline_nav}" ); ?>>
	<div class="hgrid">
		<?php foreach ( $footercols as $key => $span ) { ?>
			<div class="<?php echo sanitize_html_class( 'hgrid-span-' . $span ); ?> footer-column">
				<?php dynamic_sidebar( $key ); ?>
			</div>
		<?php } ?>
	</div>
</footer><!-- #footer -->

<?php
endif;

// Template modification Hook
do_action( 'magnb_after_footer' );