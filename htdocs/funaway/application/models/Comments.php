<?php
class Comments extends MyAppModel {
	const DB_TBL = 'tbl_comments';
	const DB_TBL_PREFIX = 'comment_';
	const ENTITY_TYPE_ORDER_CANCEL=0;

	public function __construct($id = 0) {
		$id = FatUtility::convertToType($id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		return $srch;
	}
}