<?php
class InvestmentgameController extends Zend_Controller_Action {

	public function preDispatch() {
		if (Sbftool_Controller_Action_Helper_Treatment::getCurrentModule() != "investmentgame") {
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

		$rates = $xml->getElementsByTagName("group" . $group)->item(0)->getElementsByTagName("rate");

		$array = array(1 => $rates->item(0)->nodeValue,
				2 => $rates->item(1)->nodeValue,
				3 => $rates->item(2)->nodeValue,
				4 => $rates->item(3)->nodeValue);

		return $array;
	}

	public function roundAction() {
		$this->view->headLink()->appendStylesheet('/_files/css/investmentgame.css');
		$config = Sbftool_Controller_Action_Helper_Treatment::getCurrentConfig();

		$doc = new DOMDocument();
		$doc->loadXML($config);

		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$startingmoney = $doc->getElementsByTagName("money")->item(0)->nodeValue;

		$users = new Users();
		$user = $users->find(Zend_Auth::getInstance()->getIdentity()->id);
		$user = $user->current();

		$rounds = new InvestmentGame_Rounds();
		$played_rounds = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id), "round DESC");

		/*
		 * Check whether round 20 has been played. Redirect to final site.
		*/

		if ($played_rounds->count() >= $roundstoplay) {
			$this->_helper->redirector("fin", "investmentgame");
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

		$form = new Sbftool_Form_InvestmentGame();
		$form->setAction($this->getFrontController()->getBaseUrl() . "/investmentgame/process/");
		$form->getElement("round")->setValue($played_rounds->count());

		if (!empty($_POST)) {
			if (!$form->isValid($_POST)) {
				$form->populate($_POST);
			}
		}

		$this->view->assign("group", $user->grp);
		$this->view->assign("round", $played_rounds->count());

		if ($played_rounds->count() >= 1) {
			$this->view->assign("row", $row);
			$this->view->assign("lastmoney", $row->money - $row->outcome);
		}

		$this->view->assign("startingmoney", $startingmoney);
		$this->view->assign("form", $form);

		$this->view->assign("rates", $this->getGroupRate($doc, $user->grp));

	}

	public function processAction() {
		$form = new Sbftool_Form_InvestmentGame();

		$config = Sbftool_Controller_Action_Helper_Treatment::getCurrentConfig();

		$doc = new DOMDocument();
		$doc->loadXML($config);

		$roundstoplay = $doc->getElementsByTagName("rounds")->item(0)->nodeValue;
		$startingmoney = $doc->getElementsByTagName("money")->item(0)->nodeValue;

		if (!empty($_POST)) {
			if ($form->isValid($_POST)) {
				$users = new Users();
				$user = $users->find(Zend_Auth::getInstance()->getIdentity()->id);
				$user = $user->current();
				$group = $user->grp;

				$values = $form->getValues();

				$random = mt_rand(1, 4);

				$array = $this->getGroupRate($doc, $group);
				$yield = $array[$random];

				$table = new InvestmentGame_Rounds();
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

				$outcome = (((($values["investment"]*$money)/100)*$yield)/100);

				$data = array(
						'users_id' =>  Zend_Auth::getInstance()->getIdentity()->id,
						'round'	=> $values["round"]+1,
						'treatments_id' => Sbftool_Controller_Action_Helper_Treatment::getID(),
						'investment' => $values["investment"],
						'yield' => $yield,
						'money' => $money+$outcome,
						'outcome' => $outcome
				);

				$row = $table->fetchRow(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "round = ?" => $values["round"]+1));

				if (empty($row)) {
					$row = $table->createRow($data);
					$row->save();
				} else {
					$row = $table->update($data, array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id, "round = ?" => $values["round"]+1));
				}

				$this->_helper->redirector("round", "investmentgame");

			} else {
				return $this->_helper->redirector("round", "game", "default");
			}
			$this->_helper->redirector("round", "investmentgame");

		}
		$this->_helper->redirector("round", "investmentgame");


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
