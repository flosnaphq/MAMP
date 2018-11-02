<?php
#error_reporting(E_ERROR);
class LocationsController extends AdminBaseController {
	private $canView;
	private $canEdit;
	private $admin_id; 
	public function __construct($action){
		
		$ajaxCallArray = array("cityLists","cityForm","cityAction",'cityDelete','regionsLists','regionForm','regionAction','regionDelete');
		if(!FatUtility::isAjaxCall() && in_array($action,$ajaxCallArray)){
			die("Invalid Action");
		}
		
		$this->admin_id = AdminAuthentication::getLoggedAdminAttribute("admin_id");
		$this->canView = AdminPrivilege::canViewLocation($this->admin_id);
		$this->canEdit = AdminPrivilege::canEditLocation($this->admin_id);
		if(!$this->canView){
			FatUtility::dieWithError('Unauthorized Access!');
		}
		parent::__construct($action);
		$this->set("canView",$this->canView);
		$this->set("canEdit",$this->canEdit);
	}
	
	/** city code start */
	
	public function index() {
		FatApp::redirectUser(FatUtility::generateUrl('locations','cities'));
	}
	
	public function cities(){
		$brcmb = new Breadcrumb();
		$location = new Locations();
		$brcmb->add("cities");
		$this->set('breadcrumb',$brcmb->output());
		$search = $this->getCitySearchForm();
		$this->set("search",$search);	
		$this->_template->render();
	}
	
	public function cityLists($page=1){
		$pagesize=50;
		$searchForm = $this->getCitySearchForm();
		$data = FatApp::getPostedData();
		$post = $searchForm->getFormDataFromArray($data);
		$location = new Locations();
		$search = $location->getCities();
		if(!empty($post['city_name'])){
			$search->addCondition('city_lang.city_name','like','%'.$post['city_name'].'%');
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
		$htm = $this->_template->render(false,false,"locations/_partial/cityListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function viewCity($city_id){
		$city_id = FatUtility::int($city_id);
		$location = new Locations();
		$record = $location->getCity($city_id);
		$languages = MyHelper::getLanguages();
		$this->set('records',$record);
		$this->set('languages',$languages);
		$this->_template->render(false,false,"locations/_partial/viewCity.php");
	}
	
	public function cityForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$form = $this->getCityForm(empty($post['city_id'])?0:$post['city_id']);
		if(!empty($post['city_id'])){
			$location = new Locations();
			$record = $location->getCity($post['city_id']);
			$post['city_name'] = $record['city_name'];
			$post['city_active']=$record['city_active'];
			
			$form->fill($post);
		}
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"locations/_partial/cityForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function cityDelete(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$city_id = FatUtility::int($post['city_id']);
		if($city_id <=0 ){
			FatUtility::dieWithError('Something went wrong');
			return;
		}
		$location = new Locations();
		if(!$location->saveCity(2,$city_id)){
			FatUtility::dieWithError($location->getError());
			return;
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	public function cityAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$form = $this->getCityForm($post['city_id']);
		
		$post = $form->getFormDataFromArray($post);
		if($post === false){
			FatUtility::dieWithError($form->getValidationErrors());
			return;
		}
		if(empty($post['city_name'])){
			FatUtility::dieWithError('Invalid request');
			return;
		}
		if(!is_array($post['city_name'])){
			FatUtility::dieWithError('Invalid request');
			return;
		}
		$city_id = empty($post['city_id'])?0:$post['city_id'];
		$location = new Locations();
		$action='add';
		if($city_id > 0){
			$action='update';
		}
		$city_id = $location->saveCity($post['city_active'],$city_id);
		foreach($post['city_name'] as $language_id=>$city_name){
			if($action == 'update'){
				if(!$location->updateCityLang($city_name,$language_id,$city_id)){
					FatUtility::dieWithError($location->getError());
					return;
				}
			}
			else{
				if(!$location->addCityLang($city_name,$language_id,$city_id)){
					FatUtility::dieWithError($location->getError());
					return;
				} 
			}
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	private function getCityForm($city_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($city_id >0){
			$action='Update';
		}
		$frm = new Form('frmAdmin');
		$frm->addHiddenField("", 'city_id');
		$frm->addHiddenField("", 'fIsAjax',1);
		$frm->addHiddenField("", 'fOutMode','html');
		$myHelper = new MyHelper();
		$languages = $myHelper->getLanguages();
		foreach($languages as $language_data){
			$frm->addRequiredField($language_data['language_name'].' City name','city_name['.$language_data['language_id'].']','',array('class'=>$language_data['language_css']));
		}
		$frm->addSelectBox('Status','city_active',Info::getLocationStatus(),1);
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'))->html_after_field = '<input type="button"  class="" value="Cancel" onclick = "removeFormBox();">';
		}
		return $frm;	
	}
	
	private function getCitySearchForm(){
		$frm = new Form('frmSearch');
	
		$f1 = $frm->addTextBox('City Name', 'city_name','',array('class'=>'search-input'));
		$field = $frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm'));
	//	$field->html_after_field = '<input type="button"  class="" value="Reset" onclick = "removeFormBox();"> ';
		//$frm->addButton('', 'Clear_search','Clear Search',array('onclick'=>'clearSearch()','class'=>'clear-search-btn'));
		return $frm;	
	}
	/** city code end */
	
	/* Region code start */
	
	public function regions($city_id){
		$default_lang = MyHelper::getDefaultLangId();
		$city_id = FatUtility::int($city_id);
		$brcmb = new Breadcrumb();
		$location = new Locations();
		$city_data = $location->getCity($city_id);
		//$brcmb->add("locations", FatUtility::generateUrl('locations','cities'));
		$brcmb->add("cities",FatUtility::generateUrl('locations','cities'));
		$brcmb->add($city_data['city_name'][$default_lang]);
		$this->set('breadcrumb',$brcmb->output());
		$search = $this->getRegionSearchForm($city_id);
		$this->set("search",$search);	
		$this->set("city_id",$city_id);	
		$this->set("city_name",$city_data['city_name'][$default_lang]);	
		$this->_template->render();
	}
	
	public function regionsLists($city_id,$page=1){
		$pagesize=50;
		$post = FatApp::getPostedData();
		$searchForm = $this->getRegionSearchForm(empty($post['city_id'])?0:$post['city_id']);
		
		$post = $searchForm->getFormDataFromArray($post);
		$location = new Locations();
		$search = $location->getRegions();
		
		if(!empty($post['keyword'])){
			$search->addCondition('region_name','like','%'.$post['keyword'].'%');
		}
		$search->addCondition('region_city_id','=',FatUtility::int($city_id));
		$search->setPageNumber($page);
		$search->setPageSize($pagesize);
		$rs = $search->getResultSet();
		
		$records = FatApp::getDb()->fetchAll($rs);
		//var_dump($records);
		$this->set("arr_listing",$records);
		$this->set('totalPage',$search->pages());
		$this->set('page', $page);
		$this->set('postedData', $post);
		$this->set('pageSize', $pagesize);
		$htm = $this->_template->render(false,false,"locations/_partial/regionListing.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function viewRegion($region_id){
		$region_id = FatUtility::int($region_id);
		$languages = MyHelper::getLanguages();
		$location = new Locations();
		$record = $location->getRegion($region_id);
		$this->set('records',$record);
		$this->set('languages',$languages);
		$this->_template->render(false,false,"locations/_partial/viewRegion.php");
	}
	
	public function regionForm(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$form = $this->getRegionForm(empty($post['city_id'])?0:$post['city_id'],empty($post['region_id'])?0:$post['region_id']);
		if(!empty($post['region_id'])){
			$location = new Locations();
			$post = $location->getRegion($post['region_id']);
			$form->fill($post);
		}
		$adm = new Admin();
		$this->set("frm",$form);
		$htm = $this->_template->render(false,false,"locations/_partial/regionForm.php",true,true);
		FatUtility::dieJsonSuccess($htm);
	}
	
	public function regionAction(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$form = $this->getRegionForm($post['city_id'],$post['region_id']);
		
		$post = $form->getFormDataFromArray($post);
		if($post === false){
			FatUtility::dieWithError($form->getValidationErrors());
			return;
		}
		if(empty($post['region_name'])){
			FatUtility::dieWithError('Invalid request');
			return;
		}
		if(!is_array($post['region_name'])){
			FatUtility::dieWithError('Invalid request');
			return;
		}
		
		$region_id = empty($post['region_id'])?0:$post['region_id'];
		$location = new Locations();
		$action='add';
		if($region_id > 0){
			$action='update';
		}
		
		$region_id = $location->saveRegion($post['region_active'], FatUtility::int($post['city_id']), FatUtility::int($region_id));
		foreach($post['region_name'] as $language_id=>$region_name){
			if($action == 'update'){
				if(!$location->updateRegionLang($region_name,$language_id,$region_id)){
					FatUtility::dieWithError($location->getError());
					return;
				}
			}
			else{
				if(!$location->addRegionLang($region_name,$language_id,$region_id)){
					FatUtility::dieWithError($location->getError());
					return;
				} 
			}
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	public function regionDelete(){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$post = FatApp::getPostedData();
		$region_id = FatUtility::int($post['region_id']);
		$city_id = FatUtility::int($post['city_id']);
		if($region_id <=0 ){
			FatUtility::dieWithError('Something went wrong');
			return;
		}
		$location = new Locations();
		if(!$location->saveRegion(2,$city_id,$region_id)){
			FatUtility::dieWithError($location->getError());
			return;
		}
		FatUtility::dieJsonSuccess("Record updated!");
	}
	
	private function getRegionForm($city_id,$region_id=0){
		if(!$this->canEdit){
			FatUtility::dieJsonError('Unauthorized Access!');
		}
		$action='Add';
		if($region_id >0){
			$action='Update';
		}
		$frm = new Form('frmAdmin');
		$frm->addHiddenField('', 'city_id',$city_id);
		$frm->addHiddenField('', 'region_id',$region_id);
		$frm->addHiddenField("", 'fIsAjax',1);
		$frm->addHiddenField("", 'fOutMode','html');
		$myHelper = new MyHelper();
		$languages = $myHelper->getLanguages();
		foreach($languages as $language_data){
			$frm->addRequiredField($language_data['language_name'].' Region name','region_name['.$language_data['language_id'].']','',array('class'=>$language_data['language_css']));
		}
		$frm->addSelectBox('Active','region_active',Info::getLocationStatus(),1);
		if($this->canEdit){
			$frm->addSubmitButton('', 'btn_submit',$action,array('class'=>'themebtn btn-default btn-sm'))->html_after_field = '<input type="button"  class="" value="Cancel" onclick = "removeFormBox();">';
		}
		return $frm;	
	}
	
	private function getRegionSearchForm($city_id){
		$frm = new Form('frmSearch');
		$frm->addTextBox('Name', 'keyword','',array('class'=>'search-input'));
		$frm->addHiddenField('', 'city_id',$city_id);
		$frm->addSubmitButton('', 'btn_submit','Search',array('class'=>'themebtn btn-default btn-sm')) ->html_after_field = '<input type="button"  class="" value="Reset" onclick = "removeFormBox();"> ';
		//$frm->addButton('', 'Clear_search','Clear Search',array('onclick'=>'clearSearch()','class'=>'clear-search-btn'));
		return $frm;	
	}
	/* Region code end */
	
	
	public function view(){
		
		$this->_template->render();
	}
	
}?>