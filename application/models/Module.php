<?php
class Module extends Zend_Db_Table_Row {

	public function getConfig() {
		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;
		$config = $db->query("SELECT config FROM " . $prefix . '_' . "treatments_has_module WHERE module_id = $this->id");
		$row = $config->fetch();

		return $row["config"];
	}
}