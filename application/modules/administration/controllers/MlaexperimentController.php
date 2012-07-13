<?php
class Administration_MlaexperimentController extends Zend_Controller_Action {

	public function indexAction() {

	}

	public function resultAction() {
		//set_time_limit(0);
		$rounds = 108;

		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;

		$users = $db->query("SELECT id, email FROM ibftool_users");
		$results = $db->query("SELECT u.grp, u.email, r.money, r.yield, r.investment, r.round, r.treatments_id, r.outcome FROM ibftool_mlaexperiment_results r INNER JOIN ibftool_users u ON r.users_id = u.id ORDER BY u.id, r.round");

		$string = "<tr>";
		$string .= "<td style='width: 100px'>Subject</td>";
		$string .= "<td style='width: 100px'>Treatment ID</td>";

		$users = $users->fetchAll();
		$results = $results->fetchAll();

		for($i = 1; $i <= 108; $i++) {
			$string .= "<td style='width: 100px'>$i</td>";
		}

		$string .= "</tr>";

		foreach($users as $user) {
			$answerarray[$user["email"]] = array();

			for($i = 1; $i <= $rounds; $i++) {
				$answerarray[$user["email"]][$i] = array();
			}

		}
		
		foreach($results as $result) {
			array_push($answerarray[$result["email"]][$result["round"]],
					array(
							"outcome" => $result["outcome"],
							"investment" => $result["investment"],
							"yield" => $result["yield"],
							"money" => $result["money"],
							"treatments_id" => $result["treatments_id"]
					)
			);
		}

		$string .= "<tr>";
		$string .= "<td style='width: 100px'>Investment: </td>";
		for($i = 1; $i <= $rounds+1; $i++) {
			$string .= "<td style='width: 100px'></td>";
		}
		$string .= "</tr>";

		foreach($users as $user) {
			// Investment

			$string .= "<tr>";
			$string .= "<td style='width: 150px'>" . $user["email"] . "</td>";
			$string .= "<td style='width: 50px'>" . $answerarray[$user["email"]][1][0]["treatments_id"] . "</td>";

			for($i = 1; $i <= $rounds; $i++) {
				$string .= "<td style='width: 100px'>" . $answerarray[$user["email"]][$i][0]["investment"] . "</td>";
			}
			$string .= "</tr>";
		}

		$string .= "<tr>";
		$string .= "<td style='width: 100px'>Yield: </td>";
		for($i = 1; $i <= $rounds+1; $i++) {
			$string .= "<td style='width: 100px'></td>";
		}
		$string .= "</tr>";

		foreach($users as $user) {

			// Yield

			$string .= "<tr>";
			$string .= "<td style='width: 150px'>" . $user["email"] . "</td>";
			$string .= "<td style='width: 50px'>" . $answerarray[$user["email"]][1][0]["treatments_id"] . "</td>";

			for($i = 1; $i <= $rounds; $i++) {
				$string .= "<td style='width: 150px'>" . $answerarray[$user["email"]][$i][0]["yield"] . "</td>";
			}
			$string .= "</tr>";
		}

		$string .= "<tr>";
		$string .= "<td style='width: 100px'>Money: </td>";
		for($i = 1; $i <= $rounds+1; $i++) {
			$string .= "<td style='width: 100px'></td>";
		}
		$string .= "</tr>";

		foreach($users as $user) {

			// Money
				
			$string .= "<tr>";
			$string .= "<td style='width: 150px'>" . $user["email"] . "</td>";
			$string .= "<td style='width: 50px'>" . $answerarray[$user["email"]][1][0]["treatments_id"] . "</td>";

			for($i = 1; $i <= $rounds; $i++) {
				$string .= "<td style='width: 150px'>" . $answerarray[$user["email"]][$i][0]["money"] . "</td>";
			}
			$string .= "</tr>";
		}

		$string .= "<tr>";
		$string .= "<td style='width: 100px'>Outcome: </td>";
		for($i = 1; $i <= $rounds+1; $i++) {
			$string .= "<td style='width: 100px'></td>";
		}
		$string .= "</tr>";

		foreach($users as $user) {

			// Outcome
				
			$string .= "<tr>";
			$string .= "<td style='width: 150px'>" . $user["email"] . "</td>";
			$string .= "<td style='width: 50px'>" . $answerarray[$user["email"]][1][0]["treatments_id"] . "</td>";

			for($i = 1; $i <= $rounds; $i++) {
				$string .= "<td style='width: 150px'>" . $answerarray[$user["email"]][$i][0]["outcome"] . "</td>";
			}
			$string .= "</tr>";
		}

		$this->view->assign("result", $string);
	}

	public function resetAction() {
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->_getParam("id");

		if (!empty($id)) {
			$results = new MLAExperiment_Rounds();
			$where = $results->getAdapter()->quoteInto('treatments_id = ?', $id);
			$results->delete($where);
		}

	}

}