<?php
class FaqsController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('faqForm','faqAction','getFaqForm','categoryListing','categoryForm','categoryAction','changeCategoryDisplayOrder','faqListing','changeFaqDisplayOrder');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewFaq($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditFaq($this->admin_id);
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
		FatApp::redirectUser(FatUtility::generateUrl('faqs','FaqCategory'));
	}
	//////////////////////////////////////////////// FAQ CATEGORY ///////////////////////////////////
	
	public function FaqCategory() {
		$this->set ( 'search', $this->getCategorySearchForm ());
		$brcmb = new Breadcrumb();
		$brcmb->add("FAQ Category");
		$this->set('breadcrumb',$brcmb->output());
		$this->_template->render ();
	}
	
	private function getCategorySearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Category Name', 'category_name','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function categoryListing($page=1){
		$pagesize=static::PAGESIZE;
		$searchForm = $this->getCategorySearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$search = FaqCategories::getSearchObject();
		if(!empty($post['category_name'])){
			$search->addCondition('faqcat_name','like','%'.$post['category_name'].'%');
		}
		$search->addOrder('faqcat_display_order','desc');
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
		$htm = $this->_template->render(false,false,"faqs/_partial/categoryListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function categoryForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['category_id'] = empty($post['category_id'])?0:FatUtility::int($post['category_id']);
		$form = $this->getCategoryForm($post['category_id']);
		if(!empty($post['category_id'])){
			
			$fc = new FaqCategories ($post['category_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
			
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"faqs/_partial/categoryForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getCategoryForm($category_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($category_id >0){
			$action='Update';
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addHiddenField("", 'faqcat_id',$category_id);
		
		$frm->addRequiredField('Category name','faqcat_name');

		
		$frm->addTextBox('Display Order','faqcat_display_order');
		$frm->addSelectBox('Category Status','faqcat_active',array('1'=>'Active','0'=>'Inactive'),1);
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
		}
		return $frm;	
	}
	
	public function categorySetup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = $this->getCategoryForm ();
		$data = $frm->getFormDataFromArray ( FatApp::getPostedData () );
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}

		$faqcatId = FatApp::getPostedData('faqcat_id', FatUtility::VAR_INT);
		unset($data['faqcat_id']);
		$faqcategory = new FaqCategories($faqcatId);
		$faqcategory->assignValues($data);

		if (!$faqcategory->save()) {
			FatUtility::dieWithError($faqcategory->getError());
			
		}

		$this->set('msg', 'Category Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	public function categoryDisplaySetup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$data = FatApp::getPostedData();
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}

		$faqcatId = FatApp::getPostedData('faqcat_id', FatUtility::VAR_INT);
		unset($data['faqcat_id']);
		$faqcategory = new FaqCategories($faqcatId);
		$faqcategory->assignValues($data);

		if (!$faqcategory->save()) {
			FatUtility::dieWithError($faqcategory->getError());
			
		}

		$this->set('msg', 'Category Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	public function categoryView($category_id){
		$category_id = FatUtility::int($category_id);
		if($category_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new FaqCategories($category_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$this->set('records',$fc->getFlds());
		$this->_template->render(false,false,"faqs/_partial/viewCategoty.php");
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	public function faqView($faq_id){
		$faq_id = FatUtility::int($faq_id);
		if($faq_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new Faq($faq_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$this->set('records',$fc->getFlds());
		$this->_template->render(false,false,"faqs/_partial/viewFaq.php");
	}
	
	public function faqs($faqcat_id){
		$faqcat_id = FatUtility::int($faqcat_id);
		if(empty($faqcat_id)){
			FatApp::redirectUser(FatUtility::generateUrl('faqs'));
		}
		$brcmb = new Breadcrumb();
		$brcmb->add("FAQ Category",FatUtility::generateUrl('faqs'));
		$fc = new FaqCategories ($faqcat_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$record = $fc->getFlds();
		$brcmb->add($record['faqcat_name']);
		$this->set('breadcrumb',$brcmb->output());
		$search = $this->getSearchForm($faqcat_id);
		$this->set("search",$search);	
		$this->set("faqcat_id",$faqcat_id);	
		$this->_template->render();
	}
	
	private function getSearchForm($faq_faqcat_id){
		$frm = new Form('frmSearch');
		$frm->addHiddenField('','faq_faqcat_id',$faq_faqcat_id);
		$f1 = $frm->addTextBox('Keyword', 'keyword','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function faqListing($faqcat_id,$page=1){
		$faqcat_id = FatUtility::int($faqcat_id);
		if(empty($faqcat_id)){
			FatApp::redirectUser(FatUtility::generateUrl('faqs'));
		}
		$this->set("faqcat_id",$faqcat_id);
		$pagesize=Static::PAGESIZE;
		$searchForm = $this->getSearchForm($faqcat_id);
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$search = Faq::getSearchObject();
		$search->addCondition("faq_faqcat_id","=",$faqcat_id);
		if(!empty($post['keyword'])){
			$search->addCondition('faq_question','like','%'.$post['keyword'].'%')->attachCondition('faq_answer','like','%'.$post['keyword'].'%','or');
			
		}
		$search->addOrder('faq_display_order','desc');
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
		$htm = $this->_template->render(false,false,"faqs/_partial/faqListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function faqForm($faqcat_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$faqcat_id = FatUtility::int($faqcat_id);
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		if(empty($faqcat_id)){
			FatUtility::dieJsonError('Invalid Action!');
		}
		$post = FatApp::getPostedData();
		
		$post['faq_id'] = empty($post['faq_id'])?0:FatUtility::int($post['faq_id']);
		$form = $this->getFaqForm($faqcat_id, $post['faq_id']);
		if(!empty($post['faq_id'])){
			
			$fc = new Faq($post['faq_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
			
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"faqs/_partial/faqForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getFaqForm($faqcat_id,$faq_id){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($faq_id >0){
			$action='Update';
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addHiddenField("", 'faq_faqcat_id',$faqcat_id);
		$frm->addHiddenField("", 'faq_id',$faq_id);
		$frm->addRequiredField('Question','faq_question');
		$text_area_id = "textArea";
		$editor_id = "editorArea";
		
		$frm->addTextArea('Answer','faq_answer','',array('id'=>$text_area_id))->htmlAfterField='<div id="'.$editor_id.'"></div>'.MyHelper::getInnovaEditorObj($text_area_id,$editor_id);
		
		
		
		$frm->addTextBox('Display Order','faq_display_order');
		$frm->addSelectBox('FAQ Status','faq_active',Info::getStatus(),1);
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
		}
		return $frm;	
	}
	
	public function setup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$data = FatApp::getPostedData();
		$faqId = FatApp::getPostedData('faq_id', FatUtility::VAR_INT);
		unset($data['faq_id']);
		$faq = new Faq($faqId);
		$faq->assignValues($data);

		if (!$faq->save()) {
			FatUtility::dieWithError($faqcategory->getError());
			
		}

		$this->set('msg', 'Faq Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	public function changeFaqDisplayOrder() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$data = FatApp::getPostedData();
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}

		$faqId = FatApp::getPostedData('faq_id', FatUtility::VAR_INT);
		unset($data['faq_id']);
		$faq = new Faq($faqId);
		$faq->assignValues($data);

		if (!$faq->save()) {
			FatUtility::dieWithError($faqcategory->getError());
			
		}

		$this->set('msg', 'FAQ Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
}
