<?php
class StaticController extends Zend_Controller_Action {

	public function preDispatch() {
		if (Sbftool_Controller_Action_Helper_Treatment::getCurrentModule() != "static") {
			$this->_redirect("module/");
		}
	}

	public function indexAction() {
		$config = Sbftool_Controller_Action_Helper_Treatment::getCurrentConfig();

		$doc = new DOMDocument();
		$doc->loadXML($config);

		$staticpage = $doc->getElementsByTagName("id")->item(0)->nodeValue;

		$pages = new Static_Pages();
		$page = $pages->find($staticpage);

		$this->view->assign("page", $page->current());
	}

	public function nextAction() {
		Sbftool_Controller_Action_Helper_Treatment::completeCurrentModule();
		$this->_redirect("module/");
	}

}
