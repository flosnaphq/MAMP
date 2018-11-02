<?php
class PaymentMethods extends MyAppModel
{
	const DB_TBL = 'tbl_payment_methods';
	const DB_TBL_PREFIX = 'pmethod_';
	const DB_TBL_ALIAS = 'tpm';
	
	public function __construct($pmethodId = 0)
	{
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $pmethodId );
		$this->objMainTableRecord->setSensitiveFields ( array ('') );
	}
	
	public function getPaymentMethodFields($payment_method_code)
	{
		if (empty($payment_method_code))
		{
			return array();
		}
		
		$srch = new SearchPaymentMethods();
		$srch->addCondition('pmethod_code','=',strtolower($payment_method_code));
		$rs = $srch->getResultSet();
		$row = FatApp::getDb()->fetch($rs);
		if($row==false) return array();
        else
		{
			$payment_methods_fields=$this->getPaymentMethodFieldsById($row["pmethod_id"]);
			foreach($payment_methods_fields as $fn => $fval){
				$row[$fval["pmf_key"]]=$fval["pmf_value"];
			}
			
		}
		return $row;
	}
	
	function getPaymentMethodFieldsById($pmethod_id)
	{
		$srch = new SearchBase('tbl_payment_method_fields','pmf');
		$srch->addCondition('pmf_pmethod_id','=', (int)$pmethod_id );
		$srch->addOrder('pmf_id','ASC');
		$rs = $srch->getResultSet();
		$payment_method_fields=FatApp::getDb()->fetchAll($rs);
		return $payment_method_fields;
	}
	
	public static function searchPaymentMethods()
	{
		$srch = new SearchBase(PaymentMethods::DB_TBL, PaymentMethods::DB_TBL_ALIAS);
		
		// $srch->addOrder( PaymentMethods::DB_TBL_PREFIX . 'id', 'DESC');
		
		$srch->addMultipleFields ( 
			array (
				PaymentMethods::DB_TBL_ALIAS . '.' . PaymentMethods::DB_TBL_PREFIX . 'id',
				PaymentMethods::DB_TBL_ALIAS . '.' . PaymentMethods::DB_TBL_PREFIX . 'name',
				PaymentMethods::DB_TBL_ALIAS . '.' . PaymentMethods::DB_TBL_PREFIX . 'active',
				PaymentMethods::DB_TBL_ALIAS . '.' . PaymentMethods::DB_TBL_PREFIX . 'code',
			)
		);
		
		return $srch;
	}
} 
?>