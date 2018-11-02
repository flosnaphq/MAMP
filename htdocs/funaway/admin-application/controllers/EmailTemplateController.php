<?php
class EmailTemplateController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewEmailTemp($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditEmailTemp($this->admin_id);
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
		$brcmb->add("Email Template");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	
	private function getSearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Name', 'tpl_name','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function listing($page=1){
		$pagesize=static::PAGESIZE;
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$search = EmailTemplate::getSearchObject();
		if(!empty($post['tpl_name'])){
			$search->addCondition('tpl_name','like','%'.$post['tpl_name'].'%');
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
		$htm = $this->_template->render(false,false,"email-template/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['tpl_id'] = empty($post['tpl_id'])?0:FatUtility::int($post['tpl_id']);
                
                //Disable Add Functionality
                if($post['tpl_id']==0){
                    FatUtility::dieJsonError('Unauthorized Access!');
                }
                
		$form = $this->getForm($post['tpl_id']);
                
		if(!empty($post['tpl_id'])){
			$fc = new EmailTemplate($post['tpl_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"email-template/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm($tpl_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($tpl_id >0){
			$action='Update';
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addHiddenField("", 'tpl_id',$tpl_id);
		$frm->addRequiredField('Template Name','tpl_name');
		$frm->addRequiredField('Email Subject','tpl_subject');
		$field = $frm->addTextArea('Email Body','tpl_body','',array('id'=>'tpl_body'));
		$field->htmlAfterField='<div id="tpl_body_editor"></div>'.MyHelper::getInnovaEditorObj('tpl_body','tpl_body_editor');
		$field->requirements()->setRequired();
		$frm->addTextArea('Replacements','tpl_replacements');
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
		$tplId = FatApp::getPostedData('tpl_id', FatUtility::VAR_INT);
		unset($data['tpl_id']);
		$tmp = new EmailTemplate($tplId);
		$tmp->assignValues($data);

		if (!$tmp->save()) {
			FatUtility::dieWithError($tmp->getError());
			
		}

		$this->set('msg', 'Template Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	
	
	public function view($tpl_id){
		$tpl_id = FatUtility::int($tpl_id);
		if($tpl_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new EmailTemplate($tpl_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$records =  $fc->getFlds();
		
		$this->set('records',$records);
		
		$this->_template->render(false,false,"email-template/_partial/view.php");
	}
}
