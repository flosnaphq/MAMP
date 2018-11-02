<?php
class HostReportsController extends UserController {
	
	public $reportTypes = array();
	
	public function __construct($action){
		
		parent::__construct($action);
		$this->reportTypes = Reports::getReportTypes();
		$this->set('action','report');
		$this->set("class","is--dashboard");
	}
	
	function index(){
		$brcmb = new Breadcrumb();
		$brcmb->add("Account");
		$brcmb->add(Info::t_lang("REPORTS"));
		$this->set('breadcrumb',$brcmb->output());
		$rpt = new Reports();
		$usr = new User();
		$user = $usr->getUserByUserId($this->userId);
		$last_login = $user[User::DB_TBL_PREFIX.'last_login'];
		$current_time = Info::currentDatetime();
		$current_date = Info::currentDate();
		$new_records = $rpt->getRecordCount($last_login, $current_time, $this->userId);
		$today_records = $rpt->getRecordCount($current_date.' 00:00:00', $current_date.' 23:59:59', $this->userId);
		
		$timestamp = strtotime($current_time);
		$start_date = date('Y-m-d',$timestamp-(60*60*24*7));
		$end_date = date('Y-m-d',$timestamp);
		$last_7_records = $rpt->getRecordCount($start_date.' 00:00:00', $end_date.' 23:59:59', $this->userId);
		$frm = $this->getSearchForm();
		 
		$this->set('frm', $frm);
		$this->set('last_login', $last_login);
		$this->set('last_7_records', $last_7_records);
		$this->set('new_records', $new_records);
		$this->set('today_records', $today_records);
		$this->_template->render();
	}
	
	function reportsListing(){
		$rpt = new Reports();
		$post = FatApp::getPostedData();
		$frm = $this->getSearchForm();
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError(current($frm->getValidationErrors()));
		}
		$report_type = $post['report_type'];
		if($report_type == 1){
			$report_heading = Info::t_lang('DATE');
		}
		elseif($report_type == 2){
			$report_heading = Info::t_lang('MONTH');
		}
		elseif($report_type == 3){
			$report_heading = Info::t_lang('ACTIVITIY');
		}
		$start_date = $post['start_date'];
		$activity_id = $post['activity_id'];
		$end_date = $post['end_date'];
		$start_date = $start_date.' 00:00:00';
		$end_date = $end_date.' 23:59:59';
		$records = $rpt->getReport($start_date, $end_date, $this->userId, $report_type, $activity_id);
		$this->set('arr_listing',$records);
		$this->set('report_heading',$report_heading);
		$html = $this->_template->render(false,false,'host-reports/_partial/reports-listing.php',true,true);
		FatUtility::dieJsonSuccess($html);
	}
	
	private function getSearchForm(){
		$frm = new Form('searchFrm');
		$act = new Activity();
		$activities = $act->getActivitiesForForm($this->userId);
		$activities[0]=Info::t_lang('ACTIVITY');
		$frm->addSelectBox(Info::t_lang('Activity'),'activity_id',$activities,0,array(),'');
		$fld = $frm->addDateField(Info::t_lang('START_DATE'),'start_date','',array('readonly'=>'readonly'));
		$fld->requirements()->setRequired();
		$fld = $frm->addDateField(Info::t_lang('END_DATE'),'end_date','',array('readonly'=>'readonly'));
		$fld->requirements()->setRequired();
		$frm->addSelectBox(Info::t_lang('REPORT_TYPE'),'report_type',$this->reportTypes,1,array(),'');
		$frm->addSubmitButton('','submit_btn',Info::t_lang('SEARCH'));
		return $frm;
	}
}