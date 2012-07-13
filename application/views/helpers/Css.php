<?php
class Zend_View_Helper_Css extends Zend_View_Helper_Abstract
{
	function css() {
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$file_uri = 'sbftool/_files/css/' . $request->getControllerName(). '.css';
		echo $file_uri;
		if (file_exists($file_uri)) {
			$this->view->headLink()->appendStylesheet('/' . $file_uri);
		}
		 
		return $this->view->headLink();
	}
}