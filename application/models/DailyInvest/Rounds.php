<?php
class DailyInvest_Rounds extends ibftool_Db_Table_Abstract {

	public $_name = 'dailyinvest';
	protected $_rowClass = "DailyInvest_Round";
	
	protected $_referenceMap    = array(
			'Users' => array(
					'columns'           => array('users_id'),
					'refTableClass'     => 'Users',
					'refColumns'        => array('id')
			)
	);
	
	public function hasAlreadyPlayed($usersid) {
		if ((count($this->fetchAll(array("users_id = ?" => $usersid, "DATE(created) = DATE(NOW())"))) > 0)) {
			return true;
		} else {
			return false;
		}
	}

}