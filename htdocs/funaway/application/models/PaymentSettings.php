<?php
class PaymentSettings extends MyAppModel {
	
	private $payment_method_key = '';
	
	function __construct( $payment_method_key ){ 
		$this->db = FatApp::getdb();
		$this->payment_method_key = $payment_method_key;
    }
	
	function getError() {
        return $this->error;
    }
	
	public function getPaymentSettings(){
		if (!isset($this->payment_method_key)){
			$this->error="Error: Please create an object with Payment Method Key.";
			return false;
		}
		$payment_method=$this->getPaymentMethodByCode($this->payment_method_key);
		if (!$payment_method){
			$this->error = "Error: Payment method with this payment key does not exist.";
			return false;
		}
		$payment_method_settings = $this->getPaymentMethodFieldsById($payment_method["pmethod_id"]);
		$payment_settings=array();
		foreach($payment_method_settings as $pkey=>$pval){
			$payment_settings[$pval["pmf_key"]]=$pval["pmf_value"];
		}
               $gatewaySettings = array_merge($payment_settings,$payment_method);
             
		return $gatewaySettings;
	}
	
	function getPaymentMethodFieldsById($pmethod_id) {
		$srch = new SearchBase('tbl_payment_method_fields');
		$srch->addCondition('pmf_pmethod_id', '=', (int)$pmethod_id);
		$srch->addOrder('pmf_id','ASC');
		$rs = $srch->getResultSet();
		$payment_method_settings = $this->db->fetchAll($rs);
		return $payment_method_settings;
	}
	
		
	function getPaymentMethodByCode() {
		$srch = new SearchBase('tbl_payment_methods');
		$srch->addCondition('pmethod_code', '=', $this->payment_method_key);
		
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		
		$rs = $srch->getResultSet();
		$payment_method=$this->db->fetch($rs);
		
		return $payment_method;
	}

	 function saveSetting($arr_payment_settings){
			$payment_method=$this->getPaymentMethodByCode($this->payment_method_key);
			if (!$payment_method){
				$this->error = "Error: Payment method with defined payment key does not exist.";
				return false;
			}
			$pmethod_id=$payment_method["pmethod_id"];
			if (!$this->db->deleteRecords('tbl_payment_method_fields', array('smt' => 'pmf_pmethod_id = ?', 'vals' => array($pmethod_id)))){
				$this->error = $this->db->getError();
				return false;
			}
			foreach ($arr_payment_settings as $key=>$val){
					if (!is_array($val)) {
						$this->db->insert_from_array('tbl_payment_method_fields', array('pmf_pmethod_id'=>$pmethod_id,'pmf_key'=>$key,'pmf_value'=>$val,'pmf_serialized'=>0),true,array("IGNORE"));
					}else{
						$this->db->insert_from_array('tbl_payment_method_fields', array('pmf_pmethod_id'=>$pmethod_id,'pmf_key'=>$key,'pmf_value'=>serialize($val),'pmf_serialized'=>1),true,array("IGNORE"));
					}
			}
			return true;
		}
    
	
   
}