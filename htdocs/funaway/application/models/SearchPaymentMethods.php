<?php
class SearchPaymentMethods extends SearchBase
{
	
	public function __construct()
	{
		parent::__construct ( PaymentMethods::DB_TBL, PaymentMethods::DB_TBL_ALIAS );
		
		$this->addOrder( PaymentMethods::DB_TBL_PREFIX . 'active', 'DESC');
		$this->addOrder( PaymentMethods::DB_TBL_PREFIX . 'id', 'DESC');
		
		$this->addMultipleFields ( 
			array (
				PaymentMethods::DB_TBL_ALIAS . '.' . PaymentMethods::DB_TBL_PREFIX . 'id',
				PaymentMethods::DB_TBL_ALIAS . '.' . PaymentMethods::DB_TBL_PREFIX . 'name',
				PaymentMethods::DB_TBL_ALIAS . '.' . PaymentMethods::DB_TBL_PREFIX . 'active',
				PaymentMethods::DB_TBL_ALIAS . '.' . PaymentMethods::DB_TBL_PREFIX . 'code',
			)
		);
	}
}