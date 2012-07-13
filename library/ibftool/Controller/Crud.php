<?php
abstract class ibftool_Controller_Crud extends Zend_Controller_Action {
	protected abstract function getTable();
	protected abstract function getCreateFormClass();
	protected abstract function getUpdateFormClass();
	protected function getOrder() {
		return "name";
	}

	/**
	 * Gibt das Row Objekt zurück, welches mithilfe vom ID Parameter gefunden wurde
	 *
	 * @param unknown_type $modelObject
	 * @return unknown
	 */
	protected function getRowById($modelObject, $paramName = "id") {
		if (!$this->_hasParam($paramName)) {
			return null;
		}

		$rowSet = $modelObject->find($this->_getParam($paramName));

		if (count($rowSet) == 0) {
			return null;
		}

		return $rowSet->current();
	}
	
	public function indexAction() {
		$this->view->assign("objects", $this->getTable()->fetchAll(null, $this->getOrder()));
	}

	public function updateAction() {
		$row = $this->getRowById($this->getTable());

		if (!$row) {
			$this->notFound();
			return;
		}

		$this->view->assign("row", $row);

		$form = $this->getUpdateFormClass();
		$this->view->assign("form", new $form($row));
		$this->_helper->viewRenderer('validate');
	}

	public function createAction() {
		$form = $this->getCreateFormClass();
		$this->view->assign("form", new $form());
		$this->_helper->viewRenderer('validate');
	}

	public function validateAction() {
		$key = "id";

		if ($this->_hasParam($key) && $this->_getParam($key) != "") {
			$row = $this->getRowById($this->getTable());

			if (!$row) {
				$this->notFound();
				return;
			}

			$formClass = $this->getUpdateFormClass();
			$form = new $formClass($row);
		} else {
			$formClass = $this->getCreateFormClass();
			$form = new $formClass();
		}

		if (!$form->isValid($_POST)) {
			if (is_a($form, $this->getUpdateFormClass())) {
				$this->view->assign("row", $row);
			}

			$this->view->assign("form", $form);
			return false;
		}

		$row = $form->persistData();

		$this->_redirect($this->getRequest()->getControllerName() . "/update/".$key."/".$row->$key);
	}

	public function deleteAction() {
		$row = $this->getRowById($this->getTable());

		if (!$row) {
			$this->notFound();
			return;
		}

		$row->delete();

		$this->_redirector = $this->_helper->getHelper('Redirector');
		$this->_redirector->setGoto("index");
	}
}
