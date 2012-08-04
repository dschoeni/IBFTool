<?php
class Administration_TreatmentController extends Zend_Controller_Action {

	public function indexAction() {
		/*
		 * Get the registered Modules
		*/

		$treatments = new Treatments();
		$treatments = $treatments->fetchAll();

		$this->view->assign("treatments", $treatments);
	}

	public function editAction() {
		$id = $this->_getParam("id");
		$treatments = new Treatments();
		$treatment = $treatments->find($id);
		$this->view->assign("treatment", $treatment->current());

		$available_modules = new Modules();
		$available_modules = $available_modules->fetchAll();
		$this->view->assign("available_modules", $available_modules);

		$used_modules = new Modules();
		$select = $used_modules->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)->where('ibftool_treatments_has_module.treatments_id = ?', $id)->join('ibftool_treatments_has_module', 'ibftool_treatments_has_module.module_id = ibftool_module.id')->order("ibftool_treatments_has_module.order ASC");

		$used_modules = $used_modules->fetchAll($select);

		$this->view->assign("used_modules", $used_modules);
	}

	public function saveAction() {
		$id = $this->_getParam("id");

		if (!empty($_POST) && !empty($id)) {

			$thm = new TreatmentsHasModule();
			$rows = $thm->fetchAll(array("treatments_id = ?" => $id));

			$treatments = new Treatments();
			$treatment = $treatments->find($id);
			$treatment = $treatment->current();
			$treatment->userlimit = $_POST["userlimit"];
			$treatment->order = $_POST["order"];
			$treatment->name = $_POST["name"];
			$treatment->description = $_POST["description"];

			unset($_POST["userlimit"]);
			unset($_POST["description"]);
			unset($_POST["order"]);
			unset($_POST["name"]);

			$treatment->save();

			foreach($rows as $row) {
				$row->delete();
			}

			$i = 0;
				
			foreach($_POST as $key => $value) {
				foreach($value as $val) {
					$data = array(
							'treatments_id'	=> $id,
							'module_id' => $key,
							'config' =>  $val,
							'order' => $i
					);
						
					$row = $thm->createRow($data);
					$row->save();
						
					$i++;
				}
			}
		}
		
		$this->_redirect("/administration/treatment/edit/" . $id);
	}

	public function resetAction() {
		$id = $this->_getParam("id");

		if (!empty($id)) {

			$thu = new TreatmentsHasUsers();

			$data = array("completed" => 0);
			$row = $thu->update($data, array("treatments_id = ?" => $id));

			$used_modules = new Modules();
			$select = $used_modules->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
			$select->setIntegrityCheck(false)->where('ibftool_treatments_has_module.treatments_id = ?', $id)->join('ibftool_treatments_has_module', 'ibftool_treatments_has_module.module_id = ibftool_module.id');

			$used_modules = $used_modules->fetchAll($select);

			$this->_redirect("/administration/treatment/");

			foreach($used_modules as $used_module) {
				$this->_helper->actionStack("reset", "admin_" . $used_module->prefix, "default", array("id" => $id));
			}

		}

	}

	public function resultAction() {
		$id = $this->_getParam("id");

		$treatments = new Treatments();
		$treatment = $treatments->find($id);
		$treatment->current()->generateExcelSheet();
	}

	public function downloadAction() {

	}

	public function newAction() {
		$this->_helper->viewRenderer->setNoRender(true);
		$treatments = new Treatments();
		$lastrow = $treatments->fetchRow(null, "order desc");

		$data = array("name" => "Neues Treatment", "order" => $lastrow->order + 1, "userlimit" => "0");

		$row = $treatments->createRow($data);
		$row->save();

		$this->_redirect("/administration/treatment/");
	}

	public function redirectAction() {
		$this->_redirect("/administration/treatment/");
	}

	public function lockAction() {
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->_getParam("id");

		if (!empty($id)) {
			$treatments = new Treatments();
			$treatment = $treatments->find($id);
			$treatment = $treatment->current();
			$treatment->setLocked();
		}

		$this->_redirect("/administration/treatment/");
	}

	public function unlockAction() {
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->_getParam("id");

		if (!empty($id)) {
			$treatments = new Treatments();
			$treatment = $treatments->find($id);
			$treatment = $treatment->current();
			$treatment->setPublic();
		}

		$this->_redirect("/administration/treatment/");
	}

}