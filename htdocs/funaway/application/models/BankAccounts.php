<?php
class BankAccounts extends MyAppModel {
	const DB_TBL = 'tbl_bank_accounts';
	const DB_TBL_PREFIX = 'bankaccount_';

	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	public function saveBankAccount($data){
		$user_id = FatUtility::int($data[self::DB_TBL_PREFIX.'user_id']);
		if(!($user_id > 0)) return false;
		$tbl = new TableRecord(self::DB_TBL);
		$tbl->assignValues($data);
		
		if($this->isExistUserBankAccount($user_id)){
			return $tbl->update(array('smt'=>self::DB_TBL_PREFIX.'user_id = ?','vals'=>array($user_id)));
		}
		return $tbl->addNew();
	}
	
	function isExistUserBankAccount($user_id){
		$row = $this->getBankAccount($user_id);
		return !empty($row);
	}
	
	function getBankAccount($user_id){
		$srch = Self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'user_id','=',$user_id);
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetch($rs);
	}
}