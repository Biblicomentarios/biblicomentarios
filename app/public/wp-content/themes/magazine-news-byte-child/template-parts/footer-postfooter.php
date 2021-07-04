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
					//	printf(
							/* Translators: 1 is Privacy Policy link 2 is Theme name/link, 3 is WordPress name/link, 4 is site name/link */
                        //	__( '%1$s Designed using %2$s. Powered by %3$s.', 'magazine-news-byte' ),
						//	( function_exists( 'get_the_privacy_policy_link' ) ) ? wp_kses_post( get_the_privacy_policy_link() ) : '',
					//		hoot_get_theme_link(),
					//		hoot_get_wp_link(),
					//		hoot_get_site_link()
					//	);*/
                    ?>
                <div class="mx-3 px-5 py-2" style="background-color:#444444;color:ivory;">
                    Este sitio es un esfuerzo particular de <a class="footerLink" style="color:lightgreen" href="/conoce-a-jpmarichal-el-creador-de-los-biblicomentarios-com/">Juan Pablo Marichal Catalán</a>. Se ha hecho todo esfuerzo posible por mantener
                    los artículos y contenido en armonía con las prácticas y doctrina de <a style="color:lightgreen" href="https://www.churchofjesuschrist.org/?lang=spa" target="_blank">La Iglesia de Jesucristo de los Santos de los
                        Últimos Días</a>. Se prohíbe la reproducción del contenido sin permiso por escrito del autor.
                    <div style="text-align:center;border-top:1px solid lightgreen;">
                        <a href="/category/acerca-de/dinamica/" style="color:lightgreen">Dinámica</a> |
                        <a href="/category/acerca-de/politicas/" style="color:lightgreen">Políticas</a> |
                        <a href="/category/acerca-de/staff/" style="color:lightgreen">Contacto</a>
                    </div>
                </div>
                <?php
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