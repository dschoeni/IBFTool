<?php
class Administration_InvestmentgameassetsController extends Zend_Controller_Action {

	public function indexAction() {

	}



	public function resetAction() {
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->_getParam("id");

		if (!empty($id)) {
			$results = new InvestmentGameAssets_Rounds();
			$where = $results->getAdapter()->quoteInto('treatments_id = ?', $id);
			$results->delete($where);
		}

	}

}