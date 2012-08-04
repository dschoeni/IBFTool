<?
class ibftool_Form_Element_Age extends ibftool_Form_Element_Text implements ibftool_Form_Element_Interface_Questionnaire
{
	public $helper = 'formShortText';

	public function __construct($spec, $options = null) {
		parent::__construct($spec, $options);

		$betweenValidator = new Zend_Validate_Between(16, 99);
		$betweenValidator->setMessage("Bitte nur Zahlen eintragen. Das Mindestalter betr&auml;gt 16 Jahre.", "notBetween");

		$notEmptyValidator = new Zend_Validate_NotEmpty();
		$notEmptyValidator->setMessage("Dieses Feld darf nicht leer sein.");
		
		$this->addValidators(array($betweenValidator, $notEmptyValidator));

	}
	
}