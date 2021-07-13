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
    La siguiente relación te mostrará los libros que integran cada división.
    <div class="row col-12">
        <?php

        // Divisiones del volumen
        foreach ($volumenes as $volumen) {
            $query = "select wt.term_id,name,slug,description
                    from wp_terms wt join wp_term_taxonomy wtt on wt.term_id = wtt.term_id 
                    where parent=$volumen->term_id
                    and taxonomy='category'
                    order by term_order;";
            $divisiones = $wpdb->get_results($query);
            ?>
            <div class="col-12  p-1 m-0 mt-3 mb-1" style="background-color:green;">
                <a target="_blank" class="text-white" style="font-weight:bold" href="/categoria/escrituras/<?= $volumen->slug ?>">
                    <?= $volumen->name ?>
                </a> <span style="color:lightgreen"> (<?= count($divisiones) ?> libros)</span>
            </div>
            <div class="p-1 mb-2" style="font-size:.8em;background-color:#eee8d5">
                <?= $volumen->description ?><hr/>
            </div>
            <?php
            // Libros por división
            foreach ($divisiones as $division) {
                ?>
                <div class="p-0 pl-2 col-12">
                    <i class="fas fa-book" style="font-size:.8em;color:brown;"></i>
                    <a target="_blank" href="/categoria/escrituras/<?= $volumen->slug ?>/<?= $division->slug ?>">
                        <?= $division->name ?>
                    </a>
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