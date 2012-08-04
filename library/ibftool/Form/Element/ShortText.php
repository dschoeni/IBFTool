<?
class ibftool_Form_Element_ShortText extends Zend_Form_Element_Text implements ibftool_Form_Element_Interface_Questionnaire
{
	public function init() {
	}
	
	public function setQuestion($question) {
		$this->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));
		
		$stringLengthValidator = new Zend_Validate_StringLength(0,25);
		$stringLengthValidator->setMessage("Bitte überschreiten sie die maximale Länge von 25 Zeichen nicht.", "stringLengthTooLong");
		
		$notEmptyValidator = new Zend_Validate_NotEmpty();
		$notEmptyValidator->setMessage("Dieses Feld darf nicht leer sein.");
		
		$this->addValidators(array($stringLengthValidator, $notEmptyValidator));
		$this->setValue($question->getAnswerForQuestion());
	}
}