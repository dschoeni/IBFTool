<?php
class MLAExperiment_Rounds extends ibftool_Db_Table_Abstract {

	public $_name = 'mlaexperiment_results';
	protected $_rowClass = "MLAExperiment_Round";
	
	protected $_referenceMap    = array(
			'Users' => array(
					'columns'           => array('users_id'),
					'refTableClass'     => 'Users',
					'refColumns'        => array('id')
			)
	);

}