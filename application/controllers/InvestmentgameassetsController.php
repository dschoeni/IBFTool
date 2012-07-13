<?php
class InvestmentgameassetsController extends Zend_Controller_Action {

	public function preDispatch() {
		if (Sbftool_Controller_Action_Helper_Treatment::getCurrentModule() != "investmentgameassets") {
			$this->_helper->redirector("index", "module");
		}
	}

	public function indexAction() {
		/*
		 * Get Users Group
		*/
		$users = new Users();
		$user = $users->find(Zend_Auth::getInstance()->getIdentity()->id);
		$user = $user->current();

		$this->view->assign("group", $user->grp);

		/*
		 * Display corresponding Text
		*/

		$config = Sbftool_Controller_Action_Helper_Treatment::getCurrentConfig();

		$doc = new DOMDocument();
		$doc->loadXML($config);

		$this->view->assign("rounds", $doc->getElementsByTagName("rounds")->item(0)->nodeValue);
		$this->view->assign("money", $doc->getElementsByTagName("money")->item(0)->nodeValue);
		$this->view->assign("rates", $this->getGroupRate($doc, $user->grp));
	}

	private function getGroupRate($xml, $group) {
		$array = array();
		$rates = $xml->getElementsByTagName("group")->item($group-1)->getElementsByTagName("rate");

		$array = array(1 => $rates->item(0)->nodeValue,
				2 => $rates->item(1)->nodeValue);

		return $array;
	}

	public function roundAction() {
		$config = Sbftool_Controller_Action_Helper_Treatment::getCurrentConfig();
		$form = new Sbftool_Form_InvestmentGameAssets();

		$doc = new DOMDocument();
		$doc->loadXML($config);

		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$startingmoney = $doc->getElementsByTagName("money")->item(0)->nodeValue;

		$users = new Users();
		$user = $users->find(Zend_Auth::getInstance()->getIdentity()->id);
		$user = $user->current();

		$rounds = new InvestmentGameAssets_Rounds();
		$played_rounds = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id), "round DESC");

		/*
		 * Check whether round 20 has been played. Redirect to final site.
		*/

		if ($played_rounds->count() >= $roundstoplay) {
			$this->_helper->redirector("fin", "investmentgameassets");
		}

		/*
		 *
		*/

		if ($played_rounds->count() >= 1) {
			$row = $played_rounds->getRow(0);
		}

		/*
		 * Process POST from previous Round
		*/

		$form = new Sbftool_Form_InvestmentGameAssets();
		$form->setAction($this->getFrontController()->getBaseUrl() . "/investmentgameassets/process/");

		if ($played_rounds->count() == 0) {
			$asset_a_count = 10;
			$asset_b_count = 10;
			$asset_a_value = 50;
			$asset_b_value = 50;

			$form->getElement("asset_a_value")->setValue($asset_a_value);
			$form->getElement("asset_b_value")->setValue($asset_b_value);

			$form->getElement("asset_a_count")->setValue($asset_a_count);
			$form->getElement("asset_b_count")->setValue($asset_b_count);
		}

		if ($played_rounds->count() >= 1) {
			$this->view->assign("lastround", $row);

			$form->getElement("asset_a_value")->setValue($row->asset_a_value);
			$form->getElement("asset_b_value")->setValue($row->asset_b_value);

			$form->getElement("asset_a_count")->setValue($row->asset_a_count);
			$form->getElement("asset_b_count")->setValue($row->asset_b_count);
		}

		$this->view->assign("startingmoney", $startingmoney);
		$this->view->assign("asset_a_count", $asset_a_count);
		$this->view->assign("asset_a_value", $asset_a_value);
		$this->view->assign("asset_b_count", $asset_b_count);
		$this->view->assign("asset_b_value", $asset_b_value);
		$this->view->assign("round", $played_rounds->count());
		$this->view->assign("form", $form);

	}

	public function processAction() {
		$form = new Sbftool_Form_InvestmentGameAssets();
		$config = Sbftool_Controller_Action_Helper_Treatment::getCurrentConfig();

		$doc = new DOMDocument();
		$doc->loadXML($config);

		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$startingmoney = $doc->getElementsByTagName("money")->item(0)->nodeValue;

		if (!empty($_POST)) {
			if ($form->isValid($_POST)) {
				$group = Zend_Auth::getInstance()->getIdentity()->grp;
				$values = $form->getValues();

				$random = mt_rand(1, 2);

				$array_a = $this->getGroupRate($doc, 1);
				$yield_a = $array_a[$random];

				$random = mt_rand(1, 2);

				$array_b = $this->getGroupRate($doc, 2);
				$yield_b = $array_b[$random];

				$table = new InvestmentGameAssets_Rounds();
				$lastround = $table->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id), "round DESC", 1);
				$count = $lastround->count();

				if ($count == 0) {
					$money = $startingmoney;
					$played_rounds = 0;
				} else {
					$played_rounds = $count;
					$lastround = $lastround->current();
					$money = $lastround->money;
				}

				// checking whether the inputs are even possible, if not, redirect.
				if ($money - (($values["asset_a_count"] - $lastround->asset_a_count)*$lastround->asset_a_value) - (($values["asset_b_count"] - $lastround->asset_b_count)*$lastround->asset_b_value) < 0) {
					exit("Do not attempt to cheat.");
				}

				$data = array(
						'users_id' =>  Zend_Auth::getInstance()->getIdentity()->id,
						'round'	=> $lastround->round+1,
						'treatments_id' => Sbftool_Controller_Action_Helper_Treatment::getID(),
						'asset_a_count' => $values["asset_a_count"],
						'asset_a_value' => $values["asset_a_value"]+($values["asset_a_value"]*$yield_a)/100,
						'asset_b_count' => $values["asset_b_count"],
						'asset_b_value' => $values["asset_b_value"]+($values["asset_b_value"]*$yield_b)/100,
						'asset_a_yield' => $yield_a,
						'asset_b_yield' => $yield_b,
						'money' => $money - (($values["asset_a_count"] - $lastround->asset_a_count)*$lastround->asset_a_value) - (($values["asset_b_count"] - $lastround->asset_b_count)*$lastround->asset_b_value),
				);

				$row = $table->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "round = ?" => $lastround->round+1));

				if (empty($row)) {
					$row = $table->createRow($data);
					$row->save();
				} else {
					$row = $table->update($data, array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "round = ?" => $$lastround->round+1));
				}

				$this->_helper->redirector("round", "investmentgameassets");

			} else {
				$this->_helper->redirector("error", "investmentgameassets", "default");
			}
			$this->_helper->redirector("round", "investmentgameassets");
		}
		$this->_helper->redirector("round", "investmentgameassets");


	}

	public function finAction() {
		$config = Sbftool_Controller_Action_Helper_Treatment::getCurrentConfig();

		$doc = new DOMDocument();
		$doc->loadXML($config);

		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;

		$rounds = new InvestmentGame_Rounds();
		$row = $rounds->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "round = ?" => $roundstoplay));

		$this->view->assign("lastmoney", $row->money-$row->outcome);
		$this->view->assign("row", $row);
		$this->view->assign("rounds", $roundstoplay);
	}

	public function leaveAction() {
		Sbftool_Controller_Action_Helper_Treatment::completeCurrentModule();
		$this->_helper->redirector("index", "module");

	}

}
