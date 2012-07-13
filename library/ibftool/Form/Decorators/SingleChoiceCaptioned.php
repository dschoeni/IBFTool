<?php
class ibftool_Form_Decorators_SingleChoiceCaptioned extends Zend_Form_Decorator_Abstract
{
	protected $_format = '<dt id="%s"><label for="%s">%s</label></dt>';
	protected $_button = '<input type="radio" name="%s" id="%s" value="%s" %s>%s';
	private $firstCaption;
	private $lastCaption;
	
	public function render($content)
	{
		$element = $this->getElement();
		$name    = htmlentities($element->getFullyQualifiedName());
		$label   = $element->getLabel();
		$id      = htmlentities($element->getId());
		$value   = htmlentities($element->getValue());
		$options = $element->getMultiOptions();
		
		$markup = sprintf($this->_format, $id . "-label", $id, $label);
		$i = 1;
		
		$markup .= '<dd id=' . $id . '-element>' .  $this->getOption("leftCaption") . ' ';
		foreach($options as $option) {

			if ($option == $value) {
				$selected = "checked";
			} else {
				$selected = "";
			}
			
			$markup .= sprintf($this->_button, $id, $id . "_" . $i, $option, $selected ,$option);
			$i++;
		}
		
		$markup .= ' ' . $this->getOption("rightCaption") . '</dd>';
		
		return $markup;
	}
}