<?php

class AbuseReport extends MyAppModel {

    const DB_TBL = 'tbl_abuse_report';
    const DB_TBL_PREFIX = 'abreport_';
    const ACTIVITY_ABUSE = 1;
    const REVIEW_ABUSE = 0;

    public function __construct($id = 0) {
        $id = FatUtility::convertToType($id, FatUtility::VAR_INT);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    public static function getActivitySearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->joinTable(Activity::DB_TBL, 'INNER JOIN', self::DB_TBL_PREFIX . 'record_id = ' . Activity::DB_TBL_PREFIX . "id AND " . self::DB_TBL_PREFIX . "record_type=" . self::ACTIVITY_ABUSE);
        $srch->joinTable(User::DB_TBL, 'INNER JOIN', self::DB_TBL_PREFIX . 'user_id = ' . User::DB_TBL_PREFIX . "id");
        return $srch;
    }

    public static function getActivityReportData($activityId = 0) {
        $srch = self::getActivitySearchObject(static::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . 'id', '=', $activityId);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    function getAbuseReport($abreport_record_id, $abreport_record_type, $abreport_user_id) {
        $src = new SearchBase(self::DB_TBL);
        $src->addCondition(self::DB_TBL_PREFIX . 'record_id', '=', $abreport_record_id);
        $src->addCondition(self::DB_TBL_PREFIX . 'record_type', '=', $abreport_record_type);
        $src->addCondition(self::DB_TBL_PREFIX . 'user_id', '=', $abreport_user_id);
        return FatApp::getDb()->fetch($src->getResultSet());
    }

    static function deleteAbuseRecord($abreport_record_id, $abreport_record_type) {
        return FatApp::getDb()->deleteRecords(self::DB_TBL, array('smt' => self::DB_TBL_PREFIX . 'record_id = ? and ' . self::DB_TBL_PREFIX . 'record_type = ? ', 'vals' => array($abreport_record_id, $abreport_record_type)));
    }

}
