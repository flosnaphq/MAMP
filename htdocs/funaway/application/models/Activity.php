<?php

class Activity extends MyAppModel {

    const DB_TBL = 'tbl_activities';
    const DB_TBL_PREFIX = 'activity_';
    const DB_EVENTS_TBL = 'tbl_activity_events';
    const DB_EVENT_TBL_PREFIX = 'activityevent_';

//	const SESSION_ELEMENT_NAME = 'UserSession'; 

    public function __construct($activityId = 0) {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $activityId);
        /* 	$this->objMainTableRecord->setSensitiveFields ( array (
          'user_regdate'
          ) ); */
    }

    public static function getSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    public static function getActiveSearchObject() {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition(self::DB_TBL_PREFIX . "active", "=", AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->addCondition(self::DB_TBL_PREFIX . "confirm", "=", 1);
		$srch->addCondition(self::DB_TBL_PREFIX . "state", ">=", 2);
        return $srch;
    }

    public static function getActivityData($activityId) {
        $srch = new SearchBase(static::DB_TBL);
        $srch->joinTable(City::DB_TBL, "INNER JOIN", CITY::DB_TBL_PREFIX . "id = " . self::DB_TBL_PREFIX . "city_id");
        $srch->joinTable(Country::DB_TBL, "INNER JOIN", Country::DB_TBL_PREFIX . "id = " . CITY::DB_EVENT_TBL_PREFIX . "country_id");
        $srch->joinTable(Service::DB_TBL, "INNER JOIN", Service::DB_TBL_PREFIX . "id = " . self::DB_EVENT_TBL_PREFIX . "category_id");
        $srch->joinTable(User::DB_TBL, "INNER JOIN", User::DB_TBL_PREFIX . "id = " . self::DB_TBL_PREFIX . "user_id");
        $srch->addCondition(self::DB_TBL_PREFIX . "id", '=', $activityId);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public function save() {
        if (!($this->mainTableRecordId > 0)) {
            //	$this->setFldValue ( 'user_regdate', date ( 'Y-m-d H:i:s' ) );
        }
        if ($this->mainTableRecordId > 0) {
            Sms::sendActivityUpdateNotification($this->mainTableRecordId);
        }
        return parent::save();
    }

    public function checkUserActivity($activityId, $hostId) {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('activity_id', '=', $activityId);
        $srch->addCondition('activity_user_id', '=', $hostId);
        $rs = $srch->getResultSet();
        if (FatApp::getDb()->fetch($rs))
            return true;
        return false;
    }

    function getEvent($value, $field = 'activityevent_id') {
        $srch = new SearchBase("tbl_activity_events");
        $srch->addCondition($field, '=', $value);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public function getEventWithActivity($activityId, $eventId, $attr = null)
	{
        $srch = new SearchBase(static::DB_EVENTS_TBL);
        
		$srch->joinTable(static::DB_TBL, 'INNER JOIN', 'activityevent_activity_id = activity_id and activity_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        
		$srch->addCondition('activityevent_activity_id', '=', $activityId);
        $srch->addCondition('activityevent_id', '=', $eventId);
		
        if (null != $attr) {
            if (is_array($attr)) {
                $srch->addMultipleFields($attr);
            } elseif (is_string($attr)) {
                $srch->addFld($attr);
            }
        }

		$rs = $srch->getResultSet();
		
		if(FatApp::getDb()->totalRecords($rs) < 1) {
			return false;
		}
		
		return FatApp::getDb()->fetch($rs); 
    }

    public function getActivityEventByDate($activity_id = 0, $date, $event_status = -1) {
        $event_status = FatUtility::int($event_status);
        $srch = new SearchBase('tbl_activity_events');
        $srch->addCondition('activityevent_activity_id', '=', $activity_id);
        $srch->addDirectCondition('DATE(activityevent_time) = "' . $date . '"');
        if ($event_status > -1) {
            $srch->addCondition('activityevent_status', '=', $event_status);
        }
        $rs = $srch->getResultSet();

        $records = FatApp::getDb()->fetchAll($rs);
        return $records;
    }

    public function getAnrolledMember($activity_id, $event_id) {
        $srch = new SearchBase("tbl_orders");
        $srch->joinTable('tbl_order_activities', 'inner join', 'order_id = oactivity_order_id  and order_payment_status = 1');
        $srch->addCondition('oactivity_activity_id', '=', $activity_id);
        $srch->addCondition('oactivity_event_id', '=', $event_id);
        $srch->addFld('sum(oactivity_members) as total_members');
        $rs = $srch->getResultSet();
        if ($rec = FatApp::getDb()->fetch($rs))
            return FatUtility::int($rec['total_members']);
        return 0;
    }

    public function removeEvent($activityId, $eventId) {
        FatApp::getDb()->deleteRecords('tbl_activity_events', array('smt' => 'activityevent_activity_id = ? and activityevent_id = ?', 'vals' => array($activityId, $eventId)));
    }

    public function removeEventByMonth($activityId, $year, $month) {
        FatApp::getDb()->deleteRecords('tbl_activity_events', array('smt' => 'activityevent_activity_id = ? and YEAR(	activityevent_time) = ? and MONTH(activityevent_time) = ?', 'vals' => array($activityId, $year, $month)));
    }

    public function removeEventByDate($activityId, $date) {
        return FatApp::getDb()->deleteRecords('tbl_activity_events', array('smt' => 'activityevent_activity_id = ? and date(	activityevent_time) = ? ', 'vals' => array($activityId, $date)));
    }

    public function getActivityImages($activityId, $approved = -1) {
        $files = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_ACTIVITY_PHOTO, $activityId, 0, $approved);
        return $files;
    }

    public function getActivityVideos($activityId, $active = false) {
        $srch = new SearchBase('tbl_activity_videos');
        $srch->addCondition('activityvideo_activity_id', '=', $activityId);
        if ($active == true) {
            $srch->addCondition('activityvideo_active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
        }
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        return $records;
    }

    public function addActivityVideo($data) {
        FatApp::getDb()->insertFromArray('tbl_activity_videos', $data);
        return true;
    }

    public function updateVideo($video_id, $data = array()) {
        $tbl = new TableRecord('tbl_activity_videos');
        $tbl->assignValues($data);
        return $tbl->update(array('smt' => 'activityvideo_id = ? ', 'vals' => array($video_id)));
    }

    public function getActivityAddons($activityId) {
        $srch = new SearchBase('tbl_activity_addons');
        $srch->addCondition('activityaddon_activity_id', '=', $activityId);
        $srch->addCondition('activityaddon_is_delete', '=', 0);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        return $records;
    }

    public function getAddonsByActivityAndId($activity_id, $addon_id) {
        $srch = new SearchBase('tbl_activity_addons');
        $srch->addCondition('activityaddon_activity_id', '=', $activity_id);
        $srch->addCondition('activityaddon_id', '=', $addon_id);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetch($rs);
        return $records;
    }

    public function addActivityAddons($data, $addon_id = 0) {
        if ($addon_id > 0) {
            return FatApp::getDb()->updateFromArray('tbl_activity_addons', $data, array('smt' => 'activityaddon_id = ? ', 'vals' => array($addon_id)));
        }
        return FatApp::getDb()->insertFromArray('tbl_activity_addons', $data);
    }

    public function removeActivityAddons($activityId, $addonId) {
        FatApp::getDb()->updateFromArray('tbl_activity_addons', array('activityaddon_is_delete' => 1), array('smt' => 'activityaddon_activity_id = ? and activityaddon_id = ?', 'vals' => array($activityId, $addonId)));
    }

    public function addActivityLanguage($data) {
        FatApp::getDb()->insertFromArray('tbl_activity_languages', $data);
        return true;
    }

    public function getActivityLanguages($activityId) {
        $srch = new SearchBase('tbl_activity_languages');
        $srch->addCondition('activitylanguage_activity_id', '=', $activityId);
        $srch->addFld('activitylanguage_language_id');
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $langs = array();
        foreach ($records as $rec) {
            $langs[] = $rec['activitylanguage_language_id'];
        }
        return $langs;
    }

    public function deleteActivityLanguages($activityId) {
        FatApp::getDb()->deleteRecords('tbl_activity_languages', array('smt' => 'activitylanguage_activity_id = ? ', 'vals' => array($activityId)));
    }

    public function removeActivityVideo($activityId, $videoId) {
        FatApp::getDb()->deleteRecords('tbl_activity_videos', array('smt' => 'activityvideo_activity_id = ? and activityvideo_id = ?', 'vals' => array($activityId, $videoId)));
    }

    public function addTimeSlot($data, $eventId = 0) {
        if ($eventId <= 0) {
            return FatApp::getDb()->insertFromArray('tbl_activity_events', $data);
        }
        return FatApp::getDb()->updateFromArray('tbl_activity_events', $data, array('smt' => 'activityevent_id = ?', 'vals' => array($eventId)));
    }

    public function isHostHaveActivity($hostId) {
        $srch = new SearchBase('tbl_activities');
        $srch->addCondition('activity_user_id', '=', $hostId);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        if (!empty($records)) {
            return true;
        }
        return false;
    }

    public function isAssociatedActvity($activityId, $hostId) {
        $srch = new SearchBase('tbl_activities');
        $srch->addCondition('activity_user_id', '=', $hostId);
        $srch->addCondition('activity_id', '=', $activityId);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        if (!empty($records)) {
            return true;
        }
        return false;
    }

    public function getEventByMonth($activityId, $month, $year) {
        $srch = new SearchBase('tbl_activity_events');
        $srch->addCondition('activityevent_activity_id', '=', $activityId);
        $srch->addDirectCondition('MONTH(activityevent_time) = ' . $month);
        $srch->addDirectCondition('YEAR(activityevent_time) = ' . $year);
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        return $records;
    }

    public function getEventTypeByDate($activityId, $date) {
        $srch = new SearchBase('tbl_activity_events');
        $srch->addCondition('activityevent_activity_id', '=', $activityId);
        $srch->addDirectCondition('DATE(activityevent_time) = "' . $date . '"');
        $srch->addFld("sum(activityevent_anytime) as anytime");
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetch($rs);
        return $record['anytime'];
    }

    // for admin and host user only
    public function getActivitiesForForm($host_id = 0) {
        $host_id = FatUtility::int($host_id);
        $srch = self::getSearchObject();
        if ($host_id > 0) {
            $srch->addCondition(self::DB_TBL_PREFIX . 'user_id', '=', $host_id);
        }
        $srch->addCondition(self::DB_TBL_PREFIX . 'active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->addOrder(self::DB_TBL_PREFIX . 'name');
        $srch->addFld(self::DB_TBL_PREFIX . 'id');
        $srch->addFld(self::DB_TBL_PREFIX . 'name');
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($rs);
    }

    function isValidVideoUrl($url) {
        return Helper::isValidVideoUrl($url);
    }

    function getActivity($activity_id, $active = 1) {
        $data = $this->getAttributesById($activity_id);
        if (empty($data))
            return false;
        if ($active > -1 && $data[self::DB_TBL_PREFIX . 'active'] != $active)
            return false;
        return $data;
    }

    // true : 1 ,false : 0, 2 : upcoming
    static function isActivityOpen($activity_data) {
        if (empty($activity_data)) {
            return false;
        }
        /* if(strtotime($activity_data[self::DB_TBL_PREFIX.'start_date']) >= strtotime(Info::currentDate())) {
          return 2;
          } */
        $current_timestamp = strtotime(Info::currentDatetime());
        $end_timestamp = strtotime($activity_data[self::DB_TBL_PREFIX . 'end_date']);
        $diff = ceil(($end_timestamp - $current_timestamp) / (60 * 60));


        if (strtotime($activity_data[self::DB_TBL_PREFIX . 'end_date']) >= strtotime(Info::currentDate()) && $activity_data[self::DB_TBL_PREFIX . 'booking_status'] == 1 && $diff > $activity_data[self::DB_TBL_PREFIX . 'booking']) {
            return 1;
        }
        return 0;
    }

    function saveActivityAttributeRelation($activity_id, array $attr_relations) {
        $activity_id = FatUtility::int($activity_id);
        $tbl = new TableRecord('tbl_attribute_relations');
        if (!empty($attr_relations)) {
            foreach ($attr_relations as $attr_id) {
                $tbl->assignValues(array('arelation_aattribute_id' => $attr_id, 'arelation_activity_id' => $activity_id));
                if (!$tbl->addNew()) {
                    return false;
                }
            }
        }
        return true;
    }

    function deleteActivityAttributeRelation($activity_id) {
        $activity_id = FatUtility::int($activity_id);

        return FatApp::getDb()->deleteRecords('tbl_attribute_relations', array('smt' => 'arelation_activity_id = ?', 'vals' => array($activity_id)));
    }

    function getActivityAttributeRelations($activity_id) {
        $activity_id = FatUtility::int($activity_id);
        $srch = new SearchBase('tbl_attribute_relations');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('arelation_activity_id', '=', $activity_id);

        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($rs, 'arelation_aattribute_id');
    }

    function getFeaturedActivities($cityIds = array())
    {
        $srch = $this->getActiveSearchObject();
        $srch->joinTable('tbl_services', 'inner Join', 'service_id = ' . self::DB_TBL_PREFIX . 'category_id and service_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable('tbl_users', 'inner join', 'activity_user_id = user_id and user_type = 1 and user_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable('tbl_reviews', 'left join', 'ar.review_type_id = activity_id AND review_active = '. AppUtilities::RECORD_ACTIVE_STATUS . ' AND review_type=0', 'ar');
        
        $srch->joinTable('tbl_activity_events', 'inner join', 'activityevent_activity_id = activity_id AND activityevent_time>=NOW()');        
        
        if( count($cityIds) > 0) {
			$srch->addCondition(self::DB_TBL_PREFIX . 'city_id', 'IN', $cityIds);
		}
        
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();

        $srch->addCondition(self::DB_TBL_PREFIX . 'popular', '=', 1);
        $srch->addOrder('service_display_order', 'asc');
        $srch->addGroupBy('activity_id');
        
        $srch->addMultipleFields(array('*,sum(`review_rating`) as rating,count(review_id) as reviews,count(Distinct review_id) as reviewcounter'));
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAll($rs);
    }

    function getFeaturedActivitiesWithCityKey($allfield = false, $featuredCityIds = array())
    {
        $rows = $this->getFeaturedActivities($featuredCityIds);
        if (empty($rows))
            return array();
        $records = array();
        $categories = array();
        foreach ($rows as $row) {
            $city_id = $row[self::DB_TBL_PREFIX . 'city_id'];
            $service_id = $row[self::DB_TBL_PREFIX . 'category_id'];
            $activity_id = $row[self::DB_TBL_PREFIX . 'id'];
            $activity_price = $row[self::DB_TBL_PREFIX . 'price'];
            if (!array_key_exists($city_id, $records)) {
                $records[$city_id] = array('categories' => array(), 'activities' => array());
            }
            if (!array_key_exists($service_id, $records[$city_id]['categories'])) {
                $records[$city_id]['categories'][$service_id] = array(
                    'service_id' => $row['service_id'],
                    'service_name' => $row['service_name'],
                    'service_parent_id' => $row['service_parent_id'],
                    'service_description' => $row['service_description'],
                    'min_price' => $activity_price,
                    'max_price' => $activity_price,
                );
            }
            if ($activity_price > $records[$city_id]['categories'][$service_id]['max_price']) {
                $records[$city_id]['categories'][$service_id]['max_price'] = $activity_price;
            }
            if ($activity_price < $records[$city_id]['categories'][$service_id]['min_price']) {
                $records[$city_id]['categories'][$service_id]['min_price'] = $activity_price;
            }
            if ($allfield) {
                $records[$city_id]['activities'][$activity_id] = $row;
            } else {
                $records[$city_id]['activities'][$activity_id] = array(
                    'id' => $row[self::DB_TBL_PREFIX . 'id'],
                    'name' => $row[self::DB_TBL_PREFIX . 'name'],
                    'price' => $row[self::DB_TBL_PREFIX . 'price'],
                    'price_type' => $row[self::DB_TBL_PREFIX . 'price_type'],
                    'duration' => $row[self::DB_TBL_PREFIX . 'duration'],
                    'rating' => $row['rating'],
                    'reviews' => $row['reviews'],
                    'reviewcounter' => $row['reviewcounter'],
                );
            }
        }
        return $records;
    }

    function getFeaturedActivitiesForHome() {
        $srch = $this->getActiveSearchObject();
        $srch->joinTable('tbl_services', 'inner Join', 'service_id = ' . self::DB_TBL_PREFIX . 'category_id and service_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable('tbl_users', 'inner join', 'activity_user_id = user_id and user_type = 1 and user_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);

        $srch->doNotCalculateRecords();
        $srch->setFetchRecordCount(16);
        $srch->addCondition(self::DB_TBL_PREFIX . 'popular', '=', 1);
        $srch->addOrder('service_display_order', 'asc');
        $rs = $srch->getResultSet();
     
        return (FatApp::getDb()->fetchAll($rs));
    }

    function getFeaturedActivitiesCount() {

        $cacheKey = CACHE_HOME_FEATURED_ACTIVITIES;

        if ($list = FatCache::get($cacheKey, CONF_DEF_CACHE_TIME)) {
            return json_decode($list,true);
        }

        $srch = $this->getActiveSearchObject();
        $srch->joinTable('tbl_services', 'inner Join', 'cservice.service_id = ' . self::DB_TBL_PREFIX . 'category_id ', 'cservice');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();

        $srch->addOrder('service_display_order', 'asc');
        $rs = $srch->getResultSet();
        $list = FatApp::getDb()->fetchAll($rs);
        FatCache::set($cacheKey, json_encode($list,true));
        return $list;
    }

    public function checkEventBookingAvailability($activityId = 0, $selEvent = 0, &$msg) {

        if ($activityId < 1 || $selEvent < 1) {
            return false;
        }

        if (!$eventRow = $this->getEventWithActivity($activityId, $selEvent, array(static::DB_TBL_PREFIX . 'booking', static::DB_EVENT_TBL_PREFIX . 'id', static::DB_EVENT_TBL_PREFIX . 'time'))) {
            return false;
        }

        // $eventRow[static::DB_TBL_PREFIX . 'booking'] = 2;

        if ($eventRow[static::DB_EVENT_TBL_PREFIX . 'time'] > 0) {
            $currentDatetime = Info::currentDatetime();
            $diffHours = round(((strtotime($eventRow[static::DB_EVENT_TBL_PREFIX . 'time']) - strtotime($currentDatetime))) / 60);

            if ($diffHours > $eventRow[static::DB_TBL_PREFIX . 'booking'] * 60) {
                return true;
            } else {
                if ($eventRow[static::DB_TBL_PREFIX . 'booking'] >= 24) {
                    $priorNoOfDays = ($eventRow[static::DB_TBL_PREFIX . 'booking'] / 24);
                    $msg = sprintf(Info::t_lang('%s_days_prior_confirmation_required'), $priorNoOfDays);
                } else {
                    $msg = sprintf(Info::t_lang('%s_hours_prior_confirmation_required'), $eventRow[static::DB_TBL_PREFIX . 'booking']);
                }
            }
        }
        return false;
    }

    public static function getHeaderCitiesList() {
        $srch = self::getActiveSearchObject();
        $srch->joinTable(City::DB_TBL, 'inner Join', static::DB_TBL_PREFIX . 'city_id=' . City::DB_TBL_PREFIX . 'id AND ' . City::DB_TBL_PREFIX . 'active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable(Country::DB_TBL, 'inner Join', City::DB_TBL_PREFIX . 'country_id=' . Country::DB_TBL_PREFIX . 'id AND ' . Country::DB_TBL_PREFIX . 'active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable(Region::DB_TBL, 'inner Join', Country::DB_TBL_PREFIX . 'region_id=' . Region::DB_TBL_PREFIX . 'id' . ' AND ' . Region::DB_TBL_PREFIX . 'active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->addCondition(self::DB_TBL_PREFIX . 'active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->addCondition(self::DB_TBL_PREFIX . 'confirm', '=', 1);
		
		$srch->addOrder('region_display_order', 'ASC');

        $rs = $srch->getResultSet();
        $data = array();
        if($rs){
        $list = FatApp::getDb()->fetchAll($rs);
        $data = array();
        foreach ($list as $value) {
            $regionId = $value[Region::DB_TBL_PREFIX . 'id'];
            $regionName = $value[Region::DB_TBL_PREFIX . 'name'];
            $countryId = $value[Country::DB_TBL_PREFIX . 'id'];
            $countryName = $value[Country::DB_TBL_PREFIX . 'name'];
            $cityId = $value[City::DB_TBL_PREFIX . 'id'];
            $cityName = $value[City::DB_TBL_PREFIX . 'name'];
            $data[$regionId]['name'] = $regionName;
            $data[$regionId]['countries'][$countryId]['name'] = $countryName;
            $data[$regionId]['countries'][$countryId]['cities'][$cityId] = $cityName;
        }
        }
        return $data;
    }

    public static function getHeaderServicesList() {
        $srch = self::getActiveSearchObject();
        $srch->joinTable('tbl_services', 'inner Join', 'service_id = ' . self::DB_TBL_PREFIX . 'category_id ', 'cservice');
        $srch->joinTable('tbl_services', 'inner Join', 'pservice.service_id = cservice.service_parent_id and pservice.service_active = ' . AppUtilities::RECORD_ACTIVE_STATUS, 'pservice');
        $srch->addFld('pservice.service_id');
        $srch->addFld('pservice.service_name');
        $srch->addOrder('service_name', 'asc');
        $rs = $srch->getResultSet();
	
        return FatApp::getDb()->fetchAllAssoc($rs);
    }
}
