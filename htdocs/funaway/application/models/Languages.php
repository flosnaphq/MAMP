<?php
class Languages extends MyAppModel {
	const DB_TBL = 'tbl_languages';
	const DB_TBL_PREFIX = 'language_';

	public function __construct($tableId = 0) {
		$tableId = FatUtility::convertToType($tableId, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $tableId);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	public static function getAllLang(){
		$srch = Self::getSearchObject();
		$srch->addCondition('language_active','=',1);
		$srch->addFld('language_id');
		$srch->addFld('language_name');
		$rs = $srch->getResultSet();
		$records = FatApp::getDb()->fetchAllAssoc($rs,'language_id');
		return $records;
	}
}