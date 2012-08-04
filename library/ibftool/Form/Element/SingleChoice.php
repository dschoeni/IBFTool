<?
class ibftool_Form_Element_SingleChoice extends Zend_Form_Element_Radio implements ibftool_Form_Element_Interface_Questionnaire {
	function init() {
		$this->setSeparator("");
	}
	
	public function setQuestion($question) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;
		$answers = $db->query("SELECT * FROM " . $prefix . '_' . "questionnaire_answer WHERE questionnaire_question_id = $question->id");

		if ($answers->rowCount() == 0) {
			echo "Achtung. Keine Antworten in der Datenbank vorhanden.";
			return null;
		}
		
		foreach($answers as $answer) {
			$this->addMultiOption($answer["id"], $answer["text"]);
		}
		
		$this->addDecorator(new ibftool_Form_Decorators_Payout(array("payout" => $this->payout)));
		$this->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
		
		$this->setValue($question->getAnswerForQuestion());
	}
}