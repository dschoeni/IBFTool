<?php
class Treatment extends Zend_Db_Table_Row {

	public function getUsersCompletedCount() {
		$thu = new TreatmentsHasUsers();
		$thu = $thu->fetchAll(array("treatments_id = ?" => $this->id, "completed = ?" => 1));

		return $thu->count();
	}

	public function setPublic() {
		$this->public = 1;
		$this->save();
	}

	public function setLocked() {
		$this->public = 0;
		$this->save();
	}

	public function generateExcelSheet() {
		$used_modules = new Modules();
		$select = $used_modules->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)->where('sbftool_treatments_has_module.treatments_id = ?', $id)->join('sbftool_treatments_has_module', 'sbftool_treatments_has_module.module_id = sbftool_module.id');
		$used_modules = $used_modules->fetchAll($select);

		$this->_helper->actionStack("download", "admin_treatment", "default", array("id" => $id));

		foreach($used_modules as $used_module) {
			$this->_helper->actionStack("result", "admin_" . $used_module->prefix, "default", array("id" => $id));
		}
	}
}