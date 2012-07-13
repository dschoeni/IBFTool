<?php
class Questionnaire_Result extends Zend_Db_Table_Row {

	/**
	 * Inserts a new User, sets the date and time of his registration.
	 */

	protected function _insert() {
		parent::_insert();

		$db = Zend_Db_Table::getDefaultAdapter();
		$this->timestamp = date("Y-m-d H:i:s");
	}

	protected function _update() {
		parent::_update();

		$db = Zend_Db_Table::getDefaultAdapter();
		$this->timestamp = date("Y-m-d H:i:s");
	}

}