<?php

class PhoneCodes extends MyAppModel {

    const DB_TBL = 'tbl_phone_codes';
    const DB_TBL_PREFIX = 'phonecode_';
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public function __construct($id = 0) {
        $id = FatUtility::convertToType($id, FatUtility::VAR_INT);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        return new SearchBase(static::DB_TBL);
    }

    static function getPhoneCodeArray() {
        $records = array();
        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . 'status', '=', self::STATUS_ACTIVE);
        $srch->addFld(self::DB_TBL_PREFIX . 'id');
        $srch->addFld(self::DB_TBL_PREFIX . 'code');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addOrder(self::DB_TBL_PREFIX . 'code');
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        if (empty($rows))
            return $records;
        foreach ($rows as $row) {
            $records[$row[self::DB_TBL_PREFIX . 'code']] = $row[self::DB_TBL_PREFIX . 'code'];
        }
        return $records;
    }

    static function getPhoneCodeWithCountry() {
        $records = array();
        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . 'status', '=', self::STATUS_ACTIVE);
        $srch->addFld(self::DB_TBL_PREFIX . 'id');
        $srch->addFld(self::DB_TBL_PREFIX . 'code');
        $srch->addFld(self::DB_TBL_PREFIX . 'country_id');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addOrder(self::DB_TBL_PREFIX . 'code');
        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        if (empty($rows))
            return $records;
        foreach ($rows as $row) {
            $records[$row[self::DB_TBL_PREFIX . 'country_id']][$row[self::DB_TBL_PREFIX . 'code']] = $row[self::DB_TBL_PREFIX . 'code'];
        }
        return $records;
    }
 
}
