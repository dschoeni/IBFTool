<?php
/**
 * @author Dominik Schöni
 * @copyright 2008
 */
 
//Header IE Fix
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

// Error Reporting
//error_reporting(E_ALL|E_STRICT);
//ini_set('display_errors', 'on');

 // Include Path
 
if (strpos($_SERVER["HTTP_HOST"], "elysian") !== false){
	$path = "C:/Apache22/htdocs/ibftool/";
} elseif (strpos($_SERVER["HTTP_HOST"], "84.75.38.246") !== false){
	$path = "C:/Apache22/htdocs/ibftool/";
} else {
	$path = "D:/Development/GitHub/IBFTool/";	
}

/*
 * 
 * 
 * 
 * 
 * Münster-MLA: Path-Variable hier konfigurieren, sollte zum "ibftool"-Ordner auf dem Server zeigen.
 * $path  "/";
 * 
 * 
 * 
 * 
 * 
 */

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR. $path. 'library' .PATH_SEPARATOR. $path . "application/models");

require_once 'Zend/Loader/Autoloader.php';
require_once 'ZendX/JQuery.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('ibftool_');
$autoloader->registerNamespace('PHPExcel_');
$autoloader->setFallbackAutoloader(true);

Zend_Session::start();			

$config = ibftool_Starter::start($path."application/config/db_config.ini");

// Zend Layout initialisieren
Zend_Layout::startMvc();

// Frontcontroller Instanz
$front = Zend_Controller_Front::getInstance();
$front->setControllerDirectory($path.'application/controllers');
$front->throwExceptions(true);

// BaseUrl setzen
//$front->setBaseUrl("/");

//Routing
//$front->getRouter()->addRoute('images', new Zend_Controller_Router_Route($config->route->images));
//$front->getRouter()->addRoute('imagesThumb', new Zend_Controller_Router_Route($config->route->imagesThumb));

//Menu
$front->registerPlugin(new Zend_Controller_Plugin_ActionStack());
$front->registerPlugin(new ibftool_Controller_Plugin_Menu());

//JQuery von ZenX
$view = new Zend_View();
$view->setEncoding("ISO-8859-1");
$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
$view->jQuery()->setLocalPath("/ibftool/_files/js/jquery.js");
$view->jQuery()->setUiLocalPath("/ibftool/_files/js/jquery-ui.js");
$view->jQuery()->enable();
$view->jQuery()->uiEnable();
 
$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$viewRenderer->setView($view);
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

$treatment = new ibftool_Controller_Action_Helper_Treatment();
Zend_Controller_Action_HelperBroker::addHelper($treatment);

//Cache deaktivieren
$cachepath = $path . '/cache';
$cache = Zend_Cache::factory(
         'Core',
         'File',
         array(),
         array(
            "cache_dir" => $cachepath
         ));
Zend_Locale::setCache($cache);
Zend_Translate::setCache($cache);

// Auth und Acl
$auth = Zend_Auth::getInstance();
$acl  = new ibftool_Controller_Plugin_Auth_Acl();
$front->registerPlugin(new ibftool_Controller_Plugin_Auth_AccessControl($auth, $acl));
$front->setParam('auth', $auth);

$front->dispatch();

