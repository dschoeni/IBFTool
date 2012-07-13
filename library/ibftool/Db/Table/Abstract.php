<?php
class ibftool_Db_Table_Abstract extends Zend_Db_Table_Abstract {

	protected function _setupTableName() {
		parent::_setupTableName();
		
		$config = Zend_Registry::get('config')->db;
        $prefix = $config->table_prefix;
        
        $this->_name = $prefix . '_' . $this->_name; 
	}
}
?>