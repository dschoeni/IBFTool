<?php
class RegistrationController extends Zend_Controller_Action {

	public function preDispatch() {
		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->_helper->redirector("index", "index");
		}
	}

	public function indexAction() {
		$form = new ibftool_Form_Registration();

		if (!empty($_POST)) {

			/*
			 * Check the POST Data whether it is valid.
			*/

			/*
			 $validator = new Zend_Validate_Identical($_POST['password']);
			$validator->setMessage('Die Kennw�rter stimmen nicht �berein!');
			$pwd = $form->getElement('password_check');
			$pwd->addValidator($validator);
			*/
				
			if ($form->isValid($_POST)) {
				$values = $form->getValues();

				$n = rand(10e16, 10e20);
				$password = base_convert($n, 10, 36);
				$hash = hash("sha1", time());

				$data = array(
						'email'	=> $values["email"],
						'userhash' => $hash,
						'password' => hash("sha1", $password),
						'verified' => 0,
						'role' => 'member',
				);

				$table = new Users();
				$row = $table->fetchRow(array("email = ?" => $values["email"]));

				if (empty($row)) {
					$row = $table->createRow($data);
					$row->save();

					//$config = array('ssl' => 'tls', 'port' => 587, 'auth' => 'login', 'username' => '', 'password' => '');
					//$transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);
						
					$mailstring = "Vielen Dank für Ihre Registrierung!";

					$mailstring .= "\n\n";
					$mailstring .= "Ihr Passwort zum Login: ";
					$mailstring .= $password;

					$mailstring .= "\nSie können sich mit Ihrer E-Mail-Adresse und diesem Passwort hier für den Test einloggen: www.bhfs.ch/trader";
					$mailstring .= "\nViel Erfolg!";

					$mail = new Zend_Mail();
					$mail->addTo($values["email"]);
					$mail->setSubject(Zend_Registry::getInstance()->get("config")->ibftool->title . " - Bestätigung der Accounterstellung");
					$mail->setFrom("trader@bf.uzh.ch", "IBFTool - Uni Zürich");
					$mail->setBodyText($mailstring);
					$mail->send($transport);
						
					$this->_helper->redirector("success", "registration");

				} else {
					$form->getElement("email")->addError("Die eingegebene E-Mail Adresse wird bereits verwendet.");
				}

			} else {
				$form->populate($_POST);
			}
		} else {

		}

		$this->view->assign("form", $form);
	}

	public function confirmAction() {
		$userhash = $this->_getParam("userhash");

		$this->view->assign("verified", false);

		if (!empty($userhash)) {
			$table = new Users();
			$row = $table->fetchRow(array("userhash = ?" => $userhash));

			if (!empty($row))
			{
				$row->verify();
				$this->view->assign("verified", true);
			}
		}

	}

	public function successAction() {

	}

}
