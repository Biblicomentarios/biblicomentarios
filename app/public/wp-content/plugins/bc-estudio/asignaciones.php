<?php
function vs_asignacion_semanal($atts)
{
    global $wpdb;

    $results = $wpdb->get_results("select IdSemanas, Imagen,Titulo,Asignacion,URLOficial,Reseña 
                    from vssemanas v 
                    where now() between FInicio and Ffinal;");

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
                    where now() between FInicio and Ffinal;");

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
where v.IdSemana =" . $vsIdSemana . ";");

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