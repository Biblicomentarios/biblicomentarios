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
                        <div class="col-xs-12 col-sm-6 p-1 mb-3">
                <div class="border rounded mx-1">
                    <div class="row">
                        <div class="col-12">
                            <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                                <h2 class="archive m-0 p-1"><?php the_title(); ?></h2>
                            </a>
                        </div>
                        <div class="col-xs-12 col-sm-3 m-0 text-left border-right">
                            <a href="<?= the_permalink(); ?>">
                                <?php the_post_thumbnail('archive-thumbnail'); ?>
                            </a>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <div class="p-1" style="font-size:.8em !important;">
                                <?php the_excerpt(); ?>
                            </div>
                            <div class="text-right p-1">
                                <a href="<?= the_permalink(); ?>" class="btn btn-primary btn-sm">
                                    <?php _e('Leer más') ?>
                                </a>
                            </div>
                        </div>
                    </div>
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
                </div>
            </div>

                        
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
