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
$titulo = get_field('titulo') ? : '-';
$referencia1 = get_field('referencia_1') ? : '-';
$referencia2 = get_field('referencia_2') ? : '-';
$contenido1 = get_field('contenido_1') ? : '-';
$contenido2 = get_field('contenido_2') ? : '-';
?>

<div class="content my-3 mx-1">
    <div class="row">
        <h3 class="text-center col-12 m-0" style="background-color:maroon;color:lightgoldenrodyellow;font-weight:bold;">
            <?=$titulo?>
        </h3>
    </div>
    <div class="row">
        <div class="col-6 text-center p-2 border" style="background-color: #336600; color:#33FFCC; font-weight:bold;"><?= $referencia1 ?></div>
        <div class="col-6 text-center p-2 border" style="background-color: #336600; color:#33FFCC; font-weight:bold;"><?= $referencia2 ?></div>
    </div>
    <div class="row">
        <div class="col-6 p-2 border"><?= $contenido1 ?></div>
        <div class="col-6 p-2 border"><?= $contenido2 ?></div>
    </div>
    <div class="row">
        <div class="col-12 text-center border p-0 px-1 m-0" style="font-size:.6em;background-color:#ffffdd;">
        <i class="fas fa-asterisk"></i> Utiliza la comparación lado a lado entre <?=$referencia1?> y <?=$referencia2?> para detectar similitudes y diferencias.
        </div>
    </div>
</div>