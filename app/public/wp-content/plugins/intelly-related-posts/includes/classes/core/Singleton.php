<?php
class IRP_Singleton {
    var $Lang;
    var $Utils;
    var $Form;
    var $Check;
    var $Options;
    var $Manager;
    var $Log;
    var $Cron;
    var $Tracking;
    var $Tabs;
    var $Plugin;
    var $HtmlTemplate;

    function __construct() {
        $this->Lang=new IRP_Language();
        $this->Utils=new IRP_Utils();
        $this->Form=new IRP_Form();
        $this->Check=new IRP_Check();
        $this->Options=new IRP_AppOptions();
        $this->Manager=new IRP_Manager();
        $this->Log=new IRP_Logger();
        $this->Cron=new IRP_Cron();
        $this->Tracking=new IRP_Tracking();
        $this->Tabs=new IRP_Tabs();
        $this->Plugin=new IRP_Plugin();
        $this->HtmlTemplate=new IRP_HtmlTemplate();
    }
    function init() {
        $this->Lang->load('irp', IRP_PLUGIN_ROOT.'languages/Lang.txt');
        $this->HtmlTemplate->load(IRP_PLUGIN_ROOT.'assets/templates/styles.html');
        $this->Tabs->init();
        $this->Cron->init();
    }
}