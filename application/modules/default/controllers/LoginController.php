<?php
class LoginController extends ibftool_Controller_Action {
	
	public function indexAction() {
		
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->_redirect("index/");
		}

		$registration = new ibftool_Form_Registration();
		$login = new ibftool_Form_Login();

		if (!empty($_POST)) {
			$login->getElement("email")->addError("Bitte überprüfen Sie Ihren Login-Namen.");
			$login->getElement("password")->addError("Bitte überprüfen Sie Ihr Passwort.");
		}

		if (Zend_Registry::getInstance()->get("config")->ibftool->labormodus) {
			$login->getElement("email")->setLabel("Login");
			//$login->getElement("email")->setValue(gethostbyaddr($_SERVER['REMOTE_ADDR']));
			$login->getElement("password")->renderPassword = true;
			//$login->getElement("password")->setValue("labor");
				
			$this->view->assign("login", $login);
			$this->render("labor");
		}

		$this->view->assign("login", $login);
		$this->view->assign("registration", $registration);

	}

	/**
	 * Hier wird der aktuelle Benutzer ausgeloggt.
	 */
	public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Session::destroy();
		$this->_redirect("/");
	}

	/**
	 * Hier wird dem Benutzer eine Fehlermeldung angezeigt, falls sein Login fehlschlägt.
	 */
	public function errorAction(){
		$this->view->assign("form", new ibftool_Form_Login());
	}
}

