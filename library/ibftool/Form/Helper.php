<?php
class ibftool_Form_Helper {
	private function __construct() { // Privater Konstruktor, Helper Klasse!
	}
	
	public static function styleFormQuestionnaire(Zend_Form $form) {
		$form->clearDecorators();
		$form->loadDefaultDecorators();
		
		$form->removeDecorator("Form");
		$form->removeDecorator("HtmlTag");
		
		foreach ($form->getElements() as $e) self::styleFormQuestionnaireElement($e);
	}
	
	private static function styleFormQuestionnaireElement(Zend_Form_Element $element) {

	}

	public static function styleFormHorizontal(Zend_Form $form) {
		$form->clearDecorators();
		$form->loadDefaultDecorators();

		$form->removeDecorator("Form");
		$form->removeDecorator("HtmlTag");

		$form->addDecorator('Form', array('class' => 'form-horizontal'));

		foreach ($form->getElements() as $e) self::styleFormHorizontalElement($e);
	}

	private static function styleFormHorizontalElement(Zend_Form_Element $element) {
		$decorators = $element->getDecorators();

		$file = null;
		if (isset($decorators["Zend_Form_Decorator_File"])) {
			$file = $decorators["Zend_Form_Decorator_File"];
		}

		$element->clearDecorators();

		if ($file) {
			$element->addDecorator($file);
		} else {
			if ($element instanceof ZendX_JQuery_Form_Element_UiWidget) {
				$element->addDecorator(new ZendX_JQuery_Form_Decorator_UiWidgetElement()); // = ViewHelper
			} else {
				$element->addDecorator("ViewHelper");
			}
		}

		$element->addDecorator("Description", array("tag" => "p", "class" => "help-block", "escape" => false));
		$element->addDecorator("Errors", array("class" => 'help-inline'));

		$element->addDecorator(array('DivControls' => "HtmlTag"), array("tag" => "div", "class" => "controls"));

		if ($element instanceof Zend_Form_Element_Submit) {
		} else {
			$element->addDecorator('Label', array("class" => "control-label"));
		}

		if ($element instanceof Zend_Form_Element_Textarea) {
			$element->setAttrib("class", "input-xlarge");
		}

		if ($element instanceof Zend_Form_Element_Multi) {
			$element->setSeparator('');
			if ($element instanceof Zend_Form_Element_Radio) {
				$element->setAttrib("label_class", "radio");
			} else if ($element instanceof Zend_Form_Element_MultiCheckbox) {
				$element->setAttrib("label_class", "checkbox");
			}
		}

		if ($element instanceof ZendX_JQuery_Form_Element_DatePicker) {
			$element->setJQueryParam("dateFormat", "yy-mm-dd");
		}

		$element->addDecorator(array('DivControlGroup' => "HtmlTag"), array("tag" => "div", "class" => "control-group"));

		if ($element instanceof Zend_Form_Element_Hidden) {
			$element->getDecorator("DivControlGroup")->setOption("style", "display: none");
		}

		if ($element instanceof Zend_Form_Element_Submit) {
			$element->setAttrib("class", "btn btn-primary");
			$element->removeDecorator("DivControls");
			$element->getDecorator("DivControlGroup")->setOption("class", "form-actions");
		}
	}

	public static function styleWithTable(Zend_Form $form) {
		$form->clearDecorators();
		$form->loadDefaultDecorators();

		$form->removeDecorator("Form");
		$form->removeDecorator("HtmlTag");

		//		$this->addDecorator("HtmlTag", array('tag' => 'div', 'class' => 'zend_form'));

		$form->addDecorator("HtmlTag", array('tag' => 'table', 'class' => 'form'));
		$form->addDecorator('Form');

		foreach ($form->getElements() as $e) self::decorateElement($e);
	}

	private static function decorateElement(Zend_Form_Element $element) {
		$decorators = $element->getDecorators();

		$file = null;
		if (isset($decorators["Zend_Form_Decorator_File"])) {
			$file = $decorators["Zend_Form_Decorator_File"];
		}

		$element->clearDecorators();

		if ($file) {
			$element->addDecorator($file);
		} else {
			$element->addDecorator("ViewHelper");
		}

		$element->addDecorator("Description", array("tag" => "div", "escape" => false));
		//$element->addDecorator(array('DivHtmlTag' => "HtmlTag"), array("tag" => "div", "placement" => Zend_Form_Decorator_HtmlTag::APPEND, "style" => "clear: both"));
		$element->addDecorator("Errors");
		$element->addDecorator(array('ElementClear' => "HtmlTag"), array("tag" => "div", "class" => "clear", "placement" => Zend_Form_Decorator_Abstract::APPEND));
		$element->addDecorator("HtmlTag", array("tag" => "td", "class" => "element"));

		if ($element instanceof Zend_Form_Element_Submit) {
			$element->addDecorator(array('LabelTag' => 'HtmlTag'), array("tag" => "td", "placement" => Zend_Form_Decorator_Abstract::PREPEND));
		} else {
			$element->addDecorator('Label', array("tag" => "td"));
		}

		$element->addDecorator(array('TrTag' => "HtmlTag"), array("tag" => "tr", "class" => "element-group"));

		if ($element instanceof Zend_Form_Element_Hidden) {
			$element->getDecorator("TrTag")->setOption("style", "display: none");
		}

		if ($element instanceof Zend_Form_Element_Multi) {
			$element->setSeparator('');
		}

		if ($element->isRequired()) {
			$class = $element->getDecorator("TrTag")->getOption("class");
			$class .= " required";

			$element->getDecorator("TrTag")->setOption("class", $class);
		}
	}

	public static function styleForMobile(Zend_Form $form) {
		$form->clearDecorators();
		$form->loadDefaultDecorators();

		$form->removeDecorator("HtmlTag");

		foreach ($form->getElements() as $e) self::decorateElementForMobile($e);
	}

	private static function decorateElementForMobile(Zend_Form_Element $element) {
		$decorators = $element->getDecorators();

		$file = null;
		if (isset($decorators["Zend_Form_Decorator_File"])) {
			$file = $decorators["Zend_Form_Decorator_File"];
		}

		$element->clearDecorators();

		if ($file) {
			$element->addDecorator($file);
		} else {
			$element->addDecorator("ViewHelper");
		}

		$element->addDecorator("Description", array("tag" => "p", "escape" => false));
		//$element->addDecorator(array('DivHtmlTag' => "HtmlTag"), array("tag" => "div", "placement" => Zend_Form_Decorator_HtmlTag::APPEND, "style" => "clear: both"));
		$element->addDecorator("Errors");
		$element->addDecorator('Label');

		$element->addDecorator(array('FieldContain' => "HtmlTag"), array("tag" => "div", "data-role" => "fieldcontain"));

		if ($element instanceof Zend_Form_Element_Hidden) {
			$element->getDecorator("FieldContain")->setOption("style", "display: none");
		}

		if ($element instanceof Zend_Form_Element_Submit) {
			$element->removeDecorator('Label');
		}

		if ($element instanceof Zend_Form_Element_Multi) {
			$element->setSeparator('');
		}

		if ($element->isRequired()) {
			$class = $element->getDecorator("FieldContain")->getOption("class");
			$class .= " required";

			$element->getDecorator("FieldContain")->setOption("class", $class);
		}
	}
}