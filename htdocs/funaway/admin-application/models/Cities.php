<?php

class Cities extends MyAppModel {

    const DB_TBL = 'tbl_cities';
    const DB_TBL_PREFIX = 'city_';

    public function __construct($countryId = 0) {
        $cityId = FatUtility::convertToType($countryId, FatUtility::VAR_INT);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $cityId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

}
