<?php
class ibftool_Form_Administration_Question extends ibftool_Form {

	public function __construct() {
		parent::__construct();

		$this->setMethod("post");
		$this->setName("question");

		$element = new ibftool_Form_Element_Hidden("id");
		$element->setRequired(false);
		$element->setIgnore(true);
		$this->addelement($element);
		
		$element = new Zend_Form_Element_Select("typ");
		$element->addMultiOptions(
				array(
						"age" => "Age",
						"countrycode" => "CountryCode",
						"multiplechoice" => "Multiple Choice",
						"note" => "Note",
						"number1to100" => "Number 1 To 100",
						"number" => "Number",
						"select" => "Select",
						"shorttext" => "Textanswer (short)",
						"singlechoice" => "SingleChoice",
						"singlechoiceinrowcaption" => "SingleChoice (in Row, with Caption)",
						"singlechoiceinrow" => "SingleChoice (in Row)",
						"singlechoicerandom" => "SingleChoice - Randomly Shuffled",
						"img" => "Image",
						"rs_decisivetable" => "RS - DecisiveTable",
						"rs_doublenumber" => "RS - Double Number",
						"rs_notewithvalue" => "RS - NoteWithValue",
						"rs_notewithvalueinline" => "RS - NoteWithValueInline",
						"rs_notewithvaluetid" => "RS - NoteWithValueTID",
				));
		
		$element->setLabel("Typ");
		$this->addElement($element);

		$element = new Zend_Form_Element_Select("payout");
		$element->addMultiOptions(
				array(
						"1" => "Ja",
						"0" => "Nein",
				));
		
		$element->setLabel("Auszahlungsrelevante Frage");
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text("text");
		$element->setLabel("Fragentext / Label");
		$this->addElement($element);
			
		$element = new Zend_Form_Element_Text("style");
		$element->setLabel("Weitere Konfiguration");
		$this->addElement($element);

		$submit = new Zend_Form_Element_Submit("Weiter", array("label" => "Speichern",  "class" => "btn btn-primary"));
		$submit->setIgnore(true);
		$this->addElement($submit);
	}

	public function persistData() {
		parent::persistCreateOrUpdate(new Questionnaire_Questions());
	}

}
?>