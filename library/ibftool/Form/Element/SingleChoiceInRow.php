<?
class ibftool_Form_Element_SingleChoiceInRow extends Zend_Form_Element_Radio implements ibftool_Form_Element_Interface_Questionnaire
{
	function init() {
		$this->setSeparator("");
		$this->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
	}
	
	public function setQuestion($question) {

		$this->setName($question->id);
		$this->setLabel($question->text);
		$this->setSeparator("");
		$this->setRequired(true);
		
		if ($this->style == "bold") {
			$this->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow_bold"));
		} else {
			$this->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow"));
		}
		
		$this->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
		
		$this->addMultiOption(0, 0);
		$this->addMultiOption(1, 1);
		$this->addMultiOption(2, 2);
		$this->addMultiOption(3, 3);
		$this->addMultiOption(4, 4);
		$this->addMultiOption(5, 5);
		
		$this->setValue($question->getAnswerForQuestion());
		
	}
}