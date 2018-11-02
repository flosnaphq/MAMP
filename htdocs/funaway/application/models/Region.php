<?php

class Region extends MyAppModel {

    const DB_TBL = 'tbl_regions';
    const DB_TBL_PREFIX = 'region_';

    public function __construct($countryId = 0) {
        $regionId = FatUtility::convertToType($countryId, FatUtility::VAR_INT);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $regionId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        return $srch;
    }

    public static function getRegions($isActive = true) {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        $srch->addFld(static::DB_TBL_PREFIX."id");
        $srch->addFld(static::DB_TBL_PREFIX."name");
		if(true === $isActive) {
			$srch->addCondition(self::DB_TBL_PREFIX . 'active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
		}
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }
}
