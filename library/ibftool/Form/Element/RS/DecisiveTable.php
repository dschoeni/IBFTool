<?php
class ibftool_Form_Element_RS_DecisiveTable extends Zend_Form_Element_Radio implements ibftool_Form_Element_Interface_Questionnaire
{
	function init() {
		
	}
	
	public function setQuestion($question) {
		$this->setIgnore(true);
		$configarray = explode("/", $question->style);
		$ecu = $configarray[0];
		$firstColumn = $configarray[1];
		$secondColumn = $configarray[2];
		$thirdColumn = $configarray[3];
		
		$values = explode("#", $secondColumn);
		
		for($i = 0; $i < count(explode("#", $firstColumn)); $i++) {
			$this->addMultiOption($i, $values[$i]);
		}
		
		$this->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
		$this->setValue($question->getAnswerForQuestion());
		$this->setDecorators(array(new ibftool_Form_Decorators_RS_DecisiveTable(array("id" => $question->id, "ecu" => $ecu, "firstColumn" => $firstColumn, 'secondColumn' => $secondColumn, 'thirdColumn' => $thirdColumn)), new Zend_Form_Decorator_Errors(), new ibftool_Form_Decorators_Payout(array("payout" => $question->payout))));
		
		$question->addToForm($this);
		
		$hidden = new Zend_Form_Element_Hidden("hiddenrs");
		$hidden->setName($question->id);
		$hidden->setRequired(true);
		$hidden->setValue($question->getAnswerForQuestion());
		$question->addToForm($hidden);
	}
}