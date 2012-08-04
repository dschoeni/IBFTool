<?php
class RS_CalculationController extends Zend_Controller_Action {

	public function preDispatch() {
		if (ibftool_Controller_Action_Helper_Treatment::getCurrentModule() != "rs_calculation") {
			$this->_redirect("module/");
		}
	}

	public function indexAction() {
		$money = 10000;
		$doc = new DOMDocument();
		$doc->loadXML(ibftool_Controller_Action_Helper_Treatment::getCurrentConfig());

		$table = new Questionnaire_Results();

		foreach($doc->getElementsByTagName("id") as $id) {
			$valueArray = explode("/", $id->nodeValue);

			$chosenRisk = $valueArray[0];
			$answerID = $valueArray[1];

			$answer = new Questionnaire_Questions();
			$answer = $answer->find($chosenRisk);
			$answer = $answer->current();
			$answerValue = $answer->getAnswerForQuestionNoTreatment();

			$normalRandom = $this->rnd(1.1, 0.48);
			if ($normalRandom > 1) {
				$outputPercentage = $normalRandom - 1;
			} else if ($normalRandom < 1) {
				$outputPercentage = -(1 - $normalRandom);
			} else {
				$outputPercentage = 0;
			}
			
			echo $normalRandom;
			
			$realOutput = (($money*($answerValue/100))*$outputPercentage)+((0.02)*($money*(1-($answerValue/100))));

			if ($table->fetchRow(array("questionnaire_question_id = ?" => $answerID, "users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id)) == null) {
				$data = array(
						'questionnaire_question_id'	=> $answerID,
						'treatments_id' => Sbftool_Controller_Action_Helper_Treatment::getID(),
						'users_id' =>  Zend_Auth::getInstance()->getIdentity()->id,
						'ip' => $_SERVER['REMOTE_ADDR'],
						'questionnaire_answer' => $realOutput,
				);
				$row = $table->createRow($data);
				$row->save();
				echo "<br>created row";
			} else {
				$row = $table->fetchRow(array("questionnaire_question_id = ?" => $answerID, "users_id = ?" => Zend_Auth::getInstance()->getIdentity()->id));
				$row->questionnaire_answer = $realOutput;
				$row->save();
				echo "<br>updated row";
			}
		}
		
		$this->nextAction();
	}

	public function nextAction() {
		ibftool_Controller_Action_Helper_Treatment::completeCurrentModule();
		$this->_redirect("module/");
	}

	private function rnd_snd() {
		return ($this->deepRand()*2-1)+($this->deepRand()*2-1)+($this->deepRand()*2-1);
	}

	private function rnd($mean, $stdev) {
		return (($this->rnd_snd()*$stdev)+$mean);
	}
	
	private function deepRand() {
		return mt_rand(0,1000)/1000;
	}

}
