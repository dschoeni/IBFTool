<?php
class Sbftool_Controller_Plugin_Menu extends Zend_Controller_Plugin_Abstract {
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		if ($request->getParam("format") != null) {
			return;
		}

		$actionStack = Zend_Controller_Front::getInstance()->getPlugin('Zend_Controller_Plugin_ActionStack');

		$menuAction = clone($request);
		$menuAction->setControllerName("menu");
		$menuAction->setActionName("index");
		$actionStack->pushStack($menuAction);
	}
}
