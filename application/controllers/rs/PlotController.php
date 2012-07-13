<?php
class RS_PlotController extends Zend_Controller_Action {

	public function preDispatch() {
		if (ibftool_Controller_Action_Helper_Treatment::getCurrentModule() != "rs_plot") {
			$this->_redirect("module/");
		}
	}

	public function indexAction() {
		$this->view->headScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/highcharts/highcharts.js");
		$this->view->headScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/jquery-ui.js");
		$this->view->inlineScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/rs/plot.js");
		
		$form = new Zend_Form();
		$form->setName("data");
		$form->setAction("/ibftool/rs_plot/summary");
		$form->setMethod("post");
		$form->setEnctype("application/json");
		
		$hidden = new Zend_Form_Element_Hidden("hiddenData");
		$form->addElement($hidden);
		
		$this->view->assign("form", $form);
	}
	
	public function summaryAction() {
		$this->view->headScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/highcharts/highcharts.js");
		$this->view->headScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/jquery-ui.js");
		$this->view->inlineScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/rs/summary.js");
		
		if (!empty($_POST)) {
			$this->view->assign("post", $_POST["hiddenData"]);
		}		
	}
	
	public function ajaxsummaryAction() {
		$this->_helper->viewRenderer->setNoRender();
		$this->view->inlineScript()->appendFile($this->getFrontController()->getBaseUrl() . "/_files/js/rs/ajaxsummary.js");
	}

	public function nextAction() {
		ibftool_Controller_Action_Helper_Treatment::completeCurrentModule();
		$this->_redirect("module/");
	}

}
