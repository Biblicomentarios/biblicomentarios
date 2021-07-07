<?php
function asignarPapa($padre, $hija)
{
    $catPadre = getCategoryIdByName($padre);
    $catHija = getCategoryIdByName($hija);

    wp_update_term(
        $catHija,
        'category',
        array(
            // 'menu_order' => $i,
            'parent' => $catPadre
        )
    );
}

function asignarCategoriaPadre($padre, $libro, $inicial,$final)
{
    global $wpdb;
    $catPadre = getCategoryIdByName($padre);

    for ($i = $inicial; $i <= $final; $i++) {
        $nombreCompleto = "$libro $i";
        $catId = getCategoryIdByName($nombreCompleto);
        wp_update_term(
            $catId,
            'category',
            array(
                'term_order' => $i*10,
                'parent' => $catPadre
            )
        );

        $wpdb->query('UPDATE '.$wpdb->prefix.'terms SET term_order='.$i.' WHERE term_id='.$catId);
    }
}

function insertarCategorias()
{
    $parte1 = insertar_categoria('La preparación en el Valle de Lemuel', '1 Nefi');
    if (!empty($parte1) && $parte1 > 0) {
        insertar_subCategoria('El ministerio de Lehi en Jerusalén', $parte1, '1 Nefi 1', '1 Nefi 1');
        insertar_subCategoria('Lehi y su familia salen de Jerusalén al desierto', $parte1, '1 Nefi 1', '1 Nefi 1');
        insertar_subCategoria('Primer regreso a Jerusalén por las planchas de bronce', $parte1, '1 Nefi 3', '1 Nefi 5');
        insertar_subCategoria('El propósito de los escritos de Nefi', $parte1, '1 Nefi 6', '1 Nefi 6');
        insertar_subCategoria('Segundo regreso a Jerusalén por Ismael y su familia', $parte1, '1 Nefi 7', '1 Nefi 7');
        insertar_subCategoria('El sueño de Lehi', $parte1, '1 Nefi 8', '1 Nefi 8');
        insertar_subCategoria('Los dos juegos de anales', $parte1, '1 Nefi 9', '1 Nefi 9');
        insertar_subCategoria('Profecías de Lehi sobre la cautividad, el Mesías y el recogimiento', $parte1, '1 Nefi 10', '1 Nefi 10');
        insertar_subCategoria('La visión de Nefi', $parte1, '1 Nefi 11', '1 Nefi 14');
        insertar_subCategoria('Nefi explica el significado del sueño de Lehi', $parte1, '1 Nefi 15', '1 Nefi 15');
    }

    $parte2 = insertar_categoria('La travesía del grupo de Lehi en el desierto', '1 Nefi');
    if (!empty($parte2) && $parte2 != 0) {
        insertar_subCategoria('La guía del Señor en el desierto', $parte1, '1 Nefi 16', '1 Nefi 16');
        insertar_subCategoria('Nefi construye un barco', $parte1, '1 Nefi 17', '1 Nefi 17');
        insertar_subCategoria('La travesía por mar hacia la tierra prometida', $parte1, '1 Nefi 18', '1 Nefi 18');
        insertar_subCategoria('Nefi profetiza sobre la expiación y sobre la dispersión', $parte1, '1 Nefi 19', '1 Nefi 18');
    }

    $parte3 = insertar_categoria('El ministerio de Nefi en la tierra prometida', '1 Nefi');
    if (!empty($parte3) && $parte3 != 0) {
        insertar_subCategoria('Profecías de Nefi sobre el Mesías y el recogimiento', $parte1, '1 Nefi 19', '1 Nefi 22');
    }
    /*
        $parte4 = insertar_categoria('Mensajes proféticos sobre la restauración de Judá', 'Hageo');
        if (!empty($parte4) && $parte4 != 0) {
            insertar_subCategoria('Atalaya de restauración', $parte4, 'Hageo 33', 'Hageo 33');
            insertar_subCategoria('Promesas de restauración', $parte4, 'Hageo 34', 'Hageo 37');
            insertar_subCategoria('Victoria sobre Gog y Magog', $parte4, 'Hageo 38', 'Hageo 39');
            insertar_subCategoria('La restauración de Israel en el reino', $parte4, 'Hageo 40', 'Hageo 48');
        }
*/
}

function getCategoryIdByName($nombreCategoria)
{
    global $wpdb;

    return $wpdb->get_var("select term_id 
        from wp_terms
        where name = '" . $nombreCategoria . "'");
}

function getCategoryNameById($idCategoria)
{
    global $wpdb;

    return $wpdb->get_var("select name 
        from wp_terms
        where term_id = " . $idCategoria);
}

function asignar_capitulos($catId, $capituloInicial, $capituloFinal)
{
    if ($catId != 0) {
        $capituloInicialId = getCategoryIdByName($capituloInicial);
        $capituloFinalId = getCategoryIdByName($capituloFinal);

        if ($capituloInicialId != null && $capituloFinalId != null) {
            for ($i = $capituloInicialId; $i <= $capituloFinalId; $i++) {
                wp_update_term(
                    $i,
                    'category',
                    array(
                        'parent' => $catId
                    )
                );
            }
        }
    }
}

function insertar_subCategoria($titulo, $padre, $capituloInicial, $capituloFinal)
{
    $padreName = getCategoryNameById($padre);
    $catId = insertar_categoria($titulo, $padreName);
    asignar_capitulos($catId, $capituloInicial, $capituloFinal);
}

function insertar_categoria($titulo, $padre)
{
    $cat_id = 0;

    $padreId = getCategoryIdByName($padre);

    if (!term_exists($titulo, 'category')) {
        $cid = wp_insert_term(
            $titulo,
            'category',
            array(
                'parent' => $padreId
            )
        );

        if (!is_wp_error($cid) && isset($cid['term_id'])) {
            $cat_id = $cid['term_id'];
        }
    }
    return $cat_id;
}