<?php
class Kyoto_Pollution {

	public $pollution;
	public $pollution_history;
	public $hasTechChanged;
	public $techChangeRound;
	public $maxIncrease;
	public $minIncrease;

	public function __construct($initialPollution, $maxIncrease, $minIncrease) {
		$this->pollution = $initialPollution;
		$this->pollution_history = array();
		$this->hasTechChanged = false;
		$this->techChangeRound = null;
		$this->maxIncrease = $maxIncrease;
		$this->minIncrease = $minIncrease;
	}

	public function setPollution($round) {

	}

	public function toArray() {
		return array("pollution" => $this->pollution, 
				"pollution_history" => $this->pollution_history, 
				"hasTechChanged" => $this->hasTechChanged, 
				"techChangeRound" => $this->techChangeRound,
				"maxIncrease" => $this->maxIncrease,
				"minIncrease" => $this->minIncrease);
	}
}