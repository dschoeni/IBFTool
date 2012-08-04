<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	protected function _initAutoloader() {
		require_once 'Zend/Loader/Autoloader.php';
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->setFallbackAutoloader(true);
	}

	public function _initConfig() {
		//Clubtix_Config::setConfig($this->getOptions());
	}

	protected function _initErrorHandler() {
		//set_error_handler(array("Clubtix_Error", "handler"));
	}

	protected function _initCachemanager() {
		//Zend_Registry::set("Zend_Cache_Manager", $this->getPluginResource('cachemanager')->getCacheManager());
	}

	protected function _initViewPaths() {
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('HTML5');
	}

}