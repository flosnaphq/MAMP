<?php
class Faq extends MyAppModel {
	const DB_TBL = 'tbl_faq';
	const DB_TBL_PREFIX = 'faq_';

	public function __construct($faq_id = 0) {
		$faq_id = FatUtility::convertToType($faq_id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $faq_id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
	
	// $active = false if you want to get data without check status
	function getFaqs($active=1){
         
		$srch = $this->getSearchObject();
		if($active !== false){
			$srch->addCondition(static::DB_TBL_PREFIX.'active','=', $active);
		}
		$srch->addOrder(static::DB_TBL_PREFIX.'display_order');
		$rs = $srch->getResultSet();
		$rows = FatApp::getDb()->fetchAll($rs);
		return $rows;
	}
	
	function getFaqWithCategory($active = 1){
		$records = array();
		$srch = $this->getSearchObject();
		
		if($active !== false){
			$srch->addCondition(static::DB_TBL_PREFIX.'active','=', $active);
		}
		//$srch->joinTable(FaqCategory::DB_TBL,'Inner Join', FaqCategory::DB_TBL_PREFIX.'id = '.static::DB_TBL_PREFIX.'faqcat_id and '.FaqCategory::DB_TBL_PREFIX.'active = 1');
		$srch->addOrder(static::DB_TBL_PREFIX.'display_order');
		$rs = $srch->getResultSet();
		$rows = FatApp::getDb()->fetchAll($rs);
		
		if(!empty($rows)){
			foreach($rows as $row){
				if(!array_key_exists($row['faq_faqcat_id'], $records)){
					$records[$row['faq_faqcat_id']] = array();
				}
				$records[$row['faq_faqcat_id']][$row['faq_id']] = $row;
			}
		}
		return $records;
	}
}