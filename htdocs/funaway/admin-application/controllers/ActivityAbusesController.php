<?php

require_once CONF_UTILITY_PATH . "PaginateTrait.php";

class ActivityAbusesController extends AdminBaseController {

    use PaginateTrait;

    private $canView;
    private $canEdit;
    private $admin_id;
    private $canViewMessage;
    private $canEditMessage;
    private $canViewNotification;

    public function __construct($action) {
        $ajaxCallArray = array('listing', 'form', 'setup');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewActivityAbuses($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditActivityAbuses($this->admin_id);

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
        $this->setPaginateSettings();
    }

    public function setPaginateSettings() {
        $this->pageSize = self::PAGESIZE;
        $this->paginateSorting = true;
    }

    public function index() {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("Activity Abuses");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    private function getSearchForm() {
        $frm = new Form('frmSearch');
        $f1 = $frm->addTextBox('Activity/User Name', 'keyword', '', array('class' => 'search-input'));
        $field = $frm->addSubmitButton('', 'btn_submit', 'Search', array('class' => 'themebtn btn-default btn-sm'));
        return $frm;
    }

    public function getSearchObject($page) {
        $search = AbuseReport::getActivitySearchObject();
        $search->addMultipleFields(array(
            'CONCAT_WS(" ",user_firstname,user_lastname) as username',
            'activity_name',
            'abreport_posted_on',
            'abreport_taken_care',
            'abreport_id',
        ));
        $search->setPageSize($this->pageSize);
        $search->setPageNumber($page);
        return $search;
    }

    public function addFilters(&$srch, $post) {
        if (!empty($post['keyword'])) {
            $srch->addCondition('activity_name', 'like', '%' . $post['keyword'] . '%')
                    ->attachCondition('user_firstname', 'like', '%' . $post['keyword'] . '%', 'or')
                    ->attachCondition('user_lastname', 'like', '%' . $post['keyword'] . '%', 'or');
        }

        if (isset($post['sort'])) {
            list($sortKey, $sortOrder) = explode(":", $post['sort']);
            $srch->addOrder($sortKey, $sortOrder);
        } else {
            $srch->addOrder('abreport_posted_on', 'desc');
        }
    }

    public function listFields() {

        $fields = array(
            'listserial' => 'Sr.No.',
            'username' => 'Name',
            'activity_name' => 'Activity Name',
            'abreport_posted_on' => 'Added On',
            'abreport_taken_care' => 'Status',
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

                case 'abreport_taken_care':
                    $st = Info::getAbuseReportStatusByKey($row[$key]);
                    if ($row[$key] === null) {
                        $st = '--';
                    }
                    $td->appendElement('plaintext', array(), $st);
                    break;
                case 'abreport_posted_on':
                    $td->appendElement('plaintext', array(), FatDate::format($row[$key]));
                    break;
                case 'action':
                    $ul = $td->appendElement("ul", array("class" => "actions"));
                    if ($this->canEdit) {
                        $li = $ul->appendElement("li");
                        $li->appendElement('a', array('href' => "javascript:void(0)", "onclick" => "getAbuseForm(" . $row["abreport_id"] . ");", 'class' => 'button small green', 'title' => 'View Details'), '<i class="ion-edit icon"></i>', true);
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
            'abreport_posted_on',
            'activity_name',
        );
        return true;
    }

    function abuseform() {
        if (!$this->canEdit) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }
        $post = FatApp::getPostedData();
        $report_id = isset($post['abreport_id']) ? $post['abreport_id'] : 0;
        $frm = $this->getAbuseReportForm();
        $fields = array();

        $fields = AbuseReport::getActivityReportData($report_id);

        if (!$fields) {
            FatUtility::dieJsonError('Unauthorized Access!');
        }

        $frm->fill($fields);

        $this->set('frm', $frm);
        $htm = $this->_template->render(false, false, 'activity-abuses/_partial/report-form.php', true, true);
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
        if ($post['abreport_record_type'] == 1 && $post['abreport_taken_care'] > 0) {
            $activity = new Activity($post['abreport_record_id']);
            if ($post['abreport_taken_care'] == 1) {
                $activity->assignValues(array(Activity::DB_TBL_PREFIX . 'active' => 0));
            } elseif ($post['abreport_taken_care'] == 2) {
                $activity->assignValues(array(Activity::DB_TBL_PREFIX . 'active' => 1));
            }
       
            if (!$activity->save()) {
                FatUtility::dieJsonError('Something Went Wrong. Please try again');
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
		//setRequiredStarPosition(CONF_FORM_REQUIRED_STAR_POSITION)
        $frm->addSelectBox('Status', 'abreport_taken_care', Info::getAbuseReportStatus());
        $frm->addSubmitButton('', 'submit_btn', 'UPDATE');
        return $frm;
    }

}
