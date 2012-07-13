<?php
class ibftool_Form_Decorators_RS_DoubleNumber extends Zend_Form_Decorator_Abstract
{
	protected $_format = '<dt id="%s"><label for="%s">%s</label></dt>';
	protected $_button = '<input size="2" type="text" name="%s" id="%s" value="%s" autocomplete="off">';
	
	public function render($content)
	{
		$element = $this->getElement();
		$name    = htmlentities($element->getFullyQualifiedName());
		$label   = htmlentities($element->getLabel());
		$value   = $element->getValue();
		$id 	 = htmlentities($element->getId());
		
		$ecu = $this->getOption("ecu");
		$firstColumnArray = explode("#", $this->getOption("firstColumn"));
		$secondColumnArray = explode("#", $this->getOption("secondColumn"));
		$thirdColumnArray = explode("#", $this->getOption("thirdColumn"));

		if (!isset($value[0]) && !isset($value[1])) {
			$value[0] = "";
			$value[1] = "";
		}
		
		$markup = sprintf($this->_format, $id . "-label", $id, $label);
		$markup .= '<dd id=' . $id . '-element>';
		$markup .= "In 70 von 100 Fällen glaube ich wird der Ertrag, den ich mit meiner Vermögensaufteilung erzielen werde, zwischen -";
		$markup .= sprintf($this->_button, $id . "[]", $id, $value[0]);
		$markup .= "% und +";
		$markup .= sprintf($this->_button, $id . "[]", $id, $value[1]);
		$markup .= "% liegen.";
		$markup .= '</dd>';
		
		return $markup;
	}
	
}