<?php

class HomeController extends MyAppController {

    public function __construct($action) {
        parent::__construct($action);
        $this->set('controller', 'home');
        $this->set('action', '');
    }

	public function test()
	{
		/* $actObj = new Activity();
		$eventDetail = $actObj->getEventWithActivity(29,596);
		echo '<pre>' . print_r($eventDetail, true); */
		exit;
		
	}

    public function index() {

        $block = new Block();
        $user = new User();
        $act = new Activity();
        $afile_ids = array();

        $featuredCities = City::getCitiesForHome();
        $this->set('featuredCities', $featuredCities);

        $userCounts = $user->getTotalUsersCount();
        $this->set('userCounts', $userCounts);

        $activity = count($act->getFeaturedActivitiesCount());
        $this->set('activity', $activity);
		$wuc = $block->getBlock(Block::BLOCK_WHYCHOOSE);
        $this->set('wuc', $wuc);

        $blogPost = new BlogPosts();
        $featuredPosts = $blogPost->getFeaturedPost(1, 3);
        $this->set('featuredPosts', $featuredPosts);

        $frm = $this->getSearchForm();
        $this->set('frm', $frm);

        $this->set('afile_ids', $afile_ids);

        $this->set('services', Services::getCategories(0, 8, true));

        $this->set('banners', Banner::getHomePageBanner());

        $this->set('action', 'index');

        $this->set('testimonials', Testimonial::getTestimonials());

        $this->set('mailchimpForm', Helper::getNewsletterForm());

        $this->_template->addJs('js/lazy.js');

        $this->_template->render();
    }
  function ajaxLoad2() {
        $act = new Activity();
        $featured_activities = $act->getFeaturedActivities(true, true);

        $this->set('featured_activities', $featured_activities);
        $noResult = 0;
  
        if(count($featured_activities)< 1){
            $noResult = 1;
        }
        $htm = $this->_template->render(false, false, 'home/_partial/featured-list.php', true, true);
        FatUtility::dieJsonSuccess(array('featureList' => $htm,'noResult'=>$noResult));
    }

    function ajaxLoad()
    {
        $act = new Activity();
        $featured_cities = City::getFeaturedCities(array('city_id', 'city_name'));
        /* $featured_activities = $act->getFeaturedActivitiesWithCityKey(true, true);*/
        $featuredCityIds = array_keys($featured_cities);
        $featured_activities = $act->getFeaturedActivitiesWithCityKey(true, $featuredCityIds);
        $featured_cities = array_intersect_key($featured_cities, $featured_activities);        
        $this->set('featured_cities', $featured_cities);
        $this->set('featured_activities', $featured_activities);
        $noResult = 0;
  
        if(count($featured_cities)< 1){
            $noResult = 1;
        }
        $htm = $this->_template->render(false, false, 'home/_partial/featured-list.php', true, true);
        FatUtility::dieJsonSuccess(array('featureList' => $htm,'noResult'=>$noResult));
    }

    private function getSearchForm() {
        $frm = new Form('searchFrm');
        $frm->addRequiredField(Info::t_lang('Keyword'), 'keyword', '', array('placeholder' => Info::t_lang('SEARCH_FOR_ACTIVITIES_BY_ACTIVITY_OR_LOCATION'), 'id' => 'search-autocomplete'));
        $frm->addSubmitButton(Info::t_lang('SEARCH'), 'search', Info::t_lang('SEARCH'));
        return $frm;
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    function mainSearch() {
        $post = FatApp::getPostedData();
        $keyword = $post['keyword'];
        $pageSize = 6;
        $page = isset($post['page']) ? FatUtility::int($post['page']) : 1;
        $page = $page > 1 ? $page : 1;
        $srch = Activity::getSearchObject();

        $srch->joinTable('tbl_city', 'left join', 'city_id = activity_city_id and city_active = 1');

        $srch->joinTable('tbl_services', 'left join', '(schild.service_id = activity_category_id or schild.service_parent_id = activity_category_id) and service_active =1', 'schild');

        $srch->joinTable('tbl_services', 'left join', 'sparent.service_id = schild.service_parent_id  and sparent.service_active =1', 'sparent');


        $srch->addCondition('activity_active', '=', 1);
        $srch->addCondition('activity_confirm', '=', 1);
        //$srch->addDirectCondition("date(activity_start_date) < '".Info::currentDate()."'");
        //$srch->addDirectCondition("date(activity_end_date) > '".Info::currentDate()."'");
        $con = $srch->addCondition('activity_name', 'like', '%' . $keyword . '%');
        $con->attachCondition('city_name', 'like', '%' . $keyword . '%', 'or');
        $con->attachCondition('schild.service_name', 'like', '%' . $keyword . '%', 'or');
        $srch->setPageSize($pageSize);
        $srch->setPageNumber($page);

        $srch->addFld('schild.service_name as childservice_name');
        $srch->addFld('sparent.service_name as parentservice_name');
        $srch->addFld('sparent.service_id as parentservice_id');
        $srch->addFld('tbl_city.*');
        $srch->addFld('tbl_activities.*');


        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $total_record = $srch->recordCount();

        $more_record1 = $srch->recordCount() > ($page * $pageSize);
        $more_record = FatUtility::int($srch->recordCount() > ($page * $pageSize));
        $this->set('pages', $srch->pages());
        $this->set('more_record1', $more_record1);
        $this->set('page', $page);
        $this->set('records', $records);
        $htm = $this->_template->render(false, false, 'home/_partial/main-search.php', true, true);
        FatUtility::dieJsonSuccess(array('msg' => $htm, 'more_record' => $more_record));
    }

    function error404() {
        FatUtility::exitWithErrorCode(404);
    }

    public function cities($query) {

        $searchObj = City::getSearchObject();
        $searchObj->doNotCalculateRecords();
        $searchObj->addCondition('city_name', 'like', "%" . $query . "%");
        $searchObj->addCondition('city_active', '=', 1);
        $searchObj->addFld('city_name as name');
        $searchObj->addFld('city_id as id');
        $searchObj->addFld('0 as type');
        $rs = $searchObj->getResultSet();
        $citiesQuery = $searchObj->getQuery();

		//echo $citiesQuery;die;
		
        $searchObj = Country::getSearchObject();
        $searchObj->doNotCalculateRecords();
        $searchObj->addCondition('country_name', 'like', "%" . $query . "%");
        $searchObj->addCondition('country_active', '=', 1);
        $searchObj->addFld('country_name as name');
        $searchObj->addFld('country_id as id');
        $searchObj->addFld('1 as type');
        $rs = $searchObj->getResultSet();
        $countryQuery = $searchObj->getQuery();

        $finalQuery = " ($citiesQuery) UNION ($countryQuery)";
		
        $list = array();
        $st = FatApp::getDb()->query($finalQuery);
        while ($row = $st->fetchAssoc()) {
            $type = $row['type'];
            $id = $row['id'];
            $redirectUrl = "";
            switch ($type) {
                case 1:
                    $redirectUrl = Route::getRoute('country', 'details', array($id));
                    break;
                case 0:
                    $redirectUrl = Route::getRoute('city', 'details', array($id));
                    break;
            }

            $list[] = array('value' => $row['name'], 'redirect' => $redirectUrl);
        }

        die(json_encode($list));
    }

    public function activity($query) {

        $activity = Activity::getSearchObject();
		$activity->joinTable('tbl_users', 'inner join', 'activity_user_id = user_id and user_type = 1 and user_active = 1');
		$activity->joinTable('tbl_cities', 'inner join', 'city_id = activity_city_id and city_active = 1');
		$activity->joinTable('tbl_countries', 'inner join', 'country_id = city_country_id and country_active = 1');
		$activity->joinTable('tbl_regions', 'inner join', 'region_id = country_region_id and region_active = 1');
			
        $activity->addCondition('activity_active', '=', 1);
        $activity->addCondition('activity_confirm', '=', 1);
        $activity->addCondition('activity_name', 'like', "%" . $query . "%");
        $activity->addFld('activity_name as name');
        $activity->addFld('activity_id as id');
        $activity->addFld("CONCAT_WS(' ',user_firstname,user_lastname) as username");
        $rs = $activity->getResultSet();

        $list = array();
        while ($row = FatApp::getDb()->fetch($rs)) {
            $id = $row['id'];
			$username = $row['username'];
            $redirectUrl = Route::getRoute('activity', 'detail', array($id));
            $list[] = array('value' => $row['name']."($username)", 'redirect' => $redirectUrl);
        }

        die(json_encode($list));
    }

    function addToWish() {
        if (!User::isUserLogged()) {
        
            FatUtility::dieJsonError(Info::t_lang("PLEASE_LOGIN_FIRST_TO_ADD_IN_WISHLIST"));
        }
        $post = FatApp::getPostedData();
        $activity_id = intval($post['activity_id']);
        $user_id = User::getLoggedUserId();
        $wish = new Wishlist();
        $type = $wish->wishlistAction($activity_id, $user_id);
        if ($type == 'add') {
            die(FatUtility::convertToJson(array('status' => 1, 'type' => $type, 'msg' => Info::t_lang('ACTIVIY_HAVE_BEEN_ADDED_IN_WISHLIST'))));
        } elseif ($type == "delete") {
            die(FatUtility::convertToJson(array('status' => 1, 'type' => $type, 'msg' => Info::t_lang('ACTIVIY_HAVE_BEEN_DELETED_FROM_WISHLIST'))));
        } else {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_PWENT_WRONG!'));
        }
    }

}
