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
						"Age" => "Age",
						"CountryCode" => "CountryCode",
						"MultipleChoice" => "Multiple Choice",
						"Note" => "Note",
						"Number1To100" => "Number 1 To 100",
						"Select" => "Select",
						"ShortText" => "Textanswer (short)",
						"SingleChoice" => "SingleChoice",
						"SingleChoiceInRowCaption" => "SingleChoice (in Row, with Caption)",
						"RS_DecisiveTable" => "RS - DecisiveTable",
						"RS_DoubleNumber" => "RS - Double Number",
						"RS_NoteWithValue" => "RS - NoteWithValue",
						"RS_NoteWithValueInline" => "RS - NoteWithValueInline",
						"RS_NoteWithValueTID" => "RS - NoteWithValueTID",
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