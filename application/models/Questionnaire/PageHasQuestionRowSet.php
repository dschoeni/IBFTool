<?php
class Questionnaire_PageHasQuestionRowSet extends Zend_Db_Table_Rowset {

	public function getForm() {
		$form = new ibftool_Form_Questionnaire();

		foreach($this as $row) {
			$row->populateForm($form);
		}

		$submit = new Zend_Form_Element_Submit("Weiter", array("label" => "Weiter",  "class" => "btn btn-primary"));
		$submit->setIgnore(true);
		$form->addElement($submit);
		
		//ibftool_Form_Helper::styleFormQuestionnaire($form);
		return $form;

	}
}