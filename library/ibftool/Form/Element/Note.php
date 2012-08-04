<?php
class ibftool_Form_Element_Note extends Zend_Form_Element_Xhtml  implements ibftool_Form_Element_Interface_Questionnaire
{
    function init() {
    	$this->required = false;
	}
	
	public function setQuestion($question) {
		$this->clearDecorators();
		$this->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $question->payout)));
		if ($question->style == "bold") {
			$this->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt', 'class' => "note_bold"));
		} else {
			$this->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt', 'class' => "note"));
		}
	}
}