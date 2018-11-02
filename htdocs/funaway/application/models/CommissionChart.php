<?php 
class CommissionChart extends MyAppModel {
	const DB_TBL = 'tbl_commissions_chart';
	const DB_TBL_PREFIX = 'commissionchart_';
	const ACTIVITY_REVIEW_TYPE = 0;
	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	static function getCommissionChart(){
		$srch = self::getSearchObject();
		$srch->addOrder('commissionchart_min_amount');
		$srch->addOrder('commissionchart_max_amount');
		$srch->doNotCalculateRecords();
		$srch->doNotLimitRecords();
		$srch->addFld('commissionchart_min_amount as min_amount');
		$srch->addFld('commissionchart_max_amount as max_amount');
		$srch->addFld('commissionchart_rate as commission_rate');
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetchAll($rs);
	}
	
	
}