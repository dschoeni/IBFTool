<?php
class ibftool_Form_Abstract extends Zend_Form
{
	public $elementDecorators = array(
			'ViewHelper',
			'Errors',
			array('Description', array('tag' => 'p', 'class' => 'description')),
			array('Label', array('class' => 'form-label', 'requiredSuffix' => '*'))
	);

	public function render(Zend_View_Interface $view = null)
	{
		foreach($this->_elements as $element) {
			if (is_a($element, "Zend_Form_Element_Submit")) {
				$element->removeDecorator("Label");
				$element->autocomplete = false;
			}
		}

		return parent::render($view);
	}

}
