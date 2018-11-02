<?php

require_once CONF_UTILITY_PATH . "PaginateTrait.php";

class ReviewsController extends AdminBaseController {

    use PaginateTrait;

    protected $canView;
    protected $canEdit;
    protected $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array("cuisinesListing", "changeCuisinesOrder", 'cuisinesAction', 'cuisinesForm');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewReview($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditReview($this->admin_id);

        if (!$this->canView) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);

        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
        $this->setPaginateSettings();
    }

    function viewReview($review_id) {
        $src = Reviews::getSearchObject(true);
        $src->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'user_id');
        $src->joinTable(Activity::DB_TBL, 'left join', Activity::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'type_id');
        $src->joinTable(AbuseReport::DB_TBL, 'left join', AbuseReport::DB_TBL_PREFIX . 'record_id = ' . Reviews::DB_TBL_PREFIX . 'id and ' . AbuseReport::DB_TBL_PREFIX . 'record_type = 0');
        $src->addCondition(Reviews::DB_TBL_PREFIX . 'id', '=', $review_id);
        $src->addMultipleFields(array(
            Reviews::DB_TBL . '.*',
            AbuseReport::DB_TBL . '.*',
            Activity::DB_TBL_PREFIX . 'name',
            'concat(' . User::DB_TBL_PREFIX . 'firstname, " ", ' . User::DB_TBL_PREFIX . 'lastname) as user_name',
            'count('.ReviewMessage::DB_TBL_PREFIX.'id) as numMessages',
            )
        );
        $rs = $src->getResultSet();
        //echo $src->getError();
        $records = FatApp::getDb()->fetch($rs);

        $this->set('records', $records);
        $this->_template->render(false, false, 'reviews/_partial/view-review.php');
    }

    private function getSearchForm() {
        $act = new Activity();
        $activities = $act->getActivitiesForForm();
        $activities['-1'] = 'Does not Matter';
        $status = Info::getAbuseReportStatus();
        $status['-1'] = 'Does not Matter';
        $frm = new Form('frmReviewSearch', array('class' => 'web_form', 'onsubmit' => 'search(this,1); return false;'));
        $frm->addTextBox('Keyword', 'keyword', '', array('class' => 'search-input'));
        $frm->addSelectBox('Status', 'review_active', $status, '-1', array('class' => 'search-input'), '');
        $frm->addSelectBox('Activity', 'activity_id', $activities, '-1', array('class' => 'search-input'), '');
        $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
        return $frm;
    }

    function reviewForm() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $review_id = isset($post['review_id']) ? $post['review_id'] : 0;
        $frm = $this->getReviewForm();
        $fields = array();
        if ($review_id > 0) {
            $reviewObj = new Reviews($review_id);
            $reviewObj->loadFromDb();
            $fields = $reviewObj->getFlds();

            if ($fields[Reviews::DB_TBL_PREFIX . 'user_id'] > 0) {
                $frm->removeField($frm->getField('review_user_name'));
                //$frm->removeField($frm->getField('show_image'));
                //$frm->removeField($frm->getField('image'));
            } else {
                //$frm->getField('show_image')->value='<img src="'.FatUtility::generateUrl('image', 'user', array($review_id, 100, 100,1),'/').'">';
            }
        }

        $frm->fill($fields);

        $reviewObj = new Reviews($review_id);
        $reviewObj->loadFromDb();

        $frm->fill($reviewObj->getFlds());
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'reviews/_partial/review-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function reviewAction() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = $this->getReviewForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($post == false) {
            FatUtility::dieJsonError('Something went wrong');
        }

        $review_id = @$post['review_id'];
        if ($review_id <= 0) {
            $post[Reviews::DB_TBL_PREFIX . 'date'] = Info::currentDatetime();
        }
        unset($post['review_id']);
        $reviewObj = new Reviews($review_id);
        $reviewObj->assignValues($post);
        if (!$reviewObj->save()) {
            FatUtility::dieJsonError('Something went wrong');
        }
        FatUtility::dieJsonSuccess('Record Updated');
    }

    private function getReviewForm() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = new Form('reviewForm');
        $frm->addHiddenField('', 'review_id');
        $frm->addHiddenField('', 'review_type');
        $frm->addHiddenField('', 'review_user_id');
        //$image = $frm->addHtml('User Image','show_image','<img src="'.FatUtility::generateUrl('image','user',array(0,100,100), '/').'">');
        //	$fileUpload = $frm->addFileUpload('','image');
        //	$image->attachField($fileUpload);
        $frm->addTextBox('User Name', 'review_user_name');
        $frm->AddTextArea('Content', 'review_content')->requirements()->setRequired();
        $review_rating = $frm->addSelectBox('Rating', 'review_rating', Info::getRatingArray(), 0.5, array(), '');
        $act = new Activity();
        $frm->addSelectBox('Activity', 'review_type_id', $act->getActivitiesForForm(), '', array(), '');

        $frm->addSelectBox('Status', 'review_active', Info::getReviewStatus());
        $frm->addSubmitButton('', 'submit_btn', 'ADD / UPDATE');
        return $frm;
    }

    function abuseform() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $abreport_id = isset($post['abreport_id']) ? FatUtility::int($post['abreport_id']) : 0;
        if ($abreport_id <= 0) {
            FatUtility::dieJsonError('Invalid Request');
        }
        $abReport = new AbuseReport($abreport_id);
        $frm = $this->getAbuseReportForm();
        $abReport->loadFromDb();
        $report = $abReport->getFlds();
        $frm->fill($report);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'reviews/_partial/abuse-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function abuseAction() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = $this->getAbuseReportForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if ($post == false) {
            FatUtility::dieJsonError('Something Went Wrong');
        }
        $abReport = new AbuseReport($post['abreport_id']);
        $abReport->assignValues($post);
        if (!$abReport->save()) {
            FatUtility::dieJsonError('Something Went Wrong. Please try again');
        }
        if ($post['abreport_record_type'] == 0) {
            if ($post['abreport_taken_care'] == 1) {
                $review = new Reviews($post['abreport_record_id']);
                $review->assignValues(array(Reviews::DB_TBL_PREFIX . 'active' => 2));
                if (!$review->save()) {
                    FatUtility::dieJsonError('Something Went Wrong. Please try again');
                }
            } elseif ($post['abreport_taken_care'] == 2) {
                $review = new Reviews($post['abreport_record_id']);
                $review->assignValues(array(Reviews::DB_TBL_PREFIX . 'active' => 1));
                if (!$review->save()) {
                    FatUtility::dieJsonError('Something Went Wrong. Please try again');
                }
            }
        }
        FatUtility::dieJsonSuccess('Record Updated');
    }

    private function getAbuseReportForm() {
        $frm = new Form('reviewForm');
        $frm->addHiddenField('', 'abreport_id');
        $frm->addHiddenField('', 'abreport_record_id');
        $frm->addHiddenField('', 'abreport_record_type');
        $frm->addHiddenField('', 'abreport_user_id');
        $frm->AddTextArea('User Comment', 'abreport_user_comment', '', array('disabled' => 'disabled'));
        $frm->AddTextArea('Comment', 'abreport_comments')->requirements()->setRequired();
        $frm->addSelectBox('Status', 'abreport_taken_care', Info::getAbuseReportStatus());
        $frm->addSubmitButton('', 'submit_btn', 'UPDATE');
        return $frm;
    }

    /*
     *  List and Search Functinality
     */

  public function index() {

        $brcmb = new Breadcrumb();
        $brcmb->add("Reviews Management");
        $frm = $this->getSearchForm();
        $this->set('breadcrumb', $brcmb->output());
        $this->set('search', $frm);
        $this->_template->render();
    }

    public function setPaginateSettings() {
        $this->pageSize = self::PAGESIZE;
        $this->paginateSorting = true;
    }



    public function getSearchObject($page,$joinReviewMessages= false) {
        $src = Reviews::getSearchObject($joinReviewMessages);
        $src->joinTable(User::DB_TBL, 'left join', User::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'user_id');
        $src->joinTable(Activity::DB_TBL, 'left join', Activity::DB_TBL_PREFIX . 'id = ' . Reviews::DB_TBL_PREFIX . 'type_id');
        $src->joinTable(AbuseReport::DB_TBL, 'left join', AbuseReport::DB_TBL_PREFIX . 'record_id = ' . Reviews::DB_TBL_PREFIX . 'id and ' . AbuseReport::DB_TBL_PREFIX . 'record_type = 0');
        $src->addMultipleFields(array(
            Reviews::DB_TBL . '.*',
            AbuseReport::DB_TBL . '.*',
            Activity::DB_TBL_PREFIX . 'name',
            'concat(' . User::DB_TBL_PREFIX . 'firstname, " ", ' . User::DB_TBL_PREFIX . 'lastname) as user_name',
                )
        );

        $src->setPageSize($this->pageSize);
        $src->setPageNumber($page);
        return $src;
    }

    public function addFilters(&$srch, $data) {
        if (!empty($data['activity_id']) && $data['activity_id'] != -1 && $data['activity_id'] != '') {
            $srch->addCondition(Reviews::DB_TBL_PREFIX . 'type_id', '=', $data['activity_id']);
        }

        if (!empty($data['keyword'])) {
            $srch->addCondition(Reviews::DB_TBL_PREFIX . 'content', 'LIKE', '%' . $data['keyword'] . '%');
        }
        if (isset($data['review_active']) && $data['review_active'] != -1 && $data['review_active'] != '') {
            $srch->addCondition(Reviews::DB_TBL_PREFIX . 'active', '=', $data['review_active']);
        }
        if (isset($data['sort'])) {
            list($sortKey, $sortOrder) = explode(":", $data['sort']);
            $srch->addOrder($sortKey, $sortOrder);
        } else {
            $srch->addOrder(Reviews::DB_TBL_PREFIX . 'date', 'desc');
        }
    }

    public function listFields() {

        $fields = array(
            'user_name' => 'Review By',
            'activity_name' => 'Activity Name',
            'review_rating' => 'Rating',
            'review_date' => 'Date',
            'reported' => 'Reported Inappropriate',
            'abreport_taken_care' => 'Inappropriate Status',
            'review_active' => 'Status',
            'action' => 'Action',
        );
        return $fields;
    }

    public function sortFields() {

        $this->sortFields = array(
            'review_date',
            'review_rating',
            'activity_name',
        );
        return true;
    }

    public function getTableRow(&$tr, $arr_flds, $row) {
        foreach ($arr_flds as $key => $val) {
            $td = $tr->appendElement('td');
            switch ($key) {
                case 'listserial':
                    $td->appendElement('plaintext', array(), $sr_no);
                    break;
                case 'user_name':
                    if ($row['review_user_id']) {
                        $td->appendElement('plaintext', array(), $row[$key]);
                    } else {
                        $td->appendElement('plaintext', array(), $row['review_user_name'] . "*");
                    }
                    break;
                case 'review_active':
                    $td->appendElement('plaintext', array(), Info::getReviewStatusByKey($row[$key]));
                    break;
                case 'abreport_taken_care':
                    $st = Info::getAbuseReportStatusByKey($row[$key]);
                    if ($row[$key] === null) {
                        $st = '--';
                    }
                    $td->appendElement('plaintext', array(), $st);
                    break;
                case 'review_entity_type':
                    $td->appendElement('plaintext', array(), Info::getReviewEntityTypeByKey($row[$key]));
                    break;
                case 'review_date':
                    $td->appendElement('plaintext', array(), FatDate::format($row['review_date'], true));
                    break;
                case 'reported':
                    $td->appendElement('plaintext', array(), !empty($row['abreport_id']) ? 'Yes' : 'No');
                    break;
                case 'action':
                    $ul = $td->appendElement("ul", array("class" => "actions"));

                    if ($this->canView) {
                        $li = $ul->appendElement("li");
                        $li->appendElement('a', array('href' => 'Javascript:popupView("' . FatUtility::generateUrl('reviews', 'viewReview', array('review_id' => $row['review_id'])) . '");', 'class' => 'button small green', 'title' => 'View detail'), '<i class="ion-eye icon"></i>', true);
                    }

                    if ($this->canEdit) {
                        $li = $ul->appendElement("li");
                        $li->appendElement('a', array('href' => "javascript:;", 'class' => 'button small green', 'title' => 'Edit', "onclick" => "getReviewForm(" . $row['review_id'] . ")"), '<i class="ion-edit icon"></i>', true);
                        $userTypesStr = $row['replyUserTypes'] === null?'':$row['replyUserTypes'];
                        $userTypes = explode(',',$userTypesStr);
                        
                        if(in_array((string)ReviewMessage::REVIEWMSG_USERTYPE_ADMIN, $userTypes,true) === false){
                            $li = $ul->appendElement("li");
                            $li->appendElement('a', array('href' => "javascript:;", 'class' => 'button small green', 'title' => 'Reply', "onclick" => "replyToReview(" . $row['review_id'] . ")"), '<i class="ion-reply icon"></i>', true);
                        }
                    }
                    if ($this->canEdit && !empty($row['abreport_id'])) {
                        $li = $ul->appendElement("li");
                        $li->appendElement('a', array('href' => "javascript:;", 'class' => 'button small green', 'title' => 'Edit Abuse Report', "onclick" => "getAbuseForm(" . $row['abreport_id'] . ")"), '<i class="ion-android-notifications-none icon"></i>', true);
                    }
                    break;
                default:
                    $td->appendElement('plaintext', array(), $row[$key], true);
                    break;
            }
        }
    }
    function viewReviewMessages($review_id) {
        $reviewMessageSrch = ReviewMessage::getSearchObject(false,true,true);
        $reviewMessageSrch->addCondition(ReviewMessage::DB_TBL_PREFIX . 'review_id', '=', $review_id );
        $reviewMessageSrch->addCondition(ReviewMessage::DB_TBL_PREFIX . 'active', '=', 1 );
        $reviewMessageSrch->addOrder(ReviewMessage::DB_TBL_PREFIX . 'added_on', 'desc');
        $reviewMessageSrch->addMultipleFields(array(
            ReviewMessage::DB_TBL_PREFIX . 'id',
            ReviewMessage::DB_TBL_PREFIX . 'review_id',
            ReviewMessage::DB_TBL_PREFIX . 'added_on',
            ReviewMessage::DB_TBL_PREFIX . 'message',
            ReviewMessage::DB_TBL_PREFIX . 'user_type',
            ReviewMessage::DB_TBL_PREFIX . 'user_id',
            Admin::DB_TBL_PREFIX . 'id',
            Admin::DB_TBL_PREFIX . 'name',
            User::DB_TBL_PREFIX . 'id',
            'concat('.User::DB_TBL_PREFIX . 'firstname,'.User::DB_TBL_PREFIX . 'lastname) user_full_name',
            )
        );
        $rs = $reviewMessageSrch->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);

        $this->set('records', $records);
        $this->_template->render(false, false, 'reviews/_partial/view-review-messages.php');
    }
    
    function replyToReview() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $review_id = isset($post['reviewmsg_review_id']) ? FatUtility::int($post['reviewmsg_review_id']) : 0;
        if ($review_id <= 0) {
            FatUtility::dieJsonError('Invalid Request');
        }
        $reviewmsg_id = isset($post['reviewmsg_id']) ? FatUtility::int($post['reviewmsg_id']) : 0;
        $frm = $this->getReplyToReviewForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($this->getValidationErrors()));
        }
        
        $reviews = new Reviews($review_id);
        $reviews->loadFromDb();
        $review_data = $reviews->getFlds();
        if (empty($review_data)) {
            FatUtility::dieJsonError('Invalid Request');
        }
        
        $reviewMessage = new ReviewMessage($reviewmsg_id); 
        $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'review_id'] = $review_id;
        $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'message'] = $post['reviewmsg_message'];
        if ($reviewmsg_id <= 0) {
            $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'added_on'] = Info::currentDatetime();
            $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'user_type'] = ReviewMessage::REVIEWMSG_USERTYPE_ADMIN;
            $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'user_id'] = $this->admin_id;
            $reviewMessageData[ReviewMessage::DB_TBL_PREFIX.'active'] = 1;
        }
        
        $reviewMessage->assignValues($reviewMessageData);
        if (!$reviewMessage->save()) {
            FatUtility::dieJsonError('Something went wrong');
        }
        
        FatUtility::dieJsonSuccess('Reply Posted Successfully.');
    }

    function replyToReviewForm() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $review_id = isset($post['review_id']) ? FatUtility::int($post['review_id']) : 0;
        if ($review_id <= 0) {
            FatUtility::dieJsonError('Invalid Request');
        }
        $reviewmsg_id = isset($post['reviewmsg_id']) ? FatUtility::int($post['reviewmsg_id']) : 0;
        
        $frm = $this->getReplyToReviewForm();
        $fields = array('reviewmsg_review_id'=>$review_id);
        if ($reviewmsg_id > 0) {
            $reviewMsg = new ReviewMessage($reviewmsg_id);
            $reviewMsg->loadFromDb();
            $fields = $reviewMsg->getFlds();
        }

        $frm->fill($fields);
        
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'reviews/_partial/reply-to-review-form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getReplyToReviewForm() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $frm = new Form('replyToReviewFrm');

        $frm->addHiddenField('', 'reviewmsg_id');
        $frm->addHiddenField('', 'reviewmsg_review_id');
        $fld = $frm->addTextArea('Reply', 'reviewmsg_message');
        
        $fld->requirements()->setRequired();
        $frm->addSubmitButton('', 'submit_btn', 'Submit');
        return $frm;
    }
}
?>