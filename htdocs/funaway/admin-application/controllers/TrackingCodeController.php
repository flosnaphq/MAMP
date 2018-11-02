<?php
class TrackingCodeController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewCms($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditCms($this->admin_id);
		if(!$this->canView){
			if(FatUtility::isAjaxCall()){
				FatUtility::dieJsonError('Unauthorized Access!');
			}
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	
	public function index() {
		$this->set ( 'search', $this->getSearchForm ());
		$brcmb = new Breadcrumb();
		$brcmb->add("Tracking Code");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	
	private function getSearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Keyword', 'keyword','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function listing($page=1){
		$pagesize=static::PAGESIZE;
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$search = TrackingCode::getSearchObject(true,true);
		if(!empty($post['keyword'])){
			$con = $search->addCondition(TrackingCode::DB_TBL_PREFIX.'name','like','%'.$post['keyword'].'%');
			
		}
		$search->addOrder(TrackingCode::DB_TBL_PREFIX.'name');
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
		$htm = $this->_template->render(false,false,"tracking-code/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['tcode_id'] = empty($post['tcode_id'])?0:FatUtility::int($post['tcode_id']);
		$form = $this->getForm($post['tcode_id']);
		if(!empty($post['tcode_id'])){
			$fc = new TrackingCode ($post['tcode_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"tracking-code/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		
		$frm = new Form('action_form',array('id'=>'action_form'));
		
		$frm->addHiddenField("", 'tcode_id');
		
		$frm->addRequiredField('Name',TrackingCode::DB_TBL_PREFIX.'name')->developerTags['col']  = 6;
		
		$frm->addTextArea('Code',TrackingCode::DB_TBL_PREFIX.'code','');
		$frm->addSelectBox('Status',TrackingCode::DB_TBL_PREFIX.'status',Info::getStatus())->developerTags['col']  = 6;;
		$frm->addSubmitButton('', 'btn_submit','Add / Update',array())->htmlAfterField="<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
		return $frm;
	}
	
	public function setup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = $this->getForm ();
		$data = $frm->getFormDataFromArray ( FatApp::getPostedData () );
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}
		$tcode_id = FatApp::getPostedData('tcode_id', FatUtility::VAR_INT);
		unset($data['tcode_id']);
		$fc = new TrackingCode($tcode_id);
		$fc->assignValues($data);

		if (!$fc->save()) {
			FatUtility::dieWithError($fc->getError());
			
		}
		$this->set('msg', 'Set up Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	
	
	public function view($tcode_id){
		$tcode_id = FatUtility::int($tcode_id);
		if($tcode_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new TrackingCode($tcode_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$this->set('records',$fc->getFlds());
		$this->_template->render(false,false,"tracking-code/_partial/view.php");
	}
}
