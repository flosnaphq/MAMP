<?php

class CountryController extends MyAppController {

    function __construct($action) {
        parent::__construct($action);
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    public function details($countryId = 0) {

        $this->_template->addJs('common-js/plugins/slick.min.js');
        $countryInfo = Country::getAttributesById($countryId);
        if (empty($countryInfo)) {
            FatUtility::exitWithErrorCode(404);
        }
        $banner = AttachedFile::getMultipleAttachments(AttachedFile::FILETYPE_COUNTRY_IMAGE, $countryId);

        $service = Services::getSearchObject();
        $service->joinTable('tbl_activities', 'INNER JOIN', 'cservice.service_id =  activity_category_id AND activity_active = 1 AND activity_confirm = 1');
        $service->joinTable('tbl_services', 'INNER JOIN', 'cservice.service_parent_id =  pservice.service_id', 'pservice');
        $service->joinTable('tbl_cities', 'INNER JOIN', 'activity_city_id =  city_id AND city_active=1', 'city');
        $service->joinTable('tbl_countries', 'INNER JOIN', 'city_country_id =  country_id AND country_active=1', 'country');
        $service->addCondition('pservice.service_active', '=', 1);
        $service->addCondition('country_id', '=', $countryId);
        $service->addFld('distinct(pservice.service_id) as service_id, pservice.service_name, COUNT(activity_id) as tot_activities,city.*,country.*');
        $service->addGroupBy('pservice.service_id');
        $service->addOrder('pservice.service_display_order', 'asc');
        $rs = $service->getResultSet();
   
        $services = FatApp::getDb()->fetchAll($rs, 'service_id');
     
        $this->set('services', $services);
        $this->set('banners', $banner);
        $this->set('countryInfo', $countryInfo);
        $this->set('countryId', $countryId);

        //Meta Data

        $pageTitle = sprintf("Top Activites In %s",$countryInfo['country_name']);
        $activityTypes = array_column($services,'service_name');
        
        $keywordPlaceHolder = "Top Activities in  %s %s , %s";
        $metaData = array(
            'description' => $countryInfo['country_detail'],
            'keywords' => sprintf($keywordPlaceHolder, $countryInfo['country_name'],",".implode(",",$activityTypes),FatApp::getConfig("conf_website_title"))
        );
        $this->set('pageTitle', $pageTitle);
        $this->set('__metaData', $metaData);


        $this->_template->render();
    }

    public function cities() {
        $post = FatApp::getPostedData();
        $cities = Country::getCities($post['country_id']);
        $option = "<option value=''>" . Info::t_lang('SELECT_CITY') . "</option>";
        foreach ($cities as $k => $v) {
            $option .= "<option value='{$k}'>{$v}</option>";
        }
        FatUtility::dieJsonSuccess($option);
    }

    function activities() {

        $post = FatApp::getPostedData();
        $country_id = isset($post['country_id']) ? $post['country_id'] : 0;
        $act = new Activity();
        $srch = Activity::getSearchObject();
        $srch->joinTable('tbl_services', 'INNER join', 'schild.service_id = activity_category_id', 'schild');
        $srch->joinTable('tbl_services', 'INNER join', 'schild.service_parent_id = sparent.service_id', 'sparent');
        $srch->joinTable('tbl_cities', 'INNER JOIN', 'activity_city_id =  city_id AND city_active=1', 'city');
        $srch->joinTable('tbl_countries', 'INNER JOIN', 'city_country_id =  country_id AND country_active=1', 'country');
        $srch->joinTable('tbl_reviews', 'left join', 'ar.review_type_id = activity_id AND review_active=1 AND review_type=0', 'ar');
        $srch->addCondition('activity_active', '=', 1);
        $srch->addCondition('activity_confirm', '=', 1);
        $srch->addCondition('activity_start_date', '<=', Info::currentDatetime());
        $srch->addCondition('activity_end_date', '>', Info::currentDatetime());
        $srch->addCondition('country_id', '=', $country_id);
        $srch->addFld('schild.service_name as childservice_name');
        $srch->addFld('sparent.service_name as parentservice_name');
        $srch->addFld('sparent.service_id as parentservice_id');
        $srch->addFld('tbl_activities.*');
        $srch->addFld(array('sum(`review_rating`) as rating,count(review_id) as reviews,count(Distinct review_id) as reviewcounter'));
        $srch->addGroupBy('activity_id');
      
        $srch->doNotCalculateRecords();
        $srch->setPageSize(12);
        $srch->setPageNumber(1);
        $rs = $srch->getResultSet();
   
        $activities = FatApp::getDb()->fetchAll($rs);

        $this->set('activities', $activities);
        $htm = $this->_template->render(false, false, '_partial/ajax/activities-grid.php', true, true);
        $see_all = $srch->recordCount()>12;
            $noResult = 0;

        if (count($activities) < 1) {
            $noResult = 1;
        }
        FatUtility::dieJsonSuccess(array('msg' => $htm, 'see_all' => $see_all,'noResult'=>$noResult));
    }

	public function getCountryPhoneCodes($countryId = 0)
	{
		$phoneCodes = Country::getPhoneCodes(false, $countryId);
		if(false === $phoneCodes) {
			if (true === FatUtility::isAjaxCall()) {
				FatUtility::dieWithError('Error! ' . Info::t_lang('Invalid_country_selected'));
			}
			return false;
		}
		if (true === FatUtility::isAjaxCall()) {
			FatUtility::dieJsonSuccess($phoneCodes);
		}
		return $phoneCodes;
	}
}
