<?php
class ibftool_Form_Questionnaire extends ibftool_Form_Abstract {

	public function __construct() {
		parent::__construct();
		$this->setAttrib('id', 'question');
		$this->setMethod(Zend_Form::METHOD_POST);
	}
	
}
