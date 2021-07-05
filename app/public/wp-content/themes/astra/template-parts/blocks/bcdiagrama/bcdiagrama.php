<?php

/**
 * Diagrama Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */


// Load values and assign defaults.
$titulo = get_field('titulo') ?: '-';
$imagen = get_field('imagen') ?: '-';
?>

    <div class="content border p-3">
        <div class="text-center p-1" style="background-color:green;color:ivory;font-weight:bold;">
            <?= $titulo ?>
        </div>
        <div class="text-center">
            <img src="<?= $imagen ?>" alt="<?= $titulo ?>">
        </div>
        <div class="text-center border p-1" style="font-size:.6em;background-color:#ffffdd">
            <b>(c) <?= date('Y'); ?> Juan Pablo Marichal.</b> Prohibida su reproducción sin permiso escrito del autor.
        </div>
    </div>
