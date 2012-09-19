<?php
class QuestionnaireController extends Zend_Controller_Action {

	public function preDispatch() {
		if (ibftool_Controller_Action_Helper_Treatment::getCurrentModule() != "questionnaire") {
			$this->_helper->redirector("index", "module");
		}
		
		$this->view->headScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/questionnaire/errorscrolling.js");
		$this->view->headScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/jqueryplugins/jquery.blink.js");
		//$this->view->inlineScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/questionnaire/ajaxvalidation.js");
	}

	public function indexAction() {
		$treatment_session = new Zend_Session_Namespace('treatment');
		$questionnaire_session = new Zend_Session_Namespace('questionnaire');

		$this->prepareSession($questionnaire_session, $treatment_session);

		/*
		 * Question ID is not null -> Display Questions
		*/

		$pages = new Questionnaire_Pages();
		$result = $pages->find($questionnaire_session->current_page);
		$result = $result->current();

		$form = $result->getQuestions()->getForm();

		$this->view->assign("current_page", $questionnaire_session->page);
		$this->view->assign("pages", count($questionnaire_session->pages));
		$this->view->assign("form", $form);
		$this->view->assign("title", $result->title);
		
		if ($this->getRequest()->isPost() && $this->view->form->isValid($this->_getAllParams())) {
			$this->_forward("process", "questionnaire", "default");
		}
	}

	public function processAction() {
		$questionnaire_session = new Zend_Session_Namespace('questionnaire');

		/*
		 * Get Questions and the corresponding form.
		*/

		$pages = new Questionnaire_Pages();
		$result = $pages->find($questionnaire_session->current_page);
		$result = $result->current();

		$form = $result->getQuestions()->getForm();

		/*
		 * Check the POST Data if it is empty.
		*/

		if (!empty($_POST)) {

			/*
			 * Check the POST Data whether it is valid.
			*/

			if ($form->isValid($_POST)) {

				$values = $form->getValues();
				$table = new Questionnaire_Results();

				foreach($result->getQuestions() as $row) {

					$id = $row["id"];

					/*
					 * Check if it was a multiple choice question
					*/

					if (is_array($values[$id])) {

						/*
						 * treat mc-specific, update results or create rows
						*/

						var_dump($values[$id]);

						$row = $table->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $id,  "treatments_id = ?" => ibftool_Controller_Action_Helper_Treatment::getID()));

						if ($row->count() > 0) {
							foreach($row as $singlerow) {
								$singlerow->delete();
							}
						}

						foreach($values[$id] as $value) {
							$data = array(
									'questionnaire_question_id'	=> $id,
									'treatments_id' => ibftool_Controller_Action_Helper_Treatment::getID(),
									'users_id' =>  Zend_Auth::getInstance()->getIdentity()->id,
									'ip' => $_SERVER['REMOTE_ADDR'],
									'questionnaire_answer' => $value,
							);

							$row = $table->createRow($data);
							$row->save();
						}

							
					} else {

						echo "Treating Question $id";
						echo "<br />";
						var_dump($values[$id]);
						echo "<br />";

						/*
						 * treat normally, update results or create rows
						*/

						$data = array(
								'users_id' =>  Zend_Auth::getInstance()->getIdentity()->id,
								'treatments_id' => ibftool_Controller_Action_Helper_Treatment::getID(),
								'questionnaire_question_id'	=> $id,
								'questionnaire_answer' => $values[$id],
								'ip' => $_SERVER['REMOTE_ADDR']
						);

						$row = $table->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $id,  "treatments_id = ?" => ibftool_Controller_Action_Helper_Treatment::getID()));

						if (empty($row)) {
							$row = $table->createRow($data);
							$row->save();
						} else {
							$row = $table->update($data, array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "questionnaire_question_id = ?" => $id));
						}

					}

				}

				$this->getNextPage($questionnaire_session);
				$this->_helper->redirector("index", "questionnaire");
				return;

			} else {
				/*
				 * If the form was not valid, redisplay it with the necessary error messages.
				*/
				$this->_helper->redirector("index", "questionnaire");
			}
		}

	}

	public function validateformAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$questionnaire_session = new Zend_Session_Namespace('questionnaire');

		$pages = new Questionnaire_Pages();
		$result = $pages->find($questionnaire_session->current_page);
		$result = $result->current();
		$form = $result->getQuestions()->getForm();

		$form->isValidPartial($this->_getAllParams());
		$this->_helper->json($form->getMessages());

		header('Content-type: application/json');
		echo Zend_Json::encode($json);
	}

	private function done() {
		$this->_helper->redirector("index", "module");
	}

	private function prepareSession($questionnaire_session, $treatment_session) {
		if (!isset($questionnaire_session->current_page)) {
			$config = ibftool_Controller_Action_Helper_Treatment::getCurrentConfig();

			$doc = new DOMDocument();
			$doc->loadXML($config);

			$pages = $doc->getElementsByTagName("page");
			$array = array();

			foreach($pages as $page) {
				array_push($array, $page->nodeValue);
			}

			$questionnaire_session->pages = $array;
			$questionnaire_session->current_page = $questionnaire_session->pages[0];
			$questionnaire_session->page = 1;


		}
	}

	private function getNextPage($questionnaire_session) {
		if (isset($questionnaire_session->current_page)) {

			if ($questionnaire_session->current_page == end($questionnaire_session->pages) && $questionnaire_session->done == false) {
				ibftool_Controller_Action_Helper_Treatment::completeCurrentModule();
			}

			$questionnaire_session->current_page = $questionnaire_session->pages[$questionnaire_session->page++];
		}
	}

}