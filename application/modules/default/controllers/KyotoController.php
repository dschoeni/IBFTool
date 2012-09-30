<?php
class KyotoController extends Zend_Controller_Action {

	public function init() {
		set_time_limit(20);
		$this->context = $this->_helper->getHelper('CompleteContext');
		$this->context->setAutoJsonSerialization(true);
		$this->addContexts();
		$this->context->initContext();
	}

	protected function addContexts() {
		$this->context->addActionContext("update", "json");
		$this->context->addActionContext("updatepollution", "json");
		$this->context->addActionContext("buy", "json");
		$this->context->addActionContext("sell", "json");
		$this->context->addActionContext("changetechnology", "json");
	}

	public function preDispatch() {
		if (ibftool_Controller_Action_Helper_Treatment::getCurrentModule() != "kyoto") {
			$this->_helper->redirector("index", "module");
		}

		//$this->view->inlineScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/questionnaire/ajaxvalidation.js");
	}

	public function indexAction() {
		$this->view->inlineScript()->appendFile($this->getFrontController()->getBaseUrl() . "/js/kyoto/ajax.js");
		$this->view->headScript()->appendFile("/js/highcharts/highcharts.js");

		$players = new Kyoto_Players();
		$player = $players->fetchRow(array("ibftool_users_id" =>  Zend_Auth::getInstance()->getIdentity()->id));

		if ($player->type == "NGO") {
			$this->render("ngo");
		} else if ($player->type == "HighPolluter") {
			$this->render("polluter");
		}
	}

	public function updateAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$players = new Kyoto_Players();
		$player = $players->fetchRow(array("ibftool_users_id" =>  Zend_Auth::getInstance()->getIdentity()->id));

		if (count($player) == 0) {
			$this->registerPlayer();
		}

		$player->lastPoll = date("Y-m-d H:i:s");
		$player->save();
		
		$sessions = new Kyoto_Sessions();
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  ibftool_Controller_Action_Helper_Treatment::getID()));
		
		// TOOD: Make this dynamic
		$this->view->hasStarted = true;

		// TODO: Make rounds dynamic
		$this->view->registered = true;
		$this->view->playerdata = $player->toArray();
		$this->view->session = $session->toArray();
	}

	public function updatepollutionAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$players = new Kyoto_Players();
		$player = $players->fetchRow(array("ibftool_users_id" =>  Zend_Auth::getInstance()->getIdentity()->id));
		
		$sessions = new Kyoto_Sessions();
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  ibftool_Controller_Action_Helper_Treatment::getID()));
		
		$this->view->playerdata = $player->toArray();
		$this->view->session = $session->toArray();
		$this->view->pollution = $player->getPollution()->toArray();
		
		// TODO: Get Prices dynamically from Datebase
		$this->view->otherpollution = array("minimum" => 100, "maximum" => 500, "current" => 235, "development" => array(
				"[1, 0]",
				"[2, 50]",
				"[3, 100]",
				"[4, 150]",
				"[5, 220]",
				"[6, 230]"
			));
		
		$this->view->price = array("development" => array(
				"[1, 50]",
				"[2, 60]",
				"[3, 70]",
				"[4, 110]",
				"[5, 50]",
				"[6, 300]"
		));
	}

	private function registerPlayer() {
		$sessions = new Kyoto_Sessions();
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  ibftool_Controller_Action_Helper_Treatment::getID()));
		
		$players = new Kyoto_Players();
		$player = $players->fetchNew();

		$player->ibftool_users_id = Zend_Auth::getInstance()->getIdentity()->id;
		$player->ibftool_kyoto_sessions_id = 1;
		
		$player->balance = 2500;
		$player->pollution = null;
		$player->permissions = 0;
		$player->type = "NGO";
		
		$player->save();
	}
	
	
	// TODO: Only one offer per round can be made
	public function buyAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$players = new Kyoto_Players();
		$player = $players->fetchRow(array("ibftool_users_id" =>  Zend_Auth::getInstance()->getIdentity()->id));
		
		$sessions = new Kyoto_Sessions();
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  ibftool_Controller_Action_Helper_Treatment::getID()));

		// Check if all necessary parameters are given.
		if (!$this->_hasParam("quantity") || !$this->_hasParam("price")) {
			$this->view->result = "failed";
			return;
		}
		
		$quantity = $this->_getParam("quantity");
		$price = $this->_getParam("price");
		
		// Check if we have enough money to purchase something
		if (($quantity * $price) > $player->balance) {
			$this->view->result = "insufficient balance";
			return;
		}
		
		// Insert Offer into database
		$offers = new Kyoto_Offers();
		$offer = $offers->fetchNew();
		
		$offer->ibftool_kyoto_sessions_id = $session->id;
		$offer->ibftool_kyoto_players_id = $player->id; 
		$offer->quantity = $quantity;
		$offer->price = $price;
		$offer->buy = 1;
		
		$offer->save();
		
		$this->view->result = "success";
		$this->view->offer = $offer->toArray();
			
	}
	
	/*
	 * TODO: Only one offer per round can be made
	 * Additionally, most of the checks performed here could be done in JS on the client, to reduce DB-load
	 */
	public function sellAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$players = new Kyoto_Players();
		$player = $players->fetchRow(array("ibftool_users_id" =>  Zend_Auth::getInstance()->getIdentity()->id));
		
		$sessions = new Kyoto_Sessions();
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  ibftool_Controller_Action_Helper_Treatment::getID()));

		// Check if all necessary parameters are given.
		if (!$this->_hasParam("quantity") || !$this->_hasParam("price")) {
			$this->view->result = "failed";
			return;
		}
		
		$quantity = $this->_getParam("quantity");
		$price = $this->_getParam("price");
		
		// Check if we have enough money to purchase something
		if ($quantity > $player->permissions) {
			$this->view->result = "not enough permissions";
			return;
		}
		
		// Insert Offer into database
		$offers = new Kyoto_Offers();
		$offer = $offers->fetchNew();
		
		$offer->ibftool_kyoto_sessions_id = $session->id;
		$offer->ibftool_kyoto_players_id = $player->id; 
		$offer->quantity = $quantity;
		$offer->price = $price;
		$offer->buy = 1;
		
		$offer->save();
		
		$this->view->result = "success";
		$this->view->offer = $offer->toArray();
			
	}
	
	public function changetechnologyAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		$players = new Kyoto_Players();
		$player = $players->fetchRow(array("ibftool_users_id" =>  Zend_Auth::getInstance()->getIdentity()->id));
		$player->changeTechnology($this->_getParam("round"));
		
		$this->view->result = "success";
	}

}