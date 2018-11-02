<?php

class Block extends MyAppModel {

    const DB_TBL = 'tbl_blocks';
    const DB_TBL_PREFIX = 'block_';
    const BLOCK_GUARDIAN = 7;
    const BLOCK_NEWYORK = 6;
    const BLOCK_WHYCHOOSE = 1;
    

    public function __construct($block_id = 0) {
        $block_id = FatUtility::convertToType($block_id, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $block_id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    // $active = false if you want to get data without check status
    function getBlocks($active = 1) {
        $srch = $this->getSearchObject();
        if ($active !== false) {
            $srch->addCondition(static::DB_TBL_PREFIX . 'active', '=', $active);
        }

        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();

        $rs = $srch->getResultSet();
        $rows = FatApp::getDb()->fetchAll($rs);
        return $rows;
    }

    function getBlock($block_id, $active = 1) {
        $data = $this->getAttributesById($block_id);
        if ($active !== false) {
            if (isset($data[static::DB_TBL_PREFIX . 'active']) && $data[static::DB_TBL_PREFIX . 'active'] != $active) {
                $data = array();
            }
        }
        return $data;
    }


}
