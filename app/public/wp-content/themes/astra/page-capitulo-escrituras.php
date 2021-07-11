<?php
/*
Template Name: CapituloEscrituras
*/
?>

<?php 
// Loads the header.php template.
get_header();
?>
<div class="col-12">
<?php
the_content();
?>
</div>

<?php
global $wpdb;

if(isset($_GET["capitulo"])){
    $referencia = $_GET["capitulo"];
    
    $results = $wpdb->get_results( "select * 
            from capitulos c 
            where c.Capitulo = '".$referencia."';");
}

if(isset($_GET["idcapitulo"])){
    $referencia = $_GET["idcapitulo"];
    
    $results = $wpdb->get_results( "select * 
            from capitulos c 
            where c.IdCapitulo = ".$referencia.";" );
}
        
        $vsCapitulo = $results[0]->Capitulo; 
        $vsTituloCapitulo = $results[0]->TituloCapitulo;
        $vsUrlAudio = $results[0]->UrlAudio;
        $vsIdCapitulo = $results[0]->IdCapitulo;

        // Enlaces a navegación (anterior-siguiente)
        $vsIdCapituloAnterior = $results[0]->IdCapitulo - 1;
        if($vsIdCapituloAnterior == 0)
        {
            $vsIdCapituloAnterior = 1584;
        }
        $vsIdCapituloSiguiente = $results[0]->IdCapitulo + 1;
        if($vsIdCapituloSiguiente == 1585){$vsIdCapituloSiguiente = 1;}
?>
<div>

<div class="hgrid main-content-grid mt-0">

<div class="mb-0 border-bottom" style="text-align:center;">
<h1 class="mb-0"><?=$vsCapitulo?></h1>
<h3 class="mt-0 mb-0" style="border-bottom:1px solid #eeeeee"><?=$vsTituloCapitulo?></h3>

<div class="row border" style="background-color:orange">
    <div class="col-6 p-1 border" style="color:white;font-weight:bold; text-align:left;">
        <a href="/capitulo-escrituras/?idcapitulo=<?=$vsIdCapituloAnterior?>"><i class="fas fa-arrow-left"></i> Capítulo anterior</a>
    </div>
    <div class="col-6 p-1 border" style="color:white;font-weight:bold; text-align:right">
        <a href="/capitulo-escrituras/?idcapitulo=<?=$vsIdCapituloSiguiente?>">Capitulo siguiente <i class="fas fa-arrow-right"></i></a>
    </div>
</div>

<main style="margin:0;text-align:left" class="mt-3 ml-0 mb-0" <?php // hybridextend_attr( 'content' ); ?>>
<div class="row mr-0 ml-0 col-12">

<div class="col-lg-3 col-md-12 mb-0 border">
<a name="top"></a>
<h2 class="mt-0">Audio</h2>
<audio controls>
<source src="<?=$vsUrlAudio?>" type="audio/mpeg">
</audio>
<h2 class="mt-0">Estructura del capítulo</h2>
<ol>
<?php
$pericopas = $wpdb->get_results( "select * 
            from pericopas p
            where p.IdCapitulo = '".$vsIdCapitulo."'
            order by VersiculoInicial
            ;" );
$contador = 0;
foreach($pericopas as $pericopa){
    $contador = $contador + 1;
?>
<li> <a href="#anchor<?=$contador?>"> <?=$pericopa->Titulo?></a>
<?php
}   // perícopas - estructura del capítulo                 
?>
</ol>
</div>
	
	<div class="col-lg-8 col-md-12 mt-0 border">
	<h2 class="mt-0"><?=$vsCapitulo?></h2>
	
	<?php 
	$contador = 0;           
    foreach($pericopas as $pericopa){
        $contador = $contador + 1;
        $IdPericopa = $pericopa->IdPericopa;
    ?>
    <div><a name="anchor<?=$contador?>" /></div>
    <h3 class="p-1 mb-0 mt-0" style="background-color:teal;color:white;font-weight:bold;">
        <?=$pericopa->Titulo?>
    </h3>
    <div class="mt-0" style="text-align:right;font-size:.8em">
        <a href="#top"> 
            <i class="fas fa-arrow-up"></i>
    	    Arriba
    	</a>
    </div>
    <?php
    $versiculos = $wpdb->get_results( "select * 
                from versiculos v
                where v.IdPericopa = '".$IdPericopa."'
                order by numVersiculo
                ;" );
    $isOdd = true;
    foreach($versiculos as $versiculo){
        (($c = !$c)? $bgcolor = "white" : $bgcolor = "mintcream");
        $IdVersiculo = $versiculo->IdVersiculo;
        ?>
        <div class="ml-1 mb-2" style="border-bottom:1px dotted lightgreen;font-size:1em;background-color:<?=$bgcolor?>">
            <span style="color:teal;font-weight:bold;"><?=$versiculo->NumVersiculo?></span> <?=$versiculo->Contenido?>
            <?php
            $comentarios = $wpdb->get_results( "select * 
                                              from comentariosversiculos c 
                                              where c.IdVersiculo = ".$IdVersiculo."
                                              Order by Orden;");
            
            foreach($comentarios as $comentario){
?>
                <h4><?=$comentario->Titulo?></h4>
                <?=$comentario->Comentario?>
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
 
 <div class="col-12 border">
<?php
          $args = array( 'posts_per_page' => 6,
         'category_name' => $vsCapitulo );
          $myposts = get_posts( $args );
          if(count($myposts)>0){
          ?>
          <h2 class="mb-0">Artículos de los Biblicomentarios relacionados con <?=$vsCapitulo?></h2> 
<ul>
          <?php
          foreach ( $myposts as $post ){
          setup_postdata( $post ); 
?>

    <li class="mb-1" style="border-bottom:1px dotted gray"> 
        <h3 class="mb-0 mt-0"><a href="<?=$post->guid?>" target="_blank"><?=$post->post_title?></a></h3>
        <?=$post->post_excerpt?>
<?php 
          } // Artículos relacionados
          } // Si hay Artículos relacionados
          wp_reset_postdata();
?>
</ul>

<div class="row border" style="background-color:orange">
<div class="col-6 p-1 border" style="color:white;font-weight:bold; text-align:left;">
<a href="/capitulo-escrituras/?idcapitulo=<?=$vsIdCapituloAnterior?>"><i class="fas fa-arrow-left"></i> Capítulo anterior</a>
</div>
<div class="col-6 p-1 border" style="color:white;font-weight:bold; text-align:right">
<a href="/capitulo-escrituras/?idcapitulo=<?=$vsIdCapituloSiguiente?>">Capitulo siguiente <i class="fas fa-arrow-right"></i></a>
</div>
</div>
</div>


    </div>
    
    
    
    </div>
	</main><!-- #content -->
</div>


	<?php // hybridextend_get_sidebar(); // Loads the sidebar.php template. ?>

</div><!-- .hgrid -->

<?php 

get_footer(); // Loads the footer.php template. 
