<?php

class Countries extends MyAppModel {

    const DB_TBL = 'tbl_countries';
    const DB_TBL_PREFIX = 'country_';

    public function __construct($countryId = 0) {
        $countryId = FatUtility::convertToType($countryId, FatUtility::VAR_INT);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $countryId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        return $srch;
    }

    public static function getCountries() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        $srch->addFld("country_id");
        $srch->addFld("country_name");
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }

    public static function getCountriesPhoneCode() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        $srch->addFld("country_id");
        $srch->addFld("country_phone_code");
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }

}
