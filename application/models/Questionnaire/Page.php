<?php
class Questionnaire_Page extends Zend_Db_Table_Row {

	public function getQuestions() {

		$questions = new Questionnaire_Questions();
		$select = $questions->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)->where('ibftool_questionnaire_page_has_question.page_id = ?', $this->id)->join('ibftool_questionnaire_page_has_question', 'ibftool_questionnaire_page_has_question.question_id = ibftool_questionnaire_question.id')->order("ibftool_questionnaire_page_has_question.order ASC");

		$questions = $questions->fetchAll($select);

		return $questions;
	}
}