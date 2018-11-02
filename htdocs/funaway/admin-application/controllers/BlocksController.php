<?php
class BlocksController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewBlock($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditBlock($this->admin_id);
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
		$brcmb->add("Blocks");
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
		$search = Blocks::getSearchObject();
		if(!empty($post['keyword'])){
			$con = $search->addCondition('block_name','like','%'.$post['keyword'].'%','or');
			$con->attachCondition('block_title','like','%'.$post['keyword'].'%','or');
		}
		$search->addOrder('block_name');
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
		$htm = $this->_template->render(false,false,"blocks/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$post['block_id'] = empty($post['block_id'])?0:FatUtility::int($post['block_id']);
		$form = $this->getForm($post['block_id']);
		if(!empty($post['block_id'])){
			$fc = new Blocks ($post['block_id']);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"blocks/_partial/form.php",true,true);
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
		
		$frm->addHiddenField("", 'block_id',$record_id);
		$text_area_id = 'text_area';
		$editor_id = 'editor_area';
		$frm->addRequiredField('Name','block_name')->developerTags['col']  = 6;
		$frm->addRequiredField('Title','block_title')->developerTags['col']  = 6;
		$frm->addTextArea('Content','block_content','',array('id'=>$text_area_id))->htmlAfterField='<div id="'.$editor_id.'"></div>'.MyHelper::getInnovaEditorObj($text_area_id,$editor_id);
		$frm->addSelectBox('Status','block_active',Info::getStatus())->developerTags['col']  = 6;;
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
		$block_id = FatApp::getPostedData('block_id', FatUtility::VAR_INT);
		unset($data['block_id']);
		$block = new Blocks($block_id);
		$block->assignValues($data);

		if (!$block->save()) {
			FatUtility::dieWithError($block->getError());
			
		}

		$this->set('msg', 'Block Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	
	
	public function view($block_id){
		$block_id = FatUtility::int($block_id);
		if($block_id < 0 ){
			FatUtility::dieJsonError('Something went wrong!');
		}
		$fc = new Blocks($block_id);
		if (! $fc->loadFromDb ()) {
			FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
		}
		$this->set('records',$fc->getFlds());
		$this->_template->render(false,false,"blocks/_partial/view.php");
	}
}
