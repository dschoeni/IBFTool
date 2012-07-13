<?php
class Administration_ToolController extends Zend_Controller_Action {

	public function indexAction() {
		$this->view->assign("config", Zend_Registry::getInstance()->get("config"));
	}


}
