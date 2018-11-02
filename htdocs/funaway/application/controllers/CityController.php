<?php

class CityController extends MyAppController {

    function __construct($action) {
        parent::__construct($action);
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    public function details($cityId = 0) {

        $this->_template->addJs('common-js/plugins/slick.min.js');
        $city = City::getCityById($cityId);

        if (empty($city)) {
            FatUtility::exitWithErrorCode(404);
        }
        $banner = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_CITY_IMAGE, $cityId);

        $service = Services::getSearchObject();
        $service->joinTable('tbl_activities', 'INNER JOIN', 'cservice.service_id =  activity_category_id AND activity_active = 1 AND activity_confirm = 1');
        $service->joinTable('tbl_services', 'INNER JOIN', 'cservice.service_parent_id =  pservice.service_id', 'pservice');
        $service->addCondition('pservice.service_active', '=', 1);
        $service->addCondition('activity_city_id', '=', $cityId);
        $service->addFld('distinct(pservice.service_id) as service_id, pservice.service_name, COUNT(activity_id) as tot_activities');

        $service->addGroupBy('pservice.service_id');
        $service->addOrder('pservice.service_display_order', 'asc');

        $rs = $service->getResultSet();
        $services = FatApp::getDb()->fetchAll($rs, 'service_id');

        $this->set('services', $services);
        $this->set('banners', $banner);
        $this->set('cityInfo', $city);
        $this->set('selectedcityId', $cityId);

        //Meta Data

        $pageTitle = sprintf("Top Activites In %s", $city['city_name']);
        $activityTypes = array_column($services, 'service_name');

        $keywordPlaceHolder = "Top Activities in  %s %s , %s";
        $metaData = array(
            'description' => $city['city_detail'],
            'keywords' => sprintf($keywordPlaceHolder, $city['city_name'], "," . implode(",", $activityTypes), FatApp::getConfig("conf_website_title"))
        );
        $this->set('pageTitle', $pageTitle);
        $this->set('__metaData', $metaData);

        $this->_template->render();
    }

    function activities() {

        $post = FatApp::getPostedData();
        $city_id = isset($post['city_id']) ? $post['city_id'] : 0;
        $activity_id = isset($post['activity_id']) ? $post['activity_id'] : 0;

        $srch = Activity::getSearchObject();

        $srch->joinTable('tbl_services', 'left join', 'schild.service_id = activity_category_id', 'schild');
        $srch->joinTable('tbl_services', 'left join', 'schild.service_parent_id = sparent.service_id', 'sparent');
        $srch->joinTable('tbl_reviews', 'left join', 'ar.review_type_id = activity_id AND review_active=1 AND review_type=0', 'ar');
        $srch->addCondition('activity_active', '=', 1);
        $srch->addCondition('activity_confirm', '=', 1);
        $srch->addCondition('activity_start_date', '<=', Info::currentDatetime());
        $srch->addCondition('activity_end_date', '>', Info::currentDatetime());
        $srch->addCondition('activity_city_id', '=', $city_id);
        
        //Used Activity Detail Page
        if($activity_id){
             $srch->addCondition('activity_id', '<>', $activity_id);
        }
        
        $srch->addFld('schild.service_name as childservice_name');
        $srch->addFld('sparent.service_name as parentservice_name');
        $srch->addFld('sparent.service_id as parentservice_id');
        $srch->addFld('tbl_activities.*');
        $srch->addFld(array('sum(`review_rating`) as rating,count(review_id) as reviews,count(Distinct review_id) as reviewcounter'));
        $srch->addGroupBy('activity_id');

        $srch->setPageSize(12);
        $srch->setPageNumber(1);
		//echo $srch->getQuery();die;
        $rs = $srch->getResultSet();

        $activities = FatApp::getDb()->fetchAll($rs);

        $this->set('activities', $activities);
        $htm = $this->_template->render(false, false, '_partial/ajax/activities-grid.php', true, true);
        $see_all = $srch->recordCount() > 12;
        $noResult = 0;

        if (count($activities) < 1) {
            $noResult = 1;
        }

        FatUtility::dieJsonSuccess(array('msg' => $htm, 'see_all' => $see_all, 'noResult'=>$noResult));
    }

}
