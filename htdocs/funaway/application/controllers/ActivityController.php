<?php

class ActivityController extends MyAppController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    function fatActionCatchAll()
    {
        FatUtility::exitWithErrorCode(404);
	}

    function host($host_name, $host_id)
    {
        $host_id = FatUtility::int($host_id);
        if ($host_id <= 0) {
            FatUtility::exitWithErrorCode(404);
        }

        $usr = new User();
        $user_data = $usr->getUserByUserId($host_id);
        if (empty($user_data) || $user_data[User::DB_TBL_PREFIX . 'active'] != AppUtilities::RECORD_ACTIVE_STATUS || $user_data[User::DB_TBL_PREFIX . 'type'] != 1) {
            FatUtility::exitWithErrorCode(404);
        }
        $total_review = Reviews::getHostActivityRating($host_id);
        $this->set('total_review', $total_review);
        $this->set('user_data', $user_data);
        $this->set('host_id', $host_id);
        $this->_template->render();
    }

    public function hostActivityListing()
    {
        $data = FatApp::getPostedData();
        $host_id = isset($data['host_id']) ? FatUtility::int($data['host_id']) : 0;
        if ($host_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $usr = new User();
        $user_data = $usr->getUserByUserId($host_id);
        if (empty($user_data) || $user_data[User::DB_TBL_PREFIX . 'active'] != AppUtilities::RECORD_ACTIVE_STATUS || $user_data[User::DB_TBL_PREFIX . 'type'] != 1) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $acts = new Activity();
        $srch = $acts->getSearchObject();
        $srch->joinTable('tbl_users', 'inner join', 'activity_user_id = user_id and user_type = ' . ApplicationConstants::USER_HOST_TYPE .' and user_active = '. AppUtilities::RECORD_ACTIVE_STATUS .' and user_verified = 1');
        $srch->joinTable('tbl_activity_languages', 'left join', 'activity_id = activitylanguage_activity_id');
        $srch->joinTable('tbl_reviews', 'left join', 'ar.review_type_id = activity_id AND review_active = '. AppUtilities::RECORD_ACTIVE_STATUS .' AND review_type=0', 'ar');

        $srch->joinTable('tbl_services', 'left join', 'schild.service_id = activity_category_id', 'schild');
        $srch->joinTable('tbl_services', 'left join', 'schild.service_parent_id = sparent.service_id', 'sparent');

        $srch->addFld('schild.service_name as childservice_name');
        $srch->addFld('sparent.service_name as parentservice_name');
        $srch->addFld('sparent.service_id as parentservice_id');
        $srch->addCondition('activity_confirm', '=', 1);
        $srch->addCondition('activity_active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->addCondition('activity_user_id', '=', $host_id);
		

        $srch->addGroupBy('activitylanguage_activity_id');
        $srch->addFld(array('sum(`review_rating`) as rating,count(review_id) as reviews,count(Distinct review_id) as reviewcounter'));
        $srch->addMultipleFields(array('tbl_users.*', 'tbl_activities.*', 'SUBSTRING_INDEX(group_concat(activitylanguage_language_id separator ","),",",3) as act_lang'));


        if (User::isUserLogged()) {
            $srch->joinTable('tbl_wishlist', 'left join', 'wishlist_activity_id = activity_id and 	wishlist_user_id = ' . User::getLoggedUserId());
            $srch->addFld('wishlist_activity_id');
        }

		//echo $srch->getQuery();exit;
        $pagesize = 8;
        $page = $data['page'];
        $page = FatUtility::int($page);
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();

        $records = FatApp::getDb()->fetchAll($rs);
        $more_record = ($page * $pagesize) < $srch->recordCount();
        $this->set("activities", $records);
        $this->set('totalPage', $srch->pages());
        $this->set('page', $page);
        $this->set('more_record', $more_record);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "_partial/ajax/activities-grid.php", true, true);
        FatUtility::dieJsonSuccess(array('msg' => $htm, 'more_record' => $more_record));
    }

    public function themeblock()
    {
        $post = FatApp::getPostedData();
        $services = array();
        if (isset($post['theme_id'])) {
            $theme_id = $post['theme_id'];
            $services = Services::getCategories($theme_id);
        }
        $this->set('services', $services);
        $htm = $this->_template->render(false, false, "activity/_partial/theme-block.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    public function detail($activityId = 0)
    {
        $user_id = 0;
        $this->page_class .= ' is--detail ';
        if (User::isUserLogged()) {
            $user_id = User::getLoggedUserId();
        }
        $this->_template->addJs('common-js/plugins/slick.min.js');
        $acts = new Activity();
        $srch = $acts->getSearchObject();
        $srch->joinTable('tbl_reviews', 'left join', 'ar.review_type_id = activity_id AND review_active = ' . AppUtilities::RECORD_ACTIVE_STATUS . ' AND review_type=0', 'ar');
        $srch->joinTable('tbl_users', 'inner join', 'activity_user_id = user_id and user_type = 1 and user_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable('tbl_cities', 'inner join', 'activity_city_id = city_id and city_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable('tbl_countries', 'inner join', 'country_id = city_country_id and country_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable('tbl_regions', 'inner join', 'region_id = country_region_id and region_active = ' . AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->joinTable('tbl_activity_languages', 'left join', 'activity_id = activitylanguage_activity_id');
        $srch->joinTable('tbl_languages', 'left join', 'language_id = activitylanguage_language_id');
        $srch->joinTable('tbl_services', 'left join', 'schild.service_id = activity_category_id', 'schild');
        $srch->joinTable('tbl_services', 'left join', 'schild.service_parent_id = sparent.service_id', 'sparent');
        $srch->joinTable('tbl_activity_events', 'left join', 'activityevent_activity_id = activity_id AND activityevent_time>=NOW()');

        $srch->addFld('schild.service_name as childservice_name');
        $srch->addFld('city_name');
        $srch->addFld('sparent.service_name as parentservice_name');
        $srch->addFld('sparent.service_id as parentservice_id');
        $srch->addCondition('activity_id', '=', $activityId);
        $srch->addCondition('activity_confirm', '=', 1);
        $srch->addCondition('activity_active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
        //	$srch->addDirectCondition("date(activity_start_date) < '".Info::currentDate()."'");
        //	$srch->addDirectCondition("date(activity_end_date) > '".Info::currentDate()."'");
        if (User::isUserLogged()) {
            $srch->joinTable('tbl_wishlist', 'left join', 'wishlist_activity_id = activity_id and wishlist_user_id = ' . User::getLoggedUserId());
            $srch->addFld('wishlist_activity_id');
        }


        $srch->addGroupBy('activity_id');
        $srch->addMultipleFields(array('tbl_users.*', 'tbl_activities.*', 'group_concat(DISTINCT language_name separator ", ") as act_lang,sum(review_rating) as rating,count(review_id) as ratingcounter,count(DISTINCT review_id) as reviews,city_latitude,city_longitude,count(activityevent_id) as date_available'));

        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();

        $rs = $srch->getResultSet();


        if (!$activity = FatApp::getDb()->fetch($rs)) {
            http_response_code(404);
            Message::addErrorMessage(Info::t_lang('Invalid_request'));
            FatApp::redirectUser(FatUtility::generateUrl('activity'));
        }


        $this->set('activity', $activity);
        $addons = $acts->getActivityAddons($activityId);

        foreach ($addons as $addon_key => $addon) {
            $addons[$addon_key]['images'] = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_ACTIVITY_ADDON, $addon['activityaddon_id'], $activityId);
        }

        $this->set('addons', $addons);


        $search = Activity::getSearchObject();
        $search->addCondition('activity_user_id', '=', $activity['activity_user_id']);
        $search->addCondition('activity_confirm', '=', 1);
        $search->addCondition('activity_active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
        //$search->addDirectCondition("date(activity_start_date) < '".Info::currentDate()."'");
        //$search->addDirectCondition("date(activity_end_date) > '".Info::currentDate()."'");
        $search->addGroupBy('activity_id');
        $page = 1;
        $page = FatUtility::int($page);
        $search->setPageNumber($page);
        $search->setPageSize(3);
        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        if (empty($records)) {
            FatApp::redirectUser(FatUtility::generateUrl('activity'));
        }

        $this->set("hostactivity", $records);
        $this->set("user_id", $user_id);
        $this->set("cityId", $activity['activity_city_id']);

        ///////////////////////cancellation policy\\\\\\\\\\\\\\\\\\
        $cancellation = new CancellationPolicy($activity['activity_cancelation']);
        $cancellation->loadFromDb();
        $cancellation_policy = $cancellation->getFlds();

        $canMessage = MessageThread::canMessage($user_id, $activity['activity_user_id']);

        if ($activity['activity_latitude'] == 0) {
            $lat = $activity['city_latitude'];
            $long = $activity['city_longitude'];
        } else {
            $lat = $activity['activity_latitude'];
            $long = $activity['activity_longitude'];
        }
		$actImages = $acts->getActivityImages($activityId, 1);
        $this->set('videos', $acts->getActivityVideos($activityId, true));
        $this->set('images', $actImages);
        $this->set('cancellation_policy', $cancellation_policy);
        $this->set('lat', $lat);
        $this->set('long', $long);
        $this->set('canMessage', $canMessage);
        $this->set('page_class', $this->page_class);

        // print_r($activity);exit;
        //Meta Data
        $pageTitle = $activity['activity_name'] . " | " . $activity['city_name'];
        $keywordPlaceHolder = "Activities at %s, %s, %s, %s";
        $metaData = array(
            'description' => strip_tags($activity['activity_desc']),
            'keywords' => sprintf($keywordPlaceHolder, $activity['city_name'], $activity['parentservice_name'], $activity['childservice_name'], FatApp::getConfig("conf_website_title")),
			//'other_tags' => $this->getSocialMetaTags($activity, $actImages),
        );
        $this->set('pageTitle', $pageTitle);
        $this->set('__metaData', $metaData);


        //////////////////////////////////////////////////////////////////
        $this->_template->render(true, true, null, false, false);
        // $this->_template->render();
    }
	
	private function getSocialMetaTags($actDetail, $actImages=array()) 
	{
		$activityPic = '';
		if(0 < $actDetail['activity_image_id']) {
			$activityPic = FatCache::getCachedUrl(FatUtility::generateFullUrl('Image', 'activity', array($actDetail['activity_image_id'], 579, 434)), CONF_DEF_CACHE_TIME, '.jpg');
		} else if (!empty($actImages)) {
			$img = current($actImages);
			$activityPic = FatCache::getCachedUrl(FatUtility::generateUrl('image', 'activity', array($img['afile_id'], 579, 434)), CONF_DEF_CACHE_TIME, '.jpg');
		}
		$data = '';
		$data .= '<meta property="og:type" content="article" />'.PHP_EOL;
		$data .= '<meta property="og:title" content="'.$actDetail["activity_name"] .'-aman" />'.PHP_EOL;
		$data .= '<meta property="og:site_name" content="'. FatApp::getConfig("conf_website_name").'" />'.PHP_EOL;
		$data .= '<meta property="og:image" content="'.$activityPic.'" />'.PHP_EOL;
		$data .= '<meta property="og:description" content="'.$actDetail["activity_desc"].'-aman" />'.PHP_EOL;

		$data .= '<meta name="twitter:card" content="article">'.PHP_EOL;
		$data .= '<meta name="twitter:site" content="@'.FatApp::getConfig("conf_website_name").'">'.PHP_EOL;
		$data .= '<meta name="twitter:title" content="'.$actDetail["activity_name"].'">'.PHP_EOL;
		$data .= '<meta name="twitter:description" content="'.$actDetail["activity_desc"].'">'.PHP_EOL;
		$data .= '<meta name="twitter:image:src" content="'.$activityPic.'">'.PHP_EOL;
		return $data;
	}
	
    public function calendar()
    {
        $activityId = FatApp::getPostedData('activity_id', FatUtility::VAR_INT, 0);
        $e = new Activity($activityId);
        $e->loadFromDb();
        $flds = $e->getFlds();
        $current_date = Info::currentDate();
        $current_year = date('Y', strtotime($current_date));
        $current_month = date('m', strtotime($current_date));

        $startyear = date('Y', strtotime($flds['activity_start_date']));
        $startmonth = date('m', strtotime($flds['activity_start_date']));

        $endyear = date('Y', strtotime($flds['activity_end_date']));
        $endmonth = date('m', strtotime($flds['activity_end_date']));

        $disablePrevMonth = false;
        $disableNextMonth = false;

        $post = FatApp::getPostedData();

        /* if (isset($post) && !empty($post) && isset($post['type'])) { */
        if (array_key_exists('type', $post)) {
            if ($post['type'] == 'prev') {
                $yr = $post['year'];
                $month = $post['month'];
                if ($month == 1) {
                    $month = 12;
                    $yr = $yr - 1;
                } else {
                    $month = $month - 1;
                }
            }

            if ($post['type'] == 'next') {
                $yr = $post['year'];
                $month = $post['month'];
                if ($month == 12) {
                    $month = 1;
                    $yr = $yr + 1;
                } else {
                    $month = $month + 1;
                }
            }

            if ($post['type'] == 'current') {
                $yr = $post['year'];
                $month = $post['month'];
            }
        } else {
            $yr = ($startyear < $current_year ? $current_year : $startyear);
            $month = (($startyear < $current_year) || ($startyear == $current_year && $startmonth < $current_month)) ? $current_month : $startmonth;
        }

        $next = 1;
        $prev = 1;

        $pyr = $yr;
        $pmonth = $month;
        if ($pmonth == 1) {
            $pmonth = 12;
            $pyr = $pyr - 1;
        } else {
            $pmonth = $pmonth - 1;
        }

        if ($pyr < $startyear) {
            $prev = 0;
        }
        if ($pyr == $startyear && $pmonth < $startmonth) {
            $prev = 0;
        }


        $nyr = $yr;
        $nmonth = $month;
        if ($nmonth == 12) {
            $nmonth = 1;
            $nyr = $nyr + 1;
        } else {
            $nmonth = $nmonth + 1;
        }


        if ($nyr > $endyear) {
            $next = 0;
        }
        if ($nyr == $endyear && $nmonth > $endmonth) {
            $next = 0;
        }


        $currentDate = Info::currentDate();
        $curYearMonth = date('Y-m', strtotime($currentDate));
        $reqYearMonth = date('Y-m', strtotime($yr . '-' . $month));

        $priorNoOfDays = 0;

        if ($flds['activity_booking'] > 0 && $flds['activity_booking'] >= 24) {
            $priorNoOfDays = ($flds['activity_booking'] / 24);
        }
        // Info::test($priorNoOfDays); exit;
        if (($curYearMonth > $reqYearMonth) || (strtotime($curYearMonth) > strtotime($flds['activity_end_date']))) {
            $prev = 0;
            //$ret['msg'] = FatUtility::dieJsonError(Info::t_lang('Invalid_request') . $curYearMonth . ' ' . $flds['activity_end_date']);            
            $ret['prevMonth'] = $prev;
        }

        if ($curYearMonth == $reqYearMonth) {
            $prev = 0;
        }

        if (strtotime($curYearMonth) == strtotime($flds['activity_end_date'])) {
            $next = 0;
        }

        $c = new Calendar($month, $yr);
        $calendarDates = $c->generateMonthCalendar();
        // Info::test($priorNoOfDays);
        $cals = array();
        foreach ($calendarDates as $k => $v) {
            $date = $yr . '-' . $month . '-' . $v;

            $class = "$priorNoOfDays ";

            $disable = false;
            $cals[$k]['date'] = $v;
            $cals[$k]['fulldate'] = date('Y-m-d', strtotime($date));
            $subclass = "";
            if (strtotime($currentDate) == strtotime($date) && $priorNoOfDays < 1) {
                $class .= ' current';
            }

            if (strtotime($flds['activity_start_date']) == strtotime($date)) {
                $class .= ' start';
            }

            if (strtotime($flds['activity_end_date']) == strtotime($date)) {
                $class .= ' end';
            }

            if (strtotime($date) < strtotime($flds['activity_start_date']) || strtotime($date) < strtotime($currentDate)) {
                $disable = true;
                $class .= " disable";
            }

            if (strtotime($date) > strtotime($flds['activity_end_date'])) {
                $disable = true;
                $class .= " disable";
            }

            if ($priorNoOfDays > 0 && ((strtotime($currentDate) <= strtotime($date)) && $month == Info::currentMonth())) {
                $disable = true;
                $class .= " disable";
                $priorNoOfDays--;
            }

            $cals[$k]['events'] = array();
            if ($v != "" && false == $disable) {
                if (strtotime($date) >= strtotime($flds['activity_start_date']) && strtotime($date) <= strtotime($flds['activity_end_date']) && $cals[$k]['events'] = $e->getActivityEventByDate($activityId, date('Y-m-d', strtotime($date)), 1)
                ) {
                    $class .= ' selection';
                    $subclass .= ' calc-dt';
                }
            }

            $cals[$k]['class'] = $class;
            $cals[$k]['subclass'] = $subclass;
        }
        $this->set("year", $yr);
        $dt = DateTime::createFromFormat('!m', $month);
        $this->set("showmonth", $dt->format('M'));
        $this->set("month", $month);

        $this->set("next", $next);

        $this->set("prev", $prev);

        $this->set("calendar", $cals);

        $html = $this->_template->render(false, false, 'activity/_partial/calendar.php', true, true);

        die(FatUtility::convertToJson(array('status' => 1, 'html' => $html)));
    }

    public function event()
    {
        $activityId = FatApp::getPostedData('activity_id', FatUtility::VAR_INT, 0);

        $dt = FatApp::getPostedData('date', FatUtility::VAR_INT, 0);
        $mn = FatApp::getPostedData('month', FatUtility::VAR_INT, 0);
        $yr = FatApp::getPostedData('year', FatUtility::VAR_INT, 0);

        $currentDate = Info::currentDate();
        $selectedDate = date('Y-m-d', strtotime($yr . '-' . $mn . '-' . $dt));

        if (strtotime($selectedDate) < $currentDate) {
            FatUtility::dieJsonError(Info::t_lang('CAN_NOT_BOOK_FOR_A_PASSED_DATE'));
        }

        $e = new Activity($activityId);

        if (!$e->loadFromDb()) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }

        $activityRow = $e->getFlds(array('activity_booking'));

        // Info::test($activityRow);

        if ($activityRow['activity_booking'] > 0 && $activityRow['activity_booking'] >= 24) {
            $priorNoOfDays = ($activityRow['activity_booking'] / 24);
            $dateDiff = FatDate::diff($currentDate, $selectedDate);

            if ($priorNoOfDays > $dateDiff) {
                FatUtility::dieJsonError($priorNoOfDays . ' day(s) Prior Confirmation required');
            }
        }

        if (!$events = $e->getActivityEventByDate($activityId, date('Y-m-d', strtotime($yr . '-' . $mn . '-' . $dt)), 1)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }

        // Info::test($events);exit;


        $arr = array();
        foreach ($events as $eves) {
            $event_time = date('H:i', strtotime($eves['activityevent_time']));
            if ($event_time == '00:00') {
                $event_time = Info::t_lang('FULL_DAY');
            }
            $arr[$eves['activityevent_id']] = $event_time;
        }
        $frm = new Form('frmCoords');
        // $frm->addSelectBox(Info::t_lang('SELECTL_APPROPRIATE_EVENT'), 'events',$arr,"",array("id"=>"eventOption",'onchange'=>'updatePrice()'));
        $frm->addSelectBox(
                Info::t_lang('SELECT_APPROPRIATE_EVENT'), 'events', $arr, "", array(
            "id" => "eventOption",
            'onchange' => 'validateEvent(this, ' . $activityId . ');'
                )
        );

        $this->set('frm', $frm);

        $html = $this->_template->render(false, false, 'activity/_partial/event.php', true, true);
        die(FatUtility::convertToJson(array('status' => 1, 'html' => $html)));

        //	return $frm;
    }

    /* 0081 [ */

    public function validateEvent()
    {
        $activityId = FatApp::getPostedData('activity_id', FatUtility::VAR_INT, 0);
        $selEvent = FatApp::getPostedData('selevent', FatUtility::VAR_INT, 0);

        if ($activityId < 0 || $selEvent < 0) {
            FatUtility::dieJsonError('Invalid_request');
        }
        $msg = '';
        $actObj = new Activity($activityId);

        if (!$actObj->checkEventBookingAvailability($activityId, $selEvent, $msg)) {
            FatUtility::dieJsonError($msg);
        }
        $actObj->loadFromDb();
        $activityMemberCount = $actObj->getFlds();
        $activityMemberCount = $activityMemberCount['activity_members_count'];
        $enrolledMember = $actObj->getAnrolledMember($activityId, $selEvent);
        $leftOutSeats = $activityMemberCount - $enrolledMember;
        $availableSeats = $leftOutSeats > 0 ? $leftOutSeats : 0;
        FatUtility::dieJsonSuccess(array('availableSeats' => $availableSeats));
    }

    /* ] */

    public function updateAvailability($page = 1)
    {
        $passkey = FatApp::getQueryStringData('passkey', null, '');
        if ($passkey != 'fat-funaway-cron-update-availability') {
            FatUtility::dieWithError('Access denied to cron update availability!!');
        }
        /* if ($page == 2) {
            exit;
        } */
        $startTime = microtime(true);
        /* $srch1 = new SearchBase('tbl_activity_events');
          $srch1->joinTable('tbl_activities', 'INNER JOIN', 'activityevent_activity_id = activity_id');

          $srch1->addMultipleFields(array('activity_id', 'activity_start_date', 'activity_end_date', 'MAX(activityevent_time) AS max_event_date', 'activity_booking'));

          $srch1->addCondition('activity_confirm', '=', 1);
          $srch1->addCondition('activity_active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
          $srch1->addCondition('activity_booking_status', '=', 1);
          $srch1->addCondition('activity_duration', '!=', 100);

          // condition start date not in future
          $srch1->addCondition('activity_start_date', '<=', 'mysql_func_NOW()', 'AND', true);

          $srch1->addHaving('max_event_date', '<=', 'mysql_func_NOW()', '', true);
          $srch1->addOrder('activity_id', 'ASC');

          $pagesize = 10;
          if ($page < 1) {
          $page = 1;
          }

          $srch1->setPageNumber($page);
          $srch1->setPageSize($pagesize);

          $srch1->addGroupBy('activityevent_activity_id');
          $srch1->addOrder('activityevent_time', 'DESC');

          if ($page == 1) {
          $this->pages = $srch1->pages();
          }
          $rs1 = $srch1->getResultSet();

          // echo $srch1->getQuery();
          $records = FatApp::getDb()->fetchAll($rs1, 'activity_id'); */

        $srch = new SearchBase('tbl_activities');
        $srch->joinTable('tbl_activity_events', 'LEFT JOIN', 'activityevent_activity_id = activity_id');

        $srch->addMultipleFields(array(
            'activity_id',
            'activity_start_date',
            'activity_end_date',
            '(SELECT activityevent_time from `tbl_activity_events` WHERE activityevent_activity_id = activity_id ORDER BY activityevent_time DESC LIMIT 0, 1) AS max_event_date',
            'activity_booking',
            'activity_duration',
            'activityevent_anytime')
        );

        $srch->addCondition('activity_confirm', '=', 1);
        $srch->addCondition('activity_active', '=', AppUtilities::RECORD_ACTIVE_STATUS);
        $srch->addCondition('activity_booking_status', '=', 1);
        $srch->addCondition('activity_duration', '!=', 100);

        // condition start date not in future
        $srch->addCondition('activity_start_date', '<=', date('Y-m-d'), 'AND', true);
        $srch->addGroupBy('activity_id');
        $pagesize = 10;
        if ($page < 1) {
            $page = 1;
        }

        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);

        $rs1 = $srch->getResultSet();
        
        $records = FatApp::getDb()->fetchAll($rs1, 'activity_id');
        
        if ($page == 1) {
            $this->pages = $srch->pages();
        }
        
        $res = '';
		
        if (count($records) > 0) {
            // $i = 0;
            foreach ($records as $rKey => $row) {
                $res .= "<br>---{$row['activity_id']} Starts------<br><br>";
                // $i++;
                /* todo  
                  I) Increase activity end date if less than or equal to now
                  II) Add event time availability for next three months
                  if duration_type != 100
                  then add event availability based on duration
                  if(duration less than 2 hours)
                  add three entries of 1 hours

                  if(duration 2-4 hours)
                  add two entries of 2 hours

                  if(duration 4-6 hours)
                  add one entry of 4 hours

                  if(duration 6-12 hours)
                  add one entry of 6 hours
                 */

                $activityStartDate = date('Y-m-d', strtotime($row['activity_start_date']));
                $todaysDate = date('Y-m-d');

				
                if ($activityStartDate > $todaysDate || date('Y-m-d', strtotime($row['max_event_date'])) > $todaysDate) {
                    continue;
                }

				
                $actObj = new Activity($row['activity_id']);
                /* Increase the end date first */
                $activityEndDate = date('Y-m-d', strtotime($row['activity_end_date']));

                if ($activityEndDate <= $todaysDate) {
                    $activityEndDate = date('Y-m-d', strtotime("+3 months", strtotime($todaysDate)));

                    $activityData['activity_end_date'] = $activityEndDate;

                    $actObj->assignValues($activityData);
                    if (!$actObj->save()) {
                        continue;
                    }
                }
				
                $actBookingType = ($row['activity_booking'] < 1 ? 0 : 1);

                $hourSlot = $this->getHourSlots($row['activity_duration']);

                /* add events for next three months */
                $addEventsForMonths = 3;
                $eventEndDateMonthYear = date('Y-m', strtotime($row['max_event_date']));
                
                $anyTime = (!empty($row['activityevent_anytime']) ? FatUtility::int($row['activityevent_anytime']) : 0);
				
                if ($eventEndDateMonthYear == date('Y-m')) {
					$eventEndDateMonth = date('m', strtotime($row['max_event_date']));
                    $eventEndDateYear = date('Y', strtotime($row['max_event_date']));

                    $eventStartDate = $todaysDate;
                    if ($todaysDate == date('Y-m-d', strtotime($row['max_event_date']))) {
                        $eventStartDate = date('Y-m-d', strtotime($row['max_event_date']));
                    }
                    $eventStartDate = Calendar::getStartDate($row['activity_start_date'], $eventEndDateMonth, $eventEndDateYear);
                    $eventEndDate = Calendar::getEndDate($row['activity_end_date'], $eventEndDateMonth, $eventEndDateYear);

                    $this->addEventDates($actObj, $row['activity_id'], $eventStartDate, $eventEndDate, $actBookingType, $hourSlot, $anyTime);
                    $res .= "event End Date Month Year == current year month for activity id: {$row['activity_id']} <br>";
                } elseif ($eventEndDateMonthYear < date('Y-m')) {
                    $eventEndDateMonth = date('m', strtotime($todaysDate));
                    $eventEndDateYear = date('Y', strtotime($todaysDate));

                    $activityStartDate = $todaysDate;

                    $eventStartDate = Calendar::getStartDate($activityStartDate, $eventEndDateMonth, $eventEndDateYear);
                    $eventEndDate = Calendar::getEndDate($activityEndDate, $eventEndDateMonth, $eventEndDateYear);
					
                    $this->addEventDates($actObj, $row['activity_id'], $eventStartDate, $eventEndDate, $actBookingType, $hourSlot, $anyTime);
                    $res .= "event End Date Month Year < current year month for activity id: {$row['activity_id']} <br>";
                }

                if ($row['max_event_date'] < date('Y-m-d') || empty($row['max_event_date'])) {
					for ($n = 1; $n <= $addEventsForMonths; $n++) {
                        $nactivityStartDate = date('Y-m-d', strtotime("first day of +$n month"));
                        $neventEndDateMonth = date('m', strtotime($nactivityStartDate));
                        $neventEndDateYear = date('Y', strtotime($nactivityStartDate));

                        $curMonthEndtime = strtotime($nactivityStartDate);
                        $actMonthEndTime = strtotime($activityEndDate);

                        if ($actMonthEndTime < $curMonthEndtime) {
                            echo $res .= "for continue -- of activity id: {$row['activity_id']} and dates {$actMonthEndTime} < {$curMonthEndtime} <br>";
                            break;
                        } else {
                            $neventStartDate = Calendar::getStartDate($nactivityStartDate, $neventEndDateMonth, $neventEndDateYear);
                            $neventEndDate = Calendar::getEndDate($activityEndDate, $neventEndDateMonth, $neventEndDateYear);
                            $this->addEventDates($actObj, $row['activity_id'], $neventStartDate, $neventEndDate, $actBookingType, $hourSlot, $anyTime);
                            $res .= "In forloop from {$neventStartDate} month to {$neventEndDate} for activity id: {$row['activity_id']} <br>";
                        }
                    }
                }

                $res .= "<br>{$page} of Page {$this->pages } --- Activity{$row['activity_id']} Ends------<br><br>";
            }
        } else {
            $res .= "No More records";
        }

        if ($this->pages > 1 && $page <= $this->pages) {
            $page++;
            $res .= "<br> Next Page---{$page} Starts------<br><br>";
            $this->updateAvailability($page);
        }

        $endTime = microtime(true);
        echo "Completed In  " . (($endTime - $startTime) / 60) . " Mins <br><br>Task: <br><br> {$res}";
    }

    private function addEventDates($actObj, $activityId, $eventStartDate, $eventEndDate, $entryType, $hourSlot, $anyTime)
    {
        for ($dt = strtotime($eventStartDate); $dt <= strtotime($eventEndDate); $dt = strtotime(date("Y-m-d", strtotime("+1 day", $dt)))) {
            foreach ($hourSlot as $k => $hour) {
                $tslot = date('Y-m-d', $dt) . ' ' . $hour . ':00:' . '00';
                $array['activityevent_activity_id'] = $activityId;
                $array['activityevent_time'] = $tslot;
                $array['activityevent_anytime'] = $anyTime;
                $array['activityevent_status'] = 1;
                $array['activityevent_confirmation_requrired'] = $entryType;
                $actObj->addTimeSlot($array);
            }
        }
    }

    private function getHourSlots($duration = 2)
    {
        $startingTime = 9;

        switch ($duration) {
            case 2:
                return array(9, 11, 13);
                break;

            case 4:
                return array(9, 13);
                break;

            case 5:
                return array(9, 10);
                break;

            case 12:
                return array(8, 12);
                break;

            default:
                return array(9, 11, 13);
                break;
        }
    }
	
	public function updatePendingPriorConfirmationRequests() 
	{
		$request = new EventPriorConfirmationRequest();
		$previousDate = date('Y-m-d', strtotime(' -1 day'));
		$requestslist = $request->getRequests(EventPriorConfirmationRequest::STATUS_PENDING, $previousDate);
		if(empty($requestslist)) {
			return false;
		}
		
		
		foreach($requestslist as $reqData) {
		
		// Notification for Admin
		$notification_text = sprintf(Info::t_lang('PRIOR_CONFIRMATION_REQUEST_ADDED_BY_%s_FOR_ACTIVITY_-_%s_HAS_BEEN_CANCELLED_BY_SYSTEM'), $reqData['user_firstname'], $reqData['activity_name']);
		
		$notification = new Notification();
		$notification->notify(0, 0, '', $notification_text);
		
		//Notification for Activity host
		$notification_text =  sprintf(Info::t_lang('PRIOR_CONFIRMATION_REQUEST_FOR_ACTIVITY_-_%s_HAS_BEEN_CANCELLED_BY_SYSTEM'), $reqData['activity_name']);

		$notification = new Notification();
		$notification->notify($reqData['activity_user_id'], 0, '/host/request', $notification_text);
		
		//Notification for traveller
		$notification_text = sprintf(Info::t_lang('YOUR_PRIOR_CONFIRMATION_REQUEST_FOR_ACTIVITY_-_%s_HAS_BEEN_CANCELLED_BY_SYSTEM'), $reqData['activity_name']);
		
		$notification = new Notification();
		$notification->notify($reqData['requestevent_requested_by'], 0, '/traveler/request', $notification_text);
		
		$replace_vars_traveler = array(
                '{username}' => $reqData['user_firstname'],
                '{actvity-name}' => $reqData['activity_name'],
            );
			
		Email::sendMail($reqData['user_email'], 38, $replace_vars_traveler);
		}
		//Update status
		$request->updatePendingRequests();

	}
}
