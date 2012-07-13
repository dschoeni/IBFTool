<?php
class ibftool_Form_Decorators_Payout extends Zend_Form_Decorator_Abstract
{
	public function render($content)
	{
		$markup = "";
		$markupEnd = "";
		
		if ($this->getOption("payout") == 1) {
			$markup = "<div class='questionnaire_payout'>";
			$markupEnd = "</div>";
		}

		return $markup . $content. $markupEnd;
	}
}