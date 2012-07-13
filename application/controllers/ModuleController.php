<?php
class ModuleController extends Zend_Controller_Action {
	public function indexAction() {
		$this->_redirect(Sbftool_Controller_Action_Helper_Treatment::getCurrentModule());
	}
}