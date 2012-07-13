<?php
class ibftool_Controller_Plugin_AjaxCheck extends Zend_Controller_Plugin_Abstract
{
  public function preDispatch(Zend_Controller_Request_Abstract $request)
  {
    //If the request is not an XHR, do nothing.
    if(!$request->isXmlHttpRequest())
      return;
	

    
  }
}
?>