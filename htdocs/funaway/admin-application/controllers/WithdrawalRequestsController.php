<?php

class WithdrawalRequestsController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {

        $ajaxCallArray = array('listing');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewWithdrawalRequest($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditWithdrawalRequest($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index() {
        $frm = $this->getSearchForm();
        $brcmb = new Breadcrumb();
        $brcmb->add("Withdrawal Requests");
        $this->set('breadcrumb', $brcmb->output());
        $this->set('search', $frm);
        $this->_template->render();
    }

    public function lists($page = 1) {
        $pagesize = static::PAGESIZE;
        $page = FatUtility::int($page);
        $search = WithdrawalRequests::getSearchObject();
        $search->joinTable(User::DB_TBL, 'inner Join', User::DB_TBL_PREFIX . 'id = ' . WithdrawalRequests::DB_TBL_PREFIX . 'user_id');
        //	$post = $form->getFormDataFromArray(FatApp::getPostedData());
        $post = FatApp::getPostedData();
        $wtran_user_type = @$post['wtran_user_type'];
        $wtran_user_type = FatUtility::int($wtran_user_type);
        if (!empty($post['keyword'])) {
            $con = $search->addCondition(User::DB_TBL_PREFIX . 'firstname', 'like', '%' . $post['keyword'] . '%');
            $con = $con->attachCondition(User::DB_TBL_PREFIX . 'email', 'like', '%' . $post['keyword'] . '%', 'or');
        }
        if (!empty($post['start_date'])) {
            $search->addDirectCondition('DATE(withdrawalrequest_datetime)>="' . $post['start_date'] . '"');
        }
        if (!empty($post['end_date'])) {
            $search->addDirectCondition('DATE(withdrawalrequest_datetime) <="' . $post['end_date'] . '"');
        }

        $search->setPageSize($pagesize);
        $search->setPageNumber($page);
        $search->addOrder(WithdrawalRequests::DB_TBL_PREFIX . 'id', 'desc');
        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "withdrawal-requests/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function view($request_id) {
        $request_id = FatUtility::int($request_id);
        if ($request_id <= 0) {
            FatUtility::dieWithError('Invalid Request!');
        }
        $search = WithdrawalRequests::getSearchObject();
        $search->joinTable(User::DB_TBL, 'inner Join', User::DB_TBL_PREFIX . 'id = ' . WithdrawalRequests::DB_TBL_PREFIX . 'user_id');
        $search->addCondition(WithdrawalRequests::DB_TBL_PREFIX . 'id', '=', $request_id);
        $rs = $search->getResultSet();

        $records = FatApp::getDb()->fetch($rs);
        $this->set('records', $records);

        $this->_template->render(false, false, "withdrawal-requests/_partial/view.php");
    }

    function form() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $request_id = FatUtility::int(@$post['request_id']);
        if ($request_id <= 0) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $req = new WithdrawalRequests($request_id);
        $req->loadFromDb();
        $flds = $req->getFlds();
        $frm = $this->getForm();
        $frm->fill($flds);
        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'withdrawal-requests/_partial/form.php', true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    function setup() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $request_id = @$post['withdrawalrequest_id'];
        $req = new WithdrawalRequests($request_id);
        $req->loadFromDb();
        $flds = $req->getFlds();
        $req->assignValues($post);
        if (!$req->save()) {
            FatUtility::dieJsonError('Something went wrong. Please try again.');
        }



        if ($flds[WithdrawalRequests::DB_TBL_PREFIX . 'status'] != $post[WithdrawalRequests::DB_TBL_PREFIX . 'status'] && $post[WithdrawalRequests::DB_TBL_PREFIX . 'status'] != 0) {
            $search = WithdrawalRequests::getSearchObject();
            $search->joinTable(User::DB_TBL, 'inner Join', User::DB_TBL_PREFIX . 'id = ' . WithdrawalRequests::DB_TBL_PREFIX . 'user_id');
            $search->addCondition(WithdrawalRequests::DB_TBL_PREFIX . 'id', '=', $request_id);
            $rs = $search->getResultSet();
            $records = FatApp::getDb()->fetch($rs);

            if($records['withdrawalrequest_status']==2)
            $userWallet = new Userwallet($records['withdrawalrequest_user_id'], 1);
            if (!$userWallet->addCreditToUser($records['withdrawalrequest_amount'], "WITHDRAWAL REQUEST CANCELLED")) {
                FatUtility::dieJsonError($userWallet->getError());
            }



            $replace_vars = array(
                '{username}' => $records['user_firstname'] . ' ' . $records['user_lastname'],
                '{amount}' => $records['withdrawalrequest_amount'],
                '{comment}' => nl2br($records['withdrawalrequest_comment']),
                '{admin_comment}' => nl2br($records['withdrawalrequest_admin_comment']),
                '{datetime}' => FatDate::format($records['withdrawalrequest_datetime'], true),
                '{status}' => Info::getWithdrawalRequestStatusByKey($post['withdrawalrequest_status']),
            );
            Email::sendMail($records['user_email'], 22, $replace_vars);
            $url = FatUtility::generateUrl('notification', '', array(), '/');
            $text = Info::t_lang('YOUR_WITHDRAWAL_REQUEST_HAS_BEEN_') . Info::getWithdrawalRequestStatusByKey($post['withdrawalrequest_status']);
            $notify = new Notification;
            $notify->notify($records['user_id'], 0, $url, $text);
        }

        FatUtility::dieJsonSuccess('Update Successfully!');
    }

    private function getForm() {
        $frm = new Form('requestFrm');
        $frm->addHiddenField('', 'withdrawalrequest_id');
        $fld = $frm->addTextArea('Comment', 'withdrawalrequest_admin_comment');
        $fld->requirements()->setRequired();
        $frm->addSelectBox('status', 'withdrawalrequest_status', Info::getWithdrawalRequestStatus(), 0, array(), '');
        $frm->addSubmitButton('', 'btn_submit', 'Submit');
        return $frm;
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch', array('class' => 'web_form', 'onsubmit' => 'search(this); return false;'));
        $frm->addTextBox('Name/Email', 'keyword', '', array('class' => 'search-input'));
        $frm->addDateField('Start Date', 'start_date', '', array('readonly' => 'readonly', 'class' => 'search-input'));
        $frm->addDateField('End Date', 'end_date', '', array('readonly' => 'readonly', 'class' => 'search-input'));
        $frm->addSubmitButton('&nbsp;', 'btn_submit', 'Search');
        return $frm;
    }
    

}
