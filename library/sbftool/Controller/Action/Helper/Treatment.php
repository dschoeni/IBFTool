<?php
class Sbftool_Controller_Action_Helper_Treatment extends Zend_Controller_Action_Helper_Abstract {
	private static $treatment_session;
	
	public function preDispatch()
	{
		self::$treatment_session = new Zend_Session_Namespace('treatment');
	}
	
	public static function initialiseTreatment($id) {
		self::$treatment_session->unsetAll();
		
		self::$treatment_session->id = $id;
		self::$treatment_session->current = 0;
		
		// RANDOM NUMBER FOR IRONTRADER
		self::$treatment_session->random = rand(0,100);
		
		$config = Zend_Registry::get('config')->db;
        $prefix = $config->table_prefix;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = 	 $db->select()
						->from(array("thm" => $prefix . "_" . "treatments_has_module"), array('id', 'module_id', 'config'))
						->join(array("m" => $prefix . "_" . "module"), "m.id = thm.module_id", array('prefix'))
						->where("treatments_id = $id")
						->order("thm.order ASC");
		$result = $db->query($select);

		$array = array();
		
		foreach ($result as $row) {
			var_dump($row);
			array_push($array, array("id" => $row["module_id"], "config" => $row["config"], "prefix" => $row["prefix"], "thm_id" => $row["id"]));
		}
		
		self::$treatment_session->storage = $array;
	}
	
	public static function isTreatmentOngoing() {
		return (!is_null(self::$treatment_session->id));
	}
	
	public static function getCurrentModule() {
		return self::$treatment_session->storage[self::$treatment_session->current]["prefix"];
	}
	
	public static function getCurrentModuleId() {
		return self::$treatment_session->storage[self::$treatment_session->current]["thm_id"];
	}
	
	public static function getModules() {
		return self::$treatment_session->storage;
	}
	
	public static function getID() {
		return self::$treatment_session->id;
	}
	
	public static function getCurrentConfig() {
		return self::$treatment_session->storage[self::$treatment_session->current]["config"];
	}
	
	public static function getRandom() {
		return self::$treatment_session->random;
	}
	
	public static function completeCurrentModule() {
		self::$treatment_session->current++;
		
		if (self::$treatment_session->current >= count(self::$treatment_session->storage)) {
			$table = new TreatmentsHasUsers();
			$row = $table->update(array('completed' =>  1), array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => self::$treatment_session->id));
			
			self::$treatment_session->unsetAll();
		}
	}
	

}