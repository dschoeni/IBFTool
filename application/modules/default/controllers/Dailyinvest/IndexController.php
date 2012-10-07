<?php
class Dailyinvest_indexController extends Zend_Controller_Action {

	public function preDispatch() {
		if (ibftool_Controller_Action_Helper_Treatment::getCurrentModule() != "dailyinvest_index") {
			$this->_helper->redirector("index", "module");
		}
	}

	public function indexAction() {
		$users = new Users();
		$user = $users->find(Zend_Auth::getInstance()->getIdentity()->id);
		$user = $user->current();
		
		$rounds = new DailyInvest_Rounds();
		
		if ($rounds->hasAlreadyPlayed($user->id)) {
			$this->_helper->redirector("result", "dailyinvest_index");
		}
		
		$lastround = $rounds->fetchAll(array("users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id), "created DESC", 1);
		
		$config = ibftool_Controller_Action_Helper_Treatment::getCurrentConfig();

		$form = new ibftool_Form_DailyInvest();
		
		$this->view->money = 1;
		$this->view->form = $form;
		
		if ($lastround->count() > 0) {
			$this->view->lastround = $lastround;
		}
		
		if (!empty($_POST)) {
			if ($form->isValid($_POST)) {
				$values = $form->getValues();
				$random = mt_rand(0, 3);
		
				$array = array(-0.5, 0.5, -0.25, 0.25);
				$yield = $array[$random];
		
				$count = $lastround->count();
		
				if ($count == 0) {
					$money = 1;
					$played_rounds = 0;
				} else {
					$played_rounds = $count;
					$lastround = $lastround->current();
					$money = $lastround->money;
				}
		
				$outcome = (((($values["investment"]*$money)/100)*$yield)/100);
		
				$data = array(
						'users_id' =>  Zend_Auth::getInstance()->getIdentity()->id,
						'treatments_id' => ibftool_Controller_Action_Helper_Treatment::getID(),
						'investment' => $values["investment"],
						'yield' => $yield,
						'money' => $money+$outcome,
						'outcome' => $outcome
				);
		
				$row = $rounds->createRow($data);
				$row->save();

				$this->_helper->redirector("result", "dailyinvest_index");
			}
		}
	}

	public function resultAction() {
		
	}

	public function leaveAction() {
		ibftool_Controller_Action_Helper_Treatment::completeCurrentModule();
		$this->_helper->redirector("index", "module");
	}

}
