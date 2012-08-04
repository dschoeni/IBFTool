<?
class ibftool_Form_Element_Select extends Zend_Form_Element_Select implements ibftool_Form_Element_Interface_Questionnaire
{
	function init() {
		$this->addErrorMessage("Bitte wählen Sie mindestens eine Antwortmöglichkeit aus");
		$this->addMultiOption("", "");
	}

	function setQuestion($question) {
		$this->addMultiOption("", "");

		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;
		$answers = $db->query("SELECT * FROM " . $prefix . '_' . "questionnaire_answer WHERE questionnaire_question_id = $question->id");

		if ($answers->rowCount() == 0) {
			return null;
		}

		foreach($answers as $answer) {
			$this->addMultiOption($answer["id"], $answer["text"]);
		}

		$rowset = $question->getAnswersForQuestion($question->id);
		$array = array();

		if ($rowset != null) {
			foreach($rowset as $row) {
				array_push($array, $row->questionnaire_answer);
			}
		}

		$this->setValue($array);
	}
}
