<?php
class Administration_Questionnaire_QuestionsController extends Zend_Controller_Action {

	public function indexAction() {
		$treatments = new Treatments();
		$treatments = $treatments->fetchAll();
		
		$this->view->treatments = $treatments;
		
		$questions = new Questionnaire_Questions();

		if ($this->_hasParam("treatment_id")) {
			$treatmentid = $this->_getParam("treatment_id");

			$thm = new TreatmentsHasModules();
			
			// Moduleid 1 = Questionnaire-Modul
			$thm = $thm->fetchAll(array("module_id = ?" => 1, "treatments_id = ?" => $treatmentid));
				
			$pagesarray = array();
				
			foreach($thm as $row) {
				if ($row->config) {
					$doc = new DOMDocument();
					$doc->loadXML($row->config);

					$pages = $doc->getElementsByTagName("page");

					foreach($pages as $page) {
						array_push($pagesarray, $page->nodeValue);
					}
				}
			}
			
			// If there are no pages -> Empty array
			if (empty($pagesarray)) {
				$this->view->questions = array();
				return;				
			}
			
			$questionsperpage = new Questionnaire_PageHasQuestions();
			$questionsperpage = $questionsperpage->fetchAll(array('page_id IN (?)' => $pagesarray));
			$questions = $questions->fetchAll(array('id IN (?)' => $questionsperpage->toArray()));

		} else {
			$questions = $questions->fetchAll();
		}

		$this->view->questions = $questions;

	}

	public function editAction() {

		if (!$this->_hasParam("id")) {

		}

		$id = $this->_getParam("id");

		$form = new ibftool_Form_Administration_Question();

		if (!empty($_POST) && $form->isValid($this->_getAllParams())) {
			$form->persistData();
			$this->view->persistdata = true;
		}

		$question = new Questionnaire_Questions();
		$question = $question->find($id);
		$question = $question->current();
		$question->typ = strtolower($question->typ);
		$form->setValues($question);

		$this->view->form = $form;

		$className = "ibftool_Form_Element_" . $question->typ;
		$element = new $className("preview", array("name" => $question->id, "label" => $question->text));
		$element->setQuestion($question);

		$this->view->preview = $element->render();

	}

	public function newAction() {
		$form = new ibftool_Form_Administration_Question();
		
		$element = $form->getElement("treatment_id");
		

		if (!empty($_POST) && $form->isValid($this->_getAllParams())) {
			$form->persistData();
			$this->_redirect("/administration/questionnaire_questions/index");
		}

		$this->view->form = $form;

	}

}