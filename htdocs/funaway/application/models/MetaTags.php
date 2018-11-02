<?php

class MetaTags extends MyAppModel {

    const DB_TBL = 'tbl_meta_tags';
    const DB_TBL_PREFIX = 'meta_';
    const META_COUNTRY = 1;
    const META_CITY = 2;

    public function __construct($id = 0) {
        $cms_id = FatUtility::convertToType($id, FatUtility::VAR_INT);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        return new SearchBase(static::DB_TBL);
    }

    static function  getMetaTag($controller='home', $action ='index',$record_id = 0, $subrecord_id = 0) {

        $srch = self::getSearchObject();
        if ($record_id > 0) {
            $srch->addCondition(self::DB_TBL_PREFIX . 'record_id', '=', $record_id);
        }
        if ($subrecord_id > 0) {
            $srch->addCondition(self::DB_TBL_PREFIX . 'subrecord_id', '=', $subrecord_id);
        }
        if ($controller !== '') {
            $srch->addCondition(self::DB_TBL_PREFIX . 'controller', '=', $controller);
        }
        if ($action !== '') {
            $srch->addCondition(self::DB_TBL_PREFIX . 'action', '=', $action);
        }
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    function getMetaTagByRecordType($recordType, $record_id, $subrecord_id = 0) {

        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_id', '=', $record_id);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_type', '=', $recordType);
        if ($subrecord_id > 0) {
            $srch->addCondition(self::DB_TBL_PREFIX . 'subrecord_id', '=', $subrecord_id);
        }
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }
    function getMetaTagByRecordId($record_id) {

        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . 'id', '=', $record_id);
     
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }
    function getMetaTagsValues($record_id, $subrecord_id = 0, $controller = '', $action = '') {
        $rows = self::getMetaTag($controller, $action,$record_id, $subrecord_id);
        if (empty($rows))
            return;
        $metaTags['title'] = $rows[self::DB_TBL_PREFIX . 'title'];
        $metaTags['keywords'] = $rows[self::DB_TBL_PREFIX . 'keywords'];
        $metaTags['description'] = $rows[self::DB_TBL_PREFIX . 'description'];
        return $metaTags;
    }



}
