<?php

class HostController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $canViewMessage;
    private $canEditMessage;
    private $canViewNotification;
    private $canViewBankAccount;
    private $canViewWallet;
    private $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array('listing', 'form', 'setup');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewHost($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditHost($this->admin_id);
        $this->canEditMessage = AdminPrivilege::canEditMessage($this->admin_id);
        $this->canViewMessage = AdminPrivilege::canViewMessage($this->admin_id);
        $this->canViewNotification = AdminPrivilege::canViewNotification($this->admin_id);
        $this->canViewBankAccount = AdminPrivilege::canViewBankAccount($this->admin_id);
        $this->canViewWallet = AdminPrivilege::canViewWallet($this->admin_id);
        if (!$this->canView) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError('Unauthorized Access!');
            }
            FatUtility::dieWithError('Unauthorized Access!');
        }


        parent::__construct($action);
        $this->set("action", $action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
        $this->set("canEditMessage", $this->canEditMessage);
        $this->set("canViewMessage", $this->canViewMessage);
        $this->set("canViewNotification", $this->canViewNotification);
        $this->set("canViewBankAccount", $this->canViewBankAccount);
        $this->set("canViewWallet", $this->canViewWallet);
    }

    public function index() {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("Hosts");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    public function host($user_id) {
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    public function listing($page = 1) {
        $pagesize = static::PAGESIZE;
        $searchForm = $this->getSearchForm();
        $data = FatApp::getPostedData();
        $post = $searchForm->getFormDataFromArray($data);
        $search = Users::getSearchObject();
         $search->addMultipleFields(array(
            'CONCAT_WS(" ",user_firstname,user_lastname) as username',
            'user_active',
            'user_email',
            'user_id',
            'user_phone',
            'user_regdate'
        ));
        if (!empty($post['keyword'])) {
            $con = $search->addCondition('user_email', 'like', '%' . $post['keyword'] . '%')
                    ->attachCondition('user_firstname', 'like', '%' . $post['keyword'] . '%', 'or')
                    ->attachCondition('user_lastname', 'like', '%' . $post['keyword'] . '%', 'or');
        }
        $search->addCondition("user_type", "=", ApplicationConstants::USER_HOST_TYPE);

        $search->addOrder('user_regdate', 'desc');
        $page = FatUtility::int($page);
        $page = (empty($page) || $page <= 0 ? 1 : $page);
        $search->setPageNumber($page);
        $search->setPageSize($pagesize);

        $rs = $search->getResultSet();
        $records = FatApp::getDb()->fetchAll($rs);
        $this->set("arr_listing", $records);
        $this->set('totalPage', $search->pages());
        $this->set('page', $page);
        $this->set('postedData', $post);
        $this->set('pageSize', $pagesize);
        $htm = $this->_template->render(false, false, "host/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Name/Email', 'keyword', '', array('class' => 'search-input'));
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function message($user_id) {
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 0) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'message', array($user_id)));
        }
        if (!$this->canViewMessage) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $frm = $this->getMessageSearchForm();
        $this->set("search", $frm);
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    private function getMessageSearchForm() {
        $frm = new Form('msgSearchFrm');
        $frm->addDateField(Info::t_lang('START_DATE'), 'start_date');
        $frm->addDateField(Info::t_lang('END_DATE'), 'end_date');
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SEARCH'));
        return $frm;
    }

    public function orders($user_id) {
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 0) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'orders', array($user_id)));
        }
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    public function edit($user_id) {

        $user_type = Users::getAttributesById($user_id, 'user_type');
        $user_firstname = Users::getAttributesById($user_id, 'user_firstname');
        if ($user_type == 0) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'edit', array($user_id)));
        }
        $brcmb = new Breadcrumb();
        $brcmb->add("Hosts", FatUtility::generateUrl('host'));
        $brcmb->add($user_firstname);
        $this->set('breadcrumb', $brcmb->output());
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    public function bankAccount($user_id) {
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 0) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'edit', array($user_id)));
        }
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    public function password($user_id) {
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 0) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler', 'password', array($user_id)));
        }
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    function transactions($user_id) {
        if (!$this->canViewNotification) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 0) {
            FatApp::redirectUser(FatUtility::generateUrl('traveler'));
        }
        $frm = $this->getHistorySearchForm();
        $this->set("wallet", Wallet::getWalletBalanceByUser($user_id));
        $this->set("user_id", $user_id);
        $this->set("search", $frm);
        $this->_template->render();
    }

    private function getHistorySearchForm() {
        $frm = new Form('historySearchFrm');
        $frm->addDateField(Info::t_lang('START_DATE'), 'start_date');
        $frm->addDateField(Info::t_lang('END_DATE'), 'end_date');
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SEARCH'));
        return $frm;
    }

    function transactionsLists() {
        $post = FatApp::getPostedData();
        $user_id = @$post['user_id'];
    }

}
