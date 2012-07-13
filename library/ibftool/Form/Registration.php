<?php
class ibftool_Form_Registration extends Zend_Form {
	
	public function __construct() {
		parent::__construct();

		$this->setMethod("post");
		$this->setName("registrationform");
		
		$this->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . "/registration/");
		
		$email = new Zend_Form_Element_Text("email");
		$email->setLabel("E-Mail Adresse");
		$email->setRequired(true);
		$email->addErrorMessage("Diese E-Mail Adresse ist ungültig oder wird bereits verwendet.");
		$email->addValidator(new Zend_Validate_Db_NoRecordExists("ibftool_users", "email"));	
		$email->addValidator(new Zend_Validate_EmailAddress());	
		$this->addElement($email);
		
		/*
		
		$password = new Zend_Form_Element_Password("password");
		$password->setLabel("Passwort");
		$password->setRequired(true);
		$password->addValidator(new Zend_Validate_StringLength(8, 100));
		$password->addErrorMessage("Bitte geben Sie ein gültiges Passwort mit minimal 8 Zeichen an.");
		$this->addElement($password);
		
		$password_check = new Zend_Form_Element_Password("password_check");
		$password_check->setLabel("Passwort wiederholen");
		$password_check->addErrorMessage("Ihr Passwort ist nicht identisch");
		$password_check->setRequired(true);
		$this->addElement($password_check);
		
		*/
		
		$submit = new Zend_Form_Element_Submit("submit");
		$submit->setLabel("Registrieren");
		$submit->removeDecorator('DtDdWrapper');
		$submit->setIgnore(true);
		$this->addElement($submit);
	}
}
?>