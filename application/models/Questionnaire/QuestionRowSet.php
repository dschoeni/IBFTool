<?php
class Questionnaire_QuestionRowSet extends Zend_Db_Table_Rowset {

	public function getForm() {
		$form = new Zend_Form();

		$form->setAttrib('id', 'question');
//		$form->setAction(Zend_Controller_Front::getInstance()->getBaseUrl() . '/questionnaire/process/');
		$form->setMethod(Zend_Form::METHOD_POST);

		foreach($this as $row) {
			$row->populateForm($form);
		}

		$submit = new Zend_Form_Element_Submit("Weiter");
		$submit->setIgnore(true);
		$submit->removeDecorator('DtDdWrapper');

		$form->addElement($submit);

		return $form;

	}
}