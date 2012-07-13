<?php
class ibftool_Controller_Action extends Zend_Controller_Action {
	public function init() {
		parent::init();
		
		$this->_redirector = $this->_helper->getHelper('Redirector');
		$this->_redirector->setUseAbsoluteUri(true);
	}

	protected function getModelRowObject($table) {
		$id = (int)$this->_request->getParam('id');
		$result = $table->find($id);
		return $result->current();
	}
}