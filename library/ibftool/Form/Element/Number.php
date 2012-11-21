<?php
class ibftool_Form_Element_Number extends Zend_Form_Element_Text implements ibftool_Form_Element_Interface_Questionnaire {

	function init() {
		$this->addValidator(new Zend_Validate_Float(new Zend_Locale('en_US')));
		$this->addErrorMessage("Bitte nur Zahlen eintragen. Um Kommazahlen einzugeben bitte einen Punkt verwenden: z.B. 1.5 (nicht 1,5)");
	}
	
	public function setQuestion($question) {
		$this->setValue($question->getAnswerForQuestion());
	}
	
}