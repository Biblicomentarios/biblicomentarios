<?php
/*
Plugin Name: BC Estudio VS
Description: Shortcodes relacionados con el estudio de Ven Sígueme.
Author: JPMarichal
Version: 1.0.0
Author URI: https://biblicomentarios.com/
Last Updated: 08 Jul 2021
License: Private
Text Domain: biblicomentarios
*/
/* ============================================================= */


function vs_asignacion_semanal($atts)
{
    global $wpdb;

    $results = $wpdb->get_results("select IdSemanas, Imagen,Titulo,Asignacion,URLOficial,Reseña 
                    from vssemanas v 
                    where now() between FInicio and Ffinal;",);

    $vsTitulo = $results[0]->Titulo;
    $vsImagen = $results[0]->Imagen;
    $vsAsignacion = $results[0]->Asignacion;
    $vsUrlOficial = $results[0]->URLOficial;
    $vsResenia = $results[0]->Reseña;
    $vsIdSemana = $results[0]->IdSemanas;
?>
    <div style="border:1px solid gray;margin-bottom:10px;">
        <div style="background-color:green;color:white;font-weight:bold;text-align:center;padding:3px;">
            Tema de estudio de esta semana
        </div>
        <div class="p-2">
            <div class="container">
                <div class="col-sm">
                    <div style="margin-top:0; font-size:35px;text-align:center;font-weight:bold;color:green;">
                        <?= $vsAsignacion ?>
                    </div>
                    <b><?= $vsResenia ?>:</b>
                    <?= $vsTitulo ?> (<a href="<?= $vsUrlOficial ?>" target="_blank"> enlace oficial <i class="fas fa-link"></i></a>)
                    <br>
                </div>
            </div>
        </div>
    </div>
<?php
}
add_shortcode('vs_asignacion_semanal', 'vs_asignacion_semanal');

function vs_asignacion_diaria($atts)
{
    global $wpdb;
    $results = $wpdb->get_results("select IdSemanas, Imagen,Titulo,Asignacion,URLOficial,Reseña 
                    from vssemanas v 
                    where now() between FInicio and Ffinal;",);

    $vsIdSemana = $results[0]->IdSemanas;
?>
    <div style="border:1px solid orange" class="mt-3 mb-3">
        <div style="background-color:orange;color:white;text-align:center;font-weight:bold;">
            División de asignaciones por fecha
        </div>
        <div class="p-2">
            <ul>
                <?
                $asignaciones = $wpdb->get_results("
select FAsignacion,c.Capitulo, c.TituloCapitulo,c.URLBC 
from vsasignaciones v 
join capitulos c on v.IdCapitulo = c.IdCapitulo 
where v.IdSemana =" . $vsIdSemana . ";",);

                foreach ($asignaciones as $asignacion) {
                    // $Fecha = date_format(strtotime($asignacion->FAsignacion),"dd");
                    $FechaBase = explode(" ", $asignacion->FAsignacion)[0];
                    $FArray = explode("-", $FechaBase);
                    $Fecha = $FArray[2] . "-" . $FArray[1] . "-" . $FArray[0];
                    $Asignacion = $asignacion->TituloCapitulo;
                    $Capitulo = $asignacion->Capitulo;
                ?>
                    <li> <b><?= $Fecha ?>:</b>
                        <a href="/capitulo-escrituras/?capitulo=<?= $Capitulo ?>">
                            <?= $Capitulo ?> (<?= $Asignacion ?>)
                        </a>
                    <?php
                }
                    ?>
            </ul>
        </div>
    </div>
<?php
}
add_shortcode('vs_asignacion_diaria', 'vs_asignacion_diaria');

/* -------------------------------------------------------------------- 
* Capítulo de las escrituras                                            
-------------------------------------------------------------------- */
function vs_capitulo($atts)
{
    /* Recibe por querystring el capítulo a estudiar */
    global $wpdb;

    // Valor textual
    if (isset($_GET["capitulo"])) {
        $referencia = $_GET["capitulo"];

        $results = $wpdb->get_results("select * 
            from capitulos c 
            where c.Capitulo = '" . $referencia . "';",);

            if (!$results) {
                ?>
                <div class="text-center">
                    <h2>Resultados para <b><?=$referencia?></b></h2>
                Buscaste por <b><?=$referencia?></b>, pero ese capítulo no existe en las escrituras. <br/>
                ¿Estás seguro de haber introducido el capítulo correcto?
            </div>
                 <?php
                return;
            }
    }

    // Valor numérico
    if (isset($_GET["idcapitulo"])) {
        $referencia = $_GET["idcapitulo"];

        $results = $wpdb->get_results("select * 
            from capitulos c 
            where c.IdCapitulo = " . $referencia . ";",);

        if (!$results) {

            echo '<div class="text-center">El capítulo que estás buscando no existe en las escrituras.<br>Habrá que esperar por nueva revelación :).
            <br/><br/>Mientras tanto, aprovecha para hacer una búsqueda y sigue navegando este sitio. </div>';
            return;
        }
    }

    // Valor por omisión
    if (!isset($_GET["idcapitulo"]) && !isset($_GET["capitulo"])) {
        $referencia = 'Génesis 1';

        $results = $wpdb->get_results("select * 
        from capitulos c 
        where c.Capitulo = '" . $referencia . "';",);
    }

    if (!$results) {

        echo 'No hay valores para ese capítulo';
        return;
    }

    $vsCapitulo = $results[0]->Capitulo;
    $vsTituloCapitulo = $results[0]->TituloCapitulo;
    $vsUrlAudio = $results[0]->UrlAudio;
    $vsIdCapitulo = $results[0]->IdCapitulo;

    // Enlaces a navegación (anterior-siguiente)
    $vsIdCapituloAnterior = $results[0]->IdCapitulo - 1;
    if ($vsIdCapituloAnterior == 0) {
        $vsIdCapituloAnterior = 1584;
    }
    $vsIdCapituloSiguiente = $results[0]->IdCapitulo + 1;
    if ($vsIdCapituloSiguiente == 1585) {
        $vsIdCapituloSiguiente = 1;
    }
?>
    <h1 class="text-center mb-0 mt-0"><?= $vsCapitulo ?></h1>
    <h2 class="text-center mb-0"><?= $vsTituloCapitulo ?></h2>

    <div class="mb-10 border-top border-bottom row">
        <div class="col-6 border-right">
            <a href="/capitulo-escrituras/?idcapitulo=<?= $vsIdCapituloAnterior ?>"><i class="fas fa-arrow-left"></i> Capítulo anterior</a>
        </div>
        <div class="col-6 text-right">
            <a href="/capitulo-escrituras/?idcapitulo=<?= $vsIdCapituloSiguiente ?>">Capitulo siguiente <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 border-right pt-2">
            <div id="audio-capitulo">
                <h3 class="mt-0">Audio de <?= $vsCapitulo ?></h3>
                <audio controls>
                    <source src="<?= $vsUrlAudio ?>" type="audio/mpeg">
                </audio>
            </div>

            <div id="nav-pericopas">
                <h3 class="mt-0">Estructura de <?= $vsCapitulo ?></h3>
                <ol>
                    <?php
                    $pericopas = $wpdb->get_results("select * 
            from pericopas p
            where p.IdCapitulo = '" . $vsIdCapitulo . "'
            order by VersiculoInicial
            ;",);
                    $contador = 0;
                    foreach ($pericopas as $pericopa) {
                        $contador = $contador + 1;
                    ?>
                        <li> <a href="#anchor<?= $contador ?>"> <?= $pericopa->Titulo ?></a>
                        <?php
                    }   // perícopas - estructura del capítulo                 
                        ?>
                </ol>
            </div>
        </div>

        <div class="col-md-8 pt-2 pr-0">
            <?php
            $contador = 0;
            foreach ($pericopas as $pericopa) {
                $contador = $contador + 1;
                $IdPericopa = $pericopa->IdPericopa;
            ?>
                <div><a name="anchor<?= $contador ?>" /></div>
                <h3 class="p-1 mb-0 mt-0" style="background-color:teal;color:white;font-weight:bold;">
                    <?= $pericopa->Titulo ?>
                </h3>
                <div class="mt-0" style="text-align:right;font-size:.8em">
                    <a href="#top">
                        <i class="fas fa-arrow-up"></i>
                        Arriba
                    </a>
                </div>
                <?php
                $versiculos = $wpdb->get_results("select * 
                from versiculos v
                where v.IdPericopa = '" . $IdPericopa . "'
                order by numVersiculo
                ;",);
                $isOdd = true;
                foreach ($versiculos as $versiculo) {
                    (($c = !$c) ? $bgcolor = "white" : $bgcolor = "mintcream");
                    $IdVersiculo = $versiculo->IdVersiculo;
                ?>
                    <div class="ml-1 mb-2" style="border-bottom:1px dotted lightgreen;font-size:1em;background-color:<?= $bgcolor ?>">
                        <span style="color:teal;font-weight:bold;"><?= $versiculo->NumVersiculo ?></span> <?= $versiculo->Contenido ?>
                        <?php
                        $comentarios = $wpdb->get_results("select * 
                                              from comentariosversiculos c 
                                              where c.IdVersiculo = " . $IdVersiculo . "
                                              Order by Orden;");

                        foreach ($comentarios as $comentario) {
                        ?>
                            <!-- <h4><?= $comentario->Titulo ?></h4>
                            <?= $comentario->Comentario ?> -->
                        <?php
                        }  // comentarios                           
                        ?>
                    </div>
                <?
                } // versículos
                ?>
            <?php
            } // perícopas - contenido del capítulo
            ?>
        </div>
    </div>

    <div class="mb-10 border-top border-bottom row">
        <div class="col-6 border-right">
            <a href="/capitulo-escrituras/?idcapitulo=<?= $vsIdCapituloAnterior ?>"><i class="fas fa-arrow-left"></i> Capítulo anterior</a>
        </div>
        <div class="col-6 text-right">
            <a href="/capitulo-escrituras/?idcapitulo=<?= $vsIdCapituloSiguiente ?>">Capitulo siguiente <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <!-- Artículos relacionados -->
    <div class="col-12 mt-3 border-bottom">
        <?php
        $args = array(
            'posts_per_page' => 6,
            'category_name' => $vsCapitulo
        );
        $myposts = get_posts($args);
        if (count($myposts) > 0) {
        ?>
            <h2 class="mb-0 text-center border-bottom mb-3">
                <span style="color:purple">Para saber más:</span><br />Material relacionado con <?= $vsCapitulo ?>
            </h2>
            <ul>
                <?php
                foreach ($myposts as $post) {
                    setup_postdata($post);
                ?>

                    <li class="mb-1" style=" font-weight:bold;">
                        <div class="mb-0 mt-0">
                            <a href="<?= $post->guid ?>" title="<?= $post->post_excerpt ?>" target="_blank"><?= $post->post_title ?></a>
                        </div>
                <?php
                } // Artículos relacionados
            } // Si hay Artículos relacionados
            wp_reset_postdata();
                ?>
            </ul>
    </div>
<?php
}
add_shortcode('vs_capitulo', 'vs_capitulo');
