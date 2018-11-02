<?php
class WithdrawalRequests extends MyAppModel {
	
	const DB_TBL = 'tbl_withdrawal_requests';
	const DB_TBL_PREFIX = 'withdrawalrequest_';
//	const SESSION_ELEMENT_NAME = 'UserSession'; 
	
	public function __construct($id = 0) {
		parent::__construct ( static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id );
	
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	public function save() {
		if (! ($this->mainTableRecordId > 0)) {
		//	$this->setFldValue ( 'user_regdate', date ( 'Y-m-d H:i:s' ) );
		}
		
		return parent::save ();
	}
}