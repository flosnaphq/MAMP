<?php

require_once CONF_UTILITY_PATH . "PaginateTrait.php";

class TravelerController extends AdminBaseController {

    use PaginateTrait;

    private $canView;
    private $canEdit;
    private $admin_id;
    private $canViewMessage;
    private $canEditMessage;
    private $canViewNotification;
    private $canViewBankAccount;

    public function __construct($action) {
        $ajaxCallArray = array('listing', 'form', 'setup');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewTraveller($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditTraveller($this->admin_id);
        $this->canEditMessage = AdminPrivilege::canEditMessage($this->admin_id);
        $this->canViewMessage = AdminPrivilege::canViewMessage($this->admin_id);
        $this->canViewNotification = AdminPrivilege::canViewNotification($this->admin_id);
        $this->canViewBankAccount = AdminPrivilege::canViewBankAccount($this->admin_id);
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
        $this->set("canViewBankAccount", $this->canViewBankAccount);
        $this->set("canViewNotification", $this->canViewNotification);
    }

    public function setPaginateSettings() {
        $this->pageSize = self::PAGESIZE;
    }

    public function index() {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("Traveller");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    public function traveller($user_id) {
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Name/Email', 'keyword', '', array('class' => 'search-input'));
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function getSearchObject($page) {
        $search = Users::getSearchObject();
        $search->addCondition("user_type", "=", ApplicationConstants::USER_TRAVELER_TYPE);
        $search->addMultipleFields(array(
            'CONCAT_WS(" ",user_firstname,user_lastname) as username',
            'user_active',
            'user_email',
            'user_id',
            'user_phone',
            'user_regdate'
        ));
        $search->setPageSize($this->pageSize);
        $search->setPageNumber($page);
        return $search;
    }

    public function addFilters(&$srch, $post) {
        if (!empty($post['keyword'])) {
            $srch->addCondition('user_email', 'like', '%' . $post['keyword'] . '%')
                    ->attachCondition('user_firstname', 'like', '%' . $post['keyword'] . '%', 'or')
                    ->attachCondition('user_lastname', 'like', '%' . $post['keyword'] . '%', 'or');
        }

        if (isset($post['sort'])) {
            list($sortKey, $sortOrder) = explode(":", $post['sort']);
            $srch->addOrder($sortKey, $sortOrder);
        } else {
            $srch->addOrder('user_regdate', 'desc');
        }
        $srch->addOrder('user_regdate', 'desc');
    }

    public function listFields() {

        $fields = array(
            'listserial' => 'Sr.No.',
            'username' => 'Name',
            'user_email' => 'Email',
            'user_phone' => 'Phone',
            'user_regdate' => 'Added On',
            'user_active' => 'Status',
            'action' => 'Action',
        );
        return $fields;
    }

    public function getTableRow(&$tr, $arr_flds, $row, $counter) {
        foreach ($arr_flds as $key => $val) {
            $td = $tr->appendElement('td');
            switch ($key) {
                case 'listserial':
                    $td->appendElement('plaintext', array(), $counter);
                    break;

                case 'user_active':
                    $td->appendElement('plaintext', array(), Info::getStatusByKey($row[$key]));
                    break;
                case 'user_regdate':
                    $td->appendElement('plaintext', array(), FatDate::format($row[$key]));
                    break;
                case 'cms_display_order':
                    $td->appendElement('input', array('value' => $row[$key], 'onblur' => 'changeOrder("' . $row['user_id'] . '",this)', 'class' => 'text-display-order'));
                    break;
                case 'action':
                    $ul = $td->appendElement("ul", array("class" => "actions"));
                    if ($this->canEdit) {
                        $li = $ul->appendElement("li");
                        $li->appendElement('a', array('href' => FatUtility::generateUrl("traveler", "edit", array($row['user_id'])), 'class' => 'button small green', 'title' => 'View Details'), '<i class="ion-edit icon"></i>', true);
                    }

                    break;
                default:
                    $td->appendElement('plaintext', array(), $row[$key], true);
                    break;
            }
        }
    }

    public function sortFields() {

        $this->sortFields = array(
            'username',
            'user_email',
        );
        return true;
    }

    public function message($user_id) {
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 1) {
            FatApp::redirectUser(FatUtility::generateUrl('host', 'message', array($user_id)));
        }
        if (!$this->canViewMessage) {
            FatUtility::dieWithError('Unauthorized Access!');
        }
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    public function orders($user_id) {
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 1) {
            FatApp::redirectUser(FatUtility::generateUrl('host', 'orders', array($user_id)));
        }
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    public function edit($user_id) {
        $user_type = Users::getAttributesById($user_id, 'user_type');
        $user_firstname = Users::getAttributesById($user_id, 'user_firstname');
        if ($user_type == 1) {
            FatApp::redirectUser(FatUtility::generateUrl('host', 'edit', array($user_id)));
        }
        $brcmb = new Breadcrumb();
        $brcmb->add("Traveler", FatUtility::generateUrl('traveler'));
        $brcmb->add($user_firstname);
        $this->set('breadcrumb', $brcmb->output());
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    public function password($user_id) {
        $user_type = Users::getAttributesById($user_id, 'user_type');
        if ($user_type == 1) {
            FatApp::redirectUser(FatUtility::generateUrl('host', 'password', array($user_id)));
        }
        $this->set("user_id", $user_id);
        $this->_template->render();
    }

    public function bankAccount($user_id) {

        $this->set("user_id", $user_id);
        $this->_template->render();
    }

}
