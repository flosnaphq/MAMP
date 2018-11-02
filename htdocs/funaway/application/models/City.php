<?php

class City extends MyAppModel {

    const DB_TBL = 'tbl_cities';
    const DB_TBL_PREFIX = 'city_';
    
    const LIMT_FEATURED_CITIES_FOR_HOME = 8;

    public function __construct($countryId = 0) {
        $cityId = FatUtility::convertToType($countryId, FatUtility::VAR_INT);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $cityId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->joinTable(Country::DB_TBL, 'INNER JOIN', Country::DB_TBL_PREFIX . "id=" . self::DB_TBL_PREFIX . "country_id");
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        return $srch;
    }

    public static function getCities() {
        $srch = self::getSearchObject();
        $srch->addOrder(static::DB_TBL_PREFIX . 'name');
        $srch->addFld(static::DB_TBL_PREFIX . "id");
        $srch->addFld(static::DB_TBL_PREFIX . "name");
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }

    public static function getCityById($id) {
        $srch = self::getSearchObject();
        $srch->joinTable(Region::DB_TBL, 'INNER JOIN', Region::DB_TBL_PREFIX . "id=" . Country::DB_TBL_PREFIX . "region_id");
        $srch->addCondition(self::DB_TBL_PREFIX . "id", '=', $id);
        $srch->addCondition(Country::DB_TBL_PREFIX . "active", '=', 1);
        $srch->addCondition(self::DB_TBL_PREFIX . "active", '=', 1);
        $records = FatApp::getDb()->fetch($srch->getResultSet());
        return $records;
    }

    public static function getAllCitiesByCountryId($id) {
        $srch = self::getSearchObject();
        $srch->addCondition(Country::DB_TBL_PREFIX . "id", '=', $id);
        $srch->addCondition(Country::DB_TBL_PREFIX . "active", '=', 1);
        $srch->addCondition(self::DB_TBL_PREFIX . "active", '=', 1);
        $srch->addFld(static::DB_TBL_PREFIX . "id");
        $srch->addFld(static::DB_TBL_PREFIX . "name");
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        return $records;
    }

    public static function getFeaturedCities($attr = array(), $limit = 0)
    {
        $srch = self::getSearchObject();
        
        if( $limit > 0)
        {
            $srch->setPageSize($limit);
        } else {
            $srch->doNotCalculateRecords();
            $srch->doNotLimitRecords();
        }
        $srch->addCondition(self::DB_TBL_PREFIX . 'active', '=', 1);
        $srch->addCondition(self::DB_TBL_PREFIX . 'featured', '=', 1);
        if (!empty($attr)) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            }
        }        
        $srch->addOrder(self::DB_TBL_PREFIX . 'display_order', 'asc');
        $rs = $srch->getResultSet();

        return FatApp::getDb()->fetchAll($rs, 'city_id');
    }

    public static function getCitiesForHome() {
        $cacheKey = CACHE_HOME_FEATURED_CITIES;

		if ($list = FatCache::get($cacheKey, CONF_DEF_CACHE_TIME)) {
			return json_decode($list,true);
		}
        $srch = self::getSearchObject();
        $srch->joinTable(Activity::DB_TBL, "INNER JOIN", CITY::DB_TBL_PREFIX . "id = " . Activity::DB_TBL_PREFIX . "city_id");
		$srch->joinTable('tbl_activity_events', 'inner join', 'activityevent_activity_id = activity_id AND activityevent_time>=NOW()');
		
        $srch->addCondition(self::DB_TBL_PREFIX . 'active', '=', 1);
        $srch->addCondition(self::DB_TBL_PREFIX . 'featured', '=', 1);
        $srch->addCondition(Activity::DB_TBL_PREFIX . 'active', '=', 1);
        $srch->addCondition(Activity::DB_TBL_PREFIX . 'confirm', '=', 1);
        
        
        $srch->addCondition(Activity::DB_TBL_PREFIX . 'state', '>=', 2);
        $srch->addCondition(Activity::DB_TBL_PREFIX . 'end_Date', '>', "mysql_func_now()", "AND", true);
        $srch->addCondition(Activity::DB_TBL_PREFIX . 'start_date', '<=', "mysql_func_now()", "AND", true);
        
        $srch->addOrder(self::DB_TBL_PREFIX . 'display_order', 'asc');
        $srch->addGroupBy('city_id');
        $srch->setFetchRecordCount(12);
        $srch->doNotCalculateRecords();
        $srch->addMultipleFields(array('count(DISTINCT activity_id) as activities', 'tbl_cities.*'));
        $rs = $srch->getResultSet();

        $records = FatApp::getDb()->fetchAll($rs);
		// print_r($records); exit;
		if(count($records) > 0 && true === defined('CONF_USE_FAT_CACHE') && true === CONF_USE_FAT_CACHE) {
			FatCache::set($cacheKey, json_encode($records, true));
		}
        return $records;
    }

}
