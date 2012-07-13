<?php
class Questionnaire_Question extends Zend_Db_Table_Row {
	
	private $_form;

	public function populateForm($form) {
		$this->_form = $form;
		
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
			$this->generateNoteWithValue2();
		} elseif ($this->typ == "rs_valueTID") {
			$this->generateNoteWithValueTreatmentId();
		}elseif ($this->typ == "rs_dnumber") {
			$this->generateRSDNumber();
		} elseif ($this->typ == "countrycode") {
			$this->generateCountryCode();
		}
		
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;

		$answers = $db->query("SELECT * FROM " . $prefix . '_' . "questionnaire_answer WHERE questionnaire_question_id = $this->id");

		if ($answers->rowCount() == 0) {
			return null;
		}

		if ($this->typ == "sc") {
			$this->generateSingleChoice($answers);
		} elseif ($this->typ == "mc") {
			$this->generateMultipleChoice($answers);
		} elseif ($this->typ == "sc_random") {
			$this->generateSingleChoiceRandom($answers);
		} elseif ($this->typ == "select") {
			$this->generateSelect($answers);
		}
	}
	
	private function generateSelect($answers) {
		$select = new Zend_Form_Element_Select("select");
		$select->setName($this->id);
		$select->setLabel($this->text);
		$select->setRequired(true);
		$select->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
		$select->addMultiOption("", "");
		
		foreach($answers as $answer) {
			$select->addMultiOption($answer["id"], $answer["text"]);
		}
		
		$rowset = $this->getAnswersForQuestion($this->id);
		$array = array();
		
		if ($rowset != null) {
			foreach($rowset as $row) {
				array_push($array, $row->questionnaire_answer);
			}
		}
		
		$select->setValue($array);
		$this->_form->addElement($select);
	}
	
	private function generateCountryCode() {
		$select = new Zend_Form_Element_Select("select");
		$select->setName($this->id);
		$select->setLabel($this->text);
		$select->setRequired(true);
		$select->addErrorMessage("Bitte wählen Sie ein Land aus");
		
		$countries = new Countries();
		$countriesRowSet = $countries->fetchAll();
		
		$select->addMultiOption("", "");
		
		foreach($countriesRowSet as $country) {
			$select->addMultiOption($country->code, $country->name);
		}
		
		$this->_form->addElement($select);
	}

	private function generateRSDecisiveTable() {
		$radio = new Zend_Form_Element_Radio("scrow");
		//$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->setRequired(false);
		$radio->setIgnore(true);

		$configarray = explode("/", $this->style);

		$ecu = $configarray[0];
		$firstColumn = $configarray[1];
		$secondColumn = $configarray[2];
		$thirdColumn = $configarray[3];

		$values = explode("#", $secondColumn);

		for($i = 0; $i < count(explode("#", $firstColumn)); $i++) {
			$radio->addMultiOption($i, $values[$i]);
		}

		$radio->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
		$radio->setValue($this->getAnswerForQuestion());

		$radio->setDecorators(array(new ibftool_Form_Decorators_RS_DecisiveTable(array("id" => $this->id, "ecu" => $ecu, "firstColumn" => $firstColumn, 'secondColumn' => $secondColumn, 'thirdColumn' => $thirdColumn)), new Zend_Form_Decorator_Errors(), new ibftool_Form_Decorators_Payout(array("payout" => $this->payout))));
		$this->_form->addElement($radio);

		$hidden = new Zend_Form_Element_Hidden("hiddenrs");
		$hidden->setName($this->id);
		$hidden->setRequired(true);
		$hidden->setValue($this->getAnswerForQuestion());
		$this->_form->addElement($hidden);
	}

	private function generateNoteWithValue() {
		$answer = $this->getAnswerForQuestionById($this->style);
		$note = new ibftool_Form_Element_Note($this->id, array('label' => str_replace("#", "<b>" . $answer . "</b>", $this->text)));
		$note->setRequired(false);

		if ($this->style == "bold") {
			$note->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt', 'class' => "note_bold"));
		} else {
			$note->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt', 'class' => "note"));
		}
		
		$note->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));

		$this->_form->addElement($note);
	}
	
	private function generateNoteWithValue2() {
		$answer = $this->getAnswerForQuestionById($this->style);
	
		$note = new ibftool_Form_Element_Note($this->id, array('label' => str_replace("#", "<b>" . round($answer) . "</b>", $this->text)));
		$note->setRequired(false);
	
		$note->addDecorator("Label" ,array("escape"=>false, 'tag' => 'span', 'class' => "note_nopad"));
		$note->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));
	
		$this->_form->addElement($note);
	}
	
	private function generateNoteWithValueTreatmentId() {
		$array = explode("/", $this->style);
		
		$result = new Questionnaire_Results();
		$row = $result->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $array[0], "treatments_id = ?" => $array[1]));
		
		$note = new ibftool_Form_Element_Note($this->id, array('label' => str_replace("#", "<b>" . round($row->questionnaire_answer) . "</b>", $this->text)));
		$note->setRequired(false);
		
		$note->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt', 'class' => "note"));
		$note->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));
		
		$this->_form->addElement($note);
	}

	private function generateSingleChoiceInRowCaption() {
		$radio = new Zend_Form_Element_Radio("scrow");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->setSeparator("");
		$radio->setRequired(true);

		$configarray = explode("/", $this->style);
		$firstAnswer = $configarray[0];
		$lastAnswer = $configarray[1];
		$leftString = $configarray[2];
		$rightString = $configarray[3];


		for($firstAnswer; $firstAnswer <= $lastAnswer; $firstAnswer++) {
			$radio->addMultiOption($firstAnswer, $firstAnswer);
		}

		$radio->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
		$radio->setValue($this->getAnswerForQuestion());

		$radio->setDecorators(array(new ibftool_Form_Decorators_SingleChoiceCaptioned(array("leftCaption"=>$leftString, 'rightCaption' => $rightString)), new Zend_Form_Decorator_Errors(), new ibftool_Form_Decorators_Payout(array("payout" => $this->payout))));
		$this->_form->addElement($radio);
	}

	private function generateSingleChoice($answers) {
		$radio = new Zend_Form_Element_Radio("singlechoice");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$radio->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));
		$radio->setRequired(true);
		$radio->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");

		foreach($answers as $answer) {
			$radio->addMultiOption($answer["id"], $answer["text"]);
		}

		$radio->setValue($this->getAnswerForQuestion());

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
		$formfield = new Zend_Form_Element_Text("text");
		$formfield->setName($this->id);
		$formfield->setLabel($this->text);
		$formfield->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));
		$formfield->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$formfield->setRequired(true);
		
		$stringLengthValidator = new Zend_Validate_StringLength(0,25);
		$stringLengthValidator->setMessage("Bitte überschreiten sie die maximale Länge von 25 Zeichen nicht.", "stringLengthTooLong");
		
		$notEmptyValidator = new Zend_Validate_NotEmpty();
		$notEmptyValidator->setMessage("Dieses Feld darf nicht leer sein.");
		
		$formfield->addValidators(array($stringLengthValidator, $notEmptyValidator));
		$formfield->autocomplete = "off";
		$formfield->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($formfield);
	}

	private function generateAge() {
		$formfield = new Zend_Form_Element_Text("number");
		$formfield->setName($this->id);
		$formfield->setLabel($this->text);
		$formfield->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$formfield->setRequired(true);
		$formfield->addValidator(new Zend_Validate_Between(16, 99));
		$formfield->addErrorMessage("Bitte nur Zahlen eintragen. Das Mindestalter beträgt 16 Jahre.");
		$formfield->autocomplete = "off";

		$formfield->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($formfield);
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
		$formfield = new Zend_Form_Element_Text("dnumber");
		$formfield->setName($this->id);
		$formfield->setLabel($this->text);
		$formfield->setDecorators(array(new ibftool_Form_Decorators_RS_DoubleNumber(), new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)), new Zend_Form_Decorator_Errors()));
		
		$formfield->setRequired(true);
		$formfield->addValidator(new ibftool_Validate_DNumber_NotEmpty());
		
		$formfield->addErrorMessage("Bitte nur Zahlen eintragen. Um Kommazahlen einzugeben bitte einen Punkt verwenden: z.B. 1.5 (nicht 1,5)");
		$formfield->autocomplete = "off";

		$rowset = $this->getAnswersForQuestion($this->id);
		$array = array();

		if ($rowset != null) {
			foreach($rowset as $row) {
				array_push($array, $row->questionnaire_answer);
			}
		}

		$formfield->setValue($array);

		$this->_form->addElement($formfield);
	}

	private function generateNumber1to100() {
		$formfield = new Zend_Form_Element_Text("number");
		$formfield->setName($this->id);
		$formfield->setLabel($this->text);
		$formfield->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$formfield->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));
		$formfield->setRequired(true);
		$formfield->addValidator(new Zend_Validate_Float(new Zend_Locale('en_US')));
		$formfield->addValidator(new Zend_Validate_Between(0, 100));
		$formfield->addErrorMessage("Bitte nur Zahlen eintragen. Um Kommazahlen einzugeben bitte einen Punkt verwenden: z.B. 1.5 (nicht 1,5)");
		$formfield->autocomplete = "off";

		$formfield->setValue($this->getAnswerForQuestion());

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
		$radio = new Zend_Form_Element_Radio("scrow");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->setSeparator("");
		$radio->setRequired(true);

		if ($this->style == "bold") {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow_bold"));
		} else {
			$radio->addDecorator("Label" ,array('tag' => 'dt', 'class' => "scrow"));
		}

		$radio->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");

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

		$radio->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");

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

		$radio->addErrorMessage("Bitte wählen Sie eine Antwortmöglichkeit aus (1 = gar nicht, 7 = sehr stark)");

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

		$radio->addErrorMessage("Bitte wählen Sie eine Antwortmöglichkeit aus");

		$radio->addMultiOption(1, 1);
		$radio->addMultiOption(2, 2);
		$radio->addMultiOption(3, 3);
		$radio->addMultiOption(4, 4);
		$radio->addMultiOption(5, 5);
		$radio->addMultiOption(6, 6);
		$radio->addMultiOption(7, 7);
		$radio->addMultiOption(8, "Mir ist nicht bewusst geworden, dass sich die Feedback-Frequenz geändert hat");

		$radio->setValue($this->getAnswerForQuestion());

		$this->_form->addElement($radio);
	}

	private function generateMultipleChoice($answers) {
		$radio = new Zend_Form_Element_MultiCheckbox("multiplechoice");
		$radio->setName($this->id);
		$radio->setLabel($this->text);
		$radio->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$radio->setRequired(true);
		$radio->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");

		foreach($answers as $answer) {
			$radio->addMultiOption($answer["id"], $answer["text"]);
		}

		$rowset = $this->getAnswersForQuestion($this->id);
		$array = array();

		if ($rowset != null) {
			foreach($rowset as $row) {
				array_push($array, $row->questionnaire_answer);
			}
		}

		$radio->setValue($array);
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