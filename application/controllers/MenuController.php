<?php
class MenuController extends Zend_Controller_Action {

	public function indexAction() {

		$this->_helper->viewRenderer->setResponseSegment('menu');
		$this->view->assign("isloggedin", false);

		$auth = Zend_Auth::getInstance();

		if ($auth->hasIdentity()) {
			if ($auth->getIdentity()->role == "admin") {
		  $this->view->assign("isAdmin", true);
			}
		}

	}
}