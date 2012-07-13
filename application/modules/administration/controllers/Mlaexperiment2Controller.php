<?php
class Administration_Mlaexperiment2Controller extends Zend_Controller_Action {

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
								'Content-Disposition' => "attachment; filename=mla_report_" . date("dmY") . ".xlsx")),
		);
		
		// Init the Context Switch Action helper
		$contextSwitch = $this->_helper->contextSwitch();
		
		// Add the new context
		$contextSwitch->setContexts($excelConfig);
		
		// Set the new context to the reports action
		$contextSwitch->addActionContext('result', 'excel');
		$contextSwitch->addActionContext('resultall', 'excel');
		
		// Initializes the action helper
		$contextSwitch->initContext();
	}

	public function resultAction() {

		$rounds = 36;

		$db = Zend_Db_Table::getDefaultAdapter();
		$config = Zend_Registry::get('config')->db;
		$prefix = $config->table_prefix;

		$users = $db->query("SELECT id, email FROM ibftool_users INNER JOIN ibftool_treatments_has_users thu ON thu.users_id = id WHERE thu.completed = 1");
		$results = $db->query("SELECT
				u.userhash,
				u.email,
				r.round,
				r.treatments_id,
				r.money
				FROM ibftool_mlaexperiment_results r INNER JOIN ibftool_users u ON r.users_id = u.id
				WHERE r.round = 36 ORDER BY u.userhash
				");

		$string = "<tr>";
		$string .= "<td style='width: 100px'>Userhash</td>";
		$string .= "<td style='width: 100px'>E-Mail</td>";
		$string .= "<td style='width: 100px'>MLA 1</td>";
		$string .= "<td style='width: 100px'>MLA 2</td>";
		$string .= "<td style='width: 100px'>MLA 3</td>";
		$string .= "</tr>";

		$users = $users->fetchAll();
		$results = $results->fetchAll();

		$string .= "<tr>";

		$previous = null;

		foreach($results as $result) {
			if ($previous != $result["userhash"]) {
				$string .= "</tr>";
				$string .= "<tr>";
				$string .= "<td>";
				$string .= $result["userhash"];
				$string .= "</td>";
				$string .= "<td>";
				$string .= $result["email"];
				$string .= "</td>";
			}
				
				
			$string .= "<td>";
			$string .= $result["money"];
			$string .= "</td>";
				
			$previous = $result["userhash"];
		}

		$string .= "</tr>";
		$string .= "</table>";

		$this->view->assign("result", $string);
		$this->view->assign("resultdata", $results);
	}
	
	public function resultallAction() {
		$users = new Users();
		$users = $users->fetchAll();

		$this->view->assign("users", $users);
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