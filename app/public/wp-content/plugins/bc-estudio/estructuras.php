<?php

function bc_estructura_volumen($attr)
{
    global $wpdb;
    $parent = $attr['parent'];

    $query = "select wt.term_id,name,slug,description
from wp_terms wt join wp_term_taxonomy wtt on wt.term_id = wtt.term_id 
where parent=$parent
and taxonomy='category'
order by term_order;";

    $volumenes = $wpdb->get_results($query);

    ob_start();

    echo '<ol>';
    foreach ($volumenes as $volumen) {
        echo '<li> <a target="_blank" href="/categoria/escrituras/' . $volumen->slug . '">' . $volumen->name . '</a>';
    }
    echo '</ol>';

    return ob_get_clean();
}

add_shortcode('bc_estructura_volumen', 'bc_estructura_volumen');

function bc_estructura_volumen_division($attr)
{
    global $wpdb;
    $parent = $attr['parent'];

    $query = "select wt.term_id,name,slug,description
from wp_terms wt join wp_term_taxonomy wtt on wt.term_id = wtt.term_id 
where parent=$parent
and taxonomy='category'
order by term_order;";

    $volumenes = $wpdb->get_results($query);

    ob_start();
    ?>
    <div class="border">
        <?php
        foreach ($volumenes as $volumen) {
            ?>
            <h3 class="text-white p-1 m-0 bg-primary">
                <a target="_blank" href="/categoria/escrituras/<?= $volumen->slug ?>">
                    <?= $volumen->name ?>
                </a>
            </h3>
            <div>
            <?php
                $query = "select wt.term_id,name,slug,description
                    from wp_terms wt join wp_term_taxonomy wtt on wt.term_id = wtt.term_id 
                    where parent=$volumen->term_id
                    and taxonomy='category'
                    order by term_order;";
                $divisiones = $wpdb->get_results($query);

                foreach ($divisiones as $division){
                ?>
                      <div class="p-0 pl-2">
                          <a target="_blank" href="/categoria/escrituras/<?= $volumen->slug ?>/<?= $division->slug ?>">
                              <?= $division->name ?>
                          </a>
                      </div>
            </div>
            <?php
                }
        }
        ?>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('bc_estructura_volumen_division', 'bc_estructura_volumen_division');