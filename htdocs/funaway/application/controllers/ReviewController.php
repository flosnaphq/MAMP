<?php

class ReviewController extends UserController {

    public function __construct($action) {
        parent::__construct($action);
        $this->set('controller', 'review');
        $this->set("class", "is--dashboard");
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

    public function index() {
        $act = new Activity();
        $activities = $act->getActivitiesForForm($this->userId);
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('MESSAGES'));
        $brcmb->add(Info::t_lang('Reviews'));
        $this->set('breadcrumb', $brcmb->output());
        $this->set('activities', $activities);
        $this->_template->render();
    }

    public function listing() {
        $pagesize = static::PAGESIZE;
        //	$pagesize=1;
        $activity_id = 0;
        $data = FatApp::getPostedData();

        $page = @$data['page'];
        $page = FatUtility::int($page);
        $src = Reviews::getSearchObject(true);
        $src->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'user_id');
        if ($this->user_type == 1) {
            $src->joinTable(Activity::DB_TBL, 'INNER JOIN', Activity::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'type_id and ' . Activity::DB_TBL_PREFIX . 'user_id = ' . $this->userId);
        } else {
            $src->joinTable(Activity::DB_TBL, 'INNER JOIN', Activity::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'type_id  ');
            $src->addCondition(Reviews::DB_TBL_PREFIX . 'user_id', '=', $this->userId);
        }

        $src->joinTable(AbuseReport::DB_TBL, 'left JOIN', AbuseReport::DB_TBL_PREFIX . 'record_id = ' . Reviews::DB_TBL_PREFIX . 'id and ' . AbuseReport::DB_TBL_PREFIX . 'record_type = 0');
        if (!empty($data['activity_id'])) {
            $activity_id = FatUtility::int($data['activity_id']);
            $src->addCondition(Activity::DB_TBL_PREFIX . 'id', '=', $activity_id);
        }
        if ($page > 0 && $pagesize > 0) {
            $src->setPageNumber($page);
            $src->setPageSize($pagesize);
        }
        if (!empty($post['activity_id'])) {
            $src->addCondition(Reviews::DB_TBL_PREFIX . 'type_id', '=', $post['activity_id']);
        }
        $src->addOrder(Reviews::DB_TBL_PREFIX . 'date', 'desc');
        $src->addMultipleFields(array(
            Reviews::DB_TBL . '.*',
            AbuseReport::DB_TBL . '.*',
            Activity::DB_TBL_PREFIX . 'name',
            Activity::DB_TBL_PREFIX . 'image_id',
            Activity::DB_TBL_PREFIX . 'id',
            'concat(' . User::DB_TBL_PREFIX . 'firstname," ", ' . User::DB_TBL_PREFIX . 'lastname) as user_name',
            'count('.ReviewMessage::DB_TBL_PREFIX.'id) as numMessages',
            'sum('.ReviewMessage::DB_TBL_PREFIX.'user_type) hasHostReplied',
                )
        );
        $rs = $src->getResultSet();
        //echo $src->getQuery();
        $records = FatApp::getDb()->fetchAll($rs);
        $reviewsWithMessages = [];
        array_walk($records, function(&$val, $key) use (&$reviewsWithMessages) {
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
        $this->set("arr_listing", $records);
        $this->set("messages", $reviewsWithMessages);
        $this->set('totalPage', $src->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $this->set('activity_id', $activity_id);
        if ($this->user_type == 1) {
            $htm = $this->_template->render(false, false, "review/listing.php", true, true);
        } else {
            $htm = $this->_template->render(false, false, "review/traveler-listing.php", true, true);
        }
        FatUtility::dieJsonSuccess($htm);
    }

    function markAsAbuse() {
        if ($this->user_type != 1) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $post = FatApp::getPostedData();
        $review_id = isset($post['review_id']) ? FatUtility::int($post['review_id']) : 0;
        if ($review_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $frm = $this->getMarkAsAbuseForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($this->getValidationErrors()));
        }
        $reviews = new Reviews($review_id);
        $abuseReport = new AbuseReport();
        $reviews->loadFromDb();
        $review_data = $reviews->getFlds();
        if (empty($review_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $act = new Activity($review_data[Reviews::DB_TBL_PREFIX . 'type_id']);
        $act->loadFromDb();
        $activity_data = $act->getFlds();
        if ($activity_data[Activity::DB_TBL_PREFIX . 'user_id'] != $this->userId) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $abreport_record_id = $review_id;
        $abreport_record_type = AbuseReport::REVIEW_ABUSE;
        $abreport_user_id = $this->userId;
        $abReport = $abuseReport->getAbuseReport($abreport_record_id, $abreport_record_type, $abreport_user_id);
        if (!empty($abReport)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $data[AbuseReport::DB_TBL_PREFIX . 'record_id'] = $abreport_record_id;
        $data[AbuseReport::DB_TBL_PREFIX . 'record_type'] = $abreport_record_type;
        $data[AbuseReport::DB_TBL_PREFIX . 'user_id'] = $abreport_user_id;
        $data[AbuseReport::DB_TBL_PREFIX . 'user_comment'] = $post['comment'];
        $data[AbuseReport::DB_TBL_PREFIX . 'posted_on'] = Info::currentDatetime();
        $data[AbuseReport::DB_TBL_PREFIX . 'taken_care'] = 0;
        $abuseReport->assignValues($data);
        if (!$abuseReport->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG.PlEASE_TRY_AGAIN'));
        }
        $notify = new Notification();
        $usr = new User($this->userId);
        $usr->loadFromDb();
        $host_data = $usr->getFlds();
        $host_name = $host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname'];
        $notify_msg = $host_name . ' ' . Info::t_lang('REPORTED_A_REVIEW_AS_INAPPROPRIATE_FOR_ACTIVITY_:_') . $activity_data[Activity::DB_TBL_PREFIX . 'name'];
        $notify->notify(0, 0, FatUtility::generateFullUrl('admin', 'reviews', array(), '/'), $notify_msg);
        $vars = array(
            '{host_name}' => $host_name,
            '{activity_name}' => $activity_data[Activity::DB_TBL_PREFIX . 'name']
        );
        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 15, $vars);
        FatUtility::dieJsonSuccess(Info::t_lang('YOUR_REQUEST_SUBMITTED'));
    }

    function markAsAbuseForm() {
        if ($this->user_type != 1) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $post = FatApp::getPostedData();
        $review_id = isset($post['review_id']) ? FatUtility::int($post['review_id']) : 0;
        if ($review_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $frm = $this->getMarkAsAbuseForm();
        $frm->fill(array('review_id' => $review_id));
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'review/_partial/abuse-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getMarkAsAbuseForm() {
        if ($this->user_type != 1) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $frm = new Form('markAsAbuseFrm');
        $frm->addHiddenField('', 'review_id');
        $fld = $frm->addTextArea(Info::t_lang('COMMENT'), 'comment');
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }

    function replyToReview() {
        if ($this->user_type != 1) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $post = FatApp::getPostedData();
        $review_id = isset($post['review_id']) ? FatUtility::int($post['review_id']) : 0;
        if ($review_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $frm = $this->getReplyToReviewForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($this->getValidationErrors()));
        }
        
        $reviews = new Reviews($review_id);
        $reviews->loadFromDb();
        $review_data = $reviews->getFlds();
        if (empty($review_data)) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        
        $reviewMessage = new ReviewMessage(); 
        $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'review_id'] = $review_id;
        $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'message'] = $post['message'];
        $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'added_on'] = Info::currentDatetime();
        $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'user_type'] = ReviewMessage::REVIEWMSG_USERTYPE_HOST;
        $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'user_id'] = $this->userId;
        $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'active'] = 1;
        
        $reviewMessage->assignValues($reviewMessageData);
        if (!$reviewMessage->save()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG.PlEASE_TRY_AGAIN'));
        }
        
        
        /* $notify = new Notification();
        $usr = new User($this->userId);
        $usr->loadFromDb();
        $host_data = $usr->getFlds();
        $host_name = $host_data[User::DB_TBL_PREFIX . 'firstname'] . ' ' . $host_data[User::DB_TBL_PREFIX . 'lastname'];
        $notify_msg = $host_name . ' ' . Info::t_lang('REPORTED_A_REVIEW_AS_INAPPROPRIATE_FOR_ACTIVITY_:_') . $activity_data[Activity::DB_TBL_PREFIX . 'name'];
        $notify->notify(0, 0, FatUtility::generateFullUrl('admin', 'reviews', array(), '/'), $notify_msg);
        $vars = array(
            '{host_name}' => $host_name,
            '{activity_name}' => $activity_data[Activity::DB_TBL_PREFIX . 'name']
        );
        Email::sendMail(FatApp::getConfig('conf_admin_email_id'), 15, $vars); */
        FatUtility::dieJsonSuccess(Info::t_lang('REPLY_POSTED_SUCCESSFULLY'));
    }

    function replyToReviewForm() {
        if ($this->user_type != 1) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $post = FatApp::getPostedData();
        $review_id = isset($post['review_id']) ? FatUtility::int($post['review_id']) : 0;
        if ($review_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $frm = $this->getReplyToReviewForm();
        $frm->fill(array('review_id' => $review_id));
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'review/_partial/reply-to-review-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getReplyToReviewForm() {
        if ($this->user_type != 1) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST'));
        }
        $frm = new Form('replyToReviewFrm');
        $frm->addHiddenField('', 'review_id');
        $fld = $frm->addTextArea(Info::t_lang('COMMENT'), 'message');
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SUBMIT'));
        return $frm;
    }
}
