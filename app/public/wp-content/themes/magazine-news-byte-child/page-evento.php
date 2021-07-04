<?php
/*
Template Name: Evento
*/
?>

<?php
// Loads the header.php template.
get_header();
?>

<?php
// Display Loop Meta at top
magnb_add_custom_title_content( 'pre', 'single.php' );
if ( magnb_titlearea_top() ) {
	magnb_loopmeta_header_img( 'post', false );
	get_template_part( 'template-parts/loop-meta' ); // Loads the template-parts/loop-meta.php template to display Title Area with Meta Info (of the loop)
	magnb_add_custom_title_content( 'post', 'single.php' );
} else {
	magnb_loopmeta_header_img( 'post', true );
}

// Template modification Hook
do_action( 'magnb_before_content_grid', 'single.php' );
?>

<div class="hgrid main-content-grid">

	<!-- <main <?php hoot_attr( 'content' ); ?>> -->
	<main id="content" class="content content-page-evento row">
        <div class="col-9" style="border-right: 1px dotted gray">
		<div <?php hoot_attr( 'content-wrap', 'single' ); ?>>

			<?php
			// Template modification Hook
			do_action( 'magnb_main_start', 'single.php' );

			// Checks if any posts were found.
			if ( have_posts() ) :

				// Display Featured Image if present
				if ( hoot_get_mod( 'post_featured_image' ) == 'content' ) {
					$img_size = apply_filters( 'magnb_post_imgsize', '', 'content' );
					// hoot_post_thumbnail( 'entry-content-featured-img', $img_size, true );
				}

				// Display Loop Meta in content wrap
				if ( ! magnb_titlearea_top() ) {
					magnb_add_custom_title_content( 'post', 'single.php' );
					get_template_part( 'template-parts/loop-meta' ); // Loads the template-parts/loop-meta.php template to display Title Area with Meta Info (of the loop)
				}

				// Template modification Hook
				do_action( 'magnb_loop_start', 'single.php' );

				// Begins the loop through found posts, and load the post data.
                global $wpdb;

				while ( have_posts() ) : the_post();
                    $query = 'select * from eventos where PageId='.get_the_ID();
                    $eventos = $wpdb->get_results($wpdb->prepare($query),OBJECT);
                    $evento= $eventos[0];

                    ?>
<p><?=the_field('introduccion');?></p>
<div class="content border" style=" font-size: .85em">
    <table class="table-striped">
        <?php if( have_rows('pasajes_clave') ): ?>
            <tr>
                <td class="col-2"><b>Pasajes clave</b></td>
                <td class="col-10">
                    <?php while( have_rows('pasajes_clave') ): the_row(); ?>
                        <?php the_sub_field('pasaje_clave'); ?>,
                    <?php endwhile; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php
                   $categorias = get_the_category();
                   $listCategorias = [];
                   foreach ($categorias as $categoria) {
                       if ($categoria->name != 'Evento') {
                           array_push($listCategorias,$categoria->name);
                       }
                   }
                   if (count($listCategorias)>0) {
                    ?>
        <tr>
            <td class="col-2"><b>Categorías</b></td>
            <td class="col-10">
                <?php
                    echo join(',',$listCategorias);
                ?>
            </td>
        </tr>
        <?php
                   }
        ?>
        <?php if( have_rows('menciones') ): ?>
            <tr>
                <td class="col-2"><b>Correlaciones</b></td>
                <td class="col-10">
                    <?php while( have_rows('menciones') ): the_row(); ?>
                        <?php the_sub_field('mencion'); ?>,
                    <?php endwhile; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php if( have_rows('lugares') ): ?>
            <tr>
                <td class="col-2"><b>Lugares</b></td>
                <td class="col-10">
                    <?php while( have_rows('lugares') ): the_row(); ?>
                        <?php the_sub_field('lugar'); ?>,
                    <?php endwhile; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php if( have_rows('personajes') ): ?>
            <tr>
                <td class="col-2"><b>Participantes</b></td>
                <td class="col-10">
                <?php while( have_rows('personajes') ): the_row(); ?>
                    <?php the_sub_field('personaje'); ?>,
                <?php endwhile; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php if( have_rows('objetos') ): ?>
            <tr>
                <td class="col-2"><b>Objetos</b></td>
                <td class="col-10">
                    <?php while( have_rows('objetos') ): the_row(); ?>
                        <?php the_sub_field('objeto'); ?>,
                    <?php endwhile; ?>
                </td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<?php if( get_field('resumen') ): ?>
    <h2>Resumen</h2>
    <?=the_field('resumen');?>
<?php endif; ?>

<?php if( get_field('puntos_a_destacar') ): ?>
    <h2>Puntos a destacar</h2>
    <?=the_field('puntos_a_destacar');?>
<?php endif; ?>

<?php if( get_field('observaciones') ): ?>
    <h2>Observaciones y consideraciones</h2>
    <?=the_field('observaciones');?>
<?php endif; ?>

<?php if( have_rows('imagenes') ): ?>
    <?php while( have_rows('imagenes') ): the_row(); ?>
        <h2><?= the_sub_field('titulo_imagen');?></h2>
        <div class="border p-2 px-3 mb-1">
            <?php $image = wp_get_attachment_image_src(get_sub_field('imagen'), 'full'); ?>
            <div style="text-align: center;">
                <img src="<?php echo $image[0]; ?>" alt="<?php echo get_the_title() ?>" />
            </div>
            <div class="px-4 my-2" style="font-size: small"><?= the_sub_field('descripcion_imagen');?></div>
         </div>
    <?php endwhile; ?>
<?php endif; ?>


                
                    <?php

					// Loads the template-parts/content-{$post_type}.php template.
					hoot_get_content_template();

				// End found posts loop.
				endwhile;

				// Template modification Hook
				do_action( 'magnb_loop_end', 'single.php' );

				// Loads the template-parts/loop-nav.php template.
				get_template_part( 'template-parts/loop-nav' );

				// Template modification Hook
				do_action( 'magnb_after_content_wrap', 'single.php' );

				// Loads the comments.php template
				if ( !is_attachment() ) {
					comments_template( '', true );
				};

			// If no posts were found.
			else :

				// Loads the template-parts/error.php template.
				get_template_part( 'template-parts/error' );

			// End check for posts.
			endif;

			// Template modification Hook
			do_action( 'magnb_main_end', 'single.php' );
			?>

		</div><!-- #content-wrap -->
        </div>
        <div class="col-3">

            <?php
                global $wpdb;

                $the_query = new WP_Query(
                    array(
                        'post_parent' => get_the_ID(),
                        'post_type'=> 'page',
                        'posts_per_page' => -1,
                        'orderby'=> 'menu_order',
                        'order' => 'ASC'
                    )
                );

                $query ="select * from wp_posts 
                        where Id=".get_the_ID();
                $results_current_page = $wpdb->get_results($query,OBJECT);

                foreach ($results_current_page as $current_page){
                    $parent_id = $current_page->post_parent;
                    $current_name = $current_page->post_title;
                    $current_permalink = $current_page->guid;
                }

                $query ="select * from wp_posts 
                            where Id=".$parent_id;
                $results_parent_page = $wpdb->get_results($query,OBJECT);

                foreach ($results_parent_page as $parent_page){
                    $parent_name = $parent_page->post_title;
                    $parent_permalink = $parent_page->guid;
                    $parent_id = $parent_page->ID;
                }

                ob_start(); ?>
                <div id="child-events">
                    <h4><a href="<?=the_permalink($parent_id);?>"><?=$parent_name?></a></h4>
                    <h5><a href="<?=the_permalink();?>"><?=$current_name?></a></h5>
                    <ul>
                        <?php
                        if ( $the_query->have_posts() ) :
                        while ( $the_query->have_posts() ) : $the_query->the_post();
                            ?>
                            <li>
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </li>
                        <?php
                        endwhile;
                        wp_reset_postdata(); ?>
                    </ul>
                    <?php
                    else:
                        ?>
                        <div class="blog-content">
                        </div>
                    <?php
                    endif; ?>
                </div>
                <?php
                $html = ob_get_clean();
                echo $html;
            ?>
        </div>
	</main><!-- #content -->

	<?php // hoot_get_sidebar(); // Loads the sidebar.php template. ?>

</div><!-- .main-content-grid -->

<?php get_footer(); // Loads the footer.php template. ?>