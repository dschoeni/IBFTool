<?php
class ibftool_Controller_Plugin_Auth_AccessControl extends Zend_Controller_Plugin_Abstract
{
	public function __construct(Zend_Auth $auth, Zend_Acl $acl)
	{
		$this->_auth = $auth;
		$this->_acl  = $acl;
	}

	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		if (!$this->_auth->hasIdentity() && null !== $request->getPost('email') || null !== $request->getPost('password')) {

			// POST-Daten bereinigen
			$filter = new Zend_Filter_StripTags();

			$email = $filter->filter($request->getPost('email'));
			$password = $filter->filter($request->getPost('password'));

			if (empty($email) || empty($password)) {

			} else {
				$authAdapter = new ibftool_Plugin_Auth_AuthAdapter();
				$authAdapter->setIdentity($email);
				$authAdapter->setCredential($password);

				/*
				 $select = $authAdapter->getDbSelect();
				 $select->where('verified = 1');
				 */

				$result = $this->_auth->authenticate($authAdapter);
					
				if (!$result->isValid()) {
					$messages = $result->getMessages();
					$message = $messages[0];
				} else {
					$storage = $this->_auth->getStorage();

					// die gesamte Tabellenzeile in der Session speichern,
					// wobei das Passwort unterdrückt wird

					$users = new Users();
					$row = $users->fetchRow(array("userhash = ?" => $email));

					$row->verify();
					$row->password = "";

					$storage->write($row);
					
					if (Zend_Registry::getInstance()->get("config")->ibftool->savesession) {
						Zend_Session::rememberMe("1512000");
					}

					//$storage->write($authAdapter->getResultRowObject(null, 'password'));
				}

				//$registry = Zend_Registry::getInstance();
				//$view = $registry->view;

				if (isset($message)) {
					//echo "Die eingegebenen Daten sind nicht korrekt, oder wurden nicht per E-Mail frei geschalten.";
				}
	  }
		}
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($this->_auth->hasIdentity() && is_object($this->_auth->getIdentity())) {
			$role = $this->_auth->getIdentity()->role;
		} else {
			$role = 'guest';
		}

		$module = $request->getControllerName();
		// Ressourcen = Modul -> kann hier ge�ndert werden!
		$resource   = $module;
		if (!$this->_acl->has($resource)) {
			$resource = null;
		}

		if (!$this->_acl->isAllowed($role, $resource)) {

			if ($this->_auth->hasIdentity()) {
				// angemeldet, aber keine Rechte -> Fehler!
				$request->setModuleName('default');
				$request->setControllerName('error');
				$request->setActionName('noaccess');
			} else {
				// nicht angemeldet -> Login
				$request->setModuleName('default');
				$request->setControllerName('login');
				$request->setActionName('index');
				
			}
				
		}


	}
}
?>