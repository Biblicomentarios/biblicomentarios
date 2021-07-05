<?php

/**
 * Gospel Harmony Simple Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */


// Load values and assign defaults.
$referenciaMateo = get_field('referencia_mateo') ? 'Mateo ' . get_field('referencia_mateo') : '-';
$referenciaMarcos = get_field('referencia_marcos') ? "Marcos " . get_field('referencia_marcos') : '-';
$referenciaLucas = get_field('referencia_lucas') ? "Lucas " . get_field('referencia_lucas') : '-';
$referenciaJuan = get_field('referencia_juan') ? "Juan " . get_field('referencia_juan') : '-';
$titulo = get_field('titulo') ?: '';
?>

<div class="content my-3 mx-2">
    <?php 
        if ($titulo != '') {
    ?>
    <div class="row">
        <h3 class="text-center col-12 m-0" style="background-color:maroon;color:lightgoldenrodyellow;font-weight:bold;">
            <?=$titulo?>
        </h3>
    </div>
    <?php
        }
    ?>
    <div class="row">
        <div class="text-center col-12 m-0 p-0" 
            style="background-color:#333300;
            padding:0px;
            color:#33FFCC;
            font-weight:bold;font-size:.9em">
            <i class="fas fa-arrows-alt-h"></i> Armonía de los evangelios <i class="fas fa-arrows-alt-h"></i>
        </div>
    </div>
    <div class="row">
        <div class="col-3 text-center border" style="background-color: #CCFFCC;"><?= $referenciaMateo ?></div>
        <div class="col-3 text-center border" style="background-color: #CCFFCC;"><?= $referenciaMarcos ?></div>
        <div class="col-3 text-center border" style="background-color: #CCFFCC;"><?= $referenciaLucas ?></div>
        <div class="col-3 text-center border"><?= $referenciaJuan ?></div>
    </div>
    <div class="row">
        <div class="col-12 text-left border p-0 px-1 m-0" style="font-size:.6em;background-color:#ffffdd;">
        <i class="fas fa-asterisk"></i> Las armonías de los evangelios muestran pasajes de contenido similar o complementario entre los evangelios.
        | <span style="color: #CCFFCC;"><i class="fas fa-square"></i></span> Evangelios sinópticos (Mateo, Marcos y Lucas)
        </div>
    </div>
</div>