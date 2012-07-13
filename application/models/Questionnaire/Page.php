<?php
class Questionnaire_Page extends Zend_Db_Table_Row {

	public function getQuestions() {

		$questions = new Questionnaire_Questions();
		$select = $questions->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)->where('sbftool_questionnaire_page_has_question.page_id = ?', $this->id)->join('sbftool_questionnaire_page_has_question', 'sbftool_questionnaire_page_has_question.question_id = sbftool_questionnaire_question.id')->order("sbftool_questionnaire_page_has_question.order ASC");

		$questions = $questions->fetchAll($select);

		return $questions;
	}
}