<?php
#error_reporting(E_ERROR);
class NavigationController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
		$ajaxCallArray = array('adLists','requestLists','adAction','changeDisplayOrder');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewNavigation($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditNavigation($this->admin_id);
		if(!$this->canView){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	public function index() {
		$brcmb = new Breadcrumb();
		$brcmb->add("Navigations");
		$this->set('breadcrumb',$brcmb->output());
		$links = CMS::getCmsLinks();
		$this->set('links',$links);
		$this->set('navigations',Navigation::otherNavigation());
		$this->_template->render();
	
	}
	
	public function navigationDisplaySetup() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$data = FatApp::getPostedData();
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}

		$navigationId = FatApp::getPostedData('navigation_id', FatUtility::VAR_INT);
		unset($data['navigation_id']);
		$navigation = new Navigation($navigationId);
		$navigation->assignValues($data);

		if (!$navigation->save()) {
			FatUtility::dieWithError($navigation->getError());
			
		}
		FatUtility::dieJsonSuccess('Display Order Updated Successful');
	}
	
	public function changeWindowType() {
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$data = FatApp::getPostedData();
		if (false === $data) {
			 FatUtility::dieWithError(current($frm->getValidationErrors()));
		}

		$navigationId = FatApp::getPostedData('navigation_id', FatUtility::VAR_INT);
		unset($data['navigation_id']);
		$navigation = new Navigation($navigationId);
		$navigation->assignValues($data);

		if (!$navigation->save()) {
			FatUtility::dieWithError($navigation->getError());
			
		}
		FatUtility::dieJsonSuccess('Window Option Updated Successful');
	}
	
	public function getNavigation(){
		$post = FatApp::getPostedData();
		$id = intval($post['loc_id']);
		$nav = new Navigation();
		$srch = $nav->getSearchObject();
		$srch->addCondition('navigation_loc','=',$id);
		$rs = $srch->getResultSet();
		$records = FatApp::getDb()->fetchAll($rs);
		$this->set("navigations",$records);
		$htm = $this->_template->render(false,false,"navigation/_partial/navigation.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function addCmsPage(){
		$post = FatApp::getPostedData();
		$id = intval($post['loc_id']);
		$cms_id = intval($post['cms_id']);
		$cm = new Cms();
		$cms = $cm->getDefaultCms($cms_id,false);
		$nav = array();
		$nav['navigation_cms_id'] = $cms['cms_id']; 
		$nav['navigation_caption'] = $cms['cms_name'];
		$nav['navigation_type'] = 0;
		$nav['navigation_loc'] = $id;
		$nav['navigation_link'] = '';
		$nav['navigation_display_order'] = 0;
		$nav['navigation_open'] = 0;
		$db = FatApp::getDb();
		$db->insertFromArray('tbl_navigations',$nav);
		FatUtility::dieJsonSuccess("Link Added");
	}
	
	public function addOtherPage(){
		$post = FatApp::getPostedData();
		$id = intval($post['loc_id']);
		$other_id = intval($post['other_id']);
		$navigate = Navigation::otherNavigation()[$other_id];
		$nav = array();
		$nav['navigation_cms_id'] = 0; 
		$nav['navigation_caption'] = $navigate['name'];
		$nav['navigation_type'] = 0;
		$nav['navigation_loc'] = $id;
		$nav['navigation_link'] = $navigate['link'];
		$nav['navigation_display_order'] = 0;
		$nav['navigation_open'] = 0;
		$db = FatApp::getDb();
		$db->insertFromArray('tbl_navigations',$nav);
		FatUtility::dieJsonSuccess("Link Added");
	}
	
	public function removeNavigation(){
		$post = FatApp::getPostedData();
		FatApp::getDb()->deleteRecords('tbl_navigations', array("smt"=>"navigation_id = ?",'vals'=>array($post['nav_id']))); 
		FatUtility::dieJsonSuccess("Link Deleted");
	}
	
	public function addCustomLink(){
		$frm = $this->getCustomForm();
		
		$this->set('frm',$frm);
		$htm = $this->_template->render(false,false,"navigation/_partial/customForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	private function getCustomForm(){
		$frm = new Form('action_form',array('id'=>'action_form'));
		$frm->addRequiredField('Caption', 'navigation_caption');
		$frm->addRequiredField('Link', 'navigation_link');
		$frm->addRequiredField('Display Order', 'navigation_display_order');
		$frm->addHiddenField('Navigation Id', 'navigation_id');
		$frm->addSelectBox('Open In', 'navigation_open',Navigation::windowType(),"",array(),"");
		$frm->addSubmitButton('', 'btn_submit','Add');
		return $frm;	
	}
	
	public function actionCustom(){
		$post = FatApp::getPostedData();
		$nav = array();
		$nav['navigation_cms_id'] = 0; 
		$nav['navigation_caption'] = $post['navigation_caption'];
		$nav['navigation_type'] = 1;
		$nav['navigation_loc'] = $post['loc_id'];
		$nav['navigation_link'] = $post['navigation_link'];
		$nav['navigation_display_order'] = $post['navigation_display_order'];
		$nav['navigation_open'] = $post['navigation_open'];
		$db = FatApp::getDb();
		$db->insertFromArray('tbl_navigations',$nav);
		FatUtility::dieJsonSuccess("Link Added");
	}
	
	
	
	
}