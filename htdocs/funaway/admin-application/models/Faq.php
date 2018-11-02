<?php
class Faq extends MyAppModel {
	const DB_TBL = 'tbl_faq';
	const DB_TBL_PREFIX = 'faq_';

	public function __construct($faqId = 0) {
		$faqcatId = FatUtility::convertToType($faqId, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $faqcatId);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addOrder(static::DB_TBL_PREFIX . 'active', 'DESC');
		$srch->addOrder(static::DB_TBL_PREFIX . 'question');
		
		return $srch;
	}
}