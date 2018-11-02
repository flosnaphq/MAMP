<?php
class LanguagesController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			FatUtility::dieJsonError("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewLanguage($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditLanguage($this->admin_id);
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

		$brcmb = new Breadcrumb();
		$brcmb->add("Languages");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	
	
	
	public function listing($page=1){
		$pagesize=static::PAGESIZE;
	
		$search = Languages::getSearchObject();
		$page = empty($page) || $page <= 0?1:$page;
		$page = FatUtility::int($page);
		$search->setPageNumber($page);
		$search->setPageSize($pagesize);
		$rs = $search->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"languages/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['lang_id'] = empty($post['lang_id'])?0:FatUtility::int($post['lang_id']);
		$form = $this->getForm($post['lang_id']);
		if(!empty($post['lang_id'])){
			$fc = new Languages($post['lang_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$this->set("lang_id",$post['lang_id']);
		$htm = $this->_template->render(false,false,"languages/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addHiddenField("", 'fIsAjax','1');
		$frm->addHiddenField("", 'language_id');
		$frm->addHtml('','flag','');
		//$frm->addFileUpload('','image');
		$frm->addRequiredField('Language Name','language_name');
		$frm->addSelectBox('Status','language_active',Info::getStatus());
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit','Add/Update',array('class'=>'themebtn btn-default btn-sm'));
		}
		return $frm;	
	}
	
	public function setup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		if (!empty($_FILES['image']['tmp_name']) && !is_uploaded_file($_FILES['image']['tmp_name'])){
			FatUtility::dieJsonError('Image couldn\'t not uploaded.');
		}
		$frm = $this->getForm ();
		$data = $frm->getFormDataFromArray ( FatApp::getPostedData () );
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}
		$language_id = FatApp::getPostedData('language_id', FatUtility::VAR_INT);
		unset($data['language_id']);
		$tmp = new Languages($language_id);
		$tmp->assignValues($data);

		if (!$tmp->save()) {
			FatUtility::dieJsonError($tmp->getError());
			
		}
		$lang_id = $tmp->getMainTableRecordId();
		if(!empty($_FILES['image']['tmp_name'])){
			$attachment = new AttachedFile();
			if(!$attachment->uploadAndSaveFile('image',AttachedFile::FILETYPE_LANGUAGE_PHOTO, $lang_id,0,0,true)){
				FatUtility::dieJsonError($attachment->getError());
			}
		}
		$this->set('msg', 'Language Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	
	
}
