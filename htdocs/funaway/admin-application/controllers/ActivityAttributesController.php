<?php
class ActivityAttributesController extends AdminBaseController {
	
	private $canView;
	private $canEdit;
	private $admin_id; 
	
	public function __construct($action){
		$ajaxCallArray = array('listing','form','setup');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewActivityAttribute($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditActivityAttribute($this->admin_id);
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
		$brcmb->add("Activity Attributes");
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
		$search = ActivityAttributes::getSearchObject();
		if(!empty($post['keyword'])){
			$con = $search->addCondition(ActivityAttributes::DB_TBL_PREFIX.'caption','like','%'.$post['keyword'].'%');
			
		}
		$search->addOrder(ActivityAttributes::DB_TBL_PREFIX.'caption');
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
		$htm = $this->_template->render(false,false,"activity-attributes/_partial/listing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	
	public function form(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		
		$id = empty($post['id'])?0:FatUtility::int($post['id']);
		$form = $this->getForm();
		if(!empty($id)){
			$fc = new ActivityAttributes ($id);
			if (! $fc->loadFromDb ()) {
				FatUtility::dieWithError ( 'Error! ' . $fc->getError () );
			}
			$form->fill($fc->getFlds());
		}
		
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"activity-attributes/_partial/form.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$frm = new Form('action_form',array('id'=>'action_form'));
		
		$frm->addHiddenField("", ActivityAttributes::DB_TBL_PREFIX.'id');
		$text_area_id = 'text_area';
		$editor_id = 'editor_area';
		$frm->addRequiredField('Name',ActivityAttributes::DB_TBL_PREFIX.'caption')->developerTags['col']  = 12;
		$frm->addRadioButtons('Required File', ActivityAttributes::DB_TBL_PREFIX.'file_required', Info::getIs())->developerTags['col']  = 6;;
		$frm->addSelectBox('Status', ActivityAttributes::DB_TBL_PREFIX.'status', Info::getStatus(),1, array(),'')->developerTags['col']  = 6;;
		$frm->addSubmitButton('', 'btn_submit','Add / Update',array())->htmlAfterField="<input type='button' name='cancel' value='Cancel' class='themebtn btn-default btn-sm' onclick='closeForm()'>";
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
		$id = FatApp::getPostedData(ActivityAttributes::DB_TBL_PREFIX.'id', FatUtility::VAR_INT);
		unset($data['block_id']);
		$fc = new ActivityAttributes($id);
		$fc->assignValues($data);

		if (!$fc->save()) {
			FatUtility::dieWithError($fc->getError());
			
		}

		$this->set('msg', 'Setup Successful');
		$this->_template->render(false, false, 'json-success.php');
	}
	
	
	
}
