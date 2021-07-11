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

    $volumenes =  $wpdb->get_results($query);


    echo '<ol>';
    foreach ($volumenes as $volumen) {
        echo '<li> <a target="_blank" href="/categoria/escrituras/' . $volumen->slug . '">' . $volumen->name . '</a>';
    }
    echo '</ol>';
}
add_shortcode('bc_estructura_volumen', 'bc_estructura_volumen');
