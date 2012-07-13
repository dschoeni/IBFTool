<?php
class Administration_ModuleController extends Zend_Controller_Action {

	public function indexAction() {
		$available_modules = new Modules();
		$available_modules = $available_modules->fetchAll();
		$this->view->assign("available_modules", $available_modules);
	}

}