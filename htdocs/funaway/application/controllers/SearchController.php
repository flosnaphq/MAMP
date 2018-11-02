<?php

class SearchController extends MyAppController {

    protected $searchFilter = array(
        'type',
        'city',
        'country',
        'keyword',
        'activity_type',
        'categories',
        'duration',
        'price',
        'sort',
    );
    protected $fiterSettings = array();

    public function initializeFiltersSettings() {

        $this->fiterSettings = array(
            'keyword' => array(
                'type' => 'text',
                'title' => 'Keyword',
                'attributes' => array('Placeholder' => 'Keyword', 'class' => 'custom_filters searchKeyword'),
            ),
            'city' => array(
                'type' => 'select',
                'options' => array(),
                'title' => 'Select City',
                'attributes' => array('class' => 'searchcity custom_filters'),
            ),
            'country' => array(
                'type' => 'select',
                'options' => Country::getCountries(),
                'title' => 'Select Country',
                'attributes' => array('class' => "searchcountry custom_filters", 'onchange' => "getCities(this)"),
            ),
            'categories' => array(
                'type' => 'select',
                'options' => array(),
                'title' => 'Select Category',
                'attributes' => array('class' => "searchCategories custom_filters"),
            ),
            'activity_type' => array(
                'type' => 'select',
                'options' => Services::getCategories(),
                'title' => 'Select Activity Type',
                'attributes' => array('class' => "searchThemes custom_filters", 'onchange' => "getSubService(this)"),
            ),
            'duration' => array(
                'type' => 'select',
                'options' => Info::searchDuration(),
                'title' => 'Select Duration',
                'attributes' => array('class' => "searchDuration custom_filters"),
            ),
            'price' => array(
                'type' => 'select',
                'options' => Info::searchPrice(),
                'title' => 'Select Price',
                'attributes' => array('class' => 'searchPrice custom_filters'),
            ),
            'sort' => array(
                'type' => 'select',
                'options' => Info::getSortBy(),
                'title' => 'Sort',
                'attributes' => array('id' => "sortFilter ", 'class' => "sortFilter custom_filters"),
            ),
        );
    }

    public function index() {

        $this->initializeFiltersSettings();
        $this->_template->addJs('activity/page-js/social.js');
        $searchFrm = $this->getSearchForm();
        $getRequest = $_GET;
        $searchFrm->fill($getRequest);

        //Custom Work
        if (isset($getRequest['country']) && intval($getRequest['country']) > 0) {
            $countryId = $getRequest['country'];
            $searchFrm->getField('city')->options = City::getAllCitiesByCountryId($countryId);
	
        }
		
		if (isset($getRequest['city']) && intval($getRequest['city']) > 0) {
            $cityId = $getRequest['city'];
			$cityData = City::getCityById($cityId);
		
			$countryId = $cityData['country_id'];
			$searchFrm->getField('country')->value = $countryId;
            $searchFrm->getField('city')->options = City::getAllCitiesByCountryId($countryId);
        }
		
		
        if (isset($getRequest['activity_type']) && intval($getRequest['activity_type']) > 0) {
            $activityType = $getRequest['activity_type'];
            $searchFrm->getField('categories')->options = Services::getCategories($activityType);
        }
			
        $this->set('isPreSearch', $this->isSearchPreset());
        $this->set('searchFrm', $searchFrm);
        $this->set('type', 'grid');
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('SearchForm');
        foreach ($this->searchFilter as $filter) {
            $type = (isset($this->fiterSettings[$filter]) ? $this->fiterSettings[$filter]['type'] : "text");

            $title = (isset($this->fiterSettings[$filter]) && !empty($this->fiterSettings[$filter]['title']) ? $this->fiterSettings[$filter]['title'] : $filter);
            $attributes = (isset($this->fiterSettings[$filter]) && !empty($this->fiterSettings[$filter]['attributes']) ? $this->fiterSettings[$filter]['attributes'] : array());

            switch (strtoupper($type)) {
                case 'SELECT':
                    $options = (isset($this->fiterSettings[$filter]) && !empty($this->fiterSettings[$filter]['options']) ? $this->fiterSettings[$filter]['options'] : array());
                    $frm->addSelectBox($title, $filter, $options, '', $attributes, $title);
                    break;
                default:
                    $frm->addTextBox($title, $filter, '', $attributes);
                    break;
            }
        }
        return $frm;
    }

    public function isSearchPreset() {
        $getRequest = $_GET;
        $filteredRequest = array_filter($getRequest);
        return array_intersect(array_keys($filteredRequest), $this->searchFilter);
    }

    public function listing() {
        $acts = new Activity();
        $srch = $acts->getSearchObject();
        $srch->joinTable('tbl_users', 'inner join', 'activity_user_id = user_id and user_type = 1 and user_active = 1');
        $srch->joinTable('tbl_cities', 'inner join', 'activity_city_id = city_id and city_active = 1');
        $srch->joinTable('tbl_countries', 'inner join', 'city_country_id = country_id and country_active = 1');
        $srch->joinTable('tbl_activity_languages', 'left join', 'activity_id = activitylanguage_activity_id');
        $srch->joinTable('tbl_languages', 'left join', 'language_id = activitylanguage_language_id');
        $srch->joinTable('tbl_services', 'inner join', 'schild.service_id = activity_category_id', 'schild');
        $srch->joinTable('tbl_services', 'left join', 'schild.service_parent_id = sparent.service_id', 'sparent');
        $srch->joinTable('tbl_reviews', 'left join', 'ar.review_type_id = activity_id AND review_active=1 AND review_type=0', 'ar');

        $srch->addFld('schild.service_name as childservice_name');
        $srch->addFld('sparent.service_name as parentservice_name');
        $srch->addFld('sparent.service_id as parentservice_id');
        $srch->addCondition('activity_confirm', '=', 1);
        $srch->addCondition('activity_active', '=', 1);


        $srch->addGroupBy('activitylanguage_activity_id');
        $srch->addMultipleFields(array('tbl_users.*', 'tbl_activities.*', 'SUBSTRING_INDEX(group_concat(language_name separator ","),",",3) as act_lang,sum(`review_rating`) as rating,count(review_id) as ratingcounter,count(DISTINCT review_id) as reviews'));
        $data = FatApp::getPostedData();

        foreach ($data as $k => $v) {
            switch ($k) {
                case "sort": {
                        if (trim($v[0]) == "duration") {
                            $srch->addOrder('activity_duration', 'desc');
                        }
                        if (trim($v[0]) == "price") {
                            $srch->addOrder('activity_price', 'desc');
                        }
                        if (trim($v[0]) == "popular") {

                            $srch->addOrder('rating', 'desc');
                        }

                        break;
                    }
                case "keyword": {
                        if ($v) {
                            $srch->addDirectCondition("activity_name LIKE '%$v%'");
                        }
                        break;
                    }
                case "durations": {
                        $duration = @$v[0];
                        if (!empty($duration)) {
                            $srch->addCondition('activity_duration', '=', $duration);
                        }
                        break;
                    }

                case "activity_type": {
                        $srch->addDirectCondition('schild.service_parent_id IN (' . implode(',', $v) . ')');
                        break;
                    }

                case "categories": {
                        $srch->addCondition('activity_category_id', '=', $v[0]);
                        break;
                    }
                case "cities": {
                        $srch->addCondition('activity_city_id', '=', $v[0]);
                        break;
                    }
                case "countries": {
                        $srch->addCondition('city_country_id', '=', $v[0]);
                        break;
                    }

                case "prices": {
                        $p = explode('-', $v[0]);
                        if (!empty($p[0])) {
                            $srch->addCondition('activity_price', '>=', $p[0]);
                        }
                        if (!empty($p[1])) {
                            $srch->addCondition('activity_price', '<', $p[1]);
                        }


                        break;
                    }
            }
        }

        if (User::isUserLogged()) {
            $srch->joinTable('tbl_wishlist', 'left join', 'wishlist_activity_id = activity_id and 	wishlist_user_id = ' . User::getLoggedUserId());
            $srch->addFld('wishlist_activity_id');
        }

        $pagesize = 12;
        /* $page = $data['page'];
        $page = FatUtility::int($page); */
        
		$page = 0;
		if (array_key_exists('page', $data)) {
			$page = FatUtility::int($data['page']);
		}
        
        $srch->setPageNumber($page);
        $srch->setPageSize($pagesize);
        $rs = $srch->getResultSet();

        $records = FatApp::getDb()->fetchAll($rs);
        foreach ($records as $k => $record) {
            $records[$k]['booking_status'] = Activity::isActivityOpen($record);
        }
        $more_record = ($page * $pagesize) < $srch->recordCount();
        $this->set("arr_listing", $records);
        $this->set('totalPage', $srch->pages());
        $this->set('page', $page);
        $this->set('more_record', $more_record);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "search/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess(array('msg' => $htm, 'more_record' => FatUtility::int($more_record)));
    }

}
