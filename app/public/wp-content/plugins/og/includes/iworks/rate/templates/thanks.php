<?php
/**
 * Notice displayed in admin panel.
 */
?>
<div class="notice notice-success is-dismissible notice-iworks-rate"
	data-slug="<?php echo esc_attr( $args['slug'] ); ?>"
	data-id="<?php echo esc_attr( $args['plugin_id'] ); ?>"
	data-ajax-url="<?php echo esc_url( $args['ajax_url'] ); ?>"
>
	<div class="<?php echo esc_attr( implode( ' ', $args['classes'] ) ); ?>">
		<h4>
		<?php
		if ( ! empty( $args['logo'] ) ) {
			printf( '<span class="iworks-rate-logo" style="background-image:url(%s)"></span>', esc_url( $args['logo'] ) ); }
		?>
		<span><?php printf( esc_html( __( 'Thank you for using our plugin %s!', 'og' ) ), sprintf( '<strong>%s</strong>', $args['title'] ) ); ?></span></h4>
<?php
/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
$content = __( 'Please let us know what you think about our plugin. It is important that we can develop this tool. Thank you for all the ratings, reviews and donates. If you have a technical problem, please before you add a review %1$scheck our FAQ%2$s or contact us if you did not find help there. We will try to help you!', 'og' );
echo wpautop( wp_kses_post( sprintf( $content, sprintf( '<a href="%s#faq" target="_blank">', $args['url'] ), '</a>' ) ) );
?>
		<div class="iworks-rate-buttons">
			<a data-action="get-help" href="<?php echo $args['support_url']; ?>/#new-post" target="_blank" class="iworks-rate-button iworks-rate-button--green" ><?php echo esc_html( __( 'Get help', 'og' ) ); ?></a>
<?php if ( intval( $args['rated'] ) === 0 ) { ?>
			<a data-action="add-review" href="<?php echo $args['support_url']; ?>reviews/?rate=5#new-post" target="_blank" class="iworks-rate-button iworks-rate-button--green" ><?php echo esc_html( __( 'Add review', 'og' ) ); ?></a>
<?php } ?>
			<a data-action="donate" href="https://ko-fi.com/iworks/?utm_source=<?php echo $args['slug']; ?>&utm_medium=notice-thanks" target="_blank" class="iworks-rate-button iworks-rate-button--green dashicons-heart" ><?php echo esc_html( __( 'Provide us a coffee', 'og' ) ); ?></a>
<?php if ( intval( $args['rated'] ) === 0 ) { ?>
			<button type="button" data-action="hide" class="iworks-rate-button iworks-rate-button--blue" ><?php echo esc_html( __( 'I added review, do not show again', 'og' ) ); ?></button>
<?php } ?>
		</div>
	</div>
</div>
