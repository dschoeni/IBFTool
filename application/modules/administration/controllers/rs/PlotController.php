<?php
class Administration_RS_PlotController extends Zend_Controller_Action {

	public function resetAction() {
		$this->_helper->viewRenderer->setNoRender(true);
	}

	public function resultAction() {
		$this->_helper->viewRenderer->setNoRender(true);
	}

}
