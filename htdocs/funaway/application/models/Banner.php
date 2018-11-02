<?php

class Banner extends MyAppModel {

    const DB_TBL = 'tbl_banners';
    const DB_TBL_PREFIX = 'banner_';

    public function __construct($banner_id = 0) {
        $banner_id = FatUtility::convertToType($banner_id, FatUtility::VAR_INT);

        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $banner_id);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    public static function getHomePageBanner() {
        $cacheKey = CACHE_HOME_PAGE_BANNERS;

        if ($list = FatCache::get($cacheKey, CONF_DEF_CACHE_TIME)) {
            return json_decode($list, true);
        }
        $srch = self::getSearchObject();
        $srch->joinTable('tbl_attached_files', 'inner join', 'afile_record_id = banner_id and afile_type =' . AttachedFile::FILETYPE_BANNER_PHOTO);
        $srch->addCondition('banner_active', '=', 1);
        $srch->addOrder('banner_display_order', 'asc');
        $rs = $srch->getResultSet();
        $list = FatApp::getDb()->fetchAll($rs);
        FatCache::set($cacheKey, json_encode($list, true));
        return $list;
    }

}
