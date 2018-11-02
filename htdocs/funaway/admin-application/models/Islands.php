<?php

class Islands extends MyAppModel {

    const DB_TBL = 'tbl_island';
    const DB_TBL_PREFIX = 'island_';

    public function __construct($islandId = 0) {
        $cmsId = FatUtility::convertToType($islandId, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $islandId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addOrder(static::DB_TBL_PREFIX . 'active', 'DESC');
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        return $srch;
    }

}
