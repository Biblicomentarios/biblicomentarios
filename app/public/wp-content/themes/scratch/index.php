<?php
get_header();
?>
<div class="row">
    <div class="col-xs-12 col-sm-9">
        <?php
        // Check if there are any posts to display
        if (have_posts()) : ?>
            <article>


                <div class="content mt-0 mx-3">
                    <?php
                    // The Loop
                    while (have_posts()) : the_post();
                    ?>
                        <div class="bg-light ">
                            <div class="post-header ">
                                <h1 class="post-title" style="border-bottom:1px solid black"><?php the_title(); ?></h1>
                            </div>
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                        <?php $hdrImage = get_the_post_thumbnail_url(get_the_ID(), 'medium_large'); ?>
                        <header class="align-middle text-center bg-header-image" style='border:1px solid black; background-image: url("<?= $hdrImage ?>")'>



                        </header>


                        <div class="bg-color-light p-1" style="font-size:11px;background-color:#ddd">
                            <b><?php _e('Referencias: ');
                                the_category(', '); ?> </b>
                        </div>
                        <?php
                        if (get_the_tags()) {
                        ?>
                            <div class="bg-color-light p-1" style="font-size:11px;background-color:#eee">
                                <b><?php _e('Temas relacionados: ');
                                    the_tags(', '); ?> </b>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="post-content"> 
                            <?php the_content(); ?>
                        </div> 

                        <nav>
                            <div class="row border-top border-bottom mx-1 my-3 " style="background-color:orange;">
                                <div class="text-center p-2 text-white col-12" style="background-color:navy;font-weight:bold;">Para seguir aprendiendo</div>
                                <div class="nav-previous col-6 border-right"><?php previous_post_link() ?></div>
                                <div class="nav-next col-6 text-right"><?php next_post_link() ?></div>
                            </div>
                        </nav>
                    <?php

                    endwhile;
                    ?>

                </div>
            </article>
        <?php

        else : ?>
            <p>Lo siento, aún no hay contenido en este artículo.</p>
        <?php endif; ?>
    </div>
    <div class='col-xs-12 col-sm-3'>
        <?php get_sidebar('primary'); ?>
    </div>
</div>
<?php
get_footer();
