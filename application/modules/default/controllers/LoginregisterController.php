<?php
class LoginregisterController extends ibftool_Controller_Action {
	public function indexAction() {

		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->_helper->redirector("index", "index");
		}

		$registration = new ibftool_Form_Registration();
		$login = new ibftool_Form_Login();


		if (Zend_Registry::getInstance()->get("config")->ibftool->labormodus) {
			$this->render("login");
			$login->getElement("email")->setLabel("Login");
			$login->getElement("email")->setValue(php_uname("n"));
			$login->getElement("password")->setValue("labor");
		}

		if (!empty($_POST)) {
			$login->getElement("email")->addError("Bitte überprüfen Sie Ihre E-Mail Adresse.");
			$login->getElement("password")->addError("Bitte überprüfen Sie Ihr Passwort.");
		}


		$this->view->assign("login", $login);
		$this->view->assign("registration", $registration);
	}
}
