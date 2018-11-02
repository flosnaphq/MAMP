<?php
class SmsTemplateController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewSmsTemplate($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditSmsTemplate($this->admin_id);
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
		$brcmb->add("Sms Template");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	
	private function getSearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Name', 'smstpl_name','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function listing($page=1){
		$pagesize=static::PAGESIZE;
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$search = SmsTemplate::getSearchObject();
		if(!empty($post['tpl_name'])){
			$search->addCondition('smstpl_name','like','%'.$post['smstpl_name'].'%');
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
		$htm = $this->_template->render(false,false,"sms-template/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
              
		$post = FatApp::getPostedData();
		$post['smstpl_id'] = empty($post['smstpl_id'])?0:FatUtility::int($post['smstpl_id']);
                
                //Disable Add Functionality
                if($post['smstpl_id']==0){
                    FatUtility::dieJsonError('Unauthorized Access!');
                }
                
                
		$form = $this->getForm($post['smstpl_id']);
		if(!empty($post['smstpl_id'])){
			$fc = new SmsTemplate($post['smstpl_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"sms-template/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm($smstpl_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($smstpl_id >0){
			$action='Update';
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addHiddenField("", 'smstpl_id',$smstpl_id);
		$frm->addRequiredField('Template Name','smstpl_name');
		$field = $frm->addTextArea('Sms Body','smstpl_body','');
		
		$field->requirements()->setRequired();
		$frm->addTextArea('Replacements','smstpl_replacements');
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
		}
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
		$tplId = FatApp::getPostedData('smstpl_id', FatUtility::VAR_INT);
		unset($data['smstpl_id']);
		$tmp = new SmsTemplate($tplId);
		$tmp->assignValues($data);

		if (!$tmp->save()) {
			FatUtility::dieWithError($tmp->getError());
			
		}

		$this->set('msg', 'Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	
	
	public function view($smstpl_id){
		$smstpl_id = FatUtility::int($smstpl_id);
		if($smstpl_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new SmsTemplate($smstpl_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$records =  $fc->getFlds();
		
		$this->set('records',$records);
		
		$this->_template->render(false,false,"sms-template/_partial/view.php");
	}
}
