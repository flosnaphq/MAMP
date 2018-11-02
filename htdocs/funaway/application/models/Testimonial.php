<?php

class Testimonial extends MyAppModel {

    const DB_TBL = 'tbl_testimonials';
    const DB_TBL_PREFIX = 'testimonial_';

    public function __construct($id = 0) {
        $block_id = FatUtility::convertToType($id, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject($calculateRecords = false, $calculateLimit = false) {
        $srch = new SearchBase(static::DB_TBL);
        if (!$calculateRecords) {
            $srch->doNotCalculateRecords();
        }
        if (!$calculateLimit) {
            $srch->doNotLimitRecords();
        }
        return $srch;
    }

    function getTestimonial($id, $status = 1) {
        $id = FatUtility::int($id);
        $srch = self::getSearchObject();
        $srch->addCondition(self::DB_TBL_PREFIX . 'id', '=', $id);
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetch($rs);
        if ($status !== false) {
            if (isset($data[static::DB_TBL_PREFIX . 'status']) && $data[static::DB_TBL_PREFIX . 'status'] != $status) {
                $data = array();
            }
        }
        return $data;
    }

    static function getTestimonials($status = 1) {

        $cacheKey = CACHE_HOME_PAGE_TESTIMONIAL;

        if ($list = FatCache::get($cacheKey, CONF_DEF_CACHE_TIME)) {
            return json_decode($list, true);
        }

        $status = FatUtility::int($status);
        $srch = self::getSearchObject();
        if ($status > -1) {
            $srch->addCondition(self::DB_TBL_PREFIX . 'status', '=', $status);
        }
        $srch->addOrder(self::DB_TBL_PREFIX . 'display_order');
        $rs = $srch->getResultSet();
        $data = FatApp::getDb()->fetchAll($rs, self::DB_TBL_PREFIX . 'id');
        FatCache::set($cacheKey, json_encode($data,true));
        return $data;
    }

}
