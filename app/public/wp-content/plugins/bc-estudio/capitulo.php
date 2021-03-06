<?php

/* -------------------------------------------------------------------- 
* Cap?tulo de las escrituras                                            
-------------------------------------------------------------------- */
function vs_capitulo($atts)
{
    /* Recibe por querystring el cap?tulo a estudiar */
    global $wpdb;

    // Valor textual
    if (isset($_GET["capitulo"])) {
        $referencia = str_replace( '-',' ',$_GET["capitulo"]);
echo $referencia;
        global $wpdb;
    }

    // Valor num?rico
    if (isset($_GET["idcapitulo"])) {
        $referencia = $_GET["idcapitulo"];

        $results = $wpdb->get_results("select * 
            from capitulos c 
            where c.IdCapitulo = " . $referencia . ";");

        if (!$results) {

            echo '<div class="text-center">El cap?tulo que est?s buscando no existe en las escrituras.<br>Habr? que esperar por nueva revelaci?n :).
            <br/><br/>Mientras tanto, aprovecha para hacer una b?squeda y sigue navegando este sitio. </div>';
            return;
        }
    }

    // Valor por omisi?n
    if (!isset($_GET["idcapitulo"]) && !isset($_GET["capitulo"])) {
        $referencia = 'G?nesis 1';

        $results = $wpdb->get_results("select * 
        from capitulos c 
        where c.Capitulo = '" . $referencia . "';");
    }

    if (!$results) {

        echo 'No hay valores para ese cap?tulo';
        return;
    }

    $vsCapitulo = $results[0]->Capitulo;
    $vsTituloCapitulo = $results[0]->TituloCapitulo;
    $vsUrlAudio = $results[0]->UrlAudio;
    $vsIdCapitulo = $results[0]->IdCapitulo;

    // Enlaces a navegaci?n (anterior-siguiente)
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
            <a href="/capitulo-escrituras/?idcapitulo=<?= $vsIdCapituloAnterior ?>"><i class="fas fa-arrow-left"></i> Cap?tulo anterior</a>
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
            ;");
                    $contador = 0;
                    foreach ($pericopas as $pericopa) {
                        $contador = $contador + 1;
                    ?>
                        <li> <a href="#anchor<?= $contador ?>"> <?= $pericopa->Titulo ?></a>
                        <?php
                    }   // per?copas - estructura del cap?tulo                 
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
                ;");
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
                } // vers?culos
                ?>
            <?php
            } // per?copas - contenido del cap?tulo
            ?>
        </div>
    </div>

    <div class="mb-10 border-top border-bottom row">
        <div class="col-6 border-right">
            <a href="/capitulo-escrituras/?idcapitulo=<?= $vsIdCapituloAnterior ?>"><i class="fas fa-arrow-left"></i> Cap?tulo anterior</a>
        </div>
        <div class="col-6 text-right">
            <a href="/capitulo-escrituras/?idcapitulo=<?= $vsIdCapituloSiguiente ?>">Capitulo siguiente <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <!-- Art?culos relacionados -->
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
                <span style="color:purple">Para saber m?s:</span><br />Material relacionado con <?= $vsCapitulo ?>
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
                } // Art?culos relacionados
            } // Si hay Art?culos relacionados
            wp_reset_postdata();
                ?>
            </ul>
    </div>
<?php
}
add_shortcode('vs_capitulo', 'vs_capitulo');
