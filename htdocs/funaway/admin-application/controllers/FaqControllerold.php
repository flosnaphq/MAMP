<?php
#error_reporting(E_ERROR);
class FaqController extends AdminBaseController {
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
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	public function index(){
		$brcmb = new Breadcrumb();
		$brcmb->add("FAQ Category");
		$this->set('breadcrumb',$brcmb->output());
		$search = $this->getSearchForm();
		$this->set("search",$search);	
		$this->_template->render();
	}
	
	public function lists($faqcat_id){
		$faqcat_id = FatUtility::int($faqcat_id);
		if(empty($faqcat_id)){
			FatApp::redirectUser(FatUtility::generateUrl('faq'));
		}
		$brcmb = new Breadcrumb();
		$brcmb->add("FAQ Category",FatUtility::generateUrl('faq'));
		$faqObj = new Faq();
		$record = $faqObj->getCategoryLang($faqcat_id, MyHelper::getDefaultLangId());
		$brcmb->add($record['faqcat_name']);
		$this->set('breadcrumb',$brcmb->output());
		$search = $this->getFaqSearchForm($faqcat_id);
		$this->set("search",$search);	
		$this->set("faqcat_id",$faqcat_id);	
		$this->_template->render();
	}
	
	public function faqListing($faqcat_id, $page=1){
		$faqcat_id = FatUtility::int($faqcat_id);
		if(empty($faqcat_id)){
			FatApp::redirectUser(FatUtility::generateUrl('faq'));
		}
		$pagesize=50;
		$searchForm = $this->getFaqSearchForm($faqcat_id);
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$faqObj = new Faq();
		$search = $faqObj->getFaqs($faqcat_id);
		$search->addCondition('faqlang_lang_id','=',MyHelper::getDefaultLangId());
		if(!empty($post['keyword'])){
			$search->addCondition('faq_question','like','%'.$post['keyword'].'%')->attachCondition('faq_answer','like','%'.$post['keyword'].'%','or');
			
		}
		
		$search->addOrder('faq_display_order','Desc');
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
		$this->set('postedData', $post);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"faq/_partial/faqListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
		
	}
	
	public function faqForm($faqcat_id){
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
			$faqObj = new Faq();
			$post = $faqObj->getFaq($post['faq_id']);
			$form->fill($post);
		}
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"faq/_partial/faqForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function faqAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$post['faqcat_id'] = empty($post['faqcat_id'])?0:FatUtility::int($post['faqcat_id']);
		$post['faq_id'] = empty($post['faq_id'])?0:FatUtility::int($post['faq_id']);
		$form = $this->getFaqForm($post['faqcat_id'], $post['faq_id']);
		
		$post = $form->getFormDataFromArray($post);
		if($post === false){
			FatUtility::dieJsonError($form->getValidationErrors());
			return;
		}
		if(empty($post['faq_answer']) || !is_array($post['faq_answer']) || empty($post['faq_question']) || !is_array($post['faq_question'])){
			FatUtility::dieJsonError('Invalid request');
			return;
		}
		
		$faqObj = new Faq();
		$action='add';
		if($post['faq_id'] > 0){
			$action='update';
		}
		$cat_data['faq_display_order'] = $post['faq_display_order'];
		$cat_data['faq_active'] = $post['faq_active'];
		$cat_data['faq_faqcat_id'] = $post['faqcat_id'];
		$faq_id = $faqObj->saveFaq($cat_data,$post['faq_id']);
		if($faq_id){
			foreach($post['faq_question'] as $language_id=>$post_data){
				$where =array();
				$data['faq_question'] = $post['faq_question'][$language_id];
				$data['faq_answer'] = $post['faq_answer'][$language_id];
				
				if($action == 'update'){
					$where=array('smt'=>'faqlang_faq_id = ? and faqlang_lang_id = ?','vals'=>array($faq_id,$language_id));
				}
				else{
					$data['faqlang_lang_id'] = $language_id;
					$data['faqlang_faq_id'] = $faq_id;
				}
				if(!$faqObj->saveFaqLang($data,$where)){
					FatUtility::dieJsonError($faqObj->getError());
					return;
				} 
			}
		}
		FatUtility::dieJsonSuccess("Record updated!");
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
		$frm->addHiddenField("", 'faqcat_id',$faqcat_id);
		$frm->addHiddenField("", 'faq_id',$faq_id);
		$myHelper = new MyHelper();
		$languages = $myHelper->getLanguages();
		foreach($languages as $language_data){
			$lang_id = $language_data['language_id'];
			$text_area_id = 'text_area_'.$lang_id;
			$editor_id = 'editor_'.$lang_id;
			$frm->addRequiredField('Question ['.$language_data['language_name'].']','faq_question['.$language_data['language_id'].']','',array('class'=>$language_data['language_css']));
			$frm->addTextArea('Answer ['.$language_data['language_name'].']','faq_answer['.$language_data['language_id'].']','',array('class'=>$language_data['language_css'],'id'=>$text_area_id))->htmlAfterField='<div id="'.$editor_id.'"></div>'.MyHelper::getInnovaEditorObj($text_area_id,$editor_id);
		}
		$frm->addTextBox('Display Order','faq_display_order');
		$frm->addSelectBox('Active','faq_active',Info::getStatus(),1);
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
		}
		return $frm;	
	}
	
	public function faqView($faq_id){
		$faq_id = FatUtility::int($faq_id);
		if($faq_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$faqObj = new Faq();
		$record = $faqObj->getFaq($faq_id);
		$myHelper = new MyHelper();
		$languages = $myHelper->getLanguages();
		$this->set('records',$record);
		$this->set('languages',$languages);
		$this->_template->render(false,false,"faq/_partial/viewFaq.php");
	}
	
	public function changeFaqDisplayOrder($faq_id){
		$faq_id = FatUtility::int($faq_id);
		if(empty($faq_id)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$post = FatApp::getPostedData();
		$faqObj = new Faq();
		$data['faq_display_order'] = $post['faq_display_order'];
		if(!$faq_id = $faqObj->saveFaq($data,$faq_id)){
			FatUtility::dieJsonError($faqObj->getError());
		}
		FatUtility::dieJsonSuccess("Display Order changed!");
	}
	
	public function categoryListing($page=1){
		$pagesize=50;
		$searchForm = $this->getSearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$faqObj = new Faq();
		$search = $faqObj->getFaqCategories();
		if(!empty($post['category_name'])){
			$search->addCondition('faqcat_name','like','%'.$post['category_name'].'%');
		}
		$search->addCondition('faqcatlang_lang_id','=',MyHelper::getDefaultLangId());
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
		$htm = $this->_template->render(false,false,"faq/_partial/categoryListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getSearchForm(){
		$frm = new Form('frmSearch');
		$f1 = $frm->addTextBox('Category Name', 'category_name','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	private function getFaqSearchForm($faq_faqcat_id){
		$frm = new Form('frmSearch');
		$frm->addHiddenField('','faq_faqcat_id',$faq_faqcat_id);
		$f1 = $frm->addTextBox('Keyword', 'keyword','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
		return $frm;	
	}
	
	public function categoryForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$post['category_id'] = empty($post['category_id'])?0:FatUtility::int($post['category_id']);
		$form = $this->getCategoryForm($post['category_id']);
		if(!empty($post['category_id'])){
			$faqObj = new Faq();
			$post = $faqObj->getFaqCategory($post['category_id']);
			$form->fill($post);
		}
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"faq/_partial/categoryForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function categoryView($category_id){
		$category_id = FatUtility::int($category_id);
		if($category_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$faqObj = new Faq();
		$record = $faqObj->getFaqCategory($category_id);
		$myHelper = new MyHelper();
		$languages = $myHelper->getLanguages();
		$this->set('records',$record);
		$this->set('languages',$languages);
		$this->_template->render(false,false,"faq/_partial/viewCategoty.php");
	}
	
	public function categoryAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$post['faqcat_id'] = empty($post['faqcat_id'])?0:FatUtility::int($post['faqcat_id']);
		$form = $this->getCategoryForm($post['faqcat_id']);
		
		$post = $form->getFormDataFromArray($post);
		if($post === false){
			FatUtility::dieJsonError($form->getValidationErrors());
			return;
		}
		if(empty($post['faqcat_name'])){
			FatUtility::dieJsonError('Invalid request');
			return;
		}
		if(!is_array($post['faqcat_name'])){
			FatUtility::dieJsonError('Invalid request');
			return;
		}
		
		$faqObj = new Faq();
		$action='add';
		if($post['faqcat_id'] > 0){
			$action='update';
		}
		$cat_data['faqcat_display_order'] = $post['faqcat_display_order'];
		$cat_data['faqcat_active'] = $post['faqcat_active'];
		$faqcat_id = $faqObj->saveCategory($cat_data,$post['faqcat_id']);
		if($faqcat_id){
			foreach($post['faqcat_name'] as $language_id=>$faqcat_name){
				$where =array();
				$data['faqcat_name'] = $post['faqcat_name'][$language_id];
				if($action == 'update'){
					$where=array('smt'=>'faqcatlang_faqcat_id = ? and faqcatlang_lang_id = ?','vals'=>array($faqcat_id,$language_id));
				}
				else{
					$data['faqcatlang_lang_id'] = $language_id;
					$data['faqcatlang_faqcat_id'] = $faqcat_id;
				}
				if(!$faqObj->saveCategoryLang($data,$where)){
					FatUtility::dieJsonError($faqObj->getError());
					return;
				} 
			}
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	public function changeCategoryDisplayOrder($faqcat_id){
		$faqcat_id = FatUtility::int($faqcat_id);
		if(empty($faqcat_id)){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$post = FatApp::getPostedData();
		$faqObj = new Faq();
		$cat_data['faqcat_display_order'] = $post['faqcat_display_order'];
		if(!$faqObj->saveCategory($cat_data,$faqcat_id)){
			FatUtility::dieJsonError($faqObj->getError());
		}
		FatUtility::dieJsonSuccess("Display Order changed!");
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
		$myHelper = new MyHelper();
		$languages = $myHelper->getLanguages();
		foreach($languages as $language_data){
			$lang_id = $language_data['language_id'];
			$frm->addRequiredField('Category name ['.$language_data['language_name'].']','faqcat_name['.$language_data['language_id'].']','',array('class'=>$language_data['language_css']));
		}
		$frm->addTextBox('Display Order','faqcat_display_order');
		$frm->addSelectBox('Active','faqcat_active',array('1'=>'Active','0'=>'Inactive'),1);
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'));
		}
		return $frm;	
	}
	
	
	
	
	
	
	
	
}