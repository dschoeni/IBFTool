<?php
class ibftool_View_Helper_FormShortText extends Zend_View_Helper_Abstract {
	public function formShortText($name, $value = null, $attribs = null) {
		return $this->view->partial("questionnaire/helper/formShortText.phtml", array("name" => $name, "value" => $value));
	}
}