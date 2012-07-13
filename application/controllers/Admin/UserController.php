<?php
class Admin_UserController extends Zend_Controller_Action {

	public function indexAction() {
		$users = new Users();
		$users = $users->fetchAll();

		$this->view->assign("users", $users);
	}

	public function treatmentAction() {
		$treatments = new Treatments();
		$this->view->assign("treatments", $treatments->fetchAll());

	}

	public function saveAction() {
		$id = $this->_getParam("id");

		if (!empty($_POST) && !empty($id)) {
				
			$thm = new TreatmentsHasModule();
			$rows = $thm->fetchAll(array("treatments_id = ?" => $id));
				
			foreach($rows as $row) {
				$row->delete();
			}
				
			$i = 0;
				
			foreach($_POST as $key => $value) {
				echo substr($key, 2) . " | " . $value . "<br />";

				$data = array(
						'treatments_id'	=> $id,
						'module_id' => substr($key, 2),
						'config' =>  $value,
						'order' => $i
				);

				$row = $thm->createRow($data);
				$row->save();

				$i++;
			}
				
		}
		$this->_helper->redirector("edit", "admin_treatment", "default", array("id" => $id));
	}

}