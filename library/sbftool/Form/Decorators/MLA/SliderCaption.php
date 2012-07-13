<?php
class Sbftool_Form_Decorators_MLA_SliderCaption extends Zend_Form_Decorator_Abstract
{
	
	public function render($content)
	{
		//$content = str_replace("<div class=", $this->getOption("left") . "<div class=" , $content);
		//$content = str_replace("</dd>", $this->getOption("right") . "</dd>" , $content);
		return $content;
	}
	
}