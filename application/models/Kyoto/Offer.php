<?php
class Kyoto_Offer extends Zend_Db_Table_Row {
	
	protected function _insert() {
		parent::_insert();
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$this->created = date("Y-m-d H:i:s");
	}
	
}