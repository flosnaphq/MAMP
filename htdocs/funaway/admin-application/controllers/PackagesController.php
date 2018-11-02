<?php
#error_reporting(E_ERROR);
class PackagesController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
		
		$ajaxCallArray = array('lists','form','action','optionLists','optionForm','optionAction');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewPackages($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditPackages($this->admin_id);
		if(!$this->canView){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	public function index(){
		$brcmb = new Breadcrumb();
		$brcmb->add("packages");
		$this->set('breadcrumb',$brcmb->output());
		$search = $this->getSearchForm();
		$this->set("search",$search);	
		$this->_template->render();
	}
	
	public function lists($page=1){
		$pagesize=50;
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$packageObj = new Packages();
		$search = $packageObj->getPackages();
		if(!empty($post['package_name'])){
			$search->addCondition('package_lang.package_name','like','%'.$post['package_name'].'%');
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
		$htm = $this->_template->render(false,false,"packages/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getSearchForm(){
		$frm = new Form('frmSearch');
		$frm->addHiddenField('','fIsAjax',1);
		$f1 = $frm->addTextBox('Package Name', 'package_name','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$form = $this->getForm(empty($post['package_id'])?0:$post['package_id']);
		if(!empty($post['package_id'])){
			$packageObj = new Packages();
			$post = $packageObj->getPackage($post['package_id']);
			$form->fill($post);
		}
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"packages/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function viewPackage($package_id){
		$package_id = FatUtility::int($package_id);
		$packageObj = new Packages();
		$record = $packageObj->getPackage($package_id);
		$myHelper = new MyHelper();
		$languages = $myHelper->getLanguages();
		$this->set('records',$record);
		$this->set('languages',$languages);
		$this->_template->render(false,false,"packages/_partial/viewPackage.php");
	}
	
	public function action(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$form = $this->getForm($post['package_id']);
		
		$post = $form->getFormDataFromArray($post);
		if($post === false){
			FatUtility::dieJsonError($form->getValidationErrors());
			return;
		}
		if(empty($post['package_name'])){
			FatUtility::dieJsonError('Invalid request');
			return;
		}
		if(!is_array($post['package_name'])){
			FatUtility::dieJsonError('Invalid request');
			return;
		}
		$package_id = empty($post['package_id'])?0:$post['package_id'];
		$packageObj = new Packages();
		$action='add';
		if($package_id > 0){
			$action='update';
		}
		
		$package_id = $packageObj->savePackage($post['package_active'],$package_id);
		foreach($post['package_name'] as $language_id=>$package_name){
			$data['package_name'] = $post['package_name'][$language_id];
			$data['package_sub_title'] = $post['package_sub_title'][$language_id];
			$data['package_description'] = $post['package_description'][$language_id];
			$data['packagelang_lang_id'] = $language_id;
			
			if($action == 'update'){
				if(!$packageObj->updatePackageLang($data,$language_id,$package_id)){
					FatUtility::dieJsonError($packageObj->getError());
					return;
				}
			}
			else{
				if(!$packageObj->addPackageLang($data,$language_id,$package_id)){
					FatUtility::dieJsonError($packageObj->getError());
					return;
				} 
			}
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	private function getForm($package_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($package_id >0){
			$action='Update';
		}
		$frm = new Form('frmAdmin');
		$frm->addHiddenField("", 'package_id');
		$frm->addHiddenField("", 'fIsAjax',1);
		$myHelper = new MyHelper();
		$languages = $myHelper->getLanguages();
		foreach($languages as $language_data){
			$lang_id = $language_data['language_id'];
			$frm->addRequiredField('Package name ['.$language_data['language_name'].']','package_name['.$language_data['language_id'].']','',array('class'=>$language_data['language_css']));
			$frm->addTextArea('Sub Title ['.$language_data['language_name'].']','package_sub_title['.$language_data['language_id'].']','',array('class'=>$language_data['language_css']))->requirements()->setRequired();
			
			$frm->addTextArea('Description ['.$language_data['language_name'].']','package_description['.$language_data['language_id'].']','',array('class'=>$language_data['language_css'],'id'=>'description_'.$language_data['language_id']))->htmlAfterField='<div id="editor_'.$language_data['language_id'].'"></div>'.MyHelper::getInnovaEditorObj('description_'.$language_data['language_id'], 'editor_'.$language_data['language_id']);
			
		}
		$frm->addSelectBox('Active','package_active',Info::getStatus(),1);
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'))->html_after_field = '<input type="button"  class="" value="Cancel" onclick = "removeFormBox();">';
		}
		return $frm;	
	}
	
	public function options($package_id){
		$package_id = FatUtility::int($package_id);
		if(empty($package_id)){
			FatApp::redirectUser(FatUtility::generateUrl('packages'));
			return;
		}
		$packageObj = new Packages();
		$record = $packageObj->getPackage($package_id);
		$package_name = $record['package_name'][MyHelper::getDefaultLangId()];
		$brcmb = new Breadcrumb();
		$brcmb->add("packages",FatUtility::generateUrl('packages'));
		$brcmb->add($package_name);
		$this->set('breadcrumb',$brcmb->output());
		$this->set('package_name',$package_name);
		$this->set('package_id',$package_id);
		$this->_template->render();
	}
	
	public function viewOption($option_id){
		$option_id = FatUtility::int($option_id);
		$packageObj = new Packages();
		$search = $packageObj->getOptions(0,$option_id);
		$rs = $search->getResultSet();
		$record = FatApp::getDb()->fetchAll($rs);
		$this->set('records',$record);
		$this->_template->render(false,false,"packages/_partial/viewOption.php");
	}
	
	public function optionLists($page){
		$pagesize=50;
		$data = FatApp::getPostedData();
		$package_id = FatUtility::int($data['package_id']);
		$packageObj = new Packages();
		$search = $packageObj->getOptions($package_id);
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
		$htm = $this->_template->render(false,false,"packages/_partial/optionListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function optionForm($package_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$package_id = FatUtility::int($package_id);
		$post['option_id'] = FatUtility::int($post['option_id']);
		$option_id = empty($post['option_id'])?0:$post['option_id'];
		$form = $this->getOptionForm($package_id, $option_id);
		if(!empty($post['option_id'])){
			$packageObj = new Packages();
			$search = $packageObj->getOptions($package_id, $post['option_id']);
			$rs = $search->getResultSet();
			$records = FatApp::getDb()->fetchAll($rs);
			foreach($records  as $record){
				$post['packageopt_price'] = $record['packageopt_price'];
				$post['packageopt_days']= $record['packageopt_days'];
				$post['packageopt_active']= $record['packageopt_active'];
			}
			$form->fill($post);
		}
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"packages/_partial/optionForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getOptionForm($package_id,$option_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($option_id >0){
			$action='Update';
		}
		$frm = new Form('frmAdmin');
		$frm->addHiddenField("", 'package_id',$package_id);
		$frm->addHiddenField("", 'option_id',$option_id);
		$frm->addHiddenField("", 'fIsAjax',1);
		$frm->addTextBox('Price','packageopt_price')->requirements()->setRequired();
		$frm->addSelectBox('Days','packageopt_days',Info::getPackageOptionDays())->requirements()->setRequired();
		$frm->addSelectBox('Active','packageopt_active',Info::getStatus(),1);
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'))->html_after_field = '<input type="button"  class="" value="Cancel" onclick = "removeFormBox();">';
		}
		return $frm;	
	}
	
	public function optionAction(){
		$where = array();
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$form = $this->getOptionForm($post['package_id'],$post['option_id']);
		$post = $form->getFormDataFromArray($post);
		if($post === false){
			FatUtility::dieJsonError($form->getValidationErrors());
			return;
		}
		if(!empty($post['option_id'])){
			$where=array('smt'=>'packageopt_id = ? and packageopt_package_id = ? ','vals'=>array($post['option_id'],$post['package_id']));
		}
		else{
			$post['packageopt_package_id'] = $post['package_id'];
		}
		$packageObj = new Packages();
		if(!$packageObj->saveOption($post, $where)){
			FatUtility::dieJsonError($packageObj->getError());
			return;
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	
	
	
	
}