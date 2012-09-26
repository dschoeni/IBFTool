<?php
class Kyoto_Session extends Zend_Db_Table_Row {
	
	protected function _insert() {
		parent::_insert();
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$this->created = date("Y-m-d H:i:s");
	}
	
	protected function _update() {
		parent::_update();
	
		/*
		$db = Zend_Db_Table::getDefaultAdapter();
		$this->timestamp = date("Y-m-d H:i:s");
		*/
	}
	
}