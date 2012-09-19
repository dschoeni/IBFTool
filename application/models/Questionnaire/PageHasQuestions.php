<?php
class Questionnaire_PageHasQuestions extends ibftool_Db_Table_Abstract
{
	protected $_name = 'questionnaire_page_has_question';
	protected $_rowClass = "Questionnaire_PageHasQuestion";
	protected $_rowsetClass = "Questionnaire_PageHasQuestionRowSet";
	
	protected $_referenceMap    = array(

			'modules' => array(
					'columns'           => 'page_id',
					'refTableClass'     => 'Pages',
					'refColumns'        => 'id'
			),
			'treatments' => array(
					'columns'           => 'question_id',
					'refTableClass'     => 'Questions',
					'refColumns'        => 'id'
			)
	);

}