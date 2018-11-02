<?php

class ReviewsController extends MyAppController {

    const PAGESIZE = 10;

    public function __construct($action) {
        /* $ajaxCallArray = array('listing','form','setup','cmsDisplaySetup');
          if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
          //die("Invalid Action");
          } */
        parent::__construct($action);
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    public function activity($activity_name, $activity_id) {
        $canUserAddReviews = false;
        $this->_template->addJs('common-js/plugins/slick.min.js');
        $activity_id = FatUtility::int($activity_id);
        if ($activity_id <= 0) {
            FatUtility::exitWithErrorCode(404);
        }
        $act = new Activity($activity_id);
        $activity_data = $act->getActivity($activity_id);

        if (empty($activity_data)) {
            FatUtility::exitWithErrorCode(404);
        }
        if (User::isUserLogged()) {
            $user_id = User::getLoggedUserId();
            $canUserAddReviews = Reviews::canReviewByUser($user_id, $activity_id);
        }
        $reviews = new Reviews();
        $total_reviews = $reviews->getActivityRating($activity_id);
        $this->set('total_reviews', $total_reviews);
        $this->set('activity_data', $activity_data);
        $this->set('activity_id', $activity_id);
        $this->set('canUserAddReviews', $canUserAddReviews);
        $this->_template->render();
    }

    function activityReviewListing() {
        $pagesize = Static::PAGESIZE;

        $post = FatApp::getPostedData();
        $activity_id = isset($post['activity_id']) ? FatUtility::int($post['activity_id']) : 0;
        $page = isset($post['page']) ? FatUtility::int($post['page']) : 1;
        $page = $page <= 1 ? 1 : $page;
        $act = new Activity($activity_id);
        $activity_data = $act->getActivity($activity_id);

        if (empty($activity_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        $srch = Reviews::getSearchObject();
        $srch->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'user_id and ' . User::DB_TBL_PREFIX . 'active = 1');
        $srch->addCondition(Reviews::DB_TBL_PREFIX . 'type_id', '=', $activity_id);
        $srch->addCondition(Reviews::DB_TBL_PREFIX . 'active', '=', 1);
        $srch->addCondition(Reviews::DB_TBL_PREFIX . 'type', '=', Reviews::ACTIVITY_REVIEW_TYPE);
        $srch->setPageSize($pagesize);
        $srch->setPageNumber($page);
        $srch->addMultipleFields(array(
            Reviews::DB_TBL . '.*',
            User::DB_TBL_PREFIX . 'firstname',
            User::DB_TBL_PREFIX . 'lastname',
                )
        );
        $srch->addOrder(Reviews::DB_TBL_PREFIX . 'id', 'desc');
        $rs = $srch->getResultSet();

        $records = FatApp::getDb()->fetchAll($rs);
        $more_record = FatUtility::int(($page * $pagesize) < $srch->recordCount());
        $this->set('records', $records);
        $htm = $this->_template->render(false, false, 'reviews/_partial/activity-review-listing.php', true, true);
        $response = array('msg' => $htm, 'more_record' => $more_record);
        FatUtility::dieJsonSuccess($response);
    }

    public function listing($page = 1) {
        $pagesize = static::PAGESIZE;
        $data = FatApp::getPostedData();
        $e = new Activity();
        $search = Activity::getSearchObject();
        $search->joinTable('tbl_attached_files', 'LEFT JOIN', 'afile_record_id = activity_id and afile_type = ' . AttachedFile::FILETYPE_ACTIVITY_PHOTO);
        $search->joinTable('tbl_wishlist', 'Inner JOIN', 'wishlist_activity_id = activity_id ');
        $search->addCondition('wishlist_user_id', '=', $this->userId);
        $search->addCondition('activity_confirm', '=', 1);
        $search->addCondition('activity_active', '=', 1);
        $search->addMultipleFields(array('tbl_activities.*', 'substring_index(group_concat(afile_id),",",3) as activity_images'));
        $search->addGroupBy('activity_id');
        $search->addOrder('wishlist_date', 'desc');
        $page = $data['page'];
        $page = FatUtility::int($page);
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);
        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "wishlist/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function setup() {
        if (!User::isUserLogged()) {
            FatUtility::dieJsonError(Info::t_lang("PLEASE_LOGIN_FIRST_TO_REVIEW"));
        }

        $post = FatApp::getPostedData();
        $frm = $this->getForm();
        $post = FatApp::getPostedData();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $activity_id = @$post['activity_id'];
        $activity_id = FatUtility::int($activity_id);
        $user_id = User::getLoggedUserId();
        if (!Reviews::canReviewByUser($user_id, $activity_id, 0)) {
            FatUtility::dieJsonError(Info::t_lang('YOU_CANNOT_GIVE_A_REVIEW'));
        }
        $review = new Reviews();
        $data[Reviews::DB_TBL_PREFIX . 'user_id'] = $user_id;
        $data[Reviews::DB_TBL_PREFIX . 'type_id'] = $activity_id;
        $data[Reviews::DB_TBL_PREFIX . 'type'] = 0;
        $data[Reviews::DB_TBL_PREFIX . 'content'] = $post[Reviews::DB_TBL_PREFIX . 'content'];
        $data[Reviews::DB_TBL_PREFIX . 'rating'] = $post['ratesfld'];
        $data[Reviews::DB_TBL_PREFIX . 'date'] = Info::currentDatetime();
        $data[Reviews::DB_TBL_PREFIX . 'active'] = 1;
        if (!$review->saveReview($data)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEAE_TRY_AGAIN'));
        }
        AbuseReport::deleteAbuseRecord($activity_id, 0);
        $user_name = User::getLoggedUserAttribute(User::DB_TBL_PREFIX . 'firstname');
        Reviews::sendReviewNotificationToHost($activity_id, $user_name, $post[Reviews::DB_TBL_PREFIX . 'content'], $post['ratesfld']);
        $activityReviewData = $review->getActivityRating($activity_id);
        $reviewCount = 0;
        $rating = 0;

        if (isset($activityReviewData['total_count'])) {
            $reviewCount = $activityReviewData['total_count'];
        }
        if (isset($activityReviewData['rating'])) {
            $rating = $activityReviewData['rating'];
        }
        $rate = $rating * 100 / 5;
        FatUtility::dieJsonSuccess(array("msg" => Info::t_lang('REVIEW_SENT!'), 'reviewCount' => $reviewCount, 'reviewRating' => $rate));
    }

    function form() {
        if (!User::isUserLogged()) {
            FatUtility::dieJsonError(Info::t_lang("PLEASE_LOGIN_FIRST_TO_REVIEW"));
        }

        $post = FatApp::getPostedData();
        $event_id = @$post['event_id'];
        $user_id = User::getLoggedUserId();

        if (empty($event_id)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!_PLEAE_TRY_AGAIN.'));
        }
		$ord = new Order();
		$activitiesOrderData = $ord->getOrderActivityDetailData($event_id);
		
		if (empty($activitiesOrderData)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!_PLEAE_TRY_AGAIN2.'));
        }
		$activity_id = $activitiesOrderData['oactivity_activity_id'];
		$activityTiming = $activitiesOrderData['oactivity_event_timing'];
		$activityPaymentStatus = $activitiesOrderData['order_payment_status'];

		if (!Order::canTravelerReviewBooking($activityTiming, $activityPaymentStatus)) {
            FatUtility::dieJsonError(Info::t_lang('YOU_CANNOT_GIVE_A_REVIEW'));
        }
		
	
        if (!Reviews::canReviewByUser($user_id, $activity_id, 0)) {
            FatUtility::dieJsonError(Info::t_lang('YOU_CANNOT_GIVE_A_REVIEW'));
        }

        $data = array('activity_id' => $activity_id);
        $data['ratesfld'] = .5;
        $review = new Reviews();

        $review_data = $review->getUserReview($user_id, $activity_id);
        if (!empty($review_data)) {
            $data['review_content'] = $review_data['review_content'];
            $data['ratesfld'] = $review_data['review_rating'];
        }

        $frm = $this->getForm($data['ratesfld']);
        $frm->fill($data);
        $this->set('frm', $frm);
        $html = $this->_template->render(false, false, 'reviews/_partial/form.php', true, true);
        FatUtility::dieJsonSuccess($html);
    }

    private function getForm($rating = .5) {
        $frm = new Form('reviewForm');
        $frm->addTextArea(Info::t_lang('YOUR_COMMENT'), 'review_content', '', array('id' => 'review_content'))->requirements()->setRequired();

        $frm->addHtml("", "", '
		<div class="rating__block">
			' . Info::rating($rating, true, 'rating--light') . '</div>');
        $frm->addHiddenField('', 'ratesfld', $rating, array('class' => 'ratesfld'));
        $frm->addHiddenField('', 'activity_id');
        $frm->addSubmitButton('', 'btn_submit', Info::t_lang('SEND_MY_REVIEW'), array('class' => 'button button--fill button--red'));
        return $frm;
    }

    /*
     *  Used In Activity Detail Page
     */

    function activityReview() {
        $canUserAddReviews = true;
        $review_total = array();
        $reviews = array();
        $pageSize = 2;
        /////////////////// review \\\\\\\\\\\\\\\\\\\\\
        $post = FatApp::getPostedData();


        $page = isset($post['page']) ? intval($post['page']) : 1;
        $page = FatUtility::int($page);
        if ($page <= 0) {
            $page = 1;
        }
        $activityId = isset($post['activity_id']) ? intval($post['activity_id']) : 0;
        $activityId = FatUtility::int($activityId);
        if ($activityId <= 0) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!'));
        }
        $act = new Activity($activityId);
        $activity_data = $act->getActivity($activityId);
        if (!empty($activity_data)) {
            $review = new Reviews();
            $review_total = $review->getActivityRating($activityId);
            $review_srch = Reviews::getSearchObject(true);
            $review_srch->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'user_id');
            $review_srch->addCondition(Reviews::DB_TBL_PREFIX . 'type_id', '=', $activityId);
            $review_srch->addCondition(Reviews::DB_TBL_PREFIX . 'type', '=', 0);
            $review_srch->addCondition(Reviews::DB_TBL_PREFIX . 'active', '=', 1);
            $review_srch->addOrder(Reviews::DB_TBL_PREFIX . 'date', 'desc');
            $review_srch->setPageNumber($page);
            $review_srch->setPageSize($pageSize);
            $review_srch->addMultipleFields(array(
            '*',
            'count('.ReviewMessage::DB_TBL_PREFIX.'id) as numMessages',
                )
            );
            $review_rs = $review_srch->getResultSet();
            // echo $review_srch->getQuery(); exit;
            $reviews = FatApp::getDb()->fetchAll($review_rs);
            $reviewsWithMessages = [];
            array_walk($reviews, function(&$val, $key) use (&$reviewsWithMessages) {
                if($val['numMessages']>0){
                    $reviewsWithMessages[$val['review_id']] = array();
                }
            });
            $reviewMessages= array();
            if(!empty(array_keys($reviewsWithMessages))){
                $reviewMessageSrch = ReviewMessage::getSearchObject(false,true,true);
                $reviewMessageSrch->addCondition(ReviewMessage::DB_TBL_PREFIX . 'review_id', 'IN', array_keys($reviewsWithMessages) );
                $reviewMessageSrch->addCondition(ReviewMessage::DB_TBL_PREFIX . 'active', '=', 1 );
                $reviewMessageSrch->addOrder(ReviewMessage::DB_TBL_PREFIX . 'added_on', 'desc');
                $reviewMessageSrch->addMultipleFields(array(
                    ReviewMessage::DB_TBL_PREFIX . 'id',
                    ReviewMessage::DB_TBL_PREFIX . 'review_id',
                    ReviewMessage::DB_TBL_PREFIX . 'added_on',
                    ReviewMessage::DB_TBL_PREFIX . 'message',
                    ReviewMessage::DB_TBL_PREFIX . 'user_id',
                    Admin::DB_TBL_PREFIX . 'id',
                    Admin::DB_TBL_PREFIX . 'name',
                    User::DB_TBL_PREFIX . 'id',
                    'concat('.User::DB_TBL_PREFIX . 'firstname,'.User::DB_TBL_PREFIX . 'lastname) user_full_name',
                    )
                );
                $rs = $reviewMessageSrch->getResultSet();
                $reviewMessages = FatApp::getDb()->fetchAll($rs);
            }
            
            foreach($reviewMessages as $reviewMessage){
                if(in_array($reviewMessage[ReviewMessage::DB_TBL_PREFIX . 'review_id'],array_keys($reviewsWithMessages))){
                    $reviewsWithMessages[$reviewMessage[ReviewMessage::DB_TBL_PREFIX . 'review_id']][] = $reviewMessage;
                }
            }
        }
        if (User::isUserLogged()) {
            $user_id = User::getLoggedUserId();
            $canUserAddReviews = Reviews::canReviewByUser($user_id, $activityId);
        }

        /////////////////// review end\\\\\\\\\\\\\\\\\\\\\
        $pages = $review_srch->recordCount();
        //Hide If No Result
        if ($pages == 0) {
            FatUtility::dieJsonSuccess(array('msg' => ''));
        }

        $more_record = FatUtility::int(($page * $pageSize) < $pages);
        $this->set("activity_data", $activity_data);
        $this->set("activityId", $activityId);
        $this->set("review_total", $review_total);
        $this->set("reviews", $reviews);
        $this->set("messages", $reviewsWithMessages);
        $this->set("page", $page);
        $this->set("pages", $pages);

        $this->set("canUserAddReviews", $canUserAddReviews);
        $htm = $this->_template->render(false, false, 'reviews/_partial/activity-review.php', true, true);
        FatUtility::dieJsonSuccess(array('msg' => $htm, 'more_record' => $more_record));
    }

    function activityReviewDetail() {
        $post = FatApp::getPostedData();
        $review_id = @$post['review_id'];
        $review_id = FatUtility::int($review_id);
        if ($review_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!_PLEAE_TRY_AGAIN.'));
        }
        $review = new Reviews();
        $data = $review->getReviewWithUser($review_id);
        if (empty($data)) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG!_PLEAE_TRY_AGAIN.'));
        }

        $this->set('review', $data);
        $htm = $this->_template->render(false, false, 'reviews/_partial/activity-review-detail.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

}
