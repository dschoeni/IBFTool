<?php
class Admin_InvestmentgameController extends Zend_Controller_Action {

	public function indexAction() {

	}

	public function resultAction() {

		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;

		$users = $db->query("SELECT id, email FROM ibftool_users");
		$results = $db->query("SELECT u.grp, u.email, r.money, r.yield, r.investment, r.round FROM ibftool_investmentgame_results r INNER JOIN ibftool_users u ON r.users_id = u.id ORDER BY u.id, r.round");

		$string = "<tr>";
		$string .= "<td style='width: 100px'>Subject</td>";
		$string .= "<td style='width: 100px'>Group</td>";

		$users = $users->fetchAll();
		$results = $results->fetchAll();

		for($i = 1; $i <= 25; $i++) {
			$string .= "<td style='width: 100px'>$i</td>";
		}

		$string .= "</tr>";

		foreach($users as $user) {
			$answerarray[$user["email"]] = array();

			for($i = 1; $i <= 25; $i++) {
				$answerarray[$user["email"]][$i] = array();
			}

		}

		foreach($results as $result) {
			array_push($answerarray[$result["email"]][$result["round"]],
					array(
							"investment" => $result["investment"],
							"yield" => $result["yield"],
							"money" => $result["money"],
							"group" => $result["grp"]
					)
			);
		}

		$string .= "<tr>";
		$string .= "<td style='width: 100px'>Investment: </td>";
		for($i = 1; $i <= 26; $i++) {
			$string .= "<td style='width: 100px'></td>";
		}
		$string .= "</tr>";

		foreach($users as $user) {
			// Investment

			$string .= "<tr>";
			$string .= "<td style='width: 150px'>" . $user["email"] . "</td>";
			$string .= "<td style='width: 50px'>" . $answerarray[$user["email"]][1][0]["group"] . "</td>";

			for($i = 1; $i <= 25; $i++) {
				$string .= "<td style='width: 100px'>" . $answerarray[$user["email"]][$i][0]["investment"] . "</td>";
			}
			$string .= "</tr>";
		}

		$string .= "<tr>";
		$string .= "<td style='width: 100px'>Yield: </td>";
		for($i = 1; $i <= 26; $i++) {
			$string .= "<td style='width: 100px'></td>";
		}
		$string .= "</tr>";

		foreach($users as $user) {

			// Yield

			$string .= "<tr>";
			$string .= "<td style='width: 150px'>" . $user["email"] . "</td>";
			$string .= "<td style='width: 50px'>" . $answerarray[$user["email"]][1][0]["group"] . "</td>";

			for($i = 1; $i <= 25; $i++) {
				$string .= "<td style='width: 150px'>" . $answerarray[$user["email"]][$i][0]["yield"] . "</td>";
			}
			$string .= "</tr>";
		}

		$string .= "<tr>";
		$string .= "<td style='width: 100px'>Money: </td>";
		for($i = 1; $i <= 26; $i++) {
			$string .= "<td style='width: 100px'></td>";
		}
		$string .= "</tr>";

		foreach($users as $user) {

			// Money
				
			$string .= "<tr>";
			$string .= "<td style='width: 150px'>" . $user["email"] . "</td>";
			$string .= "<td style='width: 50px'>" . $answerarray[$user["email"]][1][0]["group"] . "</td>";

			for($i = 1; $i <= 25; $i++) {
				$string .= "<td style='width: 150px'>" . $answerarray[$user["email"]][$i][0]["money"] . "</td>";
			}
			$string .= "</tr>";
		}

		$this->view->assign("result", $string);
	}

	public function resetAction() {
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->_getParam("id");

		if (!empty($id)) {
			$results = new InvestmentGame_Rounds();
			$where = $results->getAdapter()->quoteInto('treatments_id = ?', $id);
			$results->delete($where);
		}

	}

}