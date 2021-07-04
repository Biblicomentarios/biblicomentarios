<?php

/**
 * Testimonial Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'testimonial-' . $block['id'];
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'testimonial';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}

// Load values and assign defaults.
$titulo = get_field('titulo_cita') ?: 'Título de la cita...';
$pasaje = get_field('pasaje') ?: 'Texto de la cita...';
$referencia = get_field('referencia') ?: 'Referencia';
$volumen = get_field('volumen') ?: '';

?>
<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
    <div class="bcpasaje-blockquote content row">

            <?php
            $coltexto = 'col-12';

            if($titulo!='Título de la cita...'){
                ?>
                <div class="bcpasaje-titulo"><?=$titulo?></div>
                <?php
            }
                ?>
                <div class="bcpasaje-image col-1">
                    <i class="fas fa-book" style="font-size: 50px"></i>
                </div>
        <div class="col-11">
            <span class="bcpasaje-text"> <?php echo $pasaje; ?></span>

        </div>
        <div class="bcpasaje-cite col-12">
            <div class="bcpasaje-author"><?php echo "- " . $referencia; ?>, <i class="fas fa-book"></i> <?=$volumen?></div>
        </div>
    </div>
</div>