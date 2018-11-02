<?php
class CancellationPoliciesController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup','cmsDisplaySetup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewCancellationPolicy($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditCancellationPolicy($this->admin_id);
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
		$brcmb->add("Cancellation Policies");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	
	private function getSearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Name', 'cancellationpolicy_name','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function listing($page=1){
		$pagesize=static::PAGESIZE;
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$search = CancellationPolicy::getSearchObject();
		if(!empty($post['cancellationpolicy_name'])){
			$search->addCondition('cancellationpolicy_name','like','%'.$post['cancellationpolicy_name'].'%');
		}
		$search->addOrder(CancellationPolicy::DB_TBL_PREFIX.'display_order','desc');
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
		$htm = $this->_template->render(false,false,"cancellation-policies/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['cancellationpolicy_id'] = empty($post['cancellationpolicy_id'])?0:FatUtility::int($post['cancellationpolicy_id']);
		$form = $this->getForm($post['cancellationpolicy_id']);
		if(!empty($post['cancellationpolicy_id'])){
			$fc = new CancellationPolicy ($post['cancellationpolicy_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"cancellation-policies/_partial/form.php",true,true);
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
		
		$frm->addHiddenField("", CancellationPolicy::DB_TBL_PREFIX.'id',$record_id);
		$text_area_id = 'text_area';
		$editor_id = 'editor_area';
		$frm->addRequiredField('Name',CancellationPolicy::DB_TBL_PREFIX.'name')->developerTags['col']  = 6;
		$fld = $frm->addSelectBox('User Type',CancellationPolicy::DB_TBL_PREFIX.'user_type',Info::getUserType(),'0',array(),'');
		$fld->developerTags['col']  = 6;
		$fld->requirements()->setRequired();
		
		
		
		$frm->addTextArea('Content',CancellationPolicy::DB_TBL_PREFIX.'content','',array('id'=>$text_area_id))->htmlAfterField='<div id="'.$editor_id.'"></div>'.MyHelper::getInnovaEditorObj($text_area_id,$editor_id);
		
		$frm->addTextBox('Display order',CancellationPolicy::DB_TBL_PREFIX.'display_order')->developerTags['col']  = 6;;
		$fld = $frm->addRequiredField('Day(s)',CancellationPolicy::DB_TBL_PREFIX.'days');
		$fld->developerTags['col']  = 6;
		$fld->requirements()->setRequired();
		$frm->addSelectBox('Status',CancellationPolicy::DB_TBL_PREFIX.'active',Info::getStatus(),'1',array(),'')->developerTags['col']  = 6;;
		$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("CancellationPolicies","setup") );
		$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"action_form"); return(false);' );
		$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'))->htmlAfterField="<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
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
		$id = FatApp::getPostedData(CancellationPolicy::DB_TBL_PREFIX.'id', FatUtility::VAR_INT);
		
		unset($data[CancellationPolicy::DB_TBL_PREFIX.'id']);
		$policy = new CancellationPolicy($id);
		
		
		$policy->assignValues($data);

		if (!$policy->save()) {
			FatUtility::dieWithError($policy->getError());
			
		}

		$this->set('msg', 'Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	
	
	public function displaySetup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$data = FatApp::getPostedData();
		

		$id = FatApp::getPostedData(CancellationPolicy::DB_TBL_PREFIX.'id', FatUtility::VAR_INT);
		unset($data[CancellationPolicy::DB_TBL_PREFIX.'id']);
		$policy = new CancellationPolicy($id);
		$policy->assignValues($data);

		if (!$policy->save()) {
			FatUtility::dieWithError($policy->getError());
			
		}

		$this->set('msg', 'Display order updated Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	public function view($id){
		$id = FatUtility::int($id);
		if($id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new CancellationPolicy($id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$this->set('records',$fc->getFlds());
		$this->_template->render(false,false,"cancellation-policies/_partial/view.php");
	}
	
	
}
