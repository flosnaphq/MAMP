<?php
class SmsTemplate extends MyAppModel {
	const DB_TBL = 'tbl_sms_templates';
	const DB_TBL_PREFIX = 'smstpl_';

	public function __construct($tplId = 0) {
		$tplId = FatUtility::convertToType($tplId, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $tplId);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addOrder(static::DB_TBL_PREFIX . 'name');
		return $srch;
	}
}