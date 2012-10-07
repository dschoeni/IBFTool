<?php
class ibftool_Form_DailyInvest extends Zend_Form {
	
	public function __construct() {
		parent::__construct();

		$this->setMethod("post");
		$this->setName("mlaexperiment");
		
		$amount = new Zend_Form_Element_Text("amount");
		$amount->setRequired(true);
		$amount->setLabel("In dieser Runde investiere ich in das riskante Investment:");
		$amount->setAttrib("onchange", "setSliderAmount()");
		$amount->addValidator(new Zend_Validate_Between(0, 100));
		$amount->addValidator(new Zend_Validate_Digits());
		$amount->addErrorMessage("Bitte tragen sie eine Zahl von 0 bis 100 ein.");
		$amount->addDecorator(new ibftool_Form_Decorators_Currency(array("currency" => "Rappen")));
		$amount->setAttrib("autocomplete", "off");
		$this->addElement($amount);
		
		$slider = new ZendX_JQuery_Form_Element_Slider("investment");
		$slider->setRequired(true);
		$slider->setLabel("<div class='row'><span class='span4'>0 Rappen</span><span class='span2' style='text-align: right'>100 Rappen</span></div>");
		$slider->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$slider->setJQueryParams(array(
		  'min' => 0,
		  'max' => 100,
		  'value' => 0,
		  'step' => 1,
		  'slide' => new Zend_Json_Expr("function(event, ui) {
		   	document.getElementById('amount').value = ui.value;
		   	$('.ui-slider-handle').show();
		   	document.getElementById('Weiter').style.display = '';	
		   }"),
			'create' => new Zend_Json_Expr("function(event, ui) {
			if (document.getElementById('amount').disabled) {
		  		$('#investment-slider').slider( 'option', 'disabled', true );
		  	}
			}") 
		));
		
		//$slider->addDecorator(new ibftool_Form_Decorators_MLA_SliderCaption(array("left" => "0 Cent", "right" => "40 Cent")));
		$slider->addErrorMessage("Der Wert muss zwischen 0 und 100 liegen und eine ganze Zahl sein.");
		$this->addElement($slider);
		
		$submit = new Zend_Form_Element_Submit("Weiter");
		$submit->setIgnore(true);
		$submit->class = "btn btn-primary";
		$submit->removeDecorator('DtDdWrapper');
		
		$this->addElement($submit);
	}
}
?>