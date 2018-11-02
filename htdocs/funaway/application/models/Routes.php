<?php

class Routes extends MyAppModel {

    const DB_TBL = 'tbl_url_rewrite';
    const DB_TBL_PREFIX = 'url_rewrite_';

    public function __construct() {
        parent::__construct(self::DB_TBL, self::DB_TBL_PREFIX . "id", 0);
    }

    public function createNewRoute($data) {
        $db = FatApp::getDb();


        $db->startTransaction();
        $recordId = $data['url_rewrite_record_id'];
        $subRecordId = $data['url_rewrite_subrecord_id'];
        $recordType = $data['url_rewrite_record_type'];

        if (self::checkIfNoChange($data['url_rewrite_custom'], $recordType, $recordId, $subRecordId)) {
            return true;
        }


        self::markPreviouRecordInactive($recordType, $recordId, $subRecordId);
        
        if (self::updateStatusIfPrevSlug($data['url_rewrite_custom'], $recordType, $recordId, $subRecordId)) {
            $db->commitTransaction();
            return true;
        }

        $data['url_rewrite_custom'] = self::makeSlugUnique($data['url_rewrite_custom'], $recordType, $recordId, $subRecordId);

        $this->assignValues($data);
        if (!$this->save()) {
            $this->error = "Error While Saving the Data";
            return false;
        }
        $db->commitTransaction();
        return true;
    }

    public static function makeSlugUnique($slug, $recordType, $recordId, $subRecordId) {
        $requestSlug = trim($slug, "-");
        $counter = 1;
        while (self::isSlugUnique($slug, $recordType, $recordId, $subRecordId)) {
            $slug = $requestSlug . "-" . $counter;
            $counter++;
        }
        return $slug;
    }

    public static function checkIfNoChange($slug, $recordType, $recordId, $subRecordId = 0) {
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . 'custom', "=", $slug);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_type', "=", $recordType);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_id', "=", $recordId);
        $srch->addCondition(self::DB_TBL_PREFIX . 'subrecord_id', "=", $subRecordId);
        $srch->addCondition(self::DB_TBL_PREFIX . 'active', "=", 1);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function isSlugUnique($slug, $recordType, $recordId, $subRecordId = 0) {
        $srch = new SearchBase(self::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . 'custom', "=", $slug);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_type', "=", $recordType);
        $srch->addCondition(self::DB_TBL_PREFIX . 'record_id', "<>", $recordId);
        if ($subRecordId > 0) {
            $srch->addCondition(self::DB_TBL_PREFIX . 'subrecord_id', "=", $subRecordId);
        }

        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function markPreviouRecordInactive($recordType, $recordid, $subRecordId = 0) {

        $data = array(
            'url_rewrite_active' => 0
        );
        $whr = array('smt' => 'url_rewrite_record_type = ? AND url_rewrite_record_id = ? AND url_rewrite_subrecord_id=?', 'vals' => array($recordType, $recordid, $subRecordId));
  
        FatApp::getDb()->updateFromArray(self::DB_TBL, $data, $whr);

        return true;
    }

    public static function updateStatusIfPrevSlug($slug, $recordType, $recordid, $subRecordId = 0) {
        $db = FatApp::getDb();
        $data = array(
            'url_rewrite_active' => 1
        );
        $whr = array('smt' => 'url_rewrite_custom=? AND url_rewrite_record_type = ? AND url_rewrite_record_id = ? AND url_rewrite_subrecord_id=?', 'vals' => array($slug, $recordType, $recordid, $subRecordId));

        $db->updateFromArray(self::DB_TBL, $data, $whr);
    
        
        return $db->rowsAffected();
    }

}
