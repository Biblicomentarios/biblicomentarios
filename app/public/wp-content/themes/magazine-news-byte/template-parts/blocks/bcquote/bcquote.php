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
$text = get_field('texto_cita') ?: 'Texto de la cita...';
$referencia = get_field('referencia') ?: 'Referencia';
$tipo_autor = get_field('tipo_autor') ?: 'Tipo de autor';
$imagen = get_field('imagen') ?: 'Imagen';

$estrellas = 0;
switch ($tipo_autor){
    case 'Autoridad General':
    case 'Material oficial de La Iglesia de Jesucristo de los Santos de los Últimos Días':
        $estrellas=5;
        break;
    case 'Líder de La Iglesia de Jesucristo de los Santos de los Últimos Días':
    case 'Erudito Santo de los Últimos Días':
        $estrellas=4;
        break;
    case 'Autor independiente Santo de los Últimos Días':
    case 'Autor evangélico':
    case 'Autor católico':
        $estrellas=3;
        break;
    case 'Autor independiente':
    case 'Escritor de los primeros siglos':
        $estrellas=2;
        break;
    case 'Anónimo':
    case 'Desconocido':
        $estrellas=1;
        break;
}

?>
<div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>">
    <div class="bcquote-blockquote content row">

            <?php
            $coltexto = 'col-12';

            if($titulo!='Título de la cita...'){
                ?>
                <div class="bcquote-titulo col-12"><?=$titulo?></div>
                <?php
            }
            if($imagen!='Imagen'){
                ?>
                <div class="bcquote-image col-1">
                <?php
                        $coltexto = 'col-11';
                        echo wp_get_attachment_image( $imagen, 'half-thumbnail' );
                        ?>
                </div>
                <?php
            }
            ?>
        <div class="<?=$coltexto?>">
            <span class="bcquote-text"><i class="fas fa-quote-left"></i> <?php echo $text; ?> <i class="fas fa-quote-right"></i></span>

        </div>
        <div class="bcquote-cite col-12">
            <div class="bcquote-author"><?php echo "- " . $referencia; ?></div>
            <?php
                if($tipo_autor!='Tipo de autor'){
            ?>
            <div class="bcquote-tipo-autor">
                <b>Tipo de autor:</b> <?php echo $tipo_autor; ?>
                <?php
                    for ($i=1;$i<=$estrellas;$i++){
                        ?>
                        <i class="fa fa-star"></i>
                <?
                    }
                ?>
            </div>
            <?php
                }
            ?>

        </div>
    </div>
</div>