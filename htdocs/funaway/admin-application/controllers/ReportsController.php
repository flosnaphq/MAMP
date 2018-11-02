<?php

class ReportsController extends AdminBaseController {

    private $canView;
    private $canEdit;
    private $admin_id;

    public function __construct($action) {
        $ajaxCallArray = array('listing', 'form', 'setup');
        if (!FatUtility::isAjaxCall() && in_array($action, $ajaxCallArray)) {
            die("Invalid Action");
        }
        $this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
        $this->canView = AdminPrivilege::canViewReport($this->admin_id);
        $this->canEdit = AdminPrivilege::canEditReport($this->admin_id);
        if (!$this->canView) {
            if (FatUtility::isAjaxCall()) {
                FatUtility::dieJsonError('Unauthorized Access!');
            }
            FatUtility::dieWithError('Unauthorized Access!');
        }
        parent::__construct($action);
        $this->set("canView", $this->canView);
        $this->set("canEdit", $this->canEdit);
    }

    public function index() {
        $this->set('search', $this->getSearchForm());
        $brcmb = new Breadcrumb();
        $brcmb->add("Report");
        $this->set('breadcrumb', $brcmb->output());
        $this->_template->render();
    }

    public function listing() {
        $rpt = new Reports();
        $post = FatApp::getPostedData();
        $frm = $this->getSearchForm();
        $post = $frm->getFormDataFromArray($post);
        if ($post == false) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $report_type = $post['report_type'];
        if ($report_type == 1) {
            $report_heading = Info::t_lang('DATE');
        } elseif ($report_type == 2) {
            $report_heading = Info::t_lang('MONTH');
        } elseif ($report_type == 3) {
            $report_heading = Info::t_lang('ACTIVITIY');
        } elseif ($report_type == 4) {
            $report_heading = Info::t_lang('HOST');
        }
        $start_date = $post['start_date'];
        $activity_id = $post['activity_id'];
        $end_date = $post['end_date'];
        $host_id = FatUtility::int($post['host_id']);
        $start_date = $start_date . ' 00:00:00';
        $end_date = $end_date . ' 23:59:59';
        $records = $rpt->getReport($start_date, $end_date, $host_id, $report_type, $activity_id);
        $this->set('arr_listing', $records);
        $this->set('report_heading', $report_heading);
        $htm = $this->_template->render(false, false, "reports/_partial/listing.php", true, true);
        FatUtility::dieJsonSuccess($htm);
    }

    private function getSearchForm() {
        $frm = new Form('searchFrm');
        $act = new Activity();

       
        $hosts = Users::getHostUsers();
        $hosts[-1] = Info::t_lang('HOST');
		$frm->addHiddenField('', 'host_id',0);
		$frm->addTextBox(Info::t_lang('HOST'),'host_name');
		
        //$frm->addSelectBox(Info::t_lang('HOST'), 'host_id', $hosts, -1, array('class' => 'search-input','data-href'=>FatUtility::generateUrl('activities','getHostActivities'),'onClick'=>'loadDependValues(this,"activitiesId")'), '');
        
       $activities[''] = "Select A Host First";
        $frm->addSelectBox(Info::t_lang('Activity'), 'activity_id', $activities, 0, array('class' => 'search-input','id'=>'activitiesId'), '');
        $fld = $frm->addDateField(Info::t_lang('START_DATE'), 'start_date', '', array('readonly' => 'readonly', 'class' => 'search-input'));
        $fld->requirements()->setRequired();
        $fld = $frm->addDateField(Info::t_lang('END_DATE'), 'end_date', '', array('readonly' => 'readonly', 'class' => 'search-input'));
        $fld->requirements()->setRequired();
        $report = Reports::getReportTypes();
        $report[4] = 'Host Wise';
        $frm->addSelectBox(Info::t_lang('REPORT_TYPE'), 'report_type', $report, 1, array('class' => 'search-input'), '');
        $frm->addSubmitButton('', 'submit_btn', Info::t_lang('SEARCH'));
        return $frm;
    }

}
