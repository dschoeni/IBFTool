<?php
class Sbftool_Form_Decorators_Currency extends Zend_Form_Decorator_Abstract
{
	public function render($content)
	{
		return str_replace("</dd>", " " . $this->getOption("currency") . "</dd>" , $content);
	}
}