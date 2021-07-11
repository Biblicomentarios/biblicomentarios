<?php
/*
Plugin Name: BC Estudio VS
Description: Shortcodes relacionados con el estudio de Ven S�gueme.
Author: JPMarichal
Version: 1.0.0
Author URI: https://biblicomentarios.com/
Last Updated: 08 Jul 2021
License: Private
Text Domain: biblicomentarios
*/
/* ============================================================= */

/* ============================================================= 
* ASIGNACIONES
============================================================= */
function vs_asignacion_semanal($atts)
{
    global $wpdb;

    $results = $wpdb->get_results("
    SELECT * FROM vwVsSemanaActual; 
    ");

    $vsTitulo = $results[0]->Titulo;
    $vsImagen = $results[0]->Imagen;
    $vsAsignacion = $results[0]->Asignacion;
    $vsUrlOficial = $results[0]->URLOficial;
    $vsResenia = $results[0]->Resenia;
    $vsIdSemana = $results[0]->IdSemanas;
    ob_start();
?>
    <div style="border:1px solid gray;margin-bottom:10px;">
        <div style="background-color:green;color:white;font-weight:bold;text-align:center;padding:3px;">
            Tema de estudio de esta semana
        </div>
        <div class="p-2">
            <div class="container">
                <div class="col-sm">
                    <div style="margin-top:0; font-size:30px;text-align:center;font-weight:bold;color:green;">
                        <?= $vsAsignacion ?>
                    </div>
                    <div>
                        <b><?= $vsResenia ?>:</b>
                        <?= $vsTitulo ?> (<a href="<?= $vsUrlOficial ?>" target="_blank"> enlace oficial <i class="fas fa-link"></i></a>)
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('vs_asignacion_semanal', 'vs_asignacion_semanal');

function vs_asignacion_diaria($atts)
{
    global $wpdb;
    $vsIdSemana = 57; // Valor por omisión

    $results = $wpdb->get_results("SELECT * FROM vwVsSemanaActual; ");
    if (count($results) > 0) {
        $vsIdSemana = $results[0]->IdSemanas;
    }

    ob_start();
?>
    <div style="border:1pxsolid orange" class="mb-2">
        <div style="background-color:orange;color:white;text-align:center;font-weight:bold;">
            División de asignaciones por fecha
        </div>
        <div class="p-2">
            <ul>
                <?php
                $sql = "
select FAsignacion,c.Capitulo, c.TituloCapitulo,c.URLBC 
from vsasignaciones v 
join capitulos c on v.IdCapitulo = c.IdCapitulo 
where v.IdSemana =" . $vsIdSemana . ";";
                $asignaciones = $wpdb->get_results($sql);

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
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('vs_asignacion_diaria', 'vs_asignacion_diaria');

include_once('capitulo.php');
include_once('estructuras.php');
