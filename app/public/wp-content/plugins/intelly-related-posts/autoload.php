<?php
spl_autoload_register('irp_autoload');
function irp_autoload($class) {
    $root=dirname(__FILE__).'/includes/classes/';
    irp_autoload_root($root, $class);
}
function irp_autoload_root($root, $class) {
    $slash=substr($root, strlen($root)-1);
    if($slash!='/' && $slash!='\\') {
        $root.='/';
    }
    $name=str_replace(IRP_PLUGIN_PREFIX, '', $class);
    if(strpos($class, IRP_PLUGIN_PREFIX)===FALSE) {
        //autoload only plugin classes
        return;
    }

    $h=opendir($root);
    while($file=readdir($h)) {
        if(is_dir($root.$file) && $file != '.' && $file != '..') {
            irp_autoload_root($root.$file, $class);
        } elseif(file_exists($root.$name.'.php')) {
            include_once($root.$name.'.php');
        } elseif(file_exists($root.$class.'.php')) {
            include_once($root.$class.'.php');
        }
    }
}
function irp_include_php($root) {
    $h=opendir($root);
    $slash=substr($root, strlen($root)-1);
    if($slash!='/' && $slash!='\\') {
        $root.='/';
    }

    while($file=readdir($h)) {
        if(is_dir($root.$file) && $file != '.' && $file != '..'){
            irp_include_php($root.$file);
        } elseif(strlen($file)>5) {
            $ext='.php';
            $length=strlen($ext);
            $start=$length*-1; //negative
            if(strcasecmp(substr($file, $start), $ext)==0) {
                include_once($root.$file);
            }
        }
    }
}