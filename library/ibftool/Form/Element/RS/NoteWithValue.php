<?php
class ibftool_Form_Element_RS_NoteWithValue extends Zend_Form_Element implements ibftool_Form_Element_Interface_Questionnaire {
	
	function init() {
		
	}
	
	public function setQuestion($question) {
		$answer = $question->getAnswerForQuestionById($question->style);
		$this->clearDecorators();
		$this->addDecorator("Label" ,array("escape"=>false));
		$this->addDecorator("DtDdWrapper");
		$this->setLabel(str_replace("#", "<b>" . $answer . "</b>", $question->text));
	}
}
