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

		// TOOD: Make this dynamic
		$this->view->hasStarted = true;

		// TODO: Make rounds dynamic
		$this->view->round = 1;
		$this->view->registered = true;
		$this->view->playerdata = $player->toArray();
		$treatmentId = ibftool_Controller_Action_Helper_Treatment::getID();
	}

	public function updatepollutionAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		
		// TODO: Get Prices dynamically from Datebase
		$this->view->pollution = array("minimum" => 100, "maximum" => 500, "current" => 235, "development" => array(
				"[1, 50]",
				"[2, 60]",
				"[3, 70]",
				"[4, 110]",
				"[5, 50]",
				"[6, 300]"
			));
	}

	private function registerPlayer() {
		$players = new Kyoto_Players();
		$player = $players->fetchNew();

		$player->ibftool_users_id = Zend_Auth::getInstance()->getIdentity()->id;
		$player->ibftool_kyoto_sessions_id = 1;
		$player->balance = 2500;
		$player->pollution = 0;
		$player->permissions = 0;
		$player->type = "NGO";

		$player->save();
	}

}