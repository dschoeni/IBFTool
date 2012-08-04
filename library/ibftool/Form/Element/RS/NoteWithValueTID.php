<?php
class ibftool_Form_Element_RS_NoteWithValueTID extends Zend_Form_Element implements ibftool_Form_Element_Interface_Questionnaire {
	
	function init() {
		
	}
	
	public function setQuestion($question) {
		$array = explode("/", $question->style);
		
		$result = new Questionnaire_Results();
		$row = $result->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $array[0], "treatments_id = ?" => $array[1]));
		
		$this->clearDecorators();
		$this->addDecorator("DtDdWrapper");
		$this->addDecorator("Label" ,array("escape"=>false));
		$this->setLabel(str_replace("#", "<b>" . round($row->questionnaire_answer) . "</b>", $question->text));
	}
}
