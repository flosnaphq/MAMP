<?php
class OfficesController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewOffice($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditOffice($this->admin_id);
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
		$brcmb->add("Offices");
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
		$search = Office::getSearchObject();
		if(!empty($post['keyword'])){
			$con = $search->addCondition('office_country','like','%'.$post['keyword'].'%','or');
			$con->attachCondition('office_address','like','%'.$post['keyword'].'%','or');
		}
		$search->addOrder('office_country');
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
		$htm = $this->_template->render(false,false,"offices/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['office_id'] = empty($post['office_id'])?0:FatUtility::int($post['office_id']);
		$form = $this->getForm($post['office_id']);
		if(!empty($post['office_id'])){
			$fc = new Office ($post['office_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"offices/_partial/form.php",true,true);
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
		/* $frm->addHtml('','show_image',"<img src='".FatUtility::generateUrl('image','office',array($record_id, 50,50,rand(100,1000)),'/')."'>")->developerTags['col']  = 6;
		$frm->addFileUpload('Image','image')->developerTags['col']  = 6; */
		$frm->addHiddenField("", 'office_id',$record_id);
		$frm->addHiddenField("", 'fIsAjax',1);
		$frm->addRequiredField('Country Name','office_country')->developerTags['col']  = 6;
		$fld = $frm->addTextArea('Address','office_address','',array());
		$fld->requirements()->setRequired();
		$fld->developerTags['col']=6;
		$fld->htmlAfterField='Press Enter for new Line';
		$frm->addSelectBox('Status','office_active',Info::getStatus())->developerTags['col']  = 6;;
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
		$office_id = FatApp::getPostedData('office_id', FatUtility::VAR_INT);
		unset($data['office_id']);
		$office = new Office($office_id);
		$office->assignValues($data);

		if (!$office->save()) {
			FatUtility::dieWithError($office->getError());
			
		}
		$office_id = $office->getMainTableRecordId();
		if(!empty($_FILES['image']['tmp_name'])){
			$attachment = new AttachedFile();
			if(!$attachment->uploadAndSaveFile('image',AttachedFile::FILETYPE_OFFICE_PHOTO, $office_id,0,0,true)){
				FatUtility::dieJsonError($attachment->getError());
			}
		}

		$this->set('msg', 'Office Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	
	
	public function view($office_id){
		$office_id = FatUtility::int($office_id);
		if($office_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new Office($office_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$this->set('records',$fc->getFlds());
		$this->_template->render(false,false,"offices/_partial/view.php");
	}
}
