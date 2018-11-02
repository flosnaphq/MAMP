<?php
class Office extends MyAppModel {
	const DB_TBL = 'tbl_offices';
	const DB_TBL_PREFIX = 'office_';

	public function __construct($office_id = 0) {
		$office_id = FatUtility::convertToType($office_id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $office_id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	// $active = false if you want to get data without check status
	function getOffices($active=1){
		$srch = $this->getSearchObject();
		if($active !== false){
			$srch->addCondition(static::DB_TBL_PREFIX.'active','=', $active);
		}
		$rs = $srch->getResultSet();
		$rows = FatApp::getDb()->fetchAll($rs,static::DB_TBL_PREFIX.'id');
		return $rows;
	}
	
	function getOffice($active=1){
		$data = $this->getAttributesById($block_id);
		if($active !== false){
			if(isset($data[static::DB_TBL_PREFIX.'active']) && $data[static::DB_TBL_PREFIX.'active'] != $active){
				$data = array();
			}
		}
		return $data;
	}
}