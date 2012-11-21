<?
class ibftool_Form_Element_SingleChoiceRandom extends Zend_Form_Element_Radio implements ibftool_Form_Element_Interface_Questionnaire {
	function init() {
		$this->setSeparator("");
	}
	
	public function setQuestion($question) {
		$this->setName($question->id);
		$this->setLabel($question->text);
		$this->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$this->setRequired(true);
		$this->addErrorMessage("Bitte kreuzen Sie mindestens 1 Feld an.");
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;
		$answers = $db->query("SELECT * FROM " . $prefix . '_' . "questionnaire_answer WHERE questionnaire_question_id = $question->id");
		
		if ($answers->rowCount() == 0) {
			echo "Achtung. Keine Antworten in der Datenbank vorhanden.";
			return null;
		}
		
		$answerarray = array();
		foreach($answers as $answer) {
			array_push($answerarray, $answer);
		}
		
		if(ibftool_Controller_Action_Helper_Treatment::getRandom() >= 50) {
			$this->addMultiOption($answerarray[0]["id"], "Option A: " . $answerarray[0]["text"]);
			$this->addMultiOption($answerarray[1]["id"], "Option B: " . $answerarray[1]["text"]);
		} else {
			$this->addMultiOption($answerarray[1]["id"], "Option A: " . $answerarray[1]["text"]);
			$this->addMultiOption($answerarray[0]["id"], "Option B: " . $answerarray[0]["text"]);
		}
		
		$this->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $question->payout)));
		$this->setValue($question->getAnswerForQuestion());
	}
}