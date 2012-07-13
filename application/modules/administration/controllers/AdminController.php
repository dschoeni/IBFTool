<?php
class AdminController extends Zend_Controller_Action {

	public function indexAction() {
		/*
		 * Get the registered Modules
		*/

		$treatments = new Treatments();
		$treatments = $treatments->fetchAll();

		$this->view->assign("treatments", $treatments);
	}

	public function noaccessAction() {

	}

	public function multiplechoiceAction() {

	}
}
