<?php
class IndexController extends Zend_Controller_Action {

	public function indexAction() {
		$this->_redirect("treatment/");
	}

	public function finAction() {
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_redirect("login/");
	}

}
