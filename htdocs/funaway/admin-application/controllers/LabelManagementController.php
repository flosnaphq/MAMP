<?php
#error_reporting(E_ERROR);
class LabelManagementController extends AdminBaseController{
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewLabel($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditLabel($this->admin_id);
		if(!$this->canView){
			if(FatUtility::isAjaxCall()){
				FatUtility::dieJsonError('Unauthorized Access!');
			}
			else{
				FatUtility::dieWithError('Unauthorized Access!');
			}
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
		
	}
	
	
	 function index() {
		$this->set("search",$this->getSearchForm()); 
		$this->_template->render();
	}
	
	function listing($page=1){
		$page = FatUtility::int($page);
		$pagesize = static::PAGESIZE;
		$page = $page < 1?1:$page;
		
		$post = FatApp::getPostedData();
		$frm = $this->getSearchForm();
		$post = $frm->getFormDataFromArray($post);
		
		
		$srch = Translation::getSearchObject();
		if(!empty($post['keyword'])){
			$srch->addCondition('trans_val', 'LIKE', '%' . $post['keyword'] . '%')->attachCondition('trans_key', 'LIKE', '%' . $post['keyword'] . '%');
		}
		$srch->setPageNumber($page);
		$srch->setPageSize($pagesize);
		$rs = $srch->getResultSet();
		$db = FatApp::getDb();
		$this->set("arr_listing",$db->fetchAll($rs));
		$this->set('totalPage',$srch->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"label-management/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
		
    }
	
	function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		if(empty($post['trans_key'])){
			FatUtility::dieJsonError('Invalid Request');
		}
		$frm = $this->getForm();
		$translation = new Translation();
		$post['trans_key'] = html_entity_decode($post['trans_key']);
		$data = $translation->getTranslationForForm($post['trans_key']);
		
		$frm->fill($data);
		$this->set("frm",$frm);
		$htm = $this->_template->render(false,false,"label-management/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		
		$frm = new Form('translationForm');
		
		$fld = $frm->addHiddenField("", 'trans_key','',array('readonly'=>'readonly'));
		$frm->addTextArea('Label','trans_val','');
		
		$frm->addSubmitButton('', 'btn_submit','Update');
		return $frm;	
	}
	
	
	
	function setup(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$frm = $this->getForm();
		$post = $frm->getFormDataFromArray($post);
		if($post == false){
			FatUtility::dieJsonError('Something Went Wrong!');
		}
		if(!isset($post['trans_key'])){
			FatUtility::dieJsonError('Something Went Wrong!');
		}
		
		$translation = new Translation();
		if(!$translation->addUpdate($post)){
			FatUtility::dieJsonError('Something Went Wrong!');
		}
		
		FatUtility::dieJsonSuccess("Record updated");	
	}
	
	private function getSearchForm(){
		$frm = new Form('frmySearch');
		$frm->setFormTagAttribute('onSubmit','"search();return false;"');
		$frm->addTextBox("", 'keyword',"",array('placeholder'=>Info::t_lang("CONTENT")));
		$frm->addSubmitButton('', 'btn_submit', 'Search');
		return $frm;
	}
	
}