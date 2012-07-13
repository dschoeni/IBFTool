<?php
class Administration_StaticController extends Zend_Controller_Action {

	public function resetAction() {
		$this->_helper->viewRenderer->setNoRender(true);
	}

	public function resultAction() {
		$this->_helper->viewRenderer->setNoRender(true);
	}

}
