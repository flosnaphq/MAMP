<?php

class ServicesController extends MyAppController {

    public function __construct($action) {
        parent::__construct($action);
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    function index($service_id = 0) {
        $service_id = FatUtility::int($service_id);
        $service = new Services($service_id);
        $service_name = "";
        $service_desc = "";
        if ($service_id > 0) {
            $service->loadFromDb();
            $service_name = $service->getFldValue(Services::DB_TBL_PREFIX . 'name');
            $service_desc = $service->getFldValue(Services::DB_TBL_PREFIX . 'description');
        }
        $this->set('service_id', $service_id);
        $this->set('service_name', $service_name);
        $this->set('service_desc', $service_desc);

        //Meta Data

        $pageTitle = sprintf("Top Activites In %s", $service_name);

        $keywordPlaceHolder = "Top Activities in  %s , %s";
        $metaData = array(
            'description' => $service_desc,
            'keywords' => sprintf($keywordPlaceHolder, $service_name, FatApp::getConfig("conf_website_title"))
        );
        $this->set('pageTitle', $pageTitle);
        $this->set('__metaData', $metaData);

        $this->_template->render();
    }

    public function subService() {

        $post = FatApp::getPostedData();
        $post['service_id'] = $post['service_id'] < 1 ? -1 : $post['service_id'];
        $services = Services::getCategories($post['service_id']);
        $option = "<option value=''>" . Info::t_lang('CHOOSE_CATEGORY') . "</option>";
        foreach ($services as $k => $v) {
            $option .= "<option value='{$k}'>{$v}</option>";
        }
        FatUtility::dieJsonSuccess($option);
    }

    function listing() {
        $post = FatApp::getPostedData();
        $page = isset($post['page']) ? FatUtility::int($post['page']) : 1;
        $service_id = isset($post['service_id']) ? FatUtility::int($post['service_id']) : 0;
        $island_id = FatApp::getPostedData('island_id', FatUtility::VAR_INT, 0);

        $page = ($page < 1 ? 1 : $page);
        $pagesize = 12;

        $service = new Services($service_id);

        $srch = $service->getSearchObject();

        if ($service_id > 0) {
            $srch->joinTable('tbl_activities', 'INNER JOIN', 'service_id =  activity_category_id AND activity_active = 1 AND activity_confirm = 1');


            if ($service_id > 0) {
                $srch->addCondition('cservice.service_parent_id', '=', $service_id);
            }
            $srch->addFld('cservice.*, COUNT(DISTINCT activity_id) as tot_activities');
        } else {
            $srch1 = new SearchBase(Services::DB_TBL, 'tscc');
            $srch1->joinTable('tbl_activities', 'INNER JOIN', 'tscc.service_id =  ac.activity_category_id AND ac.activity_active = 1 AND ac.activity_confirm = 1', 'ac');
            $srch1->addCondition('tscc.service_parent_id', '!=', 0);
            $srch1->addCondition('tscc.service_active', '=', 1);
            $srch1->doNotCalculateRecords();
            $srch1->doNotLimitRecords();
            $srch1->addMultipleFields(array("tscc.service_parent_id, ac.activity_id"));
            $qryCountSubcatActivities = $srch1->getQuery();

            $srch->joinTable('(' . $qryCountSubcatActivities . ')', 'LEFT JOIN', 'cservice.service_id = tspc.service_parent_id', 'tspc');

            $srch->addFld('cservice.*, COUNT(DISTINCT tspc.activity_id) as tot_activities');
        }
        $srch->addCondition('cservice.service_parent_id', '=', $service_id);
        $srch->addCondition('cservice.service_active', '=', 1);

        $srch->addGroupBy('cservice.service_id');
        $srch->addOrder('cservice.service_display_order');

        $srch->addHaving('tot_activities', '>', 0);

        $srch->setPageSize($pagesize);
        $srch->setPageNumber($page);

        $rs = $srch->getResultSet();
        $categories = FatApp::getDb()->fetchAll($rs);

        $total_pages = $srch->pages();
        $total_record = $srch->recordCount();

        $more_record = FatUtility::int((($page * $pagesize) < $total_record));

        $this->set('categories', $categories);
        $this->set('page', $page);
        $this->set('service_id', $service_id);

        $this->set('island_id', $island_id);

        $htm = $this->_template->render(false, false, 'services/_parital/listing.php', true, true);
        FatUtility::dieJsonSuccess(array('msg' => $htm, 'more_record' => $more_record));
    }

    public function getactivities() {
        $post = FatApp::getPostedData();

        $service_id = FatApp::getPostedData('service_id', FatUtility::VAR_INT, 0);

        $act = new Activity();
        $srch = $act->getSearchObject();

        $srch->joinTable('tbl_services', 'left join', 'schild.service_id = activity_category_id', 'schild');
        $srch->joinTable('tbl_services', 'left join', 'schild.service_parent_id = sparent.service_id', 'sparent');
        $srch->joinTable('tbl_reviews', 'left join', 'ar.review_type_id = activity_id AND review_active=1 AND review_type=0', 'ar');

        $srch->addCondition('activity_active', '=', 1);
        $srch->addCondition('activity_confirm', '=', 1);
        $srch->addCondition('activity_start_date', '<=', Info::currentDatetime());
        $srch->addCondition('activity_end_date', '>', Info::currentDatetime());



        if ($service_id > 0) {
            $srch->addCondition('schild.service_parent_id', '=', $service_id);
        }

        $srch->addFld('schild.service_name as childservice_name');
        $srch->addFld('sparent.service_name as parentservice_name');
        $srch->addFld('sparent.service_id as parentservice_id');
        $srch->addFld('tbl_activities.*');
        $srch->addFld(array('sum(`review_rating`) as rating,count(review_id) as reviews,count(Distinct review_id) as reviewcounter'));
        $srch->addGroupBy('activity_id');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(10);
        $srch->setPageNumber(1);
        $rs = $srch->getResultSet();
        $activities = FatApp::getDb()->fetchAll($rs);



        $this->set('activities', $activities);
        $htm = $this->_template->render(false, false, '_partial/ajax/activities-grid.php', true, true);
        $see_all = $srch->recordCount() > 10;
        $noResult = 0;

        if (count($activities) < 1) {
            $noResult = 1;
        }
        FatUtility::dieJsonSuccess(array('msg' => $htm, 'see_all' => $see_all, 'noResult' => $noResult));
    }

}
