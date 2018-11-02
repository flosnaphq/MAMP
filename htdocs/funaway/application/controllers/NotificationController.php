<?php

class NotificationController extends UserController {

    public function __construct($action) {
        parent::__construct($action);
        $this->set('controller', 'notification');
        $this->set("class", "is--dashboard");
    }

    public function index() {
        $brcmb = new Breadcrumb();
        $brcmb->add(Info::t_lang('MESSAGES'));
        $brcmb->add(Info::t_lang('NOTIFICATIONS'));
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    public function listing() {
        $pagesize = static::PAGESIZE;
        $data = FatApp::getPostedData();
        $search = Notification::getSearchObject();
        //	$search->joinTable("tbl_users", "Inner JOIN", "user_id = {$this->userId} and notification_user_id = user_id" );
        $search->addCondition('notification_user_id', '=', $this->userId);
        $search->addOrder("notification_date", "desc");
        $page = $data['page'];
        $page = FatUtility::int($page);
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);
        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs, 'notification_id');
        $notification_ids = array_keys($records);

        if (!empty($notification_ids)) {
            Notification::markAsRead($notification_ids);
        }

        $this->set("notifications", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "notification/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function delete() {
        $post = FatApp::getPostedData();
        $notification_id = @$post['notification_id'];
        $notification_id = FatUtility::int($notification_id);
        if ($notification_id <= 0) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
        }
        $notify = new Notification($notification_id);
        if (!$notify->loadFromDb()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
        }
        $notity_data = $notify->getFlds();
        if ($notity_data['notification_user_id'] != $this->userId) {
            FatUtility::dieJsonError(Info::t_lang('INVALID_REQUEST!'));
        }
        if (!$notify->deleteRecord()) {
            FatUtility::dieJsonError(Info::t_lang('SOMETHING_WENT_WRONG._PLEASE_TRY_AGAIN'));
        }
        FatUtility::dieJsonSuccess(Info::t_lang('NOTIFICATION_DELETED'));
    }

    function fatActionCatchAll() {
        FatUtility::exitWithErrorCode(404);
    }

}
