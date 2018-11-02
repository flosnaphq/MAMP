<?php
class CurrencyController extends MyAppController {
	
	public function __construct($action){
		parent::__construct($action);
		$this->set('action',$action);
		
		
	}
	
	public function setCurrency($currencyId = 1){
		$currencyId = intval($currencyId);
		Info::setCurrentCurrency($currencyId);
		FatApp::redirectUser($_SERVER['HTTP_REFERER']);	
	}
}
