<?php
class Kyoto_Player extends Zend_Db_Table_Row {

	private $pollutionObj;

	public function init() {
		parent::init();
		
		if ($this->pollution == null || $this->pollution == 0) {
			if ($this->type == "HighPolluter") {
				$this->pollution = serialize(new Kyoto_Pollution(30, 30, 10));
			}
			
			if ($this->type == "LowPolluter") {
				$this->pollution = serialize(new Kyoto_Pollution(12, 12, 5));
			}
		}
		
		if ($this->type == "HighPolluter" || $this->type == "LowPolluter") {
			$this->pollutionObj = unserialize($this->pollution);
		}
	}
	
	protected function _insert() {
		parent::_insert();

		$db = Zend_Db_Table::getDefaultAdapter();
		$this->created = date("Y-m-d H:i:s");
	}

	protected function _update() {
		parent::_update();

		$db = Zend_Db_Table::getDefaultAdapter();
		$this->lastPoll = date("Y-m-d H:i:s");
	}
	
	public function getPollution() {
		return $this->pollutionObj;
	}
	
	public function changeTechnology($round) {
		$this->technologyChanged = $round;
		$this->save();
	}
}