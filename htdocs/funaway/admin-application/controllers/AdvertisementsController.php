<?php
#error_reporting(E_ERROR);
class AdvertisementsController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
		$ajaxCallArray = array('adLists','requestLists','adAction','changeDisplayOrder');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewAdvertisement($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditAdvertisement($this->admin_id);
		if(!$this->canView){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	public function index(){
		$brcmb = new Breadcrumb();
		$brcmb->add("Advertisement");
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
	
	public function adLists($page=1){
		$pagesize=50;
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$tblObj = new Advertisement();
		$tblObj->updateStatusExpireAd();
		$search = $tblObj->getAdSearch();
		if(!empty($post['keyword'])){
			$search_con = $search->addCondition('ad_title','like','%'.$post['keyword'].'%');
		}
		$search->addCondition('ad_type','=',0);
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
		$htm = $this->_template->render(false,false,"advertisements/_partial/adListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function requestLists($page=1){
		$pagesize=50;
		$searchForm = $this->getAdRequestSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$tblObj = new Advertisement();
		$search = $tblObj->getAdRequestSearch();
		
		if(!empty($post['keyword'])){
			$search_con = $search->addCondition('adrequest_name','like','%'.$post['keyword'].'%');
			$search_con->attachCondition('adrequest_email','like','%'.$post['keyword'].'%','or');
			$search_con->attachCondition('adrequest_phone','like','%'.$post['keyword'].'%','or');
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
		$htm = $this->_template->render(false,false,"advertisements/_partial/requestListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function viewAdRequest($adrequest_id){
		$adrequest_id = FatUtility::int($adrequest_id);
		$tblObj = new Advertisement();
		$search = $tblObj->getAdRequestSearch();
		$search->addCondition('adrequest_id','=',$adrequest_id);
		$record = FatApp::getDb()->fetch(($search->getResultSet()));
		$this->set('records',$record);
		$this->_template->render(false,false,"advertisements/_partial/viewAdRequest.php");
	}
	
	public function viewAd($ad_id){
		$ad_id = FatUtility::int($ad_id);
		if(empty($ad_id)){
			FatUtility::dieWithError('Something Went Wrong!');
		}
		$tblObj = new Advertisement();
		$this->set('records',$tblObj->getAd($ad_id));
		$this->_template->render(false,false,"advertisements/_partial/viewAd.php");
	}
	
	private function getAdRequestSearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Keyword', 'keyword','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}	
	
	private function getSearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Keyword', 'keyword','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function adForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$post['ad_id'] = empty($post['ad_id'])?0:FatUtility::int($post['ad_id']);
		$ad_id = $post['ad_id'];
		$form = $this->getAdForm($ad_id);
		if($ad_id){
			$tblObj = new Advertisement();
			$post = $tblObj->getAd($ad_id);
			$form->fill($post);
		}
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"advertisements/_partial/adForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function adAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		if (!empty($_FILES['ad_image']['tmp_name']) && !is_uploaded_file($_FILES['ad_image']['tmp_name'])){
			FatUtility::dieJsonError('Image couldn\'t not uploaded.');
		}
		$post = FatApp::getPostedData();
		$post['ad_id'] = isset($post['ad_id'])?FatUtility::int($post['ad_id']):0;
		$form = $this->getAdForm($post['ad_id']);
		$post = $form->getFormDataFromArray($post);
		if($post === false){
			FatUtility::dieJsonError($form->getValidationErrors());
			return;
		}
		if(empty($_FILES['ad_image']['tmp_name']) && empty($post['ad_id'])){
			FatUtility::dieJsonError('Image is mandatory. field');
			return;
		}
		$tblObj = new Advertisement();
		$ad_id = $tblObj->saveAd($post,$post['ad_id']);
		if(!$ad_id){
			FatUtility::dieJsonError($tblObj->getError());
			return;
		}
		if(!empty($_FILES['ad_image']['tmp_name'])){
			$attachment = new AttachedFile();
			if ($attachment->saveImage($_FILES['ad_image']['tmp_name'], AttachedFile::FILETYPE_AD_PHOTO, 
					$ad_id, 0, $_FILES['ad_image']['name'], 0, true)) {
				FatUtility::dieJsonSuccess('Banner Updated!');
			}
			else {
				FatUtility::dieJsonError($attachment->getError());
			}
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	public function changeDisplayOrder(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$ad_id = isset($post['record_id'])?FatUtility::int($post['record_id']):0;
		$display_order = FatUtility::int($post['display_order']);
		if($display_order <= 0){
			FatUtility::dieJsonError('Something went wrong!');
			return;
		}
		if($ad_id <= 0){
			FatUtility::dieJsonError('Something went wrong!');
			return;
		}
		$main_data['ad_display_order'] = $display_order;
		$tblObj = new Advertisement();
		if(!$tblObj->saveAd($main_data,$ad_id)){
			FatUtility::dieJsonError($tblObj->getError());
			return;
		}
		FatUtility::dieJsonSuccess("Display Order Changed!");
	}
	
	private function getAdForm($record_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($record_id >0){
			$action='Update';
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->setRequiredStarWith(FORM::FORM_REQUIRED_STAR_POSITION_NONE);
		$frm->addHiddenField("", 'ad_id',$record_id);
		$frm->addHiddenField("", 'fIsAjax',1);
		$frm->addRequiredField('Title','ad_title');
		$frm->addFileUpload('Image','ad_image');
		$frm->addTextBox('Link','ad_link','');
		$frm->addDateField('Start Date','ad_starting_date','',array('readonly'=>'readonly'));
		$frm->addDateField('End Date','ad_ending_date','',array('readonly'=>'readonly'));
		$frm->addTextBox('Display Order','ad_display_order');
		$frm->addSelectBox('Ad Place','ad_place_id',Info::getAdPlace());
		$frm->addSelectBox('Status','ad_active',Info::getAdStatus());
		$frm->setFormTagAttribute ( 'action', FatUtility::generateUrl("advertisements","ad-action") );
		$frm->setValidatorJsObjectName ( 'formValidator' );
		$frm->setFormTagAttribute ( 'onsubmit', 'submitForm(formValidator,"action_form"); return(false);' );
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
		}
		return $frm;	
	}
	
}
