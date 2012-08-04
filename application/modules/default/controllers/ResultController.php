<?php
class ResultController extends Zend_Controller_Action {

	public function indexAction() {

		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;

		$users = $db->query("SELECT id, userhash FROM " . $prefix . '_' . "users WHERE completed = '1'");
		$results = $db->query("SELECT u.userhash, q.typ, r.question_id, q.text, r.timestamp, r.answer, a.text FROM " . $prefix . '_' . "result r INNER JOIN " . $prefix . '_' . "users u ON r.users_id = u.id INNER JOIN " . $prefix . '_' . "question q ON r.question_id = q.id LEFT OUTER JOIN answer a ON r.answer = a.id WHERE r.answer IS NOT NULL ORDER BY u.id, r.question_id");
		$questions = $db->query("SELECT * FROM " . $prefix . '_' . "question WHERE survey_id IS NOT NULL AND TYP != 'note' && TYP != 'img'");
		$games = $db->query("SELECT * FROM " . $prefix . '_' . "results_game");


		$string = "<tr>";
		$string .= "<td style='width: 100px'>Subject</td>";

		$questions = $questions->fetchAll();
		$users = $users->fetchAll();
		$results = $results->fetchAll();

		$string = "";

		foreach($users as $user) {
			$answerarray[$user["userhash"]] = array();
			foreach($questions as $question) {
				array_push($answerarray[$user["userhash"]], $question["id"]);
			}
				
			for ($i = 1; $i <= 20; $i++) {
				array_push($answerarray[$user["userhash"]], "round" . $i);
			}
		}

		foreach($results as $result) {
			$answerarray[$result["userhash"]][$result["question_id"]] = array();
		}


		foreach($results as $result) {
			array_push($answerarray[$result["userhash"]][$result["question_id"]],  $result["answer"]);
		}

		$string .= "<tr><td>Subject ID</td>";

		foreach($questions as $question) {
			$string .= "<td>";
			$string .= $question["id"];
			$string .= "<br />";
			$string .= substr($question["text"], 0, 20) . "...";
			$string .= "</td>";
		}

		$string .= "</tr>";

		foreach($users as $user) {
			$string .= "<tr><td>";
			$string .= $user["userhash"];
			$string .= "</td>";
				
			foreach($questions as $question) {
				$string .= "<td>";
				//echo $answerarray[$user["userhash"]][$question["id"]];
				if (!empty($answerarray[$user["userhash"]][$question["id"]]) && is_array($answerarray[$user["userhash"]][$question["id"]])) {
					foreach($answerarray[$user["userhash"]][$question["id"]] as $test) {
						$string .= $test . "<br />";
					}
				}
				$string .= "</td>";
			}
				
			$string .= "</tr>";
		}

		$this->view->assign("result", $string);
	}

}