<?php
/*
Template Name: VenSigueme
*/
$urlWhatsApp = "https://chat.whatsapp.com/HBiWoft74MYHohxx3H33Ci";
?>

<?php
// Loads the header.php template.
get_header();
?>

<?php
// Template modification Hook
do_action('magnb_before_content_grid', 'single.php');
?>

<div class="hgrid main-content-grid">

    <main>
        <div>

            <?php
            // Checks if any posts were found.
            if (have_posts()) :
                while (have_posts()) : the_post();
                    the_content();
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
                                    <h2 style="margin-top:0"><?= $vsAsignacion ?></h2>
                                    <b><?= $vsResenia ?>:</b>
                                    <?= $vsTitulo ?> (<a href="<?= $vsUrlOficial ?>" target="_blank"> enlace oficial <i class="fas fa-link"></i></a>)
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h2 class="mt-3">Plan de Estudio de los Biblicomentarios</h2>
                    Mi propuesta para ti consiste en tres mejoras al programa de estudio oficial de la Iglesia:
                    <ol>
                        <li> Primero, he dividido las asignaciones semanales en <b>asignaciones diarias</b>. Así sabrás lo que tienes que estudiar en cada día.
                        <li> Segundo, he creado un <b>sistema de notificaciones</b> para avisarte de tus asignaciones.
                            Puedes suscribirte a este sistema por medio de
                            <a href="<?= $urlWhatsApp ?>" class="btn btn-success text-white btn-outline-success btn-sm" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                            <!-- o por
                                <a href="#" class="btn btn-outline-success btn-sm" target="_blank"><i class="far fa-envelope"></i> correo electrónico</a>
                                , como parte de los servicios del Boletín de los Biblicomentarios -->.
                        <li> Y tercero, he agregado <b>anotaciones de estudio, artículos y material adicional</b> a la mayoría de los capítulos estudiados, de manera que puedas aumentar tu conocimiento y llevarlo tan lejos como lo desees.
                    </ol>
                    <h2>Enlaces de estudio para esta semana</h2>
                    Por ejemplo, para esta semana, esta es mi división propuesta de la asignación semanal en asignaciones diarias. Haz click en los enlaces
                    para obtener el audio y el material relacionado para cada capítulo.

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
                    <h2>Cómo suscribirte</h2>
                    Haz click en cualquiera de los siguientes botones para recibir las notificaciones directamente en tu WhatsApp o por correo electrónico.
                    <h5>Medios de suscripción</h5>
                    <a href="<?= $urlWhatsApp ?>" class="btn btn-outline-success btn-sm" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>

            <?php
                // End found posts loop.
                endwhile;

                // Loads the comments.php template
                if (!is_attachment()) {
                    comments_template('', true);
                };

            // If no posts were found.
            else :

            endif;

            ?>

        </div><!-- #content-wrap -->
    </main><!-- #content -->

</div><!-- .main-content-grid -->

<?php get_footer(); // Loads the footer.php template. 
?>