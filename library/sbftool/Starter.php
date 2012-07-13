<?php
class Sbftool_Starter {
	public static function start($config_file, $section = null) {
		if ($section == null) {
			if (!empty($_SERVER["HTTP_HOST"]) && ($_SERVER["HTTP_HOST"] == "elysian" || substr($_SERVER["HTTP_HOST"], 0, 4) == "192.")) {
				$section = "developer";
			} else if (!empty($_SERVER["HTTP_HOST"]) && ($_SERVER["HTTP_HOST"] == "fotocomm" || substr($_SERVER["HTTP_HOST"], 0, 4) == "139.")) {
				$section = "labor";
			} elseif (empty($_SERVER["HTTP_HOST"]) || $_SERVER["HTTP_HOST"] == "localhost") {
				$section = "laptop";
			} elseif ( $_SERVER["HTTP_HOST"] == "theelysianfields.dyndns.org") {
				$section = "internet";
			} elseif ( $_SERVER["HTTP_HOST"] == "84.75.38.246") {
				$section = "developer";
			} else {
				$section = "production";
			}
		}
		
		$config = new Zend_Config_Ini($config_file, $section);
		Zend_Registry::set('config', $config);

		$db = Zend_Db::factory($config->database);
		
		Zend_Db_Table_Abstract::setDefaultAdapter($db);

		Zend_Registry::set("dbAdapter", $db);

		return $config;
	}
}
