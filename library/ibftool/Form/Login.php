<?php
class ibftool_Form_Login extends Zend_Form {
	
	public function __construct() {
		parent::__construct();
		
		$this->setMethod("post");
		$this->setName("loginform");
		
		$this->setElements(array(
				"email"	 => array("text", array("label" => "E-Mail:", "required" => true)),
				"password"	 => array("password", array("label" => "Passwort:", "required" => true)),
				"submit" => array("submit", array("label" => "Login")),
		));
		
		ibftool_Form_Helper::styleFormHorizontal($this);
	}
}
