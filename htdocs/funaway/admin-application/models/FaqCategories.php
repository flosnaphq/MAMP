<?php
class FaqCategories extends MyAppModel {
	const DB_TBL = 'tbl_faq_categories';
	const DB_TBL_PREFIX = 'faqcat_';

	public function __construct($faqcatId = 0) {
		$faqcatId = FatUtility::convertToType($faqcatId, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $faqcatId);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addOrder(static::DB_TBL_PREFIX . 'active', 'DESC');
		$srch->addOrder(static::DB_TBL_PREFIX . 'name');
		
		return $srch;
	}
}