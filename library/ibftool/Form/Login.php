<?php
class ibftool_Form_Login extends Zend_Form {
	
	public function __construct() {
		parent::__construct();

		$this->setMethod("post");
		$this->setName("loginform");
		
		$email = new Zend_Form_Element_Text("email");
		$email->setLabel("E-Mail");
		$email->setRequired(true);
		$this->addElement($email);
		
		$password = new Zend_Form_Element_Password("password");
		$password->setLabel("Passwort");
		$password->setRequired(true);
		$this->addElement($password);
		
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel("Login");
		$submit->removeDecorator('DtDdWrapper');
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}
?>