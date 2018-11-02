<?php
class Investor extends MyAppModel {
	const DB_TBL = 'tbl_investors';
	const DB_TBL_PREFIX = 'investor_';

	public function __construct($investor_id = 0) {
		$investor_id = FatUtility::convertToType($investor_id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $investor_id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	// $active = false if you want to get data without check status
	function getInvestors($active=1){
		$srch = $this->getSearchObject();
		if($active !== false){
			$srch->addCondition(static::DB_TBL_PREFIX.'active','=', $active);
		}
		$srch->addOrder(static::DB_TBL_PREFIX.'display_order');
		$rs = $srch->getResultSet();
		$rows = FatApp::getDb()->fetchAll($rs);
		return $rows;
	}
}