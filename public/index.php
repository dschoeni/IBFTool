<?php
/**
 * @author Dominik Schöni
 * @copyright 2008
 */

//Header IE Fix
//header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

// Error Reporting
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', 'on');

// Include Path
if (strpos($_SERVER["HTTP_HOST"], "ibftoollaptop") !== false) {
	$path = "D:/GitHub/IBFTool/";
} elseif (strpos($_SERVER["HTTP_HOST"], "theelysianfields") > 0) {
	ini_set('include_path', " ");
	$path = "/var/www/ibf.theelysianfields.ch/ibftool/";
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

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . $path. 'library' .PATH_SEPARATOR. $path . "application/models");
require_once 'Zend/Loader/Autoloader.php';
require_once 'ZendX/JQuery.php';

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('ibftool_');
$autoloader->registerNamespace('PHPExcel_');
$autoloader->setFallbackAutoloader(true);

Zend_Session::start();

$config = ibftool_Starter::start($path . "application/config/db_config.ini");

// Zend Layout initialisieren
Zend_Layout::startMvc();

// Frontcontroller Instanz
$front = Zend_Controller_Front::getInstance();
$front->throwExceptions(true);

// BaseUrl setzen

//Routing
//$front->getRouter()->addRoute('images', new Zend_Controller_Router_Route($config->route->images));
//$front->getRouter()->addRoute('imagesThumb', new Zend_Controller_Router_Route($config->route->imagesThumb));

//Menu
$front->registerPlugin(new Zend_Controller_Plugin_ActionStack());

$front->addModuleDirectory($path . 'application/modules');

$view = new Zend_View();
$view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
$view->addHelperPath($path . "library/ibftool/View/Helper/", "ibftool_View_Helper");

//JQuery von ZenX
$view->jQuery()->setLocalPath("/js/jquery-1.7.2.min.js");
$view->jQuery()->setUiLocalPath("/js/jquery-ui-1.8.20.custom.min.js");
$view->jQuery()->enable();
$view->jQuery()->uiEnable();

Zend_Layout::getMvcInstance()->setLayoutPath($path . "application/layouts/");

$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$viewRenderer->setView($view);
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

$treatment = new ibftool_Controller_Action_Helper_Treatment();
Zend_Controller_Action_HelperBroker::addHelper($treatment);

$contextHelper = new ibftool_Controller_Action_Helper_CompleteContext();
Zend_Controller_Action_HelperBroker::addHelper($contextHelper);

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


