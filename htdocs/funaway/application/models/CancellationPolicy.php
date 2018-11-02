<?php
class CancellationPolicy extends MyAppModel {
	const DB_TBL = 'tbl_cancellation_policies';
	const DB_TBL_PREFIX = 'cancellationpolicy_';

	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addOrder(static::DB_TBL_PREFIX . 'active', 'DESC');
		$srch->addOrder(static::DB_TBL_PREFIX . 'name');
		return $srch;
	}
	
	function getPolicies($active = -1){
		$active = FatUtility::int($active);
		$srch = self::getSearchObject();
		if($active >= 0){
			$srch->addCondition(self::DB_TBL_PREFIX.'active','=', $active);
		}
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetchAll($rs, self::DB_TBL_PREFIX.'id');
	}
	
	function getActiveRecords($order_by='display_order',$sort_by='asc'){
		$srch = new SearchBase(static::DB_TBL);
		$srch->addCondition(static::DB_TBL_PREFIX .'active','=',1);
		$srch->addOrder(static::DB_TBL_PREFIX .$order_by);
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetchAll($rs,static::DB_TBL_PREFIX.'id');
	}
	
	function getRecordByUserType($user_type = 0){
		$user_type = FatUtility::int($user_type);
		$srch = new SearchBase(static::DB_TBL);
		$srch->addCondition(static::DB_TBL_PREFIX .'active','=',1);
		$srch->addCondition(static::DB_TBL_PREFIX .'user_type','=',$user_type);
		$srch->addOrder(static::DB_TBL_PREFIX .'display_order');
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetchAll($rs);
	}
	
	static function getRecordByUserTypeForForm($user_type = 0){
		$user_type = FatUtility::int($user_type);
		$srch = new SearchBase(static::DB_TBL);
		$srch->addCondition(static::DB_TBL_PREFIX .'active','=',1);
		$srch->addCondition(static::DB_TBL_PREFIX .'user_type','=',$user_type);
		$srch->addOrder(static::DB_TBL_PREFIX .'display_order');
		$srch->addFld(static::DB_TBL_PREFIX.'id');
		$srch->addFld(static::DB_TBL_PREFIX.'name');
		$rs = $srch->getResultSet();
		
		return FatApp::getDb()->fetchAllAssoc($rs);
	}
	
	
}