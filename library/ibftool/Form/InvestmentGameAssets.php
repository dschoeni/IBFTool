<?php
class ibftool_Form_InvestmentGameAssets extends Zend_Form {
	
	public function __construct() {
		parent::__construct();
		
		$this->setDisableLoadDefaultDecorators(true);
 
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'investmentgameassets/_form.phtml')),
            'Form'
        ));

		$this->setMethod("post");
		$this->setName("investmentgameassets");
		$this->setAction("/investmentgameassets/process/");
		
		$asset_a_count = new Zend_Form_Element_Text("asset_a_count");
		$asset_a_count->setAttrib("readonly", "readonly");
		$asset_a_count->setRequired(true);
		$asset_a_count->setDecorators(array('ViewHelper'));
		$this->addElement($asset_a_count);
		
		$asset_b_count = new Zend_Form_Element_Text("asset_b_count");
		$asset_b_count->setAttrib("readonly", "readonly");
		$asset_b_count->setRequired(true);
		$asset_b_count->setDecorators(array('ViewHelper'));
		$this->addElement($asset_b_count);
		
		$asset_a_value = new Zend_Form_Element_Text("asset_a_value");
		$asset_a_value->setAttrib("readonly", "readonly");		
		$asset_a_value->setRequired(true);
		$asset_a_value->setDecorators(array('ViewHelper'));
		$this->addElement($asset_a_value);
		
		$asset_b_value = new Zend_Form_Element_Text("asset_b_value");
		$asset_b_value->setAttrib("readonly", "readonly");		
		$asset_b_value->setRequired(true);
		$asset_b_value->setDecorators(array('ViewHelper'));
		$this->addElement($asset_b_value);
		
		$submit = new Zend_Form_Element_Submit("Weiter");
		$submit->setIgnore(true);
		$submit->removeDecorator('DtDdWrapper');
		
		$this->addElement($submit);
	}
}
?>