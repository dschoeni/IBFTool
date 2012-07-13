<?php
class Admin_QuestionnaireController extends Zend_Controller_Action {

	public function init()
	{
		// Optional added for consistency
		parent::init();
		
		// Excel format context
		
		$excelConfig =
		array('excel' => array(
						'suffix'  => 'excel',
						'headers' => array(
								'Content-type' => 'application/ms-excel',
								'Content-Disposition' => "attachment; filename=" . $this->_getParam("id") . "_report_" . date("dmY") . ".xlsx")),
		);
		
		// Init the Context Switch Action helper
		$contextSwitch = $this->_helper->contextSwitch();
		
		// Add the new context
		$contextSwitch->setContexts($excelConfig);
		
		// Set the new context to the reports action
		$contextSwitch->addActionContext('result', 'excel');
		
		// Initializes the action helper
		$contextSwitch->initContext();
	}

	public function indexAction() {

	}

	public function resetAction() {
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->_getParam("id");

		if (!empty($id)) {
			$results = new Questionnaire_Results();
			$where = $results->getAdapter()->quoteInto('treatments_id = ?', $id);
			$results->delete($where);
		}
	}

	public function resultAction() {
		$id = $this->_getParam("id");

		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;

		$users = $db->query("SELECT id, email, userhash, treatments_id FROM sbftool_users u JOIN sbftool_treatments_has_users thu ON u.id = thu.users_id WHERE thu.treatments_id = " . $id . " AND thu.completed = 1 ORDER BY userhash");
		$results = $db->query("SELECT u.email, q.typ, r.questionnaire_question_id, r.timestamp, r.questionnaire_answer, a.text FROM sbftool_questionnaire_result r INNER JOIN sbftool_users u ON r.users_id = u.id INNER JOIN sbftool_questionnaire_question q ON r.questionnaire_question_id = q.id LEFT OUTER JOIN sbftool_questionnaire_answer a ON r.questionnaire_answer = a.id WHERE r.questionnaire_answer IS NOT NULL ORDER BY u.id, r.questionnaire_question_id");
		$questions = $db->query("SELECT q.id, q.text FROM sbftool_questionnaire_question q JOIN sbftool_questionnaire_page_has_question phq ON phq.question_id = q.id");
		
		$questions = $questions->fetchAll();
		$users = $users->fetchAll();
		$results = $results->fetchAll();
		
		// Generate Answerarray
		foreach($users as $user) {
			$answerarray[$user["email"]] = array();
			foreach($questions as $question) {
				array_push($answerarray[$user["email"]], $question["id"]);
			}
		}
		
		foreach($results as $result) {
			$answerarray[$result["email"]][$result["questionnaire_question_id"]] = array();
		}
		
		foreach($results as $result) {
			if ($result["typ"] == "number" 
					|| $result["typ"] == "scrowcap" 
					|| $result["typ"] == "countrycode" 
					|| $result["typ"] == "rs_dnumber" 
					|| $result["typ"] == "rs_dectab" 
					|| $result["typ"] ==  "shorttext" 
					|| $result["typ"] ==  "text" 
					|| $result["typ"] ==  "scrow" 
					|| $result["typ"] ==  "scrowten" 
					|| $result["typ"] ==  "scrowseven" 
					|| $result["typ"] ==  "scrowmla"
					|| $result["typ"] ==  "number1to100") {
				array_push($answerarray[$result["email"]][$result["questionnaire_question_id"]],  $result["questionnaire_answer"]);
			} else {
				array_push($answerarray[$result["email"]][$result["questionnaire_question_id"]],  $result["text"]);
			}
		}
		
		$this->view->assign("id", $id);
		$this->view->assign("questions", $questions);
		$this->view->assign("users", $users);
		$this->view->assign("results", $results);
		$this->view->assign("answerarray", $answerarray);

		/*
		$string .= "</table>";
		$string .= "<h2>Treatment</h2><table class='admin_questionnaire_result'>";


		foreach($users as $user) {
			$answerarray[$user["email"]] = array();
			foreach($questions as $question) {
				array_push($answerarray[$user["email"]], $question["id"]);
			}
		}

		foreach($results as $result) {
			$answerarray[$result["email"]][$result["questionnaire_question_id"]] = array();
		}

		foreach($results as $result) {
			if ($result["typ"] == "number" || $result["typ"] ==  "shorttext" || $result["typ"] ==  "text" || $result["typ"] ==  "scrow" || $result["typ"] ==  "scrowten" || $result["typ"] ==  "scrowseven" || $result["typ"] ==  "scrowmla" || $result["typ"] ==  "number1to100") {
				array_push($answerarray[$result["email"]][$result["questionnaire_question_id"]],  $result["questionnaire_answer"]);
			} else {
				array_push($answerarray[$result["email"]][$result["questionnaire_question_id"]],  $result["text"]);
			}
		}

		$string .= "<tr><td>Subject ID</td>";

		foreach($questions as $question) {
			$string .= "<td>";
			$string .= $question["id"];
			$string .= "<br />";
			$string .= $question["text"];
			$string .= "</td>";
		}

		$string .= "</tr>";

		foreach($users as $user) {
			$string .= "<tr><td>";
			$string .= $user["userhash"];
			$string .= "</td>";
			$string .= "<td>";
			$string .= $user["treatments_id"];
			$string .= "</td>";

			foreach($questions as $question) {
				$string .= "<td>";
				if (!empty($answerarray[$user["email"]][$question["id"]]) && is_array($answerarray[$user["email"]][$question["id"]])) {
					foreach($answerarray[$user["email"]][$question["id"]] as $test) {
						$string .= $test . "<br />";
					}
				}
				$string .= "</td>";
			}

			$string .= "</tr>";
		}


		$this->view->assign("result", $string);
		
		*/
	}
}