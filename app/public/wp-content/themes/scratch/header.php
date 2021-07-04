<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= get_bloginfo('title') ?> - <?= get_bloginfo('description') ?></title>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Descripción">
    <meta name="author" content="https://www.biblicomentarios.com">
    <link rel="shortcut icon" href="images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <?php
    wp_head();
    ?>
</head>

<body <?php body_class();?>>
<header>
    <?php
    $site_description = get_bloginfo('description', 'display');
    $site_name = get_bloginfo('name');
    ?>

    <div class="row">
        <div class="col-lg-4 col-sm-12 border">
            <div id="site-name"><a href="/"><?= $site_name ?></a></div>
            <div id="site-description"><?= $site_description ?></div>

        </div>
        <div class="col-lg-8 col-sm-12 border">
            <div style="background-color:maroon;width:468px;height:60px"></div>
        </div>
    </div>
    <div id="top-menu">
        <nav class="navbar navbar-expand-md navbar-light bg-light m-0 p-0" role="navigation">
            <div class="text-center">
                <!-- Brand and toggle get grouped for better mobile display -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-controls="bs-example-navbar-collapse-1" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'your-theme-slug'); ?>">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- <a class="navbar-brand" href="#">Navbar</a> -->
                <?php
                wp_nav_menu(array(
                    'menu'              => 2159,
                    'theme_location'    => 'top-menu',
                    'depth'             => 2,
                    'container'         => 'div',
                    'container_id'      => 'bs-example-navbar-collapse-1',
                    'container_class'   => 'collapse navbar-collapse',
                    'menu_class'        => 'nav navbar-nav font-weight-bold',
                    'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                    'walker'            => new WP_Bootstrap_Navwalker(),

                    'li_class'          => 'nav-item',
                    'a_class'           => 'nav-link',
                    // 'active_class'      => 'active'
                ));
                ?>
            </div>
        </nav>
    </div><!-- Navigation -->

    <?php
    // wp_nav_menu(array(
    //     'at-menu' => 'Antiguo Testamento',
    //     'nt-menu' => 'Nuevo Testamento'
    // ));

    ?>
    <div id="mnuEscrituras">
        <ul>
            <?php // wp_nav_menu(array('theme_location' => 'at-menu', 'items_wrap' => '%3$s', 'container' => false)); 
            ?>
            <?php // wp_nav_menu(array('theme_location' => 'nt-menu', 'items_wrap' => '%3$s', 'container' => false)); 
            ?>
            <?php // wp_nav_menu(array('theme_location' => 'lm-menu', 'items_wrap' => '%3$s', 'container' => false)); 
            ?>
            <?php // wp_nav_menu(array('theme_location' => 'dc-menu', 'items_wrap' => '%3$s', 'container' => false)); 
            ?>
            <?php // wp_nav_menu(array('theme_location' => 'pgp-menu', 'items_wrap' => '%3$s', 'container' => false)); 
            ?>
        </ul>
    </div>
    <div class="breadcrumb">
        <?php
        get_breadcrumbs(
            array(
                'separator'   => '',
                'linkFinal'   => false,
                'echo'        => true,
                'printOnHome' => false,
                'before'      => '',
            )
        );
        ?>
    </div>
</header>

<section class="content">
    <div id="content" class="container col-12" role="main">