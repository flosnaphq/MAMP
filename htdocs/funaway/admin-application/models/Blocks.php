<?php
class Blocks extends MyAppModel {
	const DB_TBL = 'tbl_blocks';
	const DB_TBL_PREFIX = 'block_';

	public function __construct($block_id = 0) {
		$block_id = FatUtility::convertToType($block_id, FatUtility::VAR_INT);

		parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $block_id);
		$this->objMainTableRecord->setSensitiveFields(array());
	}
	
	public static function getSearchObject() {
		$srch = new SearchBase(static::DB_TBL);
		$srch->addOrder(static::DB_TBL_PREFIX . 'active', 'DESC');
		$srch->addOrder(static::DB_TBL_PREFIX . 'name');
		return $srch;
	}
}