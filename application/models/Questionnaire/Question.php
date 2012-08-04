<?php
class Questionnaire_Question extends Zend_Db_Table_Row {
	
	private $_form;
	private $_answers = null;
	
	public function populateForm($form) {
		$this->_form = $form;
		
		$className = "ibftool_Form_Element_" . $this->typ;
		echo $className . "<br>";
		$element = new $className($this->id, array("name" => $this->id, "label" => $this->text));
		$element->setQuestion($this);
		$this->_form->addElement($element);
		
		/*
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if ($this->typ == "text") {
			$this->generateFreeText();
		} elseif ($this->typ == "shorttext") {
			$this->generateShortText();
		} elseif ($this->typ == "number") {
			$this->generateNumber();
		} elseif ($this->typ == "number1to100") {
			$this->generateNumber1to100();
		} elseif ($this->typ == "note") {
			$this->generateNote();
		} elseif ($this->typ == "scrow") {
			$this->generateSingleChoiceInRow();
		} elseif ($this->typ == "scrowten") {
			$this->generateSingleChoiceInRowTen();
		} elseif ($this->typ == "scrowseven") {
			$this->generateSingleChoiceInRowSeven();
		} elseif ($this->typ == "scrowmla") {
			$this->generateSingleChoiceInRowMLA();
		} elseif ($this->typ == "img") {
			$this->generateImage();
		} elseif ($this->typ == "age") {
			$this->generateAge();
		} elseif ($this->typ == "scrowcap") {
			$this->generateSingleChoiceInRowCaption();
		} elseif ($this->typ == "rs_dectab") {
			$this->generateRSDecisiveTable();
		} elseif ($this->typ == "rs_value") {
			$this->generateNoteWithValue();
		} elseif ($this->typ == "rs_value2") {
			$this->generateNoteWithValueInline();
		} elseif ($this->typ == "rs_valueTID") {
			$this->generateNoteWithValueTreatmentId();
		}elseif ($this->typ == "rs_dnumber") {
			$this->generateRSDNumber();
		} elseif ($this->typ == "countrycode") {
			$this->generateCountryCode();
		} elseif ($this->typ == "sc") {
			$this->generateSingleChoice();
		} elseif ($this->typ == "mc") {
			$this->generateMultipleChoice();
		}
		
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;

		$answers = $db->query("SELECT * FROM " . $prefix . '_' . "questionnaire_answer WHERE questionnaire_question_id = $this->id");

		if ($answers->rowCount() == 0) {
			return null;
		}

		if ($this->typ == "sc_random") {
			$this->generateSingleChoiceRandom($answers);
		} elseif ($this->typ == "select") {
			$this->generateSelect($answers);
		}
		
		*/
		//$this->_form->addElement("submit", "test", array("label" => "Weiter", "ignore" => true, "class" => "btn btn-primary", 'decorators' => array(array("ViewHelper"))));
	}
	
	public function addToForm($element) {
		$this->_form->addElement($element);
	}
	
	private function generateSelect($answers) {
		$select = new ibftool_Form_Element_Select("select", array("name" => $this->id, "label" => $this->text));
		$select->setQuestion($this);
		$this->_form->addElement($select);
	}
	
	private function generateCountryCode() {
		$countrycode = new ibftool_Form_Element_CountryCode("select", array("name" => $this->id, "label" => $this->text));
		$this->_form->addElement($countrycode);
	}

	private function generateRSDecisiveTable() {
		$rs = new ibftool_Form_Element_RS_DecisiveTable("rs", array("label" => $this->text));
		$rs->setQuestion($this);
		$this->_form->addElement($rs);
	}

	private function generateNoteWithValue() {
		$note = new ibftool_Form_Element_RS_NoteWithValue("note", array("name" => $this->id, "label" => $this->text));
		$note->setQuestion($this);
		$this->_form->addElement($note);
	}
		
	private function generateNoteWithValueInline() {
		$note = new ibftool_Form_Element_RS_NoteWithValueInline("note", array("name" => $this->id, "label" => $this->text));
		$note->setQuestion($this);
		$this->_form->addElement($note);
	}
	
	private function generateNoteWithValueTreatmentId() {
		$note = new ibftool_Form_Element_RS_NoteWithValueTID("note", array("name" => $this->id, "label" => $this->text));
		$note->setQuestion($this);
		$this->_form->addElement($note);
	}

	private function generateSingleChoiceInRowCaption() {
		$radio = new ibftool_Form_Element_SingleChoiceInRowCaption("scrow", array("name" => $this->id, "label" => $this->text, "required" => "true"));
		$radio->setQuestion($this);
		$this->_form->addElement($radio);
	}

	private function generateSingleChoice() {
		$radio = new ibftool_Form_Element_SingleChoice("singlechoice", array("name" => $this->id, "label" => $this->text, "required" => "true"));
		$radio->setQuestion($this);
		$this->_form->addElement($radio);
	}

	private function generateFreeText() {
		$formfield = new Zend_Form_Element_Textarea("text");
		$formfield->setName($this->id);
		$formfield->setLabel($this->text);
		$formfield->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$formfield->setRequired(true);
		$formfield->addValidator(new Zend_Validate_StringLength(10,1500));

		$formfield->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($formfield);
	}

	private function generateShortText() {
		$field = new ibftool_Form_Element_ShortText("text", array("name" => $this->id, "label" => $this->text, "required" => "true", "value" => $this->getAnswerForQuestion()));
		$field->setQuestion($this);
		$this->_form->addElement($field);
	}

	private function generateAge() {
		$field = new ibftool_Form_Element_Age($this->id, array("label" => $this->text, "value" => $this->getAnswerForQuestion(), "required" => true));
		$this->_form->addElement($field);
	}

	private function generateNumber() {
		$formfield = new Zend_Form_Element_Text("number");
		$formfield->setName($this->id);
		$formfield->setLabel($this->text);
		$formfield->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$formfield->setRequired(true);
		$formfield->addValidator(new Zend_Validate_Float(new Zend_Locale('en_US')));
		$formfield->addValidator(new Zend_Validate_Between(0, 50000));
		$formfield->addErrorMessage("Bitte nur Zahlen eintragen. Um Kommazahlen einzugeben bitte einen Punkt verwenden: z.B. 1.5 (nicht 1,5)");
		$formfield->autocomplete = "off";

		$formfield->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($formfield);
	}

	private function generateRSDNumber() {
		$formfield = new ibftool_Form_Element_RS_DoubleNumber("dnumber", array("name" => $this->id, "label" => $this->text, "required" => "true"));
		$formfield->setQuestion($this);
		$this->_form->addElement($formfield);
	}

	private function generateNumber1to100() {
		$formfield = new ibftool_Form_Element_1To100("number", array("name" => $this->id, "label" => $this->text, "required" => "true", "value" => $this->getAnswerForQuestion()));
		$formfield->setQuestion($this);
		$this->_form->addElement($formfield);
	}

	private function generateNote() {
		$note = new ibftool_Form_Element_Note($this->id, array('label' => $this->text));
		$note->setRequired(false);
		
		$note->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));

		if ($this->style == "bold") {
			$note->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt', 'class' => "note_bold"));
		} else {
			$note->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt', 'class' => "note"));
		}

		$this->_form->addElement($note);
	}

	private function generateImage() {
		$note = new ibftool_Form_Element_Note($this->id);
		$note->setIgnore(true);
		$note->setLabel("<img src='/_files/images/" . $this->text . "' />");
		$note->setRequired(false);
		$note->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$this->_form->addElement($note);
	}

	private function generateSingleChoiceInRow() {
		$radio = new ibftool_Form_Element_SCRow("scrow");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->setSeparator("");
		$radio->setRequired(true);

		if ($this->style == "bold") {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow_bold"));
		} else {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow"));
		}

		$radio->addErrorMessage("Bitte w�hlen Sie mindestens eine Antwortm�glichkeit aus");

		$radio->addMultiOption(0, 0);
		$radio->addMultiOption(1, 1);
		$radio->addMultiOption(2, 2);
		$radio->addMultiOption(3, 3);
		$radio->addMultiOption(4, 4);
		$radio->addMultiOption(5, 5);

		$radio->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($radio);
	}

	private function generateSingleChoiceInRowTen() {
		$radio = new Zend_Form_Element_Radio("scrowten");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->setSeparator("");
		$radio->setRequired(true);

		if ($this->style == "bold") {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow_bold"));
		} else {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow"));
		}

		$radio->addErrorMessage("Bitte w�hlen Sie mindestens eine Antwortm�glichkeit aus");

		$radio->addMultiOption(0, 0);
		$radio->addMultiOption(1, 1);
		$radio->addMultiOption(2, 2);
		$radio->addMultiOption(3, 3);
		$radio->addMultiOption(4, 4);
		$radio->addMultiOption(5, 5);
		$radio->addMultiOption(6, 6);
		$radio->addMultiOption(7, 7);
		$radio->addMultiOption(8, 8);
		$radio->addMultiOption(9, 9);
		$radio->addMultiOption(10, 10);

		$radio->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($radio);
	}

	private function generateSingleChoiceInRowSeven() {
		$radio = new Zend_Form_Element_Radio("scrowten");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->setSeparator("");
		$radio->setRequired(true);

		if ($this->style == "bold") {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow_bold"));
		} else {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow"));
		}

		$radio->addErrorMessage("Bitte w�hlen Sie eine Antwortm�glichkeit aus (1 = gar nicht, 7 = sehr stark)");

		$radio->addMultiOption(1, 1);
		$radio->addMultiOption(2, 2);
		$radio->addMultiOption(3, 3);
		$radio->addMultiOption(4, 4);
		$radio->addMultiOption(5, 5);
		$radio->addMultiOption(6, 6);
		$radio->addMultiOption(7, 7);

		$radio->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($radio);
	}

	private function generateSingleChoiceInRowMLA() {
		$radio = new Zend_Form_Element_Radio("scrowten");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->setSeparator("");
		$radio->setRequired(true);

		if ($this->style == "bold") {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow_bold"));
		} else {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow"));
		}

		$radio->addErrorMessage("Bitte w�hlen Sie eine Antwortm�glichkeit aus");

		$radio->addMultiOption(1, 1);
		$radio->addMultiOption(2, 2);
		$radio->addMultiOption(3, 3);
		$radio->addMultiOption(4, 4);
		$radio->addMultiOption(5, 5);
		$radio->addMultiOption(6, 6);
		$radio->addMultiOption(7, 7);
		$radio->addMultiOption(8, "Mir ist nicht bewusst geworden, dass sich die Feedback-Frequenz ge�ndert hat");

		$radio->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($radio);
	}

	private function generateMultipleChoice() {
		$radio = new ibftool_Form_Element_MultipleChoice("multiplechoice", array("name" => $this->id, "label" => $this->text, "required" => "true"));
		$radio->setQuestion($this);
		$this->_form->addElement($radio);
	}

	private function generateSingleChoiceRandom($answers) {
		$radio = new Zend_Form_Element_Radio("singlechoice");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$radio->setRequired(true);
		$radio->addErrorMessage("Bitte kreuzen Sie mindestens 1 Feld an.");

		$answerarray = array();
		foreach($answers as $answer) {
			array_push($answerarray, $answer);
		}

		if(ibftool_Controller_Action_Helper_Treatment::getRandom() >= 50) {
			$radio->addMultiOption($answerarray[0]["id"], "Option A: " . $answerarray[0]["text"]);
			$radio->addMultiOption($answerarray[1]["id"], "Option B: " . $answerarray[1]["text"]);
		} else {
			$radio->addMultiOption($answerarray[1]["id"], "Option A: " . $answerarray[1]["text"]);
			$radio->addMultiOption($answerarray[0]["id"], "Option B: " . $answerarray[0]["text"]);
		}

		$radio->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($radio);
	}

	public function getAnswerForQuestion() {
		$result = new Questionnaire_Results();
		$row = $result->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $this->id, "treatments_id = ?" => ibftool_Controller_Action_Helper_Treatment::getID()));

		if ($row == null) {
			return null;
		} else {
			return $row->questionnaire_answer;
		}
	}
	
	public function getAnswerForQuestionInTreatment($id) {
		$result = new Questionnaire_Results();
		$row = $result->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $this->id, "treatments_id = ?" => $id));
	
		if ($row == null) {
			return null;
		} else {
			return $row->questionnaire_answer;
		}
	}
	
	public function getAnswerForQuestionNoTreatment() {
		$result = new Questionnaire_Results();
		$row = $result->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $this->id));
	
		if ($row == null) {
			return null;
		} else {
			return $row->questionnaire_answer;
		}
	}

	public function getAnswerForQuestionById($id) {
		$result = new Questionnaire_Results();
		$row = $result->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $id, "treatments_id = ?" => ibftool_Controller_Action_Helper_Treatment::getID()));

		if ($row == null) {
			return null;
		} else {
			return $row->questionnaire_answer;
		}
	}

	public function getAnswersForQuestion($id) {
		$result = new Questionnaire_Results();
		$rows = $result->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $id, "treatments_id = ?" => ibftool_Controller_Action_Helper_Treatment::getID()));

		if ($rows->count() == 0) {
			return null;
		} else {
			return $rows;
		}
	}


}