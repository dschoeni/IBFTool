<?php

/**
 * @author thisisme
 * @copyright 2008
 */

class Sbftool_Controller_Plugin_Db extends Zend_Controller_Plugin_Abstract {
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$db = Zend_Db::factory(Zend_Registry::get('config')->database);
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		
		Zend_Registry::set("dbAdapter", $db);
	}
}
