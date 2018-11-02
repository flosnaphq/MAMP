<?php
class CancellationPolicyController extends MyAppController {
	
	public function __construct($action){
		parent::__construct($action);
		
	}
	public function index($user='traveler') {
		$user_type =0;
		if($user =='host'){
			$user_type =1;
		}
		
		$policy = new CancellationPolicy();
		$records = $policy->getRecordByUserType($user_type);
		
		$this->set('active_tab', $user);
		$this->set('records', $records);
		$this->_template->render();
	}
	
	
	
}
