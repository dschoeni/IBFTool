<?php
class ibftool_Form_Element_CountryCode extends Zend_Form_Element_Select implements ibftool_Form_Element_Interface_Questionnaire {
	
	function init() {
		$this->setRequired(true);
		$this->addErrorMessage("Bitte wählen Sie ein Land aus");

		$countries = new Countries();
		$countriesRowSet = $countries->fetchAll();
		
		$this->addMultiOption("", "");
		
		foreach($countriesRowSet as $country) {
			$this->addMultiOption($country->code, $country->name);
		}
	}
	
	public function setQuestion($question) {
		$this->setValue($question->getAnswerForQuestion());
	}
}