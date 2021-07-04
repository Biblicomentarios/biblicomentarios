<?php
// Get option values
foreach ( array(
		'background',
		'url',
		'url_target',
		'url_scope',
		'content_stretch',
		'content_nopad',
		'content_bg',
		'content_title',
		'content',
	) as $id ) {
	$$id = hoot_get_mod( 'hktb_' . $id );
}
$url         = esc_url( $url );
$background  = esc_url( $background );
$inlinestyle = $styleclass = $backgroundurl = $contenturl = '';

// Customizer live preview compatibility
if ( is_customize_preview() ) {
	if ( empty( $background ) && empty( $content_title ) && empty( $content ) ) {
		$background = 'none'; // So the template does not return
	}
	if ( empty( $url ) ) {
		$url = '#';
		$styleclass .= ' hide-contenturl';
	}
}

// Return if no content to show
if ( empty( $background ) && empty( $content_title ) && empty( $content ) )
	return;

// Set styles and classes
$styleclass  .= ( $content_stretch == 'stretch' ) ? ' topbanner-content-stretch' : ' topbanner-content-grid';
$styleclass  .= ( $content_stretch == 'stretch' && $content_nopad ) ? ' topbar-content-nopad' : '';
$styleclass  .= ( !empty( $background ) && $background != 'none' ) ? ' topbanner-hasbg' : ' topbanner-nobg';
$inlinestyle .= ( !empty( $background ) && $background != 'none' ) ? 'background-image:url(' . $background . ');' : '';
if ( !empty( $url ) ) {
	$urltarget = ( $url_target ) ? '_blank' : '_self';
	if ( $url_scope == 'background' )
		$backgroundurl = '<a href="' . $url . '" ' . hoot_get_attr( 'topbanner-url', 'background', array(
				'classes' => 'topbanner-background-url',
				'target' => $urltarget,
			) ) . '></a>';
	else
		$contenturl    = '<a href="' . $url . '" ' . hoot_get_attr( 'topbanner-url', 'content', array(
				'classes' => 'topbanner-content-url',
				'target' => $urltarget,
			) ) . '></a>';
}
?>

<div id="topbanner" class="topbanner-wrap <?php echo $styleclass; ?>" <?php if ( $inlinestyle ) echo 'style="' . $inlinestyle . '"';?>>
	<?php echo $backgroundurl; ?>
	<?php if ( !empty( $content_title ) || !empty( $content ) ): ?>
		<div class="topbanner-contentbox <?php echo 'style-' . $content_bg ?>">
			<?php echo $contenturl; ?>
			<?php if ( !empty( $content_title ) ): ?>
				<h5 class="topbanner-content-title"><?php echo do_shortcode( wp_kses_post( $content_title ) ); ?></h5>
			<?php endif; ?>
			<?php if ( !empty( $content ) ): ?>
				<div class="topbanner-content"><?php
					// $content = str_replace( array( "\n", "\t" ), '<br />', $content ); // We do not include "\r"
					echo do_shortcode( wp_kses_post( wpautop( $content ) ) );
					?></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>