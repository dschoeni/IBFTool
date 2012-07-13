<?php
class TreatmentController extends Zend_Controller_Action {

	public function preDispatch() {
		if (Sbftool_Controller_Action_Helper_Treatment::isTreatmentOngoing()) {
			$this->_redirect("module/");
		}
	}

	public function indexAction() {
		/*
		 * Get the Userobject
		*/
		$row = Zend_Auth::getInstance()->getIdentity();
		$row->setTable(new Users());

		/*
		 * Get all available Treatments for this User
		*/

		$select = $row->select();
		$select->where('completed = ?', 0);
		$select->where('public = ?', 1);

		$available = $row->findManyToManyRowset("Treatments", "TreatmentsHasUsers", null, null, $select);
		$this->view->assign("treatments", $available);

		$select = $row->select();
		$select->where('completed = ?', 1);

		$nonavailable = $row->findManyToManyRowset("Treatments", "TreatmentsHasUsers", null, null, $select);
		$this->view->assign("treatments_completed", $nonavailable);
	}

	public function loadAction() {
		if (is_null($this->_getParam("id"))) {
			$this->_redirect("treatment/");
		}

		$row = Zend_Auth::getInstance()->getIdentity();
		$row->setTable(new Users());

		$select = $row->select();
		$select->where('completed = ?', 0);

		$result = $row->findManyToManyRowset("Treatments", "TreatmentsHasUsers", null, null, $select);
		$id = $this->_getParam("id");

		$array = array();

		foreach($result as $row) {
			array_push($array, $row->id);
		}

		if (!in_array($id, $array)) {
			$this->_redirect("treatment/");
		} else {
			Sbftool_Controller_Action_Helper_Treatment::initialiseTreatment($id);
			$this->_redirect("module/");
		}
	}

	public function testAction() {
		$this->view->assign("current", Sbftool_Controller_Action_Helper_Treatment::getCurrentModule());
		$this->view->assign("modules", Sbftool_Controller_Action_Helper_Treatment::getModules());
		$this->view->assign("id", Sbftool_Controller_Action_Helper_Treatment::getID());
	}

	public function finAction() {
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_redirect("login/");
	}

	private function loadTreatment($id) {
		Sbftool_Controller_Action_Helper_Module::initialiseHelper($id);
		$this->_redirect("module/");
	}

}
