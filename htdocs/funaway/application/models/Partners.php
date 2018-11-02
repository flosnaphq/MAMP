<?php
class Partners extends MyAppModel {
	const DB_TBL = 'tbl_partners';
	const DB_TBL_PREFIX = 'partner_';

	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);
		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	function isEmailExist($email, $partner_id=0){
		$partner_id = FatUtility::int($partner_id);
		
		$srch = self::getSearchObject();
		$srch->addCondition(self::DB_TBL_PREFIX.'email','=', $email);
		if($partner_id > 0){
			$srch->addCondition(self::DB_TBL_PREFIX.'id','!=', $partner_id);
		}
		$rs = $srch->getResultSet();
		$records = FatApp::getDb()->fetch($rs);
		return !empty($records);
	}
}