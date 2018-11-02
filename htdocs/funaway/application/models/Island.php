<?php

class Island extends MyAppModel {

    const DB_TBL = 'tbl_island';
    const DB_TBL_PREFIX = 'island_';

    public function __construct($id = 0) {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function getIsland($order_by = 'island_name', $sort_by = 'asc') {
        $srch = new SearchBase('tbl_island');
        $srch->addCondition('island_active', '=', 1);
        $srch->addFld('island_id');
        $srch->addFld('island_name');
        $srch->addOrder($order_by, $sort_by);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAllAssoc($rs);
        return $records;
    }

    /* 	public static function getIslands($order_by = 'island_name', $sort_by='asc'){
      $srch = new SearchBase('tbl_island');
      $srch->addCondition('island_active','=',1);

      $srch->addOrder($order_by, $sort_by);
      $rs = $srch->getResultSet();
      $records = FatApp::getDb()->fetchAll($rs);
      return $records;
      } */

    public static function getIslandForHome() {
        $srch = new SearchBase('tbl_island');
        $srch->addCondition('island_active', '=', 1);
        $srch->addCondition('island_featured', '=', 1);
        $srch->addOrder('island_display_order');
        $srch->setFetchRecordCount(8);
        //$srch->doNotLimitRecords();
        $srch->doNotCalculateRecords();
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        return $records;
    }

    public static function getIslandById($island_id, $activeRecords = true) {
        $island_id = FatUtility::int($island_id);
        $srch = new SearchBase('tbl_island');

        if ($activeRecords === true) {
            $srch->addCondition('island_active', '=', 1);
        }

        $srch->addCondition('island_id', '=', $island_id);
        $srch->doNotLimitRecords();
        $srch->doNotCalculateRecords();
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetch($rs);
        return $records;
    }

    function getFeaturedIsland($attr = array()) {
        $srch = new SearchBase('tbl_island');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('island_active', '=', 1);
        $srch->addCondition('island_featured', '=', 1);
        if (!empty($attr)) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            }
        }
        $srch->addOrder('island_display_order', 'asc');
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($rs, 'island_id');
    }

    public function getAllIslands() {
        $srch = new SearchBase('tbl_island');
        $srch->addCondition('island_active', '=', 1);
        $srch->addCondition('island_featured', '=', 1);
        $srch->addOrder('island_display_order');
        //$srch->doNotLimitRecords();
        //$srch->doNotCalculateRecords();
        return $srch;
    }

}

?>