<?
class ibftool_Form_Element_SingleChoiceInRowCaption extends Zend_Form_Element_Radio
{
	function init() {
		$this->setSeparator("");
		$this->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
	}
	
	public function setQuestion($question) {
		$configarray = explode("/", $question->style);
		$firstAnswer = $configarray[0];
		$lastAnswer = $configarray[1];
		$leftString = $configarray[2];
		$rightString = $configarray[3];

		for($firstAnswer; $firstAnswer <= $lastAnswer; $firstAnswer++) {
			$this->addMultiOption($firstAnswer, $firstAnswer);
		}
		
		$this->setValue($question->getAnswerForQuestion());
		$this->setDecorators(array(new ibftool_Form_Decorators_SingleChoiceCaptioned(array("leftCaption"=>$leftString, 'rightCaption' => $rightString)), new Zend_Form_Decorator_Errors(), new ibftool_Form_Decorators_Payout(array("payout" => $question->payout))));
		
	}
}