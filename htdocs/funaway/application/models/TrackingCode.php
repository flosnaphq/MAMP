<?php
class TrackingCode extends MyAppModel {
	const DB_TBL = 'tbl_tracking_code';
	const DB_TBL_PREFIX = 'tcode_';

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
	
	static function getTrackingCode($code_id, $status=1){
		$srch = self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'id','=', $code_id);
		$rs = $srch->getResultSet();
		$data =FatApp::getDb()->fetch($rs);
		if($status !== false){
			if(isset($data[static::DB_TBL_PREFIX.'status']) && $data[static::DB_TBL_PREFIX.'status'] != $status){
				$data = array();
			}
		}
		return empty($data)?'':$data[self::DB_TBL_PREFIX.'code'];
	}
	
}