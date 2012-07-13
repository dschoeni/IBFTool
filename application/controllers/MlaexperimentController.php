<?php
class MlaexperimentController extends Zend_Controller_Action {

	public function preDispatch() {
		if (Sbftool_Controller_Action_Helper_Treatment::getCurrentModule() != "mlaexperiment") {
			$this->_helper->redirector("index", "module");
		}
	}

	public function indexAction() {
		//var_dump(Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId());
	}

	public function introduction1Action() {

	}

	public function introduction2Action() {

	}

	public function introduction3Action() {
		$doc = $this->getConfigDoc();
		$cycle_length = $doc->getElementsByTagName("cycle_length")->item(0)->nodeValue;

		$this->view->assign("cycle_length", $cycle_length);

		if ($cycle_length > 1) {
			$this->renderScript('mlaexperiment/introduction3_L.phtml');
		} else {
			$this->renderScript('mlaexperiment/introduction3_H.phtml');
		}
	}

	public function introduction4Action() {

	}

	private function getGroupRate($xml, $group) {
		$array = array();

		$rates = $xml->getElementsByTagName("group" . $group)->item(0)->getElementsByTagName("rate");

		$array = array(1 => $rates->item(0)->nodeValue,
				2 => $rates->item(1)->nodeValue,
				3 => $rates->item(2)->nodeValue,
				4 => $rates->item(3)->nodeValue);

		return $array;
	}

	public function roundAction() {
		$this->view->headLink()->appendStylesheet('/sbftool/_files/css/mlaexperiment.css');

		$doc = $this->getConfigDoc();

		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$startingmoney = $doc->getElementsByTagName("money")->item(0)->nodeValue;
		$cycle_length = $doc->getElementsByTagName("cycle_length")->item(0)->nodeValue;

		$users = new Users();
		$user = $users->find(Zend_Auth::getInstance()->getIdentity()->id);
		$user = $user->current();

		$rounds = new MLAExperiment_Rounds();
		$played_rounds = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC");

		/*
		 * Check whether rounds have been played. Redirect to final site.
		*/

		if ($played_rounds->count() >= $roundstoplay) {
			$this->_helper->redirector("result", "mlaexperiment");
		}

		if ($played_rounds->count() >= 1) {
			$row = $played_rounds->getRow(0);
		}

		/*
		 * Process POST from previous Round
		*/

		$form = new Sbftool_Form_MLAExperiment();
		$form->setAction($this->getFrontController()->getBaseUrl() . "/mlaexperiment/process/");
		$form->getElement("round")->setValue($played_rounds->count());

		$this->view->assign("group", $user->grp);
		$this->view->assign("round", $played_rounds->count());
		$this->view->assign("money", $startingmoney);

		if ($played_rounds->count() >= 1) {
			$this->view->assign("row", $row);
		}

		if (($played_rounds->count() % $cycle_length == 0) && ($played_rounds->count() != 0)) {
			$this->view->assign("result", true);

			$rows = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC", $cycle_length);
			$outcome = 0;

			foreach($rows as $row) {
				$outcome += $row->outcome;
			}

			// Display results
			$this->view->assign("outcome", $outcome);

		}

		$this->view->assign("cycle_length", $cycle_length);
		$this->view->assign("form", $form);

		$lastround = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getID(), "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC", 1);
		//echo $lastround->current()->money;

	}

	public function processAction() {
		$form = new Sbftool_Form_MLAExperiment();
		$doc = $this->getConfigDoc();

		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$startingmoney = $doc->getElementsByTagName("money")->item(0)->nodeValue;
		$cycle_length = $doc->getElementsByTagName("cycle_length")->item(0)->nodeValue;

		$table = new MLAExperiment_Rounds();
		$lastround = $table->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getID(), "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC", 1);

		$money = 0;

		if ($lastround->count() !=0) {
			$lastround = $lastround->current();
			$money = $lastround->money;
		}

		if (!empty($_POST)) {
			if ($form->isValid($_POST)) {
				$users = new Users();
				$user = $users->find(Zend_Auth::getInstance()->getIdentity()->id);
				$user = $user->current();

				$group = $user->grp;
				$values = $form->getValues();

				// Disabled because of externel CSV-File to parse Values from
				// 				$random = mt_rand(1,3);
				//$array = $this->getGroupRate($doc, $group);

				$high_prob_rate = $doc->getElementsByTagName("high_prob_rate")->item(0)->nodeValue;
				$low_prob_rate = $doc->getElementsByTagName("low_prob_rate")->item(0)->nodeValue;

				$fileinput = file('_files/excel/mla_random.csv') or die ("cannot open required inputfile.");
				$csv_lines = explode(";", $fileinput[$group]);

				echo $csv_lines[$values["round"]+1];

				if($csv_lines[$values["round"]+1] == 1) {
					$yield = $high_prob_rate;
				} else {
					$yield = $low_prob_rate;
				}

				fclose($fp);

				$actualoutcome = ((($values["amount"]/100)*$yield/100));
				$safemoney = $startingmoney - $values["amount"]/100;
				
// 				if ($actualoutcome < 0) {
// 					$outcome = ($startingmoney-($values["amount"]/100));
// 				} else {
// 					$outcome = $startingmoney+$actualoutcome+($startingmoney-($values["amount"]/100));
// 				}

				$outcome = $startingmoney + $actualoutcome;

				$data = array(
						'users_id' =>  Zend_Auth::getInstance()->getIdentity()->id,
						'round'	=> $values["round"]+1,
						"treatments_id" => Sbftool_Controller_Action_Helper_Treatment::getID(),
						"treatments_has_module_id" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId(),
						'investment' => $values["amount"]/100,
						'yield' => $yield,
						'money' => $money+$outcome,
						'outcome' => $outcome
				);

				$row = $table->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getID(), "round = ?" => $values["round"]+1, "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()));

				if (empty($row)) {
					$row = $table->createRow($data);
					$row->save();
				} else {
						
				}

			} else {
				//$this->_helper->redirector("round", "mlaexperiment");
			}
		}
		$this->_helper->redirector("round", "mlaexperiment");


	}

	public function confirmAction() {
		$doc = $this->getConfigDoc();

		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$cycle_length = $doc->getElementsByTagName("cycle_length")->item(0)->nodeValue;

		$table = new MLAExperiment_Rounds();
		$row_last = $table->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getID(), "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC");

		$playedrounds = $row_last->count();


		if ($playedrounds == $cycle_length) {
			$money_past = $doc->getElementsByTagName("money")->item(0)->nodeValue;
		} else {
			$row_past = $table->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getID(), "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC", 1, 3);
			$money_past = $row_past->current()->money;
		}

		// Check if user is authorised to see this page.
		if ($playedrounds % $cycle_length != 0) {
			//$this->_helper->redirector("round", "mlaexperiment");
		}

		// Display results
		$this->view->assign("cycle_length", $cycle_length);
		$this->view->assign("money_last", $row_last->current()->money);
		$this->view->assign("money_past", $money_past);

	}

	private function getConfigDoc() {
		$config = Sbftool_Controller_Action_Helper_Treatment::getCurrentConfig();
		$doc = new DOMDocument();
		$doc->loadXML($config);

		return $doc;
	}

	public function resultAction() {
		$doc = $this->getConfigDoc();

		$startingmoney = $doc->getElementsByTagName("money")->item(0)->nodeValue;
		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$cycle_length = $doc->getElementsByTagName("cycle_length")->item(0)->nodeValue;

		$rounds = new MLAExperiment_Rounds();
		$row = $rounds->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "round = ?" => $roundstoplay));

		$this->view->assign("cycle_length", $cycle_length);
		$this->view->assign("row", $row);
		$this->view->assign("rounds", $roundstoplay);
		$this->view->assign("money", $startingmoney);

		$rows = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getID(), "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC", $cycle_length);
		$outcome = 0;

		foreach($rows as $row) {
			$outcome += $row->outcome;
		}

		// Display results
		$this->view->assign("outcome", $outcome);

		$result = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getID(), "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC", 1);
		$this->view->assign("result", $result->current()->money);
	}

	public function finAction() {
		$doc = $this->getConfigDoc();

		$startingmoney = $doc->getElementsByTagName("money")->item(0)->nodeValue;
		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$cycle_length = $doc->getElementsByTagName("cycle_length")->item(0)->nodeValue;

		$rounds = new MLAExperiment_Rounds();
		$row = $rounds->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "round = ?" => $roundstoplay));

		$this->view->assign("cycle_length", $cycle_length);
		$this->view->assign("row", $row);
		$this->view->assign("rounds", $roundstoplay);
		$this->view->assign("money", $startingmoney);

		$rows = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getID(), "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC");
		$outcome = 0;

		foreach($rows as $row) {
			$outcome += $row->outcome;
		}

		// Display results
		$this->view->assign("outcome", $outcome);

		//echo $outcome;

		$result = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "treatments_has_module_id = ?" => Sbftool_Controller_Action_Helper_Treatment::getCurrentModuleId()), "round DESC", 1);
		$this->view->assign("result", $result->current()->money);
	}

	public function leaveAction() {
		Sbftool_Controller_Action_Helper_Treatment::completeCurrentModule();
		$this->_helper->redirector("index", "module");
	}

}
