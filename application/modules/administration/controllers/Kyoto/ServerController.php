<?php
class Administration_Kyoto_ServerController extends Zend_Controller_Action {

	public function init() {
		set_time_limit(20);
		$this->context = $this->_helper->getHelper('CompleteContext');
		$this->context->setAutoJsonSerialization(true);
		$this->addContexts();
		$this->context->initContext();
	}

	protected function addContexts() {
		$this->context->addActionContext("nextround", "json");
	}
	
	public function indexAction() {
		$this->view->inlineScript()->appendFile($this->getFrontController()->getBaseUrl() . "/js/kyoto/server.js");
		$sessions = new Kyoto_Sessions();
		// TODO Make Treatmentselection dynamically
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  1));
		
		$this->view->session = $session;
	}
	
	public function nextroundAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$sessions = new Kyoto_Sessions();
		// TODO Make Treatmentselection dynamically
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  1));
		$session->currentround++;
		$session->currentRoundTime = time();
		$session->save();
		
		$this->view->currentround = $session->currentround;
		$this->view->result = "success";
		return;
	}
	
}
