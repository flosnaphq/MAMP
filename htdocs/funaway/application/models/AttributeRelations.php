<?php
class AttributeRelations extends MyAppModel {
	const DB_TBL = 'tbl_attribute_relations';
	const DB_TBL_PREFIX = 'arelation_';

	public function __construct($aattribute_id = 0) {
		$aattribute_id = FatUtility::convertToType($aattribute_id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'aattribute_id', $aattribute_id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject($calculateRecord = false, $calculateLimit = false) {
		$srch = new SearchBase(static::DB_TBL);
		if(!$calculateRecord){
			$srch->doNotCalculateRecords();
		}
		if(!$calculateLimit){
			$srch->doNotLimitRecords();
		}
		return $srch;
	}
	
	function getActvityRelations($value, $field_name = ''){
		if($field_name == ''){
			$field_name = self::DB_TBL_PREFIX.'activity_id';
		}
		$srch = $this->getSearchObject();
		$srch->joinTable(ActivityAttributes::DB_TBL,'inner join',ActivityAttributes::DB_TBL_PREFIX.'id = '.self::DB_TBL_PREFIX.'aattribute_id and '.ActivityAttributes::DB_TBL_PREFIX.'status = 1');
		$srch->addCondition($field_name, '=', $value);
		$rs = $srch->getResultSet();
		return FatApp::getDb()->fetchAll($rs);
	}
}