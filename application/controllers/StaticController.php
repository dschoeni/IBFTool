<?php
class StaticController extends Zend_Controller_Action {

	public function preDispatch() {
		if (ibftool_Controller_Action_Helper_Treatment::getCurrentModule() != "static") {
			$this->_redirect("module/");
		}
	}

	public function indexAction() {
		$config = ibftool_Controller_Action_Helper_Treatment::getCurrentConfig();

		$doc = new DOMDocument();
		$doc->loadXML($config);

		$staticpage = $doc->getElementsByTagName("id")->item(0)->nodeValue;

		$pages = new Static_Pages();
		$page = $pages->find($staticpage);

		$this->view->assign("page", $page->current());
	}

	public function nextAction() {
		ibftool_Controller_Action_Helper_Treatment::completeCurrentModule();
		$this->_redirect("module/");
	}

}
