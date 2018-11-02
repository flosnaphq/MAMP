<?php

require_once CONF_UTILITY_PATH . "PaginateTrait.php";

class UserRequestController extends AdminBaseController {

    use PaginateTrait;

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {

        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewUserRequests($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditUserRequests($this->admin_id);
        if (!$this->canView) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("action", $action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    /*
     *  List and Search Functionality
     */

    public function index() {

        $brcmb = new Breadcrumb();
        $brcmb->add("User Requests");
        $this->set('breadcrumb', $brcmb->output());
        $this->set('search', $this->getSearchForm());
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $status = Info::getUserRequestConfirmStatus();
        $status['-1'] = 'Does Not Matter';
        $hosts = Users::getHostUsers();
        $hosts['0'] = 'Does Not Matter';

        $frm->addDateField('Start Date', 'start_date', '', array('class' => 'search-input'));
        $frm->addDateField('End Date', 'end_date', '', array('class' => 'search-input'));

        $frm->addSelectBox('Host', 'ucrequest_user_id', $hosts, '0', array('class' => 'search-input'), '');

        $frm->addSelectBox('Status', 'ucrequest_status', $status, '-1', array('class' => 'search-input'), '');
        $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function setPaginateSettings() {
        $this->pageSize = self::PAGESIZE;
    }

    public function getSearchObject($page) {
        $search = UserRequest::getSearchObject();
        $search->addMultipleFields(array(
            'CONCAT_WS(" ",user_firstname,user_lastname) as username',
            'ucrequest_text',
            'ucrequest_date',
            'ucrequest_status',
            'ucrequest_id',
            'country_name',
        ));
        $search->setPageSize($this->pageSize);
        $search->setPageNumber($page);
        $search->addOrder('ucrequest_id', 'Desc');
        return $search;
    }

    public function addFilters(&$srch, $post) {
        
        if (!empty($post['start_date'])) {
            $srch->addCondition('ucrequest_date', '>=', $post['start_date']);
        }
        if (!empty($post['end_date'])) {
            $srch->addCondition('ucrequest_date', '<=', $post['end_date']);
        }
        if (isset($post['ucrequest_status']) && $post['ucrequest_status'] > -1 && $post['ucrequest_status'] != '') {
            $srch->addCondition('ucrequest_status', '=', $post['ucrequest_status']);
        }
        if (isset($post['ucrequest_user_id']) && $post['ucrequest_user_id'] > 0) {
            $srch->addCondition('ucrequest_user_id', '=', $post['ucrequest_user_id']);
        }
    }

    public function listFields() {

        $fields = array(
            'username' => 'User Name',
            'ucrequest_text' => 'Request City',
            'country_name' => 'Country Name',
            'ucrequest_date' => 'Requested On',
            'ucrequest_status' => 'Requested Status',
        );
        return $fields;
    }

    public function getTableRow(&$tr, $arr_flds, $row) {
        foreach ($arr_flds as $key => $val) {

            $td = $tr->appendElement('td');
            switch ($key) {
                case "ucrequest_date";
                    $td->appendElement('plaintext', array(), FatDate::format($row[$key], true), true);
                    break;

                case "ucrequest_status":
                     $confirmed = Info::getUserRequestConfirmStatus();
                    if ($this->canEdit && $row[$key] == 0) {
                       
                     
                        $select = $td->appendElement('select', array('name' => 'active', 'onchange' => "changeStatus('Do You Want To Change ?', '" . $row['ucrequest_id'] . "', this.value)"), '');
                        foreach ($confirmed as $status => $status_name) {
                            $ar = array('value' => $status);
                            if ($row[$key] == $status) {
                                $ar['selected'] = 'selected';
                            }
                            $select->appendElement('option', $ar, $status_name);
                        }
                    } else {
                        $td->appendElement('plaintext', array(), $confirmed[$row[$key]]);
                    }
                    break;
                default:
                    $td->appendElement('plaintext', array(), $row[$key], true);
            }
        }
    }

    public function changeStatus() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();

        $request_id = isset($post['request_id']) ? FatUtility::int($post['request_id']) : 0;

        if (!($request_id > 0)) {
            FatUtility::dieJsonError('Invalid Request!');
        }
        $data['ucrequest_status'] = isset($post['status']) ? FatUtility::int($post['status']) : 0;

        $request = new UserRequest($request_id);
        $request->assignValues($data);
        if (!$request->save()) {
            FatUtility::dieJsonError($request->getError());
        }
        
       
        
        FatUtility::dieJsonSuccess("Status Updated");
    }

}
