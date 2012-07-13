<?php
class ibftool_Form_InvestmentGame extends Zend_Form {
	
	public function __construct() {
		parent::__construct();

		$this->setMethod("post");
		$this->setName("investmentgame");
		
		$round = new Zend_Form_Element_Hidden("round");
		$round->setRequired(true);
		$round->setDecorators(array('ViewHelper'));
		$this->addElement($round);
		
		$amount = new ibftool_Form_Element_Note("amount");
		$amount->setRequired(false);
		$amount->setLabel("Investment: ");
		$amount->setValue("<span id='amount' class='amount'>Anteil bitte wählen</span><span id='amounteuro'></span>");
		$this->addElement($amount);
		
		$slider = new ZendX_JQuery_Form_Element_Slider("investment");
		$slider->setRequired(true);
		$slider->setLabel("<span class='investment-label-null'>0%</span><span class='investment-label-hundred'>100%</span>");
		$slider->addDecorator("Label" ,array("escape"=>false, 'tag' => 'dt'));
		$slider->setJQueryParams(array(
		  'min' => 0,
		  'max' => 100,
		  'value' => 0,
		  'step' => 1,
		  'slide' => new Zend_Json_Expr("function(event, ui) {
		   	document.getElementById('amount').innerHTML = ui.value + \"%\";
		   	$('.ui-slider-handle').show();
		   	document.getElementById('amounteuro').innerHTML = \" (=\" + runden((money/100)*ui.value) + \" von \" + money + \" Euro)\";
		   	document.getElementById('Weiter').style.display = '';	
		   }")
		));
		
		$slider->addErrorMessage("Der Wert muss zwischen 0 und 100 liegen und eine ganze Zahl sein.");
		$this->addElement($slider);
		
		$submit = new Zend_Form_Element_Submit("Weiter");
		$submit->setIgnore(true);
		$submit->removeDecorator('DtDdWrapper');
		
		$this->addElement($submit);
	}
}
?>