<?php
#error_reporting(E_ERROR);
class NewslettersController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
		$ajaxCallArray = array('adLists','requestLists','adAction','changeDisplayOrder');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewNewsletter($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditNewsletter($this->admin_id);
		if(!$this->canView){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	public function index(){
		
		$brcmb = new Breadcrumb();
		$brcmb->add("Newsletters");
		$search = $this->getSearchForm();
		$this->set('breadcrumb',$brcmb->output());
		$this->set("search",$search);	
		$this->_template->render();
	}
	
	public function adRequest(){
		$brcmb = new Breadcrumb();
		$brcmb->add("Advertisements",FatUtility::generateUrl('advertisement'));
		$brcmb->add("Requests");
		$search = $this->getAdRequestSearchForm();
		$this->set('breadcrumb',$brcmb->output());
		$this->set("search",$search);	
		$this->_template->render();
	}
	
	public function lists($page=1){
		$pagesize=50;
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$tblObj = new Newsletters();
		$search = $tblObj->getSearch();
		if(!empty($post['from_date'])){
			$search_con = $search->addCondition('newsletter_created','>=',$post['from_date']);
		}
		if(!empty($post['to_date'])){
			$search_con = $search->addCondition('newsletter_created','<=',$post['to_date']);
		}
		
		$page = empty($page) || $page <= 0?1:$page;
		$page = FatUtility::int($page);
		$search->setPageNumber($page);
		$search->setPageSize($pagesize);
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"newsletters/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getSearchForm(){
		$frm = new Form('search_newsletter',array('id'=>'search_newsletter'));
		$f1 = $frm->addDateField('Start Date', 'from_date','',array('class'=>'search-input', 'readonly'=>'readonly'));
		$f1 = $frm->addDateField('End Date', 'to_date','',array('class'=>'search-input', 'readonly'=>'readonly'));
		//$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		$field = $frm->addButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm','onclick'=>'search(this); return(false);'));
		return $frm;	
	}
	
	function generate(){
		if(!$this->canView){
			FatUtility::dieJsonError("Unauthorized Access");
		}
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$tblObj = new Newsletters();
		$search = $tblObj->getSearch();
		if(!empty($post['from_date'])){
			$search_con = $search->addCondition('newsletter_created','>=',$post['from_date']);
		}
		if(!empty($post['to_date'])){
			$search_con = $search->addCondition('newsletter_created','<=',$post['to_date']);
		}
		$search->addMultipleFields(array('newsletter_email_id','newsletter_created'));
		$rs = $search->getResultSet();
		
		$records = FatApp::getDb()->fetchAll($rs);
		
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=report.csv');
		$output = fopen('php://output', 'w');
		$csv_headers = array('EMAIL_ID', 'Date');
		
		fputcsv($output, $csv_headers);
		foreach ($records as $rec)
		fputcsv($output, $rec);
	}
	
}
