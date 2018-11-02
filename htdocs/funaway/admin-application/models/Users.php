<?php
class Users extends MyAppModel {
	const DB_TBL = 'tbl_users';
	const DB_TBL_PREFIX = 'user_';

	public function __construct($userId = 0) {
		$userId = FatUtility::convertToType($userId, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $userId);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addOrder(static::DB_TBL_PREFIX . 'active', 'DESC');
	//	$srch->addOrder(static::DB_TBL_PREFIX . 'first_name');
		return $srch;
	}
	
	static function getHostUsers(){
		$srch = self::getSearchObject();
		$srch->addOrder(self::DB_TBL_PREFIX.'firstname');
		$srch->addCondition(self::DB_TBL_PREFIX.'type','=',1);
		$srch->addFld(self::DB_TBL_PREFIX.'id');
		$srch->addFld('concat('.self::DB_TBL_PREFIX.'firstname," ", '.self::DB_TBL_PREFIX.'lastname) as user_name');
		$srch->addCondition(self::DB_TBL_PREFIX.'active','=',1);
		$srch->addOrder('user_name');
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$rs = $srch->getResultSet();
		
		return FatApp::getDb()->fetchAllAssoc($rs);
	}
	

}