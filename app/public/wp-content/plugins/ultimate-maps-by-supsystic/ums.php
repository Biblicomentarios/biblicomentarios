<?php
/**
 * Plugin Name: Ultimate Maps by Supsystic
 * Plugin URI: https://supsystic.com/plugins/ultimate-maps/
 * Description: All in One
 * Version: 1.2.7
 * Author: supsystic.com
 * Author URI: http://supsystic.com
 * Text Domain: ultimate-maps-by-supsystic
 * Domain Path: /languages
 **/
	/**
	 * Base config constants and functions
	 */
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
	/**
	 * Connect all required core classes
	 */
    importClassUms('dbUms');
    importClassUms('installerUms');
    importClassUms('baseObjectUms');
    importClassUms('moduleUms');
    importClassUms('modelUms');
    importClassUms('viewUms');
    importClassUms('controllerUms');
    importClassUms('helperUms');
    importClassUms('dispatcherUms');
    importClassUms('fieldUms');
    importClassUms('tableUms');
    importClassUms('frameUms');
	/**
	 * @deprecated since version 1.0.1
	 */
    importClassUms('langUms');
    importClassUms('reqUms');
    importClassUms('uriUms');
    importClassUms('htmlUms');
    importClassUms('responseUms');
    importClassUms('fieldAdapterUms');
    importClassUms('validatorUms');
    importClassUms('errorsUms');
    importClassUms('utilsUms');
    importClassUms('modInstallerUms');
  	importClassUms('installerDbUpdaterUms');
  	importClassUms('dateUms');
	/**
	 * Check plugin version - maybe we need to update database, and check global errors in request
	 */
    installerUms::update();
    errorsUms::init();
    /**
	 * Start application
	 */
    frameUms::_()->parseRoute();
    frameUms::_()->init();
    frameUms::_()->exec();

	//var_dump(frameUms::_()->getActivationErrors()); exit();
