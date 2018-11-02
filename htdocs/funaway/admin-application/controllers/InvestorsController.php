<?php
class InvestorsController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewInvestor($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditInvestor($this->admin_id);
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
		$brcmb->add("Investors");
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
		$search = Investors::getSearchObject();
		if(!empty($post['keyword'])){
			$con = $search->addCondition('investor_name','like','%'.$post['keyword'].'%');
		}
		$search->addOrder('investor_name');
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
		$htm = $this->_template->render(false,false,"investors/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['investor_id'] = empty($post['investor_id'])?0:FatUtility::int($post['investor_id']);
		$form = $this->getForm($post['investor_id']);
		if(!empty($post['investor_id'])){
			$fc = new Investors ($post['investor_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"investors/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm($record_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($record_id >0){
			$action='Update';
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		
		$frm->addHiddenField("", 'investor_id',$record_id);
		$frm->addHiddenField("", 'fIsAjax',1);
	
		$frm->addHtml('','show_image',"<img src='".FatUtility::generateUrl('image','investor',array($record_id, 50,50,rand(100,1000)),CONF_WEBROOT_URL)."'>")->developerTags['col']  = 6;
		$frm->addFileUpload('Logo','image')->developerTags['col']  = 6;
		$frm->addRequiredField('Name','investor_name')->developerTags['col']  = 6;
		$frm->addRequiredField('Link','investor_link')->developerTags['col']  = 6;
		
		$frm->addFloatField('Display Order','investor_display_order',0)->developerTags['col']  = 6;
		$frm->addSelectBox('Status','investor_active',Info::getStatus())->developerTags['col']  = 6;
		
		$frm->addSubmitButton('', 'btn_submit',$action,array())->htmlAfterField="<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
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
		if (!empty($_FILES['image']['tmp_name']) && !is_uploaded_file($_FILES['image']['tmp_name'])){
			FatUtility::dieJsonError('Image couldn\'t not uploaded.');
		}
		$investor_id = FatApp::getPostedData('investor_id', FatUtility::VAR_INT);
		unset($data['investor_id']);
		$investor = new Investors($investor_id);
		$investor->assignValues($data);

		if (!$investor->save()) {
			FatUtility::dieWithError($investor->getError());
		}
		$investor_id = $investor->getMainTableRecordId();
		if(!empty($_FILES['image']['tmp_name'])){
			$attachment = new AttachedFile();
			if(!$attachment->uploadAndSaveFile('image',AttachedFile::FILETYPE_INVESTOR_PHOTO, $investor_id,0,0,true)){
				FatUtility::dieJsonError($attachment->getError());
			}
		}
		$this->set('msg', 'Investor Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	public function displayOrderSetup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$investor_id = FatApp::getPostedData('investor_id', FatUtility::VAR_INT);
		$data['investor_display_order'] = @$post['display_order'];
		$investor = new Investors($investor_id);
		$investor->assignValues($data);

		if (!$investor->save()) {
			FatUtility::dieWithError($investor->getError());
		}
		$this->set('msg', 'Display Order Changed');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	public function view($investor_id){
		$investor_id = FatUtility::int($investor_id);
		if($investor_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new Investors($investor_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$this->set('records',$fc->getFlds());
		$this->_template->render(false,false,"investors/_partial/view.php");
	}
}
