<?php
class User extends Zend_Db_Table_Row {

	private $_treatment;

	/**
	 * Inserts a new User, sets the date and time of his registration.
	 */

	protected function _insert() {
		parent::_insert();

		$db = Zend_Db_Table::getDefaultAdapter();
		$this->registration_time = date("Y-m-d H:i:s");
	}

	protected function _postInsert() {
		parent::_postInsert();

		//self::assignToTreatment();
	}

	protected function assignToTreatment() {
		$treatments_table = new Treatments();
		$treatments = $treatments_table->fetchAll(array("public = ?" => 1), "ORDER asc", 4);

		// Determine the Treatment with the least participation
		foreach($treatments as $treatment) {
			if ($least == null) {
				$least = $treatment;
			}

			if ($least->usercount > $treatment->usercount) {
				$least = $treatment;
			}
		}

		$treatment_row = $least;
		$treatment_row->usercount++;
		$treatment_row->save();

		$thu = new TreatmentsHasUsers();

		$data = array(
				'treatments_id'	=> $treatment_row->id,
				'users_id' => $this->id,
				'completed' => 0
		);

		$row = $thu->createRow($data);
		$row->save();

		$this->_refresh();

		if ($treatment_row->id == 2) {
			$this->grp = 1;
		} elseif ($treatment_row->id == 3) {
			$this->grp = 2;
		} elseif ($treatment_row->id == 4) {
			$this->grp = 3;
		} elseif ($treatment_row->id == 5) {
			$this->grp = 4;
		}

		$this->save();
	}

	/**
	 * Returns ID of next module.
	 * @return: integer
	 */

	public function getTreatments() {
		$treatments = new TreatmentRowSet();
		$treatments->findDependentRowSet(Zend_Auth::getInstance()->getIdentity());

		return $treatments;
	}

	/**
	 * Returns ID of next module.
	 * @return: integer
	 */

	public function getNextModule() {
		$db = Zend_Db_Table::getDefaultAdapter();

		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;

		$completed_modules = $db->query("SELECT module_id FROM " . $prefix . '_' . "users_has_module WHERE users_id = $this->id AND completed = 1");

		return $completed_modules->rowCount() + 1;
	}

	public function verify() {
		$this->verified = 1;
		$this->save();
	}

}