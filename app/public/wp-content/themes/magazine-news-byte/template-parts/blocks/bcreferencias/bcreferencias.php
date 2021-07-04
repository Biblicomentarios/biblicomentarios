<?php

/**
 * BC Referencias Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'bcreferencias-' . $block['id'];
if (!empty($block['anchor'])) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'bcreferencias';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
}

// Load values and assign defaults.
$titulo_listado = get_field('titulo_listado') ?: '';
$objetivo = get_field('objetivo') ?: '';
$secciones = get_field('secciones') ?: '';

// $titulo = get_field('titulo') ?: '';

?>
<div class="content">
    <div class="row">
        <h2 class="col-12 titulo-listado"><?= $titulo_listado ?></h2>
        <div id="referencias_objetivo" class="col-12">
            <p><b>Objetivo:</b> <?= $objetivo ?>.</p>
        </div>
        <?php
        foreach ($secciones as $seccion) {
            $titulo_seccion = $seccion['titulo_seccion'];
        ?>
            <h3 class="col-12 titulo-seccion"><?= $titulo_seccion ?></h3>
            <?php
            $grupos = $seccion['grupos'];


            if ($grupos != false) {
                foreach ($grupos as $grupo) {
                    $referencias_grupo = $grupo['referencias_grupo'];

                    if ($referencias_grupo) {
                        foreach ($referencias_grupo as $referencia_grupo) {
                            var_dump($referencia_grupo);
                            $titulo_grupo = get_sub_field('titulo_grupo');
                            $referencias_seccion = get_sub_field('referencias_seccion');
                            echo 'Titulo';
                            var_dump($titulo_grupo);
                            // echo $titulo_grupo;
                            // $referencias_seccion = $referencia_grupo['referencias_seccion'];
                            // foreach ($referencias_seccion as $referencia_seccion) {
                            //     echo $referencia_seccion['concepto'];
                            //     echo $referencia_seccion['referencias'];
                            // }
                        }
                    }
                }
            }


            $referencias_seccion = $seccion['referencias_seccion'];
            $i = 1;
            $renglonPar = '';

            foreach ($referencias_seccion as $referencia) {
                if (fmod($i, 2) == 0) {
                    $renglonPar = 'renglonPar';
                } else {
                    $renglonPar = '';
                }
                $i++;

                if ($referencia['agrupador_referencias'] != '') {
            ?>
                    <div class="col-12 agrupador-referencia"><?= $referencia['agrupador_referencias'] ?></div>
                <?php
                }
                ?>
                <div class="border col-8 referencias-concepto <?= $renglonPar ?>"><?= $referencia['concepto'] ?></div>
                <div class="border col-4 referencias-referencia <?= $renglonPar ?>"><?= $referencia['referencias'] ?></div>
            <?php
            }
            ?>
        <?php
        }
        ?>
    </div>

</div>