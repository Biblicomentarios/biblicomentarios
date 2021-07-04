<?php
$site_info = hoot_get_mod( 'site_info' );
if ( !empty( $site_info ) ) :
?>
	<div <?php hoot_attr( 'post-footer', '', 'hgrid-stretch linkstyle' ); ?>>
		<div class="hgrid">
			<div class="hgrid-span-12">
				<p class="credit small">
					<?php
					if ( htmlspecialchars_decode( trim( $site_info ) ) == '<!--default-->' ) { // decode for default theme set value
						printf(
							/* Translators: 1 is Privacy Policy link 2 is Theme name/link, 3 is WordPress name/link, 4 is site name/link */
							__( '%1$s Designed using %2$s. Powered by %3$s.', 'magazine-news-byte' ),
							( function_exists( 'get_the_privacy_policy_link' ) ) ? wp_kses_post( get_the_privacy_policy_link() ) : '',
							hoot_get_theme_link(),
							hoot_get_wp_link(),
							hoot_get_site_link()
						);
					} else {
						$site_info = str_replace( "<!--year-->" , date_i18n( 'Y' ) , $site_info );
						echo wp_kses_post( $site_info );
					} ?>
				</p><!-- .credit -->
			</div>
		</div>
	</div>
<?php
endif;
?>