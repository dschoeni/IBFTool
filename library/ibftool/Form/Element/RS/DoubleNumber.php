<?php
class ibftool_Form_Element_RS_DoubleNumber extends Zend_Form_Element_Text implements ibftool_Form_Element_Interface_Questionnaire {
	
	function init() {
		
	}
	
	public function setQuestion($question) {
		$this->setDecorators(array(new ibftool_Form_Decorators_RS_DoubleNumber(), new ibftool_Form_Decorators_Payout(array("payout" => $question->payout)), new Zend_Form_Decorator_Errors()));
		$this->addValidator(new ibftool_Validate_DNumber_NotEmpty());
		
		$this->addErrorMessage("Bitte nur Zahlen eintragen. Um Kommazahlen einzugeben bitte einen Punkt verwenden: z.B. 1.5 (nicht 1,5)");
		
		$rowset = $question->getAnswersForQuestion($question->id);
		$array = array();
		
		if ($rowset != null) {
			foreach($rowset as $row) {
				array_push($array, $row->questionnaire_answer);
			}
		}
		
		$this->setValue($array);
	}
}
