<?php
class Administration_Kyoto_ServerController extends Zend_Controller_Action {

	public function init() {
		set_time_limit(20);
		$this->context = $this->_helper->getHelper('CompleteContext');
		$this->context->setAutoJsonSerialization(true);
		$this->addContexts();
		$this->context->initContext();
	}

	protected function addContexts() {
		$this->context->addActionContext("nextround", "json");
	}
	
	public function indexAction() {
		$this->view->inlineScript()->appendFile($this->getFrontController()->getBaseUrl() . "/js/kyoto/server.js");
		$sessions = new Kyoto_Sessions();
		// TODO Make Treatmentselection dynamically
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  1));
		
		$this->view->session = $session;
	}
	
	public function nextroundAction() {
		Zend_Layout::getMvcInstance()->disableLayout();
		
		$sessions = new Kyoto_Sessions();
		// TODO Make Treatmentselection dynamically
		$session = $sessions->fetchRow(array("ibftool_treatments_id" =>  1));
		
		$this->computePricesAndPermissions($session);
		
		$session->currentround++;
		$session->currentRoundTime = time();
		$session->save();
		
		$this->view->currentround = $session->currentround;
		$this->view->result = "success";
		return;
	}
	
	private function computePricesAndPermissions($session) {
		$players = new Kyoto_Players();
		$allPlayersInSession = $players->fetchAll(array("ibftool_kyoto_sessions_id" =>  1));
		
		foreach ($allPlayersInSession as $player) {
			$pollution = $player->getPollution();
			$pollution->setPollution($session->currentround);
			
			// TODO right distribution of everything
			$player->balance = $player->balance + rand(-500, 500);
			$player->permissions = $player->permissions + rand(-20, 20);
			
			$player->save();
		}
	}
	
	/*
	private function computePricesAndPermissions() {
		$offers = new Offers();
		$buyOffers = $offers->fetchAll(array("buy = ?" => 1), "price DESC");
		$sellOffers = $offers->fetchAll(array("sell = ?" => 1), "price DESC");
		
		$orderBookSell = array();
		$orderBookBuy = array();
		
		$amount = 0;
		
		$priceList = array();
		
		
		for ($i = 0; i < count($orderBookSell); $i++) {
			$o1 = $orderBookSell[i];
			$amount = $orderBookSell[i]->quantity;
			for ($j = $i + 1; $j < count($buyOffers); $j++) {
			 	$o2 = $buyOffers[j];
				if ($o1->price <= $o2->price) {
					$amount = $amount + $o2->quantity;
				}
			}
			
			if (!priceList.contains(o1.getPrice())) {
				$orderBookBuy[] = array($o1, $amount);
				$priceList[] = $o1->price;
			}
		}
		
		$priceList = array();
		
		for (int i = 0; i < sellOfferList.size(); i++) {
			Offer o1 = (Offer) sellOfferList.elementAt(i);
			amount = o1.getAmount();
			for (int j = i + 1; j < sellOfferList.size(); j++) {
				Offer o2 = (Offer) sellOfferList.elementAt(j);
				if (o1.getPrice() >= o2.getPrice()) {
					amount = amount + o2.getAmount();
				}
			}
			if (!priceList.contains(o1.getPrice())) {
				Offer matching = new Offer(period,o1.getPrice(),amount, false);
				orderbookSell.add(matching);
				id.clear();
				priceList.add(o1.getPrice());
			}
		}
		this.orderBookBuy = orderbookBuy;
		this.orderBookSell = orderbookSell;
		getPriceWithFirstMoverAdvantage(orderbookBuy, orderbookSell);
	}
	*/
	
}
