<?php
class ibftool_Form_Element_Number1To100 extends Zend_Form_Element_Text implements ibftool_Form_Element_Interface_Questionnaire {

	function init() {
		$this->addValidator(new Zend_Validate_Float(new Zend_Locale('en_US')));
		$this->addValidator(new Zend_Validate_Between(0, 100));
		$this->addErrorMessage("Bitte nur Zahlen zwischen 1 und 100 eintragen. Um Kommazahlen einzugeben bitte einen Punkt verwenden: z.B. 1.5 (nicht 1,5)");
	}
	
	public function setQuestion($question) {
		$this->setValue($question->getAnswerForQuestion());
	}
	
}