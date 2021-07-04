<?php
get_header();
?>
<?php
// Check if there are any posts to display
if (have_posts()) : ?>

    <header class="archive-header">
        <div class="category-header p-2">
            <h1 class="archive-title"><?php single_cat_title('', true); ?></h1>
            <div class="archive-excerpt">
                <?php
                // Display optional category description
                if (category_description()) : ?>
                    <div class="archive-meta"><?php echo category_description(); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="px-3 py-0 my-0 small text-center">
        <?php
            echo bootstrap_pagination();
        ?>
    </div>

    <div class="row mt-3 mx-2">
        <?php
        $i = 1;
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
            $i++;
        endwhile;
        ?>
    </div>

    <div class="px-3 py-0 my-0 small text-center">
        <?php
            echo bootstrap_pagination();
        ?>
    </div>
<?php
else : ?>
    <p>Lo siento, aún no tengo artículos sobre este concepto.</p>


<?php endif; ?>
</div>
</section>


<?php // get_sidebar(); 
?>

<?php
get_footer();
