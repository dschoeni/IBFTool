<?php
class Viser_Controller_Action_Api extends Zend_Controller_Action {

	protected $user = null;
	protected $context = null;

	public function init() {
		set_time_limit(60);
		Zend_Layout::getMvcInstance()->disableLayout();

		if ($this->_hasParam("apikey")) {
			$t = new Users();
			$this->user = $t->fetchRow(array("apikey = ?" => $this->_getParam("apikey")));
		}

		$this->context = $this->_helper->getHelper('CompleteContext');
		$this->context->setAutoJsonSerialization(true);
		$this->addContexts();
		$this->context->initContext();
	}

	public function preDispatch() {
		if (!$this->user) {
			$this->_forward("nouser", "error");
		}
	}

	protected function addContexts() {
	}
}