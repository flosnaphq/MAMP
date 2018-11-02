<?php
class FaqCategory extends MyAppModel {
	const DB_TBL = 'tbl_faq_categories';
	const DB_TBL_PREFIX = 'faqcat_';

	public function __construct($faqcat_id = 0) {
		$faqcat_id = FatUtility::convertToType($faqcat_id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $faqcat_id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	// $active = false if you want to get data without check status
	function getFaqCategories($active=1, $user_type = -1){
		$user_type = FatUtility::int($user_type);
		$srch = $this->getSearchObject();
		if($active !== false){
			$srch->addCondition(static::DB_TBL_PREFIX.'active','=', $active);
		}
		if($user_type > -1){
			$srch->addCondition(static::DB_TBL_PREFIX.'user_type','=', $user_type);
		}
		$srch->addOrder(static::DB_TBL_PREFIX.'display_order');
		$rs = $srch->getResultSet();
		$rows = FatApp::getDb()->fetchAll($rs);
		return $rows;
	}
}