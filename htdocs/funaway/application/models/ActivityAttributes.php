<?php
class ActivityAttributes extends MyAppModel {
	const DB_TBL = 'tbl_activity_attributes';
	const DB_TBL_PREFIX = 'aattribute_';

	public function __construct($id = 0) {
		$block_id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject($calculateRecords = false, $calculateLimit = false) {
		$srch = new SearchBase(static::DB_TBL);
		if(!$calculateRecords){
			$srch->doNotCalculateRecords();
		}
		if(!$calculateLimit){
			$srch->doNotLimitRecords();
		}
		return $srch;
	}
	
	static function getAttribute($code_id, $status=1){
		$srch = self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'id','=', $code_id);
		$rs = $srch->getResultSet();
		$data =FatApp::getDb()->fetch($rs);
		if($status !== false){
			if(isset($data[static::DB_TBL_PREFIX.'status']) && $data[static::DB_TBL_PREFIX.'status'] != $status){
				$data = array();
			}
		}
		return $data;
	}
	
	static function getAttributes($status = 1){
		$status = FatUtility::int($status);
		$srch = self::getSearchObject();
		if($status > -1){
			$srch->addCondition(self::DB_TBL_PREFIX.'status','=', $status);
		}
		$rs = $srch->getResultSet();
		$data =FatApp::getDb()->fetchAll($rs,self::DB_TBL_PREFIX.'id');
		return $data;
	}
	
}